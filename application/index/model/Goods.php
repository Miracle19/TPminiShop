<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Goods extends Model{
    public function getRecGoods($field){
        return $this->field($field,1)->select();
    }
    public function getGoodsInfo($goods_id){
       $goods = Db::name('Goods')->find($goods_id);
       $goods['img'] = Db::name('Goods_img')->where('goods_id',$goods_id)->select();
    //    获取商品的属性值 并取的属性类型与属性名 联查  属性表与属性值表
       $attr =Db::name('Goods_attr')->alias('a')->field('a.*,b.attr_name,b.attr_type')->Join('tedi_attribute b','a.attr_id=b.id','left')
       ->where('goods_id',$goods_id)->select();
        $sql = Db::name('Goods_attr')->getLastSql();
        // dump($attr);
        // 拆分为唯一属性  将唯一属性保存到 goods数组中 并以二维形式  requee作为键保存 以便后续循环数据输出
        foreach($attr as $value){
            if($value['attr_type']==1){
                $goods['requee'] []= $value;
            }else{
                // 单选属性保存到三维数组中
                $goods['signle'][$value['attr_id']][]=$value;
            }
        }
        // halt($goods);
       return $goods;
    }
}