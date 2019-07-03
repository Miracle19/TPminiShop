<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Common extends Controller{
    public $_user=[]; //使用类属性保存用户的数据
    public function __construct()
    {
        parent::__construct();
        $user_info =cookie('user_info');
        if(!$user_info){
            $this->error('请先登入','login/login');
        }
        // 将用户的基本信息存储到属性中
        $this->_user=$user_info;
        // 如果用户为超级管理员 即角色id等于1则开放所有的权限
        if($this->_user['role_id']==1){
            $rule_info = Db::name('Rule')->select();
        }else{
            // 根据角色id获取权限id
            $role_info = Db::name('Role')->where('id',$this->_user['role_id'])->find();
            // halt($role_info);
            // 根据权限id获取全部权限信息
            $rule_info = Db::name('Rule')->where('id','in',$role_info['rule_ids'])->select();
            // halt($rule_info);
        }
        foreach($rule_info as $key=>$values){
            // 将用户具备的权限信息保存到user属性中
            $this->_user['rules'] [] =strtolower($values['controller_name'].'/'.$values['action_name']);
            // 将导航菜单数据储存到_user属性中
            if($values['is_show']==1){
                $this->_user['menus'] []=$values;
            }
        }
        // halt($this->_user);  
        // 判断是否有权访问
        if($this->_user['role_id']!=1){
            // 不是超级管理员的用户才需要验证权限
            // 给所有用户增加固定权限
            $this->_user['rules'] [] ='index/index';
            $this->_user['rules'] [] ='index/top';
            $this->_user['rules'] [] ='index/menu';
            $this->_user['rules'] [] ='index/main';
            $action = strtolower(request()->controller().'/'.request()->action());
            if(!in_array($action,$this->_user['rules'])){
                if(request()->isAjax()){
                    echo json_encode(['code'=>0,'msg'=>'无权访问']);
                    exit;
                }else{
                    $this->error('无足够的访问权限');
                }
            }
        }
    }
    
}