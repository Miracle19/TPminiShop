<?php
namespace app\index\controller;
use think\Controller;
use app\index\controller\Common;

class Index extends Common{
    public function Index(){
        // 在首页分配一个模板标识符号 说明是首页
        $this->assign('show',1);
        $goods_model = model('Goods');
        $goods['hot'] = $goods_model->getRecGoods('is_hot');
        $goods['new'] = $goods_model->getRecGoods('is_new');
        $goods['rec'] = $goods_model->getRecGoods('is_rec');
        $this->assign('goods',$goods['hot']);
        // halt($goods);
        return $this->fetch();
    }
}