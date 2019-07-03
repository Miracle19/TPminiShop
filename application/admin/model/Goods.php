<?php
namespace app\admin\model;

use think\Model;
use think\Db;


class Goods extends Model
{
   public function getGoodsList($is_del = 0)
   {
      // 保存我们的查询条件
      // is_del 代表查询出当前所有伪删除条件为0的数据
      $where = ['is_del' => $is_del];
      // 接收关键字字段拼where条件
      $keyword = input('keyword');
      if ($keyword) {
         $where['goods_name'] = ['like', '%' . $keyword . '%'];
      }
      // 接收分类id字段 
      $cate_id = input('cate_id');
      if ($cate_id) {
         // echo $cate_id;
         // 第二次调用
         $cate = model('Category')->getTreeList($cate_id, true);
         // 拼in 语法
         $in = [];
         $in[] = $cate_id;
         foreach ($cate as $v) {
            $in[] = $v['id'];
         }
         $where['cate_id'] = ['in', $in];
      }
      // 接收推荐状态
      $intro_type = input('intro_type');
      if ($intro_type) {
         $where[$intro_type] = 1;
      }
      //这里用db 不用model
      // dump($where);
      //                                                             这里的paginate参数第3个代表我们要传的url 避免二次刷新丢失参数
      $query = Db::name('Goods')->Where($where)->paginate(10, false, ['query' => input()]);
      //  echo Db::name('Goods')->getLastSql();

      return $query;
   }
   public function goodsAdd($goods_data)
   {
      //  数据入库之后发现时间不会自动入进去
      $goods_data['addtime'] = time();
      // 这里商品入库时 两个独立的sql语句 两句话必须同时执行成功 不能让数据缺失
      Db::startTrans(); //开启事务
      try {
         $this->allowField(true)->isUpdate(false)->save($goods_data);
         // 获取商品id
         $goods_id = $this->getLastInsId();
         // 调用模型方法 实现商品属性的最终入库
         model('GoodsAttr')->addAll($goods_id, input('attr_ids/a'), input('attr_values/a'));
         model('GoodsImg')->addAll($goods_id);
         Db::commit();
      } catch (\Exception $e) {
         Db::rollback();
         $this->error = '写入错误';
         return false;
      }
   }
   public function changeStatus($goods_id, $filed)
   {
      // 接收参数 查找当前值的 状态 并修改
      $goods_info = $this->get($goods_id);
      //    控制器接收商品id 获得模型对象   
      if (!$goods_info) {
         $this->error = '参数错误';
         return false;
      }
      //    调用模型对象下的获取属性方法    获得相应的属性值   
      // 如果查出属性值为1 则将status值更改为0
      $status = $goods_info->getAttr($filed) ? 0 : 1;
      //    重新修改 模型对象中的 $filed 即我们三个状态中的 属性值
      $goods_info->$filed = $status;
      $goods_info->isUpdate(true)->save();
      return $status;
   }
   public function editGoods($goods_data)
   {
      // 由于checkbox没有勾选的项目不会传参数 所以还是未修改之前的状态  我们这里要手动判断
      $goods_data['is_rec'] = isset($goods_data['is_rec']) ? 1 : 0;
      $goods_data['is_new'] = isset($goods_data['is_new']) ? 1 : 0;
      $goods_data['is_hot'] = isset($goods_data['is_hot']) ? 1 : 0;
      // halt($goods_data);
      unset($goods_data['goods_sn']);
      return $this->isUpdate(true)->allowField(true)->save($goods_data);
   }
   public function goodsDel()
   {
      $del_id  = input('id');
      $res = $this->get($del_id);
      // 查出数据并根据路径  删除掉对应文件夹下的图片
      $goods_img = $res->goods_img;
      $goods_thumb = $res->goods_thumb;
      @unlink($goods_img);
      @unlink($goods_thumb);
      // 调用delete方法需注意 调用者必须是当前的模型对象
      return $res->delete();
   }
}
