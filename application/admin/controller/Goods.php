<?php
namespace app\admin\controller;

use think\Request;
use think\Image;
use model\Model;
use think\Db;

class Goods extends Common
{
    // 显示商品列表
    public function Index()
    {
        // 查询分类数据  第一次调用
        $category = Model('Category')->getTreeList();
        // halt($category);
        $this->assign('category', $category);
        // $goods_info = Model('Goods')->goodsList();
        $goos_info = model('Goods')->getGoodsList();
        // halt($goos_info);   
        $this->assign('goods_info', $goos_info);
        return $this->fetch();
        // return $this->fetch();
    }
    // 商品修改
    public function GoodsEdit()
    {
        $goods_model = Model('Goods');
        $request = Request::instance();
        if ($request->isGet()) {
            $goods_info = $goods_model->get(input('id'));
            // dump($goods_info);
            $category = Model('Category')->getTreeList();
            $type = model('Type')->getType();
            // dump($type);
            $attrs = model('GoodsAttr')->getAttrById(input('id/d'));
            // exit;
            // dump($attrs);
            
            $this->assign('goods_info', $goods_info);
            $this->assign('category', $category);
            $this->assign('type',$type);
            $this->assign('attrs',$attrs);
            return $this->fetch();
        }
        //   接收post参数
        $post_data = input();
        //    验证数据
        $res = $this->validate($post_data, 'Goods');
        if ($res !== true) {
            $this->error($res);
        }
        //    是否必须上传图片 验证
        $this->uploadImg($post_data, false);
        //    调用模型下的修改方法
        $rs = $goods_model->editGoods($post_data);
        if ($rs === false) {
            $this->error($goods_model->getError());
        }
        $this->success('ok', 'index');
    }
    // 商品入库
    public function  goodsAdd()
    {
        $Request = Request::instance();
        if ($Request->isGet()) {
            // 获取分类模型对象  获得分类信息
            $category = Model('Category');
            $category = $category->getTreeList();
            // 渲染模板数据 
            $type = model('Type')->getType();
            $this->assign('type', $type);
            $this->assign('category', $category);
            return $this->fetch();
        }
        // 获取商品模型对象   取得商品添加的方法
        $goods_model = model('Goods');
        // 接收 post 参数
        // // 使用验证器
        // $res = $this->validate($goods_data,'Goods');
        // if(true!==$res){
        //     $this->error($res);
        // }
        // 从助手函数拿到验证器对象
        $goods_data = input();
        // dump($goods_data);exit;
        $res = validate('Goods');
        if (!$res->check($goods_data)) {
            $this->error($res->getError());
        };
        // 图片处理
        $this->uploadImg($goods_data);
        // 判断商品编码是否重复或自动生成
        $this->checkdata($goods_data);
        // 调用模型中的商品入库方法
        $rs = $goods_model->goodsAdd($goods_data);
        if($rs===false){
            $this->error($goods_model->getError());
        }
        $this->success('商品入库成功', 'index');
    }
    //  &&&& 引用传值     保证变量的延续性
    // 判断货号是否唯一或者自动生成
    protected function checkdata(&$goods_data)
    {
        $goods_model = Model('Goods');
        if ($goods_data['goods_sn']) {
            if ($goods_model->get(['goods_sn' => $goods_data['goods_sn']])) {
                $this->error('商品编码重复');
            }
        } {
            $goods_data['goods_sn'] = 'tedi_shop' . uniqid();
        }
    }
    //   上传的图片处理 保存图片路径和图片缩略图路径到数据库
    protected function uploadImg(&$goods_data, $is_must = true)
    {
        $request = Request::instance();
        $file = $request->file('goods_img');
        if (!$file) {
            if ($is_must) {
                // 如果file对象不存在   在必须上传图片情况下 走这里
                $this->error('必须上传图片');
            } else {
                // 如果不必须上传图片 直接返回true
                return true;
            }
        }
        $img = $file->validate(['ext' => 'jpg,png'])->move('upload');
        if ($img === false) {
            $this->error($file->getError());
        }
        // 获取图片本地保存路径
        $img_dir = $img->getPathname();
        // 拼出缩略图的路径
        $name = 'upload/' . date('Ymd') . '/thumb_' . $img->getFilename();
        // 将两个路径存到  post参数数组中
        // 对getPathname 得到的图片路径  处理下 \  获取到linus下能够读取到的路径
        $goods_img = str_replace('\\', '/', $img->getPathname());
        $goods_data['goods_img'] = $goods_img;
        $goods_data['goods_thumb'] = $name;
        // 对图片的操作
        $img = image::open($img_dir);
        $img->thumb(150, 150)->save($name);
    }
    //    ajax改变状态请求
    public function changeStatus()
    {
        // 接收ajax发来的post参数
        $res = model('Goods')->changeStatus(input('goods_id'), input('filed'));
        if ($res === false) {
            echo json_encode([
                'code' => 0,
                'msg' => model('Goods')->getError(),
            ]);
            exit;
        }
        echo json_encode([
            'code' => 1,
            'msg' => 'ok',
            'status' => $res,
        ]);
    }
    // ajax 获取类型对应的属性列表
    // 流程三   调用模型方法    获得渲染模板数据
    public function showAttr()
    {
        $type_id = input('type_id');

        // 获取类型下的属性
        $attr = model('Attribute')->getAttrByID($type_id);
        // halt($attr);
        $this->assign('attr', $attr);
        // 直接返回属性列表模板给ajax请求
        return $this->fetch();
    }
    //     伪删除
    public function remove()
    {
        $del_id = input('id/d');
        $res = Db::name('Goods')->where('id', $del_id)->setField('is_del', 1);
        if ($res) {
            $this->success('ok', 'index');
        }
    }
    // 显示商品回收站
    public function recyle()
    {
        $category = model('Category')->getTreeList();
        $goods_info = model('Goods')->getGoodsList(1);
        $this->assign('category', $category);
        $this->assign('goods_info', $goods_info);
        return $this->fetch();
    }
    // 还原商品
    public function rollBack()
    {
        $r_id = input('id/d');
        $query = Db::name('Goods')->where('id', $r_id)->setField('is_del', 0);
        if ($query) {
            $this->success('ok', 'index');
        }
    }
    // 彻底删除
    public function del()
    {
        // 根据接收的id 删除商品信息 并且删除图片
        $res = model('Goods')->goodsDel();
        if ($res) {
            $this->success('删除成功', 'index');
        }
    }
}
