<?php
namespace app\admin\model;

use think\Model;

class Role extends Model{
    public function addRole($data){
      return   $this->isUpdate(false)->allowField(true)->save($data);
    }
    public function roleEdit($data){
      return   $this->isUpdate(true)->allowField(true)->save($data);
    }
}