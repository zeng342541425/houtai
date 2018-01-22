<?php
/**
 * 分类和文章列表类
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Category extends Allow{

	//分类列表
    public function index(){
        $data=db::name('category')->order('list_order asc')->select();
        $tree=tree($data);
        $this->assign('data',$tree);
        return view('index');
    }
    //添加分类
    public function add(){
        if(Request::instance()->isPost()){
            $data=Request::instance()->param();
            $data['time']=time();
            if($file1=request()->file('file1')){
               $info1 = $file1->move(ROOT_PATH . 'public' . DS . 'static/uploads'); 
                if($info1){
                    $data['img']="uploads/".$info1->getSaveName();
                }
            }
            if(db::name('category')->insert($data)){
                $this->success('添加成功',url('category/index'));
            }
        }else{
            $data=db::name('category')->order('list_order asc')->select();
            $tree=tree($data);
            $this->assign('data',$tree);
            return view('add');
        }
        
    }

    //编辑分类
    public function edit(){
        if(Request::instance()->isPost()){
            $data=Request::instance()->param();
            $data['time']=time();
            if($file1=request()->file('file1')){
               $info1 = $file1->move(ROOT_PATH . 'public' . DS . 'static/uploads'); 
                if($info1){
                    $data['img']="uploads/".$info1->getSaveName();
                }
            }
            if(db::name('category')->update($data)){
                $this->success('修改成功',url('category/index'));
            }
        }else{
            $id=Request::instance()->param('id');
            $rowData=db::name('category')->find($id);
            $this->assign('rowdata',$rowData);
            $data=db::name('category')->order('list_order asc')->select();
            $tree=tree($data);
            $this->assign('data',$tree);
            return view('edit');
        }
        
    }


    //删除分类
    public function ajax_del(){
        $id=Request::instance()->param('id');
        $data=db::name('category')->where("parent_id='$id'")->select();
        if($data){
            echo "请先删除子分类";
        }else{
            echo db::name('category')->delete($id);
        }
    }

    //文章列表
    public function index_article(){
        $data=db::name('article')->order('id desc')->paginate(10);
        $this->assign('data',$data);
        return view('index_article');
    }
    //添加文章
    public function add_article(){
        if(Request::instance()->isPost()){
            $data=Request::instance()->param();
            $id=Request::instance()->param('parent_id');
            $data['time']=time();
            if($file1=request()->file('file1')){
               $info1 = $file1->move(ROOT_PATH . 'public' . DS . 'static/uploads'); 
                if($info1){
                    $data['img']="uploads/".$info1->getSaveName();
                }
            }
            if(db::name('article')->insert($data)){
                $this->success('添加成功',url('category/index_article'));
            }
        }else{
            $data=db::name('category')->order('list_order asc')->select();
            $tree=tree($data);
            $this->assign('data',$tree);
            return view('add_article');
        }
        
    }

    //编辑文章
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
            if(db::name('article')->update($data)){
                $this->success('修改成功',url('category/index_article'));
            }
        }else{
            $id=Request::instance()->param('id');
            $rowdata=db::name('article')->find($id);
            $this->assign('rowdata',$rowdata);
            $data=db::name('category')->order('list_order asc')->select();
            $tree=tree($data);
            $this->assign('data',$tree);
            return view('edit_article');
        }
        
    }


    //删除文章
    public function ajax_del_article(){
        $id=Request::instance()->param('id');
        echo db::name('article')->delete($id);
    }
}
