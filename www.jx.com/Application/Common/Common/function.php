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

/**
 * 发送验证邮箱。
 * @param string $address 收件人地址.
 * @param string $subject 邮件主题.
 * @param string $content 邮件正文.
 * @param array $attachment 邮件附件.如果有就添加,为空就不添加.
 * @return bool
 */
function sendEmail($address, $subject, $content, array $attachment = []) {
    $config           = C('EMAIL_SETTING');
    vendor('PHPMailer.PHPMailerAutoload');
    $mail             = new \PHPMailer;

    $mail->isSMTP(); //使用smtp发送邮件
    $mail->Host       = $config['host']; //配置发送服务器,如果是多个,使用英文逗号分隔
    $mail->SMTPAuth   = true; //需要认证信息
    $mail->Username   = $config['username']; //用户名
    $mail->Password   = $config['password']; //密码
    $mail->SMTPSecure = $config['smtpsecure']; //传输协议
    $mail->Port       = $config['port']; //端口
    $mail->setFrom($config['username']); // 发件人
    $mail->addAddress($address);     // 收件人

    if ($attachment) {
        foreach ($attachment as $item) {
            $mail->addAttachment($item);         // 添加附件
        }
    }
    $mail->isHTML(true); //HTML格式邮件
    $mail->Subject = $subject; //标题
    $mail->Body    = $content; //正文
    $mail->CharSet = 'utf-8'; //编码
    return $mail->send();
}

/**
 * 获取redis对象.
 * @staticvar type $instance
 * @return \Redis
 */
function get_redis(){
    static $instance = null;
    if(empty($instance)){
        $instance = new Redis();
        $instance->connect(C('REDIS_HOST'),C('REDIS_PORT'));
    }
    return $instance;
}

/**
 * 金额格式化
 * @param number $number        原始数字.
 * @param integer $decimals     小数点后的位数.
 * @param string $dec_point     小数点使用的字符.
 * @param string $thousands_sep 千位分隔符.
 * @return string
 */
function money_format($number,$decimals=2,$dec_point ='.',$thousands_sep=''){
    return number_format($number,$decimals,$dec_point,$thousands_sep);
}