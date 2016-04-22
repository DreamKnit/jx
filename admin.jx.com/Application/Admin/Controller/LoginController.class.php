<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/18
 * Time: 11:45
 */

namespace Admin\Controller;


use Think\Controller;

class LoginController extends Controller{
    private $_model = null;
    protected function _initialize() {
        $this->_model = D('Login');
    }

    public function index(){
        $this->display();
    }

    public function captcha(){
        $config=C('CAPTCHA');
        $captcha=new \Think\Verify($config);
        $captcha->entry();
    }

    public function login(){
        if($this->_model->create('','login')){
            if($this->_model->login()){
                $this->success('登录成功！',U('Index/Index'));
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $this->error($this->_model->getError());
        }
    }

    public function logout(){
        session(null);
        cookie(null);
        $this->success('正在退出...',U('Index'));
    }
}