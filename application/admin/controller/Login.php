<?php 
namespace app\admin\controller;

use think\Request;
use think\Controller;

class Login extends Controller{
     public function Login(Request $request){
        if($request->isGet()){
            return $this->fetch();
        }
        $res = model('Admin')->login(input());
        if($res===false){
            $this->error(model('Admin')->getError());
        }
        $this->success('欢迎使用ecshop管理系统','admin/index/index');
     }
    //  生成验证码
     public function Captcha(){
        $obj = new \think\captcha\Captcha([
            'length'=>4,
            'codeSet'=>"123456789",
        ]);
        return $obj->entry();
     }
     public function loginout(){
         cookie('user_info',null);
        $this->success('退出系统中','login');
     }
     
}