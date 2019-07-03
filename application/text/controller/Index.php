<?php
namespace app\text\controller;

use think\Controller;
use think\Request;
use think\Url;

class Index extends Controller{
    public function index(){
    //    $obj = Request::instance()->ip();
    //    dump($obj);
    // dump(Request::instance()->get("name",'','strtoupper'));
    // dump(input('name',''));
    // 生成当前控制器下的方法
        // dump(Url::build('ADDhtml',['id'=>10],'.jsp',true));
        // dump(Url('admin/addhtml'));
        // dump(Url("admin/index/index"));
     return    view();
       
    }
  
}