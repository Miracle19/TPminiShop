<?php
namespace app\admin\model;

use think\Model;
use think\helper\hash\Md5;
use think\Db;

class Admin extends Model{
    public function Login($data){
        // 检查验证码
        $obj = new \think\captcha\Captcha();
        if(!$obj->check($data['captcha'])){
            $this->error = '验证码错误';
            return false;
        }
        // 检查账号和密码
        $user_info = $this->get(['username'=>$data['username'],'password'=>md5($data['password'])]);
        if(!$user_info){
            $this->error = '用户名或密码错误';
            return false;
        }
        // return true;
        // 保存用户状态
        $i = 0;
        if(isset($data['remember'])){
            $i = 3600*24;
        }
        // 设置cookie
        cookie('user_info',$user_info->toArray(),$i);
    }
    public function adminAdd($data){
        // $user_info = Db::name('Admin')->select();
        // halt($user_info);
        if($this->get(['username'=>$data['username']])){
            $this->error = '用户已存在';
            return false;
        }   
        $data['password']=Md5($data['password']);
        return $this->isUpdate(false)->allowField(true)->save($data);
    }
    public function getAdmin(){
        $query = Db::name('Admin');
       return  $query->alias('a')->field('a.*,b.role_name')->join('tedi_role b','a.role_id=b.id','left')->select();
    }
    public function adminEdit($data){
        $role_id = input('role_id/d');
        // halt($role_id);
        if($role_id===1){
            $this->error='无权增加新的超级管理员用户,请重新分配';
            return false;
        }
        // 拼出get条件
        $map []=[
            'username'=>$data['username'],
            // 当不修改用户名时 我们肯定要排除当前记录的username
            'id'=>['neq',$data['id']]
        ];
       
        if($this->get($map)){
            $this->error = '用户名已存在';
            return false;
        }
        if($data['password']){
            $data['password']=Md5($data['password']);
        }else{
            // 密码为空不能修改
            unset($data['password']);
        }
        return $this->isUpdate(true)->allowField(true)->save($data);
    }
}
