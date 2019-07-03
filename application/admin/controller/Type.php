<?php
namespace app\admin\controller;

use think\Request;
use think\Db;


class Type extends Common
{
    // 显示类型表
    public function Index(Request $request)
    {
        if ($request->isGet()) {
            $type_model = model('Type');
            $type  = $type_model->getType();
            $this->assign('type', $type);
            // halt($type);
            return $this->fetch();
        }
    }
    public function typeEdit()
    {
        if (Request::instance()->isGet()) {
            $type_id = input('id');
            $type_data = Db::name('Type')->find($type_id);
            $this->assign('type', $type_data);
            return $this->fetch();
        }
        $post_data = input();
        $res = $this->validate($post_data, 'Type');
        if ($res !== true) {
            $this->error($res);
        }
        model('Type')->typeEdit($post_data);
        $this->success('ok', 'index');
    }
    public function typeAdd()
    {
        if (Request::instance()->isGet()) {
            return $this->fetch();
        }
        $post_data = input();

        $rs = $this->validate($post_data, 'Type');
        if ($rs !== true) {
            $this->error($rs);
        }
        cache('type',null);
        Db::name('Type')->insert($post_data);
        $this->success('ok', 'index');
    }
    public function typeDel()
    {
        $query = Db::name('Type');
        cache('type',null);
        $query->delete(input('id/d'));
        $this->success('ok', 'index');
    }
}
