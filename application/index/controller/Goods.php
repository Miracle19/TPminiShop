<?php
namespace app\index\controller;
class Goods extends Common{
    public function detail(){
        $goods_info = model('Goods')->getGoodsInfo(input('id/d'));
        // halt($goods_info);
        $this->assign("goodsinfo",$goods_info);
        return $this->fetch();
    }
}