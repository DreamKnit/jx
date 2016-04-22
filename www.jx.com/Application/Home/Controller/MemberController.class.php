<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/20
 * Time: 17:22
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller{

    private $_model = null;

    /**
     * 初始化.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '京西商城',
            'register'    => '用户注册',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME])?$meta_titles[ACTION_NAME]:'京西商城';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Member');
    }
    public function register(){
        if(IS_POST){
            if($this->_model->create()){
                if($this->_model->addMember()===false){
                    $this->error($this->_model->getError());
                }else{
                   $this->success('注册成功！',U('login'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $this->display();
        }
    }

    /**
     * 验证数据表信息的唯一性
     */
    public function checkUniqueByParams(){
        $condition = I('get.');
        if($condition){
            $result=$this->_model->where($condition)->count();
            if($result){
                $this->ajaxReturn(false); // 信息存在验证失败
            }
        }
        $this->ajaxReturn(true);
    }

    // 手机短信验证功能
    public function sendSMS($telephone){
        $code = \Org\Util\String::randNumber(1000, 9999); // 得到随机验证码
        session('TEL_CAPTCHA',$code); // 存入session，在模型层调取（用户看不到）
        $data = [
            'code'=>$code,
            'product'=>'京西商城',
        ];
        // 调用阿里大鱼短信函数，把生成的验证码用短信的方式发送给用户
        if(sendSMS($telephone, $data)){
            $this->success('发送成功');
        }else{
            $this->error('发送失败');
        }
    }
}