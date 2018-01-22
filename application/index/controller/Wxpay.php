<?php
/**
 * 微信支付接口
 * @  wxappid 微信的appid
 * @  wxmch_id 商户号
 * @  wxnotify_url 异步接收通知地址
 * @  wxkey 密匙
 */
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Wxpay extends Controller{
    private $wxappid;
    private $wxmch_id;
    private $wxnotify_url;
    private $wxkey;

    public function __construct(){
        $wxdata=db::name('wxset')->find('1');
        $this->wxappid=$wxdata['appid'];
        $this->wxmch_id=$wxdata['mch_id'];
        $this->wxnotify_url=$wxdata['notify_url'];
        $this->wxkey=$wxdata['wxkey'];
    }

    /* 小程序报名，生成订单 */
    public function make_order(){
        if(Request::instance()->isPost()){

            $data=Request::instance()->param();
            $data['openid']=Request::instance()->param('openid');
            $data['username']=$data['address']['userName'];
            $data['tel']=$data['address']['telNumber'];
            $data['address']=$data['address']['provinceName'].$data['address']['cityName'].$data['address']['countyName'].$data['address']['detailInfo'];
            $data['return_code']="false";
            $data['time']=time();

            $data_total=Request::instance()->param('TotalMoney');
            $data['crsNo'] = 'W'.date('YmdHis',time()).randomkeys(3);
            if(empty($data['openid'])){
                echo json_encode(array('state'=>0,'Msg'=>'登录失效，请重新登录(openid参数有误)'));exit;
            }
            $insertId =db::name('order')->insert($data);

            if($insertId){
                $this->insertID = $insertId;
                $this->data_total = $data_total*100;    //订单总金额，单位分
                /* 调用微信【统一下单】 */
                $this->pay($data_total*100,$data['openid'],$data['crsNo'],$insertId);
            }else{
                echo $insertId;
            }
            
        }
    }

    /* 首先在服务器端调用微信【统一下单】接口，返回prepay_id和sign签名等信息给前端，前端调用微信支付接口 */
    private function Pay($total_fee,$openid,$order_id,$insertId){
        if(empty($total_fee)){
            echo json_encode(array('state'=>0,'Msg'=>'金额有误'));exit;
        }
        if(empty($openid)){
            echo json_encode(array('state'=>0,'Msg'=>'登录失效，请重新登录(openid参数有误)'));exit;
        }
        if(empty($order_id)){
            echo json_encode(array('state'=>0,'Msg'=>'自定义订单有误'));exit;
        }
        // $appid =        'wx57133acd96904e04';
        $appid =        $this->wxappid;
        //如果是公众号 就是公众号的appid;小程序就是小程序的appid
        $body =         "自定义";
        // $mch_id =       '1252484701';
        $mch_id =       $this->wxmch_id;
        // $KEY = 'am36cotthedgmv102uei7wwyz8fl6fbk';
        $KEY =          $this->wxkey;
        $nonce_str =    randomkeys(32);//随机字符串
        // $notify_url =   'https://www.qitmx.com/shop/tp5/public/index.php/index/wxpay/notify_url';
        $notify_url =   $this->wxnotify_url;  //支付完成回调地址url,不能带参数
        $out_trade_no = $order_id;//商户订单号
        $spbill_create_ip = $_SERVER['SERVER_ADDR'];
        $trade_type = 'JSAPI';//交易类型 默认JSAPI
    
        //这里是按照顺序的 因为下面的签名是按照(字典序)顺序 排序错误 肯定出错
        $post['appid'] = $appid;
        $post['body'] = $body;
        $post['mch_id'] = $mch_id;
        $post['nonce_str'] = $nonce_str;//随机字符串
        $post['notify_url'] = $notify_url;
        $post['openid'] = $openid;
        $post['out_trade_no'] = $out_trade_no;
        $post['spbill_create_ip'] = $spbill_create_ip;//服务器终端的ip
        $post['total_fee'] = intval($total_fee);        //总金额 最低为一分钱 必须是整数
        $post['trade_type'] = $trade_type;
        $sign = $this->MakeSign($post,$KEY);              //签名
        $this->sign = $sign;
    
        $post_xml = '<xml>
               <appid>'.$appid.'</appid>
               <body>'.$body.'</body>
               <mch_id>'.$mch_id.'</mch_id>
               <nonce_str>'.$nonce_str.'</nonce_str>
               <notify_url>'.$notify_url.'</notify_url>
               <openid>'.$openid.'</openid>
               <out_trade_no>'.$out_trade_no.'</out_trade_no>
               <spbill_create_ip>'.$spbill_create_ip.'</spbill_create_ip>
               <total_fee>'.$total_fee.'</total_fee>
               <trade_type>'.$trade_type.'</trade_type>
               <sign>'.$sign.'</sign>
            </xml> ';
    
        //统一下单接口prepay_id
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $xml = $this->http_request($url,$post_xml);     //POST方式请求http
        $array = $this->xml2array($xml);               //将【统一下单】api返回xml数据转换成数组，全要大写
        if($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS'){
            $time = time();
            $tmp='';                            //临时数组用于签名
            $tmp['appId'] = $appid;
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id='.$array['PREPAY_ID'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";
            $data['state'] = 1;
            $data['timeStamp'] = "$time";           //时间戳
            $data['nonceStr'] = $nonce_str;         //随机字符串
            $data['signType'] = 'MD5';              //签名算法，暂支持 MD5
            $data['package'] = 'prepay_id='.$array['PREPAY_ID'];   //统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $data['paySign'] = $this->MakeSign($tmp,$KEY);       //签名,具体签名方案参见微信公众号支付帮助文档;
            $data['out_trade_no'] = $out_trade_no;
    
        }else{
            $data['state'] = 0;
            $data['text'] = "错误";
            $data['RETURN_CODE'] = $array['RETURN_CODE'];
            $data['RETURN_MSG'] = $array['RETURN_MSG'];
        }
        exit(json_encode($data));
    }
    
    /**
     * 生成签名, $KEY就是支付key
     * @return 签名
     */
    public function MakeSign( $params,$KEY){
        //签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = $this->ToUrlParams($params);  //参数进行拼接key=value&k=v
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     * 将参数拼接为url: key=value&key=value
     * @param $params
     * @return string
     */
    public function ToUrlParams( $params ){
        $string = '';
        if( !empty($params) ){
            $array = array();
            foreach( $params as $key => $value ){
                $array[] = $key.'='.$value;
            }
            $string = implode("&",$array);
        }
        return $string;
    }
    /**
     * 调用接口， $data是数组参数
     * @return 签名
     */
    public function http_request($url,$data = null,$headers=array())
    {
        $curl = curl_init();
        if( count($headers) >= 1 ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
    
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    //获取xml里面数据，转换成array
    private function xml2array($xml){
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $data = "";
        foreach ($index as $key=>$value) {
            if($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];
            $data[$tag] = $value;
        }
        return $data;
    }

    function xmlToArray($xml)
    {    
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $values;
    }
    /**
     * 将xml转为array
     * @param string $xml
     * return array
     */
    public function xml_to_array($xml){
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

       /* 微信支付完成，回调地址url方法  xiao_notify_url() */
    public function notify_url(){
        $post = $GLOBALS['HTTP_RAW_POST_DATA']; 
        $post_data = $this->xmlToArray($post);   //微信支付成功，返回回调地址url的数据：XML转数组Array
        $postSign = $post_data['sign'];
        unset($post_data['sign']);
        /* 微信官方提醒：
         *  商户系统对于支付结果通知的内容一定要做【签名验证】,
         *  并校验返回的【订单金额是否与商户侧的订单金额】一致，
         *  防止数据泄漏导致出现“假通知”，造成资金损失。
         */
        // ksort($post_data);// 对数据进行排序
        // $str = $this->ToUrlParams($post_data);//对数组数据拼接成key=value字符串
        // $user_sign = strtoupper(md5($post_data));   //再次生成签名，与$postSign比较
        
        $where['crsNo'] = $post_data['out_trade_no'];
        $crsNo=$post_data['out_trade_no'];
        $order_status = db::name('order')->where($where)->find();
        if($post_data['return_code']=='SUCCESS'){
            /*
            * 首先判断，订单是否已经更新为ok，因为微信会总共发送8次回调确认
            * 其次，订单已经为ok的，直接返回SUCCESS
            * 最后，订单没有为ok的，更新状态为ok，返回SUCCESS
            */
            if($order_status['return_code']=='1'){
                $this->return_success();
            }else{
                $updata['return_code'] = '1';
                if(db::table("order")->where($where)->update($updata)){

                    $this->return_success();
                }
            }
        }else{
            $this->intext("微信支付失败");
            // echo '微信支付失败';
        }
    }
    
    /*
     * 给微信发送确认订单金额和签名正确，SUCCESS信息 -xzz0521
     */
    private function return_success(){
        $return['return_code'] = 'SUCCESS';
        $return['return_msg'] = 'OK';
        $xml_post = '<xml>
                    <return_code>'.$return['return_code'].'</return_code>
                    <return_msg>'.$return['return_msg'].'</return_msg>
                    </xml>';
        echo $xml_post;exit;
    }
    

    //写入文件操作
    public function intext($data){
        $myfile = fopen("a.txt", "a+");
        fwrite($myfile, $data);
        fclose($myfile);
    }

}