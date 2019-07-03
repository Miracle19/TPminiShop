<?php
namespace app\admin\controller;

use think\Request;
use think\Request as ThinkRequest;
use think\Db;

class Role extends Common{
    public function roleAdd(){
        if(Request::instance()->isGet()){
           return  $this->fetch();
        }
        model('Role')->addRole(input());
        $this->success('ok','index');
    }
    public function roleEdit(){
        $role_id = input('id/d');
        // 在这里判断特殊id   1   为1的不能执行后续方法
        if($role_id<=1){
            $this->error('非法请求');
        }
        if(Request::instance()->isGet()){
           $role =  model('Role')->get(input('id/d'));
           $this->assign('role',$role);
           return $this->fetch();
        }
        model('Role')->roleEdit(input());
        $this->success('ok','index');
    }
    public function index(){
        $role = model('Role')->all();
        $this->assign('role',$role);
        return $this->fetch();
    }
    public function fetchRule(){
        // 在角色表分配角色拥有的权限 角色标的rule_ids 字段 与权限表的id字段对应  
        // 处理表单提交最后的数据必须是所有的权限id存到rule_ids字段当中
        // 接收角色id 即表单传来的id值
        $rule_id = input('id/d');
        if(ThinkRequest::instance()->isGet()){

            // 获取角色当前已具有的权限信息  
            $role_info = Db::name('Role')->where('id',$rule_id)->find();
            // halt($role_info);
            $this->assign('hasrule',$role_info['rule_ids']);
            $rules = Db::name('Rule')->select();
            // halt($rules);
            $this->assign('rules',$rules);
            return $this->fetch();
        }
        
        // 接收权限ids数组
        $rules = input('rules/a');
        // 转换为逗号连起来的字符串
        $rule_ids =implode(',',$rules);
        // 修改数据
        Db::name('Role')->where('id',$rule_id)->setField('rule_ids',$rule_ids);
        $this->success('ok','index');
    }
}