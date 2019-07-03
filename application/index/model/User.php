<?php 
namespace app\index\model;

use think\Model;
use think\helper\hash\Md5;

class User extends Model{
    // 返回数据的结果集
    protected $resultSetType ='think\Collection';
    public function regist($data){
        // 非空判断
        if(!$data['username']||!$data['password']){
            $this->error = '没有填写任何内容';
            return false;
        }
         // 检查用户名是否重名
        if($this->get(['username'=>$data['username']])){
            $this->error='用户名已存在';
            return false;
        }
        // 检查手机号
        if($this->get(['tel'=>$data['tel']])){
            $this->error='手机号已存在';
            return false;
        }
        // 生成盐
        // $salt= rand(10000,99999);
        $data['salt']=rand(10000,999999);
        // 手机注册用户 默认激活状态为已激活
        $data['status']=1;
        // 处理密码
        $data['password']=md5($data['password'].$data['salt']);
        // 准备入库数据
        session('SmsCode',null);
        return $this->isUpdate(false)->allowField(true)->save($data);
        
    }
    public function doLogin($data){
    //  验证验证码
        $obj =new \think\captcha\Captcha();
        if(!$obj->check($data['checkcode'])){
            $this->error='验证码错误';
            return false;
        }
        // 验证用户名与密码
        if(!$this->get(['username'=>$data['username']])){
            $this->error='用户名错误';
            return false;
        }
        $user_info = $this->get(['username'=>$data['username']]);
        // 比对密码
        if($user_info['password']!=md5($data['password'].$user_info['salt'])){
            $this->error = '密码错误';
            return false;
        }

        session('user_info',$user_info->toArray());
        if($user_info['status']==0){
            $this->error='当前用户未激活';
            return false;
        }
        model('Cart')->cookie2db();
        return true;
    }
    public function emailReg($username,$password,$email){
        // 判断重复字段
        if($this->get(['username'=>$username])){
            $this->error='用户名已存在';
            return false;
        }
        if($this->get(['email'=>$email])){
            $this->error='该邮箱账号下已存在账户';
            return false;
        }
        // 生成激活码
        $active_code = uniqid();
        // 生成盐
        $salt = rand(10000,99999);
        $password=md5($password.$salt);

        // 入库
        $data = [
            'username' =>$username,
            'password' =>$password,
            'active_code'=>$active_code,
            'salt'=>$salt,
            'email'=>$email
        ];
        $this->save($data);
        // 入库完成以后发送邮件
        $link =url('index/user/active','',true,true).'?key='.$active_code;
        $content = "您好,您有一份来自京西商城的注册消息,请<a href='$link' style='font-size:18px;color:sky-blue'>点击激活账户</a>";
        send_email($email,$content);
    }
}