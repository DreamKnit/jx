<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/18
 * Time: 11:46
 */

namespace Admin\Model;


use Think\Model;

class LoginModel extends Model{
    // 虚拟模型，没有实际的表，避开tp基础模型的数据库操作
    Protected $autoCheckFields = false;
    protected $_validate=array(
        array('username', 'require', '请输入用户名', self::MUST_VALIDATE, '', 'login'),
        array('password', 'require', '请输入密码', self::MUST_VALIDATE, '', 'login'),
        //array('captcha', 'require', '请输入验证码', self::MUST_VALIDATE, '', 'login'),
        //array('captcha', 'check_captcha', '验证码不正确', self::MUST_VALIDATE, 'callback', 'login'),
    );



    protected function check_captcha($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    public function login(){
        session('USERINFO',null);
        $request_data = $this->data;
        $admin_model=D('admin');
        $userinfo = $admin_model->getByUsername($this->data['username']);
        if(empty($userinfo)){
            $this->error = '用户不存在';
            return false;
        }
        $password = salt_password($request_data['password'], $userinfo['salt']);
        if($password != $userinfo['password']){
            $this->error = '密码不正确';
            return false;
        }
        session('USERINFO',$userinfo);
        $this->_getPermissions($userinfo['id']);
        return true;
    }

    private function _getPermissions($admin_id){
        session('PATHS',null); // 清空之前管理员允许路径的session，准备重新取值
        session('PERM_IDS',null); // 清空权限id
        //获取通过角色得到的权限
        $role_permssions = $this->distinct(true)->table('__ADMIN_ROLE__ as ar')->join('__ROLE_PERMISSION__ as rp ON ar.`role_id`=rp.`role_id`')->join('__PERMISSION__ as p ON rp.`permission_id`=p.`id`')->where(['admin_id'=>$admin_id,'path'=>['neq',''] ])->getField('permission_id,path',true);

        //dump($this->getLastSql());exit;
        //获取额外权限
        $admin_permissions = $this->distinct(true)->table('__ADMIN_PERMISSION__ as ap')->join('__PERMISSION__ as p ON ap.`permission_id` = p.`id`')->where(['admin_id'=>$admin_id ,'path'=>['neq','']])->getField('permission_id,path',true);
//        $paths = array_merge($role_permssions,$admin_permissions);
        //由于前面获取的都是关联数组,+合并会自动合并键名相同的元素,也就等同于做了去重
        $role_permssions=$role_permssions?:[];
        $admin_permissions=$admin_permissions?:[];
        $permissions = $role_permssions+$admin_permissions;
        //获取权限id列表
        $permission_ids = array_keys($permissions);
        //获取权限路径列表
        $paths = array_values($permissions);
        session('PATHS',$paths); // 保存该管理员允许访问的路径（给行为层处理）
        session('PERM_IDS',$permission_ids); // 保存该管理员允许访问的路径id（给menu模型层处理）
    }
}