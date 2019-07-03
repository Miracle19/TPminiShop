<?php
namespace app\index\controller;

use app\index\controller\Common;

class Cart extends Common{
    public function addCart(){
        // dump(input());
        // 接收需要的参数
        $goods_id = input('goods_id');
        $attr_id = input('attr_id/a');
        $goods_count = input('goods_count');
        $goods_attr_ids  = implode(',',$attr_id);
        // dump($goods_attr_ids);
        model('Cart')->addCart($goods_id,$goods_attr_ids,$goods_count);
        $this->redirect('index');
        // $this->fetch('index');
    }
    // 显示购物车列表
    public function index(){
        $cart_list = model('Cart')->getList();
        $total = model('Cart')->getTotal($cart_list);
        $this->assign('total',$total);
        $this->assign('cartlist',$cart_list);
        return $this->fetch();
    }
    public function remove(){
        $goods_attr_ids = input('goods_attr_ids','');
        $goods_id = input('goods_id/d');
        model('Cart')->remove($goods_id,$goods_attr_ids);
        $this->success('ok','index');
    }
    public function changeNumber(){
        // 先接参数
        $type= input('type/d') ? input('type/d') : false;
        $goods_id = input('goods_id/d');
        $goods_attr_ids =input('goods_attr_ids','');
        if(!$type){
            $goods_count =input('goods_count/d');
            $res = model('Cart')->changeNumber($goods_id,$goods_attr_ids,$goods_count);
            return json(['code'=>1]);
        }else{
            $res = model('Cart')->changeNumber2($goods_id,$goods_attr_ids,$type);
            return json(['code'=>1]);
        }
            
    }
}