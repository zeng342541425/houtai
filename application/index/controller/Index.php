<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Index extends Wxlogin{
// class Index extends Controller{
    public function index(){
    	$openid=Session::get('user_openid');
    	echo $openid;
    }

    //获取用户地理位置信息 并展示用户附近的网点
    public function getaddress(){

    	$getSignPackage=json_decode(controller('wxconf')->getSignPackage(),true);

    	return $this->fetch("",['getSignPackage'=>$getSignPackage]);
    }

    //获取用户地理位置信息 并添加搜索框
    public function address_search(){

        //post接受搜索的关键字
        if(!$data=Request::instance()->param()){
            $data['address']="";
        }  

        $getSignPackage=json_decode(controller('wxconf')->getSignPackage(),true);

        return $this->fetch("",['getSignPackage'=>$getSignPackage,'data'=>$data]);
    }

    //创建菜单
    public function add_menu(){
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".controller('wxconf')->get_access_token()."";
        $data='
            {
                "button":[
                {    
                    "type":"view",
                    "name":"首页",
                    "url":"http://bbs.qitmx.com/home.html"
                },
                {
                    "type":"view",
                    "name":"个人中心",
                    "url":"http://bbs.qitmx.com/center.html"

                }]
            }
        ';
        controller('wxconf')->https_request($url,$data);
    }

    //用户关注事件
    public function test(){
        echo "aaa";
    }
}

