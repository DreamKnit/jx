<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/17
 * Time: 12:04
 */

namespace Admin\Model;


use Think\Model;

class AdminModel extends Model{
    public $_success;
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('username', 'require', '管理员名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('username', '', '该管理员已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('username', '2,16', '管理员名称长度为2至16位', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('password', '6,16', '密码长度为6至16位', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('email', 'require', '邮箱不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('email', 'email', '邮箱不合法', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
    );

    protected $_auto = array(
        // 在执行插入的时候会执行内置函数增加一个salt字段
        array('salt', '\Org\Util\String::randString', self::MODEL_INSERT, 'function', 4),
        // 自定义自动完成，调用resetPwd重置密码自动生成一个盐
        array('salt', '\Org\Util\String::randString', 'reset_pwd', 'function', 4),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
    );

    public function getList($field = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }


    public function getAdminInfo($id){
        // 找到符合条件的角色数据
        $row = $this->find($id);
        // 没有数据反馈
        if(empty($row)){
            $this->error = '管理员不存在';
            return false;
        }
        $role_model=M('AdminRole');
        $role_ids  = $role_model->where(array('admin_id' => $id))->getField('role_id', true);
        $row['role_ids'] = $role_ids;

        $permission_model =M('AdminPermission'); // 管理员权限关联表
        // 查询出对应id的所有关联数据（permission_id数组）
        $permission_ids = $permission_model->where(array('admin_id'=>$id))->getField('permission_id',true);
        $row['permission_ids'] = json_encode($permission_ids); // 添加一个字段json数据
        return $row; // 返回数据处理好的数据
    }

    public function addAdmin(){
        unset($this->data['id']);
        // 密码加盐
        $this->data['password'] = salt_password($this->data['password'], $this->data['salt']);

        // 基本数据的保存
        if(($admin_id = $this->add()) === false){
            return false;
        }

        if($this->_saveRole($admin_id)===false){
            $this->_success = '添加成功，但该管理员没有任何角色关联！';
        }else{
            $this->_success='角色关联添加成功！';
        }

        // 保存额外权限关联数据
        if($this->_savePermission($admin_id)===false){
            $this->_success.= '添加成功，但该管理员没有任何额外权限关联！';
        }else{
            $this->_success.='额外权限添加成功！';
        }
        return true;
    }

    public function updateAdmin(){
        $request_data = $this->data; // 获取数据
        /*if($this->save() === false){ // 保存基础数据
            return false;
        }*/

        if($this->_saveRole($request_data['id'])===false){
            $this->_success = '添加成功，但该管理员没有任何角色关联！';
        }else{
            $this->_success='角色关联添加成功！';
        }

        // 保存额外权限关联数据
        if($this->_savePermission($request_data['id'], false) === false){
            $this->_success.= '修改成功，但该管理员没有任何权限！';
        }else{
            $this->_success.='额外权限添加成功！';
        }
        return true;
    }

    public function removeAdmin($id) {
        if ($this->delete($id) === false) {
            return false;
        }
        if($this->_removeRole($id)===false){
            $this->error = '角色关联删除失败';
            return false;
        }
        if($this->_removePermission($id)===false){
            $this->error = '权限关联删除失败';
            return false;
        }
        return true;
    }

    private function _removeRole($admin_id) {
        if (M('AdminRole')->where(array('admin_id' => $admin_id))->delete() === false) {
            return false;
        }
    }

    private function _removePermission($admin_id) {
        if (M('AdminPermission')->where(array('admin_id' => $admin_id))->delete() === false) {
            return false;
        }
    }

    /**
     * 权限关联数据保存
     * @param string $role_id 角色id
     * @param bool $is_new 新增数据还是修改数据的开关
     * @return bool|string
     */
    private function _savePermission($admin_id,$is_new=true){
        $perms = I('post.perm'); // 得到权限树
        // 两种可能。
        // 一种是添加的时候可以允许是没有权限，
        // 第二种是修改的时候可以为空权限
        // 由于注释了判断插入的数据是空返回false，同时数据表清空，实现了零权限
        /*if(empty($perms)){
            return true;
        }*/

        $data = array();
        foreach ($perms as $perm){
            $data[] = array(
                'admin_id'=>$admin_id,
                'permission_id'=>$perm,
            );
        }
        $model = M('AdminPermission');
        // 判断是更新还是插入，如果是更新还需要先删除一道数据
        if(!$is_new){
            $model->where(array('admin_id'=>$admin_id))->delete();
        }
        return M('AdminPermission')->addAll($data);
    }

    private function _saveRole($admin_id, $is_new = true) {
        $roles = I('post.role'); // 得到角色
        // 两种可能。
        // 一种是添加的时候可以允许是没有权限，
        // 第二种是修改的时候可以为空权限
        // 由于注释了判断插入的数据是空返回false，同时数据表清空，实现了零权限
        /*if(empty($perms)){
            return true;
        }*/

        $data = array();
        foreach ($roles as $role){
            $data[] = array(
                'admin_id'=>$admin_id,
                'role_id'=>$role,
            );
        }
        $model = M('AdminRole');
        // 判断是更新还是插入，如果是更新还需要先删除一道数据
        if(!$is_new){
            $model->where(array('admin_id'=>$admin_id))->delete();
        }
        return M('AdminRole')->addAll($data);
    }

    public function resetPwd(){
        $password=I('get.password'); // 获取输入的密码
        if(!$password){ // 没有密码自动生成
            $length=mt_rand(6,16);
            // 调用tp的随机生成字符串，规定长度追加字符
            $password = \Org\Util\String::randString($length,'','~!@#$%^&*()_+');
        }

        $this->data['password'] = salt_password($password, $this->data['salt']);
        return $this->save()?$password:false; // 如果保存成功返回密码
    }
}