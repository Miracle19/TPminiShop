<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 在tp框架之中  我们最好是先判断一下这个函数存在不存在
include_once('../extend/SendSMS.php');
include_once('../extend/163mail/class.phpmailer.php');
if(!function_exists('send_email')){
    function send_email($to,$msg,$Subject='账号注册邮件激活'){
        // require '../extend/PHPMailer/class.phpmailer.php';
        $mail             = new \PHPMailer();
        // 读取配置信息
        $server = config('email_server');
        /*服务器相关信息*/
        $mail->IsSMTP();   //启用smtp服务发送邮件                     
        $mail->SMTPAuth   = true;  //设置开启认证             
        $mail->Host       = $server['host'];      //指定发件箱smtp邮件服务器地址  
        $mail->Username   = $server['username'];     //指定用户名 
        $mail->Password   = $server['password'];     //邮箱的第三方客户端的授权密码
        /*内容信息*/
        $mail->IsHTML(true);
        $mail->CharSet    ="UTF-8";         
        $mail->From       = $server['from'];         
        $mail->FromName   ="商城管理员";   //发件人昵称
        $mail->Subject    = $Subject; //发件主题
        $mail->MsgHTML($msg);  //邮件内容 支持HTML代码
        $mail->AddAddress($to);  
        return $mail->Send();          //发送邮箱
    }
}
if (!function_exists("get_tree")) {
    // 创建一个获取商品分类层级关系的函数
    /**
     * @param $data    接收的数组
     * @param $id      主键id
     * @param $lev     层级关系
     * @return $list 返回一个带有层级lev索引的关系数组
     */
    function get_tree($data, $id = 0, $lev = 0, $is_clear = false)
    {
        // static 修饰变量的时候  他不会随着调用而消失  保存在内存中的对应区域    和普通变量不一样
        static  $list = [];   //保存最终结果
        if ($is_clear) {
            $list = [];
        }
        foreach ($data as $v) {
            if ($v['parent_id'] == $id) {
                $v['lev'] = $lev;
                //    向$list中添加一组数据 把pid=0的 存放在一起
                $list[] = $v;
                get_tree($data, $v['id'], $lev + 1, false);
            }
        }
        return $list;
    }
}
// 发送短信验证码的函数
if (!function_exists('send_ms')) {
        /**
         * 发送模板短信
         * @param to 手机号码集合,用英文逗号分开
         * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
         * @param $tempId 模板Id
         */
    function send_ms($to, $datas, $tempId = 1)
    {

        $accountSid = '8a216da86b8863a1016ba83678e91116';
        //主帐号Token
        $accountToken = '4f34492c50c2493c8b38a2c3094d6ad0';
        //应用Id
        $appId = '8a216da86b8863a1016ba8367949111d';
        //请求地址，格式如下，不需要写https://
        $serverIP = 'app.cloopen.com';
        //请求端口 
        $serverPort = '8883';
        //REST版本号
        $softVersion = '2013-12-26';
    
            // 初始化REST SDK
            // global $accountSid, $accountToken, $appId, $serverIP, $serverPort, $softVersion;
            $rest = new \REST($serverIP, $serverPort, $softVersion);
            $rest->setAccount($accountSid, $accountToken);
            $rest->setAppId($appId);
            // 发送模板短信
            // echo "Sending TemplateSMS to $to <br/>";
            $result = $rest->sendTemplateSMS($to, $datas, $tempId);
            if ($result == NULL) {
                // echo "result error!";
                return false;
            }
            if ($result->statusCode != 0) {
                // echo "error code :" . $result->statusCode . "<br>";
                // echo "error msg :" . $result->statusMsg . "<br>";
                return false;
                //TODO 添加错误处理逻辑
            } else {
                // echo "Sendind TemplateSMS success!<br/>";
                // // 获取返回信息
                // $smsmessage = $result->TemplateSMS;
                // echo "dateCreated:" . $smsmessage->dateCreated . "<br/>";
                // echo "smsMessageSid:" . $smsmessage->smsMessageSid . "<br/>";
                return true;
                //TODO 添加成功处理逻辑
            }
        
    }
}
