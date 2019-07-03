<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Rule extends Model{
    public function getTreeList($id=0,$is_clear=false){
       $ruledata =  Db::name('Rule')->select();
       return get_tree($ruledata,$id,0,$is_clear);
    }
    public function ruleAdd($data)
    {
        $this->isUpdate(false)->allowField(true)->save($data);
    }
}