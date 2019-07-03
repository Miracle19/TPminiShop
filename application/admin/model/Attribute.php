<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Attribute extends Model{
    public function attrAdd($data){
        if($data['attr_input_type']==2 && !$data['attr_values']){
            // 如果输入类型为2 select选择的时候  必须录入默认值
            $this->error='select默认值必须设置';
            return false;
        }
       return  $this->isUpdate(false)->allowField(true)->save($data);
    }   
    public function getAttrData(){
        $query =Db::name('Attribute');
       return  $query->alias('a')->join('tedi_type b','a.type_id = b.id','left')->field('a.*,b.type_name')->paginate(5);
    }
    public function delAttr($attr_id){
       return  Db::name('Attribute')->where('id',$attr_id)->delete();
    }
    public function attrEdit($data){
        if($data['attr_input_type']==2 && !$data['attr_values']){
            // 如果输入类型为2 select选择的时候  必须录入默认值
            $this->error='select默认值必须设置';
            return false;
        }
       return  $this->isUpdate(true)->allowField(true)->save($data);
    }   
    // 流程四   根据type_id 获得 属性的对象   对对象数据进行处理 
    public function getAttrByID($type_id){
        $data= $this->all(['type_id'=>$type_id]);
        $list =[]; // 保存最终结果的变量
        foreach($data as $value){
            $value = $value->toArray();
            if($value['attr_input_type']==2){
                // select 列表选择
                $value['attr_values'] = explode(',',$value['attr_values']);
            }
            $list []  = $value;
        }
        // dump($list);
        return $list;
     }
}