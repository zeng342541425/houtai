<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Wxconf extends Controller{
	public $appid;
	public $secret;

	public function __construct(){
		$this->appid="wx1cbba0166977dca4";
		$this->secret="1f7926bb4ea8750885d893e038287c18";
	}

    // 检验签名
	public function checkSignature(){
		$echostr=Request::instance()->param("echostr");

		$signature=Request::instance()->param("signature");
		// 时间戳
		$timestamp=Request::instance()->param("timestamp");
		// 随机数
		$nonce=Request::instance()->param("nonce");
		// 生成TOKEN
		define("TOKEN",'baomingxuan217');
		// 字典序排序
		$tmpArr=array(TOKEN,$timestamp,$nonce);
		sort($tmpArr,SORT_STRING);
		$tmpStr=join($tmpArr);
		$tmpStr=sha1($tmpStr);
		$this->intext($tmpStr);
		if($tmpStr==$signature){
			echo $echostr;
			exit;  //没exit验证会失败
		}else{
			echo "error";
			exit;
		}
	}


	// 获取access_token
	public function get_access_token(){
		$token_data=db::name('baseinfo')->field("name,time")->find(1);
		if($token_data['time']+7000<time()){
			$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}";
			$request=$this->https_request($url);
			$data['name']=$request['access_token'];
			$data['time']=time();
			db::name('baseinfo')->where("id",1)->update($data);
			return $request['access_token'];
		}else{
			return $token_data['name'];
		}
	}

	//获取网页sdk接口 
	public function get_jsapi_ticket(){
		$jsapi_ticket=db::name('baseinfo')->field("name,time")->find(2);
		if($jsapi_ticket['time']+7000<time()){
			$url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$this->get_access_token()."&type=jsapi";
			$request=$this->https_request($url);
			$data['name']=$request['ticket'];
			$data['time']=time();
			db::name('baseinfo')->where("id",2)->update($data);
			return $request['ticket'];
		}else{
			return $jsapi_ticket['name'];
		}
	}

	//生成网页jssdk签名
	public function getSignPackage() {
    	$jsapiTicket = $this->get_jsapi_ticket();

    	// 注意 URL 一定要动态获取，不能 hardcode.
    	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    	$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    	$timestamp = time();

    	$nonceStr =randomkeys(16);  //生成随机数

    	// 这里参数的顺序要按照 key 值 ASCII 码升序排序
    	$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    	$signature = sha1($string);

	    $signPackage = array(
	      	"appId"     => $this->appid,
	      	"nonceStr"  => $nonceStr,
	      	"timestamp" => $timestamp,
	      	"url"       => $url,
	      	"signature" => $signature,
	      	"rawString" => $string
	    );	
    	return json_encode($signPackage);
  	}


	// 模拟get请求和post请求
	public function https_request($url,$data=""){
		// 开启curl
		$ch=curl_init();
		// 设置传输选项
		curl_setopt($ch, CURLOPT_URL  , $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1);//以文件流返回
		//执行并获取结果
		if($data){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		$request=curl_exec($ch);
		// $arrs=json_decode($arr,ture);
		$tmpArr=json_decode($request,true);
		if(is_array($tmpArr)){
			return $tmpArr;
		}else{
			return $request;
		}
		//释放curl 
		curl_close($ch);
	}

	//授权获取用户的信息
	public function get_info_code(){
		//静默授权
		$url="http://www.qitmx.com/shop/baoxian/public/index.php/index/Wxconf/get_openid";

		$request_info="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".urlEncode($url)."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
		
    	header("Location:$request_info"); 
    }

    //  通过code获取用户的唯一openid  并存入session
    public function get_openid(){
    	$code=Request::instance()->param("code");

    	$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->secret."&code={$code}&grant_type=authorization_code";

    	$data=$this->https_request($url);

    	$openid=$data['openid'];

    	Session::set("user_openid",$openid);

    	//判断用户是否授权过
    	if(db::name("wxuser")->where("openid='$openid'")->find()){
    		header("Location:http://www.qitmx.com/shop/baoxian/public/index.php/index/index/index"); 
    	}else{
    		//没有则获取用户所有信息
    		$url="http://www.qitmx.com/shop/baoxian/public/index.php/index/Wxconf/get_user_info";

    		$request_info="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".urlEncode($url)."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

    		header("Location:$request_info"); 
    	}    	
    }

    // 通过code获取用户的所有信息  并将openid存入session 
    public function get_user_info(){
    	$code=Request::instance()->param("code");
    	$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->secret."&code={$code}&grant_type=authorization_code";

    	$data=$this->https_request($url);

    	$url_info="https://api.weixin.qq.com/sns/userinfo?access_token=".$data['access_token']."&openid=".$data['openid']."&lang=zh_CN";

    	$data_info=$this->https_request($url_info);

    	if(!db::name("wxuser")->where("openid='".$data['openid']."'")->find()){
    		if(db::name("wxuser")->insert($data_info)){
	    		header("Location:http://www.qitmx.com/shop/baoxian/public/index.php/index/index/index");
	    	}
    	}   	
    }

	//写入文件操作
    public function intext($data){
        $myfile = fopen("a.txt", "a+");
        fwrite($myfile, $data);
        fclose($myfile);
    }

    public function qing(){
    	echo Session::get("user_openid");
    	Session::clear();
    }
}
