<?php 
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Index extends Allow{
	public function index(){
		$this->assign("adminname",Session::get("adminname"));
		return view('index');
		
	}

	//管理员列表
	public function user(){
		$data=db::name('admin')->paginate(10);
		$this->assign('data',$data);
		return view('user');
	}

	//添加管理员
	public function user_add(){
		if(Request::instance()->isPost()){
			$_POST['time']=time();
			if(!$_POST['pass1'] || !$_POST['pass2'] || !$_POST['name']){
				$this->error('帐号或密码不能为空');
			}else{
				$_POST['pass']=md5($_POST['pass1']);
				$_POST['pass2']=md5($_POST['pass2']);
				if($_POST['pass'] !== $_POST['pass2']){
					$this->error('两次密码不相同');
				}else{
					if(db::name("admin")->insert($_POST)){
						$this->success('添加成功',url("index/user"));
					}
				}
			}
		}else{
			return view('user_add');
		}
	}

	//修改管理员
	public function user_edit(){
		if(Request::instance()->isPost()){
			if(!$_POST['pass1'] || !$_POST['pass2'] || !$_POST['name']){
				$this->error('帐号或密码不能为空');
			}else{
				$_POST['pass']=md5($_POST['pass1']);
				$_POST['pass2']=md5($_POST['pass2']);
				if($_POST['pass'] !== $_POST['pass2']){
					$this->error('两次密码不相同');
				}else{
					if(db::name("admin")->update($_POST)){
						$this->success('修改成功',url("index/user"));
					}
				}
			}
		}else{
			$id=Request::instance()->param("id");
			$data=db::name('admin')->find($id);
			$this->assign('data',$data);
			return view('user_edit');
		}
	}

	//删除管理员
	public function del_user(){
		$id=Request::instance()->param("id");
		echo db::name("admin")->delete($id);
	}

	//默认首页
	public function index_v1(){
		return view('index_v1');
	}





	//表格
	public function table(){
		return view('table');
	}

	//表单
	public function add(){
		return view('add');
	}
	//按钮
	public function buttons(){
		return view('buttons');
	}

	public function test(){
		return view('test');
	}
}
?>