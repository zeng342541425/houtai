<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Imgc extends Allow{

	//分类列表
    public function index(){
        $data=db::name('imgc')->order('id desc')->select();
        $this->assign('data',$data);
        return view('index');
    }
    //添加分类
    public function add(){
        if(Request::instance()->isPost()){
            $data=Request::instance()->param();
            $data['time']=time();
            if(db::name('imgc')->insert($data)){
                $this->success('添加成功',url('imgc/index'));
            }
        }else{
            return view('add');
        }
        
    }

    //编辑分类
    public function edit(){
        if(Request::instance()->isPost()){
            $data=Request::instance()->param();
            if(db::name('imgc')->update($data)){
                $this->success('修改成功',url('imgc/index'));
            }
        }else{
            $id=Request::instance()->param('id');
            $rowData=db::name('imgc')->find($id);
            $this->assign('rowdata',$rowData);
            return view('edit');
        }
        
    }


    //删除轮播图
    public function ajax_del(){
        $id=Request::instance()->param('id');
        echo db::name('imgc')->delete($id);
    }


        //分类列表
    public function index_article(){
        $data=db::name('imgl')->order('id desc')->select();
        $this->assign('data',$data);
        $imgc=db::name('imgc')->select();
        $this->assign('imgc',$imgc);
        return view('index_article');
    }
    //添加分类
    public function add_article(){
        if(Request::instance()->isPost()){
            $data=Request::instance()->param();
            $data['time']=time();
            if($file1=request()->file('file1')){
               $info1 = $file1->move(ROOT_PATH . 'public' . DS . 'static/uploads'); 
                if($info1){
                    $data['img']="uploads/".$info1->getSaveName();
                }
            }
            if(db::name('imgl')->insert($data)){
                $this->success('添加成功',url('imgc/index_article'));
            }
        }else{
            $data=db::name('imgc')->select();
            $this->assign('data',$data);
            return view('add_article');
        }
        
    }

    //编辑分类
    public function edit_article(){
        if(Request::instance()->isPost()){
            $data=Request::instance()->param();
            $data['time']=time();
            if($file1=request()->file('file1')){
               $info1 = $file1->move(ROOT_PATH . 'public' . DS . 'static/uploads'); 
                if($info1){
                    $data['img']="uploads/".$info1->getSaveName();
                }
            }
            if(db::name('imgl')->update($data)){
                $this->success('修改成功',url('imgc/index_article'));
            }
        }else{
            $id=Request::instance()->param('id');
            $rowData=db::name('imgl')->find($id);
            $this->assign('data',$rowData);
            return view('edit_article');
        }
        
    }


    //删除轮播图
    public function ajax_del_article(){
        $id=Request::instance()->param('id');
        echo db::name('imgl')->delete($id);
    }
}
