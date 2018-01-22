<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;
use think\Request;
//生成随机数
function randomkeys($length){  
	$pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
	$key='';  
    for($i=0;$i<$length;$i++){    
        $key.=$pattern{mt_rand(0,35)};//生成php随机数  
    }  
    return $key;  
};

//分类排序
function tree ($array,$pid=0){
	$arr = array();
    
	foreach($array as $v){
    	if($v['parent_id']==$pid){
        	$arr[] = $v;
        	$arr = array_merge($arr,tree($array,$v['id']));
    	}
	}
	return $arr;
}

/**
 * 通过文章id得到分类名
 */
function getcategory($id){
    $data=db::name('category')->field('name')->where("id='$id'")->find()['name'];
    return $data;
}

/**
 * 通过图片id得到分类名
 */
function getimgc($id){
    $data=db::name('imgc')->field('name')->where("id='$id'")->find()['name'];
    return $data;
}

