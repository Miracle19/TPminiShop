<?php
namespace app\admin\controller;

use think\Request;

class Admin extends Common{
    public function adminAdd(){
        if(Request::instance()->isGet()){
            $role = model('Role')->all();
            $this->assign('role',$role);
            return $this->fetch();
        }
        $rs = model('Admin')->adminAdd(input());
        if(!$rs){
            $this->error(model('Admin')->getError());
        }
        $this->success('ok','index');
    }
    public function index(){
        $userinfo = model('Admin')->getAdmin();
        $this->assign('userinfo',$userinfo);
        return $this->fetch();
    }
    public function adminEdit(){
        $admin_id = input('id/d');
        if($admin_id<=1){
           $this->error('参数错误');
        }
        if(Request::instance()->isGet()){
            $userinfo = model('Admin')->get(input('id/d'));
            $role = model('Role')->all();
            $this->assign('userinfo',$userinfo);
            $this->assign('role',$role);
           return  $this->fetch();
        }
        $res = model('Admin')->adminEdit(input());
        if(!$res){
            $this->error(model('Admin')->getError());
        }
        $this->success('ok','index');
    }
}