<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Category extends Model{
    // 设置默认接收数据结果集
    protected $resultSetType = 'collection';
    // 创建获取格式化之后分类的数据    is_clear  默认不清空
    public function getTreeList($id=0,$is_clear=false){
        $category = Db::name('Category')->select();
        return get_tree($category,$id,0,$is_clear);
    }
    // 删除分类数据
    public function cateDel($cate_id){
        // 保存最终要删除的id 
        $where = [$cate_id];
        // 查找当前id分类下的子分类
        
        $cate_son = $this->getTreeList($cate_id);
        if($cate_son){
            foreach($cate_son as $v){
                $where [] = $v['id'];
            }
        }
        $this->destroy($where);


    }
    public function cateEdit($data){
       if($data['id']==$data['parent_id']){
           $this->error='不能设置自己为自己的父类';
           return false;
       }
    // 查出当前类的已有子类
       $son = $this->getTreeList($data['id']);
    
       foreach($son as $v){
           if($v['id']==$data['parent_id']){
               $this->error='子分类与父分类逻辑错乱';
               return false;
           }
       }
   
        return $this->allowField(true)->isUpdate(true)->save($data);
    }
}