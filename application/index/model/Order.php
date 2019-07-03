<?php 
namespace app\index\model;

use think\Model;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use think\Db;
use think\Exception;

class Order extends Model{
    public function order($data){
        // 获取字段 用户id
        $data['user_id'] =session('user_info')['id'];
        $cart_list = model('Cart')->getList();
        // 获取字段 商品总价
        $data['total']=model('Cart')->getTotal($cart_list);
        // 获取字段 订单编号
        $data['order_sn']=date('YmdHis').rand('1000000','9999999');
        $this->save($data);
        // 获取id  此也为订单详情表的id
        $data['id'] = $this->getLastInsId();

        foreach($cart_list as $key=>$value){
            $order_detail []= [
                'order_id'=>$data['id'],
                'goods_id'=>$value['goods_id'],
                'goods_count'=>$value['goods_count'],
                'goods_attr_ids'=>$value['goods_attr_ids']
            ];
        }
        // halt($order_detail);
        Db::startTrans();
        try{
            Db::name('Order_detail')->insertAll($order_detail);
            // 同时删除原购物车数据
            Db::name('Cart')->where('user_id',$data['user_id'])->delete();
            Db::commit();
        }catch(\Exception $e)
        {
            Db::rollback();
             $this->error='错误';
             return false;
        }
        return $data;
    }
}