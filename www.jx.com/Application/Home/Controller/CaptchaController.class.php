<?php


namespace Home\Controller;

class CaptchaController extends \Think\Controller{
    public function captcha(){
        $options = C('CAPTCHA');
        $verify = new \Think\Verify($options);
        $verify->entry();
    }
}
