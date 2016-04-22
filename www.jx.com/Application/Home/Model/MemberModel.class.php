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
        // 密码加盐
        $this->data['password'] = salt_password($this->data['password'], $this->data['salt']);

        // 数据保存
        if(($admin_id = $this->add()) === false){
            return false;
        }
        return true;
    }
}