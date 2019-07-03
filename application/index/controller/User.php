<?php
namespace app\index\controller;

use think\Request;
use think\Request as ThinkRequest;
use think\Db;

class User extends Common{
    public function Regist(){
        return $this->fetch();
        // 由于是ajax注册所有这个方法只需要渲染模板即可
    }
    // 邮箱注册
    public  function emailReg(){
        if(ThinkRequest::instance()->isGet()){
            return $this->fetch();
        }
        $res=  model('User')->emailReg(input('username'),input('password'),input('email'));
        if($res===false){
            $this->error(model('User')->getError());
        }
        $this->success('注册成功','login');
    }
    // 实现激活
    public function active(){
        $key=input();
        // dump($key);
        // dump($key["key"]);
        // 查询用户信息
        $user_info =Db::name('User')->where('active_code',$key['key'])->find();
        // $user_info=Db::name('User')->getLastSql();
        // exit;
        if(!$user_info){
            // 如果查不到当前用户信息记录,直接跳转至用户首页
            $this->redirect('index/index');
        }
        if($user_info['status']==1){
            $this->error('您的账户已经激活过了','login');
        }
        db('user')->where(['active_code'=>$key['key']])->setField('status',1);
        $this->success('激活完成','login');
    }
    // 短信接口
    public function sendSms(){
        $tel = input('tel');
        $code = rand(1000,9999);
        $res = send_ms($tel,[$code,5]);
        if(!$res){
            return json(['code'=>0,'msg'=>'网络异常']);
        }
        // 存入session中
        session('SmsCode',['code'=>$code,'time'=>time()]);
        return json(['code'=>1,'msg'=>'ok']);
    }
    // ajax注册
    public function doregist(Request $request)
	{
        // 防止其他url方式请求
		if(!$request->isAjax()){
			return json(['code'=>'0','msg'=>'非法请求IP已被记录']);
        }
        $data = input('post.');
        $model = model('User');
        // 从session取得四位短信随机号码
        $session_data = session('SmsCode');
        $session_code = $session_data['code'];
        // var_dump(input('capt'), $session_data);exit;
        // 比对验证码是否存在 或者与前session值是否相等
        if(!$session_code || $session_code != input('capt')){
            return json(['code'=>0,'msg'=>'验证码错误']);
        }
        // echo $session_data['time'];exit;
        // 比对验证码是否过期
        if($session_data['time']+300 < time()){
            session('SmsCode',null);
            return json(['code'=>0,'msg'=>'验证码已经过期']);
        }
        // 调用模型方法注册
        $res = $model->regist($data);
		if($res === false){
		return json(['code'=>0,'msg'=>$model->getError()]);
        }
        // $this->success('ok','login');
		return json(['code'=>1,'msg'=>'ok']);
    }
    public function login(){
        // cart 是否具有购物车传来的参数  有将参数传递进登入页面
        $cart = input('cart') ? input('cart') : '';
        if(Request::instance()->isGet()){
            
            $this->assign('cart', $cart);
            return $this->fetch();
        }
        $res = model('User')->doLogin(input('post.'));
        if(!$res){
            $this->error(model('User')->getError());
        }
        $cart = input('cart') ? input('cart') : '';
        // 在这里判断 成功走哪个页面
        if ($cart) {
            $this->success('ok','cart/index');
        }
        $this->success('ok','index/index');
    }
    public function logOut(){
        // session('user_info',$user_info->toArray());
        // 清空用户信息
        session('user_info',null);
        $this->success('ok','login');
    }
    public function checkcode(){
        $obj = new \think\captcha\Captcha(['length'=>4,'codeSet'=>'12345689'
        ,'useNoise'=>false]);
        return $obj->entry();
    }
}