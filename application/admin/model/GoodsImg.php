<?php
namespace app\admin\model;

use think\Model;

class GoodsImg extends Model{
    public function addAll($goods_id){
        $array = []; // 最终要批量写入的数据
        $files = request()->file('imgs');
        // halt($files);
        foreach($files as $fileObj){
            $info = $fileObj->validate(['ext'=>'jpg,png'])->move('upload');
            if(!$info){
                continue;
            }
            // 获取商品上传后的地址
            $goods_img = str_replace('\\','/',$info->getPathName());
            $img = \think\Image::open($goods_img);
            // 获取裁切图片保存路径
            $goods_thumb ='upload/'.date('Ymd').'/thumb'.$info->getFileName();
            $img->thumb(150,150)->save($goods_thumb);
            $array[] = [
                'goods_id' =>$goods_id,
                'goods_img'=>$goods_img,
                'goods_thumb' =>$goods_thumb
            ];
        }
        // 将所有数据批量入库
        $this->saveAll($array);
        
    }

}