<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Common extends Controller{
    public function __construct()
    {
        parent::__construct();
        $category = Db::name('Category')->select();
        // halt($category);
        $this->assign('category',$category);
    }
}