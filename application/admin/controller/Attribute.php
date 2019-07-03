<?php
namespace app\admin\controller;

use think\Request;

class Attribute extends Common
{
    public function attrAdd(Request $request)
    {
        if ($request->isGet()) {
            $type = model('Type')->getType();
            $this->assign('type', $type);
            return $this->fetch();
        }
        $attr_model = model('Attribute');
        $res = $attr_model->attrAdd(input());
        if ($res === false) {
            $this->error($attr_model->getError());
        }
        $this->success('ok', 'index');
    }
    public function Index()
    {
        $attr_data = model('Attribute')->getAttrData();
        $this->assign('attr', $attr_data);
        return $this->fetch();
    }
    public function Del()
    {
        model('Attribute')->delAttr(input('id/d'));
        $this->success('ok', 'index');
    }
    public function attrEdit()
    {
        if (Request::instance()->isGet()) {
            $attr = model('Attribute')->get(input('id/d'));
            $type = model('Type')->getType();
            $this->assign('type', $type);
            $this->assign('attr', $attr);
            return $this->fetch();
        }
        $attr_model = model('Attribute');
        $res = $attr_model->attrEdit(input());
        if ($res === false) {
            $this->error($attr_model->getError());
        }
        $this->success('ok', 'index');
    }
}
