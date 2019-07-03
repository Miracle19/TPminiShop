<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Cart extends Model
{
    // 添加购物车
    public function addCart($goods_id, $goods_attr_ids, $goods_count)
    {
        // 判断用户是否登入
        $user_info = session('user_info');
        if ($user_info) {
            // 如果登入操作数据库,先查询已经购买的商品,如果存在相同属性的商品,则实现商品数量累加
            $map = [
                'goods_id' => $goods_id,
                'goods_attr_ids' => $goods_attr_ids,
                'user_id' => $user_info['id']
            ];
            if ($this->where($map)->find()) {
                // setInc让字段的值增加
                $this->where($map)->setInc('goods_count', $goods_count);
            } else {
                // 不存在 写入商品数据
                $map['goods_count'] = $goods_count;
                $this->save($map);
            }
        } else {
            // 未登入操作cookie  先查询cookie是否具有相同属性的商品 有就修改数量 没有就添加到cookie
            // 读取cookie中的内容
            $cart_list = cookie('cart_list') ? cookie('cart_list') : [];
            // 组装下标名称  
            $key = $goods_id . '-' . $goods_attr_ids;
            // 如果该$KEY 一个由属性id与商品id拼起来的对比数据 存在于cookie中  增加商品数量
            if (array_key_exists($key, $cart_list)) {
                $cart_list[$key] += $goods_count;
            } else {
                // 不存在    写入商品数量
                $cart_list[$key] = $goods_count;
            }
            //从后将商品数据保存于cookie中
            cookie('cart_list', $cart_list, 3600 * 24 * 6);
        }
        // dump(cookie('cart_list'));

    }
    // 购物车商品列表显示
    public function getList()
    {
        $user_info = session('user_info');
        if ($user_info) {
            // 登入从数据库拿
            $cart_list = Db::name('Cart')->where('user_id', $user_info['id'])->select();
        } else {
            // 未登入从cookie拿
            $cart = cookie('cart_list') ? cookie('cart_list') : [];
            $cart_list = [];
            foreach ($cart as $key => $values) {
                // 处理$key中的内容
                $temp = explode('-', $key);
                $cart_list[] = [
                    "goods_id" => $temp[0],
                    "goods_attr_ids" => $temp[1],
                    "goods_count" => $values
                ];
            }
        }
        // dump($cart_list);
        // 获取商品属性
        foreach ($cart_list as $key => $values) {
            // 获取商品基本信息
            $cart_list[$key]['goods'] = Db::name('Goods')->where('id', $values['goods_id'])->find();
            // 获取商品属性值信息
            // dump($values);
            $sql = "SELECT a.attr_value,b.attr_name FROM tedi_goods_attr a LEFT JOIN tedi_attribute b ON a.attr_id = b.id
            WHERE a.id IN ({$values['goods_attr_ids']})";
            $cart_list[$key]['attrs'] = db('goods_attr')->query($sql);
        }
        return $cart_list;
    }
    // 获取商品总价格
    public function getTotal($data)
    {
        $total = 0;
        foreach ($data as $key => $values) {
            $total += $values['goods_count'] * $values['goods']['shop_price'];
        }
        return $total;
    }
    // 删除购物车商品
    public function remove($goods_id, $goods_attr_ids)
    {
        // dump($goods_attr_ids);
        // dump($goods_id);
        $user_info = session('user_info');
        // dump($user_info['id']);
        if ($user_info) {
            // echo '111';exit;
            $map = [
                'goods_id' => $goods_id,
                'goods_attr_ids' => $goods_attr_ids,
                'user_id' => $user_info['id']
            ];
            Db::name('Cart')->where($map)->delete();
            // dump(Db::name('Cart')->getLastSql());
            // echo 111;
        } else {
            // echo 12211;
            $cart_list = cookie('cart_list') ? cookie('cart_list') : [];
            $key = $goods_id . '-' . $goods_attr_ids;
            unset($cart_list[$key]);
            cookie('cart_list', $cart_list, 3600 * 24);
        }
    }
    // ajax发起改变商品购买数量
    public function changeNumber($goods_id, $goods_attr_ids, $goods_count)
    {
        // 老规矩 先走有没有登入
        $user_info = session('user_info');
        if ($user_info) {
            $map = [
                "goods_id" => $goods_id,
                "goods_attr_ids" => $goods_attr_ids,
                'user_id' => $user_info['id']
            ];
            $this->where($map)->setField('goods_count', $goods_count);
        } else {
            $cart_list = cookie('cart_list') ? cookie('cart_list') : [];
            //  拼出key
            $key = $goods_id . '-' . $goods_attr_ids;
            $cart_list[$key] = $goods_count;
            cookie('cart_list', $cart_list, 3600 * 24);
        }
    }
    public function changeNumber2($goods_id,$goods_attr_ids,$type){
        $user_info =session('user_info');
        if($user_info){
            $map=[
                'goods_id'=>$goods_id,
                'goods_attr_ids'=>$goods_attr_ids,
                'user_id'=>$user_info['id']
            ];
            if($type==-1){
                $this->where($map)->setInc('goods_count', $type);
            }else{
                $this->where($map)->setInc('goods_count', $type);
            }
        }else{
            $cart_list = cookie('cart_list') ? cookie('cart_list') : [];
            // 拼出key
            $key = $goods_id . '-' . $goods_attr_ids;
            $cart_list[$key] += $type;
            cookie('cart_list', $cart_list, 3600 * 24);
        }

    }
    // 处理 当登录的时候触发 将cookie中的数据转移至数据库
    public function cookie2db()
    {
        $user_info = session('user_info');
        if (!$user_info) {
            // 登入情况下 组织该方法执行
            return false;
        }
        $cart_list = cookie('cart_list') ? cookie('cart_list') : [];
        // dump($cart_list);
        foreach ($cart_list as $key => $values) {
            $temp = explode('-', $key);
            // dump($temp);
            $map = [
                'goods_id' => $temp[0],
                'goods_attr_ids' => $temp[1],
                'user_id' => $user_info['id']
            ];
            if ($this->where($map)->find()) {
                $this->where($map)->setField('goods_count', $values);
            } else {
                $map['goods_count'] = $values;
                $this->save($map);
            }
        }

        //  清空cookie
        cookie('cart_list', null);
    }
}
