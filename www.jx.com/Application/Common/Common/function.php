<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/20
 * Time: 19:24
 */
/**
 *
 * @param string $telephone 发送到的电话
 * @param array $params 验证码及商家
 * 以下参数均为自己（独占）的阿里大鱼数据。
 * @param string $sign_name 签名
 * @param string $template_code 模板
 * @return bool
 */
function sendSMS($telephone,$params,$sign_name='大鱼测试',$template_code = 'SMS_8100064') {

    $config = C('ALIDAYU_SETTING'); // 得到阿里大鱼配置(ak,sk)
    vendor('Alidayu.Autoloader'); // 载入autoloader

    /*--------------------以下复制至阿里大鱼API测试工具（改编）--------------------*/
    $c            = new \TopClient;
    $c->format = 'json';
    $c->appkey    = $config['ak'];
    $c->secretKey = $config['sk'];
    $req          = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($sign_name);

    $req->setSmsParam(json_encode($params));
    $req->setRecNum($telephone);
    $req->setSmsTemplateCode($template_code);
    $resp         = $c->execute($req);
    if(isset($resp->result->success) && $resp->result->success){
        return true;
    }else{
        return false;
    }


}