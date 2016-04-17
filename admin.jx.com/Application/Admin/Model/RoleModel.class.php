<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/16
 * Time: 9:59
 */

namespace Admin\Model;


use Think\Model;

class RoleModel extends Model{
    public $_success;
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('name','require','角色名字不能为空！',self::EXISTS_VALIDATE,'',1),
    );
    /**
     * 获取符合条件的数据
     * @param string $field
     * @return mixed
     */
    public function getList($field = '*'){
        // 根据圈图排序应该用lft
        return $this->field($field)->where(array('status' => 1))->select();
    }

    /**
     * 显示分页及相关数据
     * @param array $conditions
     * @return array
     */
    public function showPage(array $conditions=array()){
        $conditions+=array(
            'status'=>1,
        );
        $page_size=C('PAGE_SIZE');
        $count=$this->where($conditions)->count();
        $page=new \Think\Page($count,$page_size);
        $show_page=$page->show();
        $rows=$this->where($conditions)->page(I('get.p'),$page_size)->select();
        return array(
            'show_page'=>$show_page,
            'rows'=>$rows,
        );
    }

    public function addRole(){
        // 基本数据的保存
        if(($role_id = $this->add()) === false){
            return false;
        }
        // 保存权限关联数据
        if($this->_savePermission($role_id)===false){
            $this->_success = '添加成功，但该角色没有任何权限！';
        }else{
            $this->_success='添加成功！';
        }
        return true;
    }

    public function updateRole(){
        $request_data = $this->data; // 获取数据
        if($this->save() === false){ // 保存基础数据
            return false;
        }

        // 保存权限关联数据
        if($this->_savePermission($request_data['id'], false) === false){
            $this->_success = '修改成功，但该角色没有任何权限！';
        }else{
            $this->_success='修改成功！';
        }
        return true;
    }

    public function getRoleInfo($id){
        // 找到符合条件的角色数据
        $row = $this->where(array('status' => 1))->find($id);
        // 没有数据反馈
        if(empty($row)){
            $this->error = '角色不存在';
            return false;
        }
        $permission_model =M('RolePermission'); // 角色权限关联表
        // 查询出对应id的所有关联数据（permission_id数组）
        $permission_ids = $permission_model->where(array('role_id'=>$id))->getField('permission_id',true);
        $row['permission_ids'] = json_encode($permission_ids); // 添加一个字段json数据
        return $row; // 返回数据处理好的数据
    }

    public function removeRole($id){
        return $this->where(array('id'=>$id))->setField('status',0);
    }

    /**
     * 权限关联数据保存
     * @param string $role_id 角色id
     * @param bool $is_new 新增数据还是修改数据的开关
     * @return bool|string
     */
    private function _savePermission($role_id,$is_new=true){
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
                'role_id'=>$role_id,
                'permission_id'=>$perm,
            );
        }
        $model = M('RolePermission');
        // 判断是更新还是插入，如果是更新还需要先删除一道数据
        if(!$is_new){
            $model->where(array('role_id'=>$role_id))->delete();
        }
        return M('RolePermission')->addAll($data);
    }

}