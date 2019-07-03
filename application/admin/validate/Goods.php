<?php
namespace app\admin\validate;
use think\validate;
class Goods extends validate{
    protected $rule = [
        // 任意规则下加入token tp会自动验证token
        'goods_name|商品名称'=>'require',
        'cate_id|分类'=>'require|gt:0',
        'shop_price|本店售价'=>'require|gt:0',
        'market_price|市场售价'=>'require|checkPrice'
    ];
    // tp内置验证 必须有值传递进来才会验证
    public function checkPrice($value,$rule,$data){
        if($value < $data['shop_price']){
            return false;
        }
        return true;
    }

}