<?php
/**
 * 登录控制器
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Login extends Controller{
    public function login(){
        if(Request::instance()->isPost()){
            $_POST['pass']=md5($_POST['pass']);
            $data=db::name("admin")->where($_POST)->find();
            if($data){
                Session::set('adminlogin','1');
                Session::set('adminname',$data['name']);
                $this->success('登录成功',url("index/index")); 
            }else{
                $this->error('用户名或密码错误');
            }
        }else{
            return view("login");
        }
    }

    public function check(){
    	$name=Request::instance()->param("name");
    	$pass=md5(Request::instance()->param("pass"));
    	$data=Db::name('admin')->where("name","=","$name")->where("password","=","$pass")->find();
    	if($data){
    		Session::set('adminlogin','1');
    		$this->success('登录成功',url('index/index'));
    	}else{
    		$this->error('登录失败',url("login/login"));
    	}
    }

    public function logout(){
        Session::clear();
        $this->success('已退出',url('Login/login'));
    }
}