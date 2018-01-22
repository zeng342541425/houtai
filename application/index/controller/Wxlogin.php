<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Wxlogin extends Controller{
    public function _initialize(){
    	$openid=Session::get('user_openid');
    	if(!$openid){
    		controller('Wxconf')->get_info_code();		
    	}  	
    }

    public function baidu(){
    	$data=Request::instance()->param();
    	return $this->fetch('',['data'=>$data]);
    }
}

