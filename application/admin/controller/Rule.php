<?php
namespace app\admin\controller;

use think\Request;

class Rule extends Common
{
    public function ruleAdd()
    {
        if (Request::instance()->isGet()) {
            // 获取格式化之后的权限信息
            $rule =  model('Rule')->getTreeList();
            // halt($rule);
            $this->assign('rule', $rule);
            return $this->fetch();
        }
        model('Rule')->ruleAdd(input());
        $this->success('ok','index');
    }
    public function index(){
        $rule = model('Rule')->getTreeList();
        $this->assign('rule',$rule);
        return $this->fetch();
    }
}
