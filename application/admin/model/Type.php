<?php
namespace app\admin\model;

use think\Model;

class Type extends Model{
    public function getType(){
        $data = cache('type');
        if(!$data){
            // echo 'db';
            $data = $this->all();
            cache('type',$data,3600);
        }
        return $data;
    }
    public function typeEdit($data){
      
       $this->allowField(true)->isUpdate(true)->save($data);
       cache('type',null);
    }
}