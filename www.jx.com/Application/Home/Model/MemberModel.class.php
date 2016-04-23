<?php

/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/20
 * Time: 19:17
 */
namespace Home\Model;
use Think\Model;

class MemberModel extends Model{
    protected $_validate = [
        ['captcha','checkPhoneCode','手机验证码错误！',self::EXISTS_VALIDATE,'callback',self::MODEL_INSERT],
        array('captcha', 'require', '短信验证码不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('checkcode', 'require', '验证码不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('username', 'require', '用户名名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('username', '', '该用户名已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('username', '3,20', '用户名名称长度为3至20位', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('password', '6,20', '密码长度为6至20位', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('email', 'require', '邮箱不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('email', 'email', '邮箱不合法', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('email', '', '邮箱已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('tel', 'require', '手机不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('tel', '', '手机已被注册', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    ];
    protected $_auto = array(
        // 在执行插入的时候会执行内置函数增加一个salt字段
        array('salt', '\Org\Util\String::randString', self::MODEL_INSERT, 'function', 4),
        array('add_time', NOW_TIME, self::MODEL_INSERT), // 注册时间
        /*---邮箱验证相关---*/
        ['token','Org\Util\String::randString',self::MODEL_INSERT,'function',40], // 激活所需token码（40位）
        ['send_time',NOW_TIME,self::MODEL_INSERT], //邮件发送时间（便于计算邮件有效期）
    );

    /**
     * 自动验证手机验证码
     * @param $code
     * @return bool
     */
    protected function checkPhoneCode($code){
        $session_code = session('TEL_CAPTCHA'); // 得到验证码
        session('TEL_CAPTCHA',null); // 保存到变量后清空
        if($code == $session_code){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 注册会员
     * @return bool
     */
    public function addMember() {
        $data = $this->data;
        // 密码加盐
        $this->data['password'] = salt_password($this->data['password'], $this->data['salt']);

        // 数据保存
        if(($admin_id = $this->add()) === false){
            return false;
        }
        if($this->_sendActiveEmail($data['email'],$data['token']) === false){
            $this->error = '激活邮件发送失败';
            return false;
        }
        return true;
    }

    /**
     * 执行验证邮箱的发送
     * @param string $email 邮箱地址
     * @param string $token token码
     * @return mixed
     */
    private function _sendActiveEmail($email,$token){
        $url = U('active', ['email' => $email, 'token' =>$token ], true, true);
        $content = <<<EMAIL
<h1 style="color: yellowgreen;">注册成功,点击账号激活</h1>
<p style="border:1px dotted blue">请点击<a href='$url'>链接</a>进行激活,如果无法访问,请手动访问:$url</p>
<p>你有京东，我有京西！</p>
<p style="text-align:right; font-weight: bold;">京西商城</p>
EMAIL;

        return sendEmail($email, '注册成功,请激活账号！', $content) ;
    }

    public function login(){
        //为了安全我们将用户信息都删除
        session('MEMBER_INFO',null);
        $request_data = $this->data;
        //1.验证用户名是否存在
        $userinfo = $this->getByUsername($this->data['username']);
        if(empty($userinfo)){
            $this->error = '用户不存在';
            return false;
        }
        //2.进行密码匹配验证
        $password = salt_password($request_data['password'], $userinfo['salt']);
        if($password != $userinfo['password']){
            $this->error = '密码不正确';
            return false;
        }
        //为了后续会话获取用户信息,我们存下来
        session('MEMBER_INFO',$userinfo);

        //保存自动登陆信息
        $this->_saveToken($userinfo['id']);
        if($this->_cookie2db() === false){
            $this->error = '购物车同步失败';
            return false;
        }
        return true;
    }

    private function _cookie2db(){
        //将用户的cookie购物车保存到数据库中
        $shopping_car_model = D('ShoppingCar');
        return $shopping_car_model->cookie2db();
    }
}