<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
class Allow extends Controller{
    public function _initialize(){
        if(Session::get('adminlogin') != 1){
            $this->error('登录失效',url('login/login'));
        }
    }
}
