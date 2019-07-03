<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class GoodsAttr extends Model
{
    /*  
        商品表 id字段
        属性表 id字段
        属性值表 属性值
    */
    public function addAll($goods_id, $attr_ids, $attrs_values)
    {
        // 组装属性值表 需要的字段元素  good_id 商品 名称     attrs_id 对应属性id   attrs_values 对应属性下的属性值
        $list = [];   //保留最终要写入的数据变量
        $temp = [];   //去重的临时变量 该变量保存的数据格式为 属性id - 属性值
        foreach ($attr_ids as $key => $values) {
            $string  = $values . '-' . $attrs_values[$key];
            if(in_array($string,$temp)){
                //说明数据已经重复
                continue;
            }
            // 说明数据无重复
            $temp [] = $string;
            $list[] = [
                'goods_id' => $goods_id,
                'attr_id'  => $values,
                'attr_value' => $attrs_values[$key]
            ];
        }
        // halt($temp);
        // halt($list);
        $this->saveAll($list);
    }
    public function getAttrById($goods_id){
        $sql = "SELECT a.*,b.attr_name,b.attr_type,b.attr_input_type,b.attr_values FROM `tedi_goods_attr` a LEFT JOIN tedi_attribute b ON a.attr_id = b.id WHERE a.goods_id=?
        ";
        $list = Db::query($sql,[$goods_id]);
        // halt($list);
        $attrs = [];// 保存最终的结果
        // 这里做了一个什么事  把2维数组 中的attr_values 炸成第三个维度 对应可以使用的属性值
        foreach($list as $key => $values){
            if($values['attr_input_type']==2){
                $values['attr_values'] = explode(',',$values['attr_values']);
            }
            // $attrs[] = $values;
            // 将遍历后的attr_id 作为下标   然后再把遍历后的$values 加入到数组中
            $attrs[$values['attr_id']] [] = $values; 
        }
        // dump($attrs);
        return $attrs;
    }
}
