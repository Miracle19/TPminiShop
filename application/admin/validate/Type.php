<?php
namespace app\admin\validate;
use think\validate;
use think\Validate as ThinkValidate;

class Type extends ThinkValidate{
    //                           同名验证
    protected $rule = [
        'type_name|商品类型' => 'require|unique:type'
    ];
}