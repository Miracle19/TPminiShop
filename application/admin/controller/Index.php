<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Index extends Common{
    public function index(){
        return $this->fetch('admin@index/index');
        // dump(Db::name('category')->select());
        // // dump( Db::name('category'));exit;

    }
    // 加载副主页面视图方法
    public function main(){
        return  $this->fetch();
    }
    // 加载导航栏视图方法
    public function menu(){
        $this->assign('menus',$this->_user['menus']);
        return  $this->fetch();
    }
    // 加载顶部视图方法
    public function top(){
        return  $this->fetch();
    }
    // 加载商品分类列表视图
    // public function categoryList(){
    //     $data = Db::name('category')->select();
    //     $data = get_tree($data);
    //     $this->assign('category',$data);
        
    //     return $this->fetch('category/catelist');
    // }
}