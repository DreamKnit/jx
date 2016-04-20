<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/17
 * Time: 18:38
 */

namespace Admin\Model;


use Think\Model;

class MenuModel extends Model{
    public $_success;
    public function getList($field='*'){
        $cond = array(
            'status'=>1,
        );
        return $this->field($field)->where($cond)->order('lft')->select();
    }

    public function addMenu(){
        $nestedsets = $this->_getNestedsets();
        if(($menu_id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom'))===false){
            $this->error = '添加菜单失败';
            return false;
        }

        //保存权限关系
        if($this->_savePermission($menu_id, false)){
            $this->_success='保存成功！';
        }else{
            $this->_success.= '保存成功，但没有人能看到它！';
        }
        return true;
    }

    public function getMenuInfo($id){
        // 找到符合条件的菜单数据
        $row = $this->where(['status'=>1])->find($id);
        // 没有数据反馈
        if(empty($row)){
            $this->error = '菜单不存在';
            return false;
        }

        $permission_model =M('MenuPermission'); // 菜单关联表
        // 查询出对应id的所有关联数据（permission_id数组）
        $permission_ids = $permission_model->where(array('menu_id'=>$id))->getField('permission_id',true);
        $row['permission_ids'] = json_encode($permission_ids); // 添加一个字段json数据
        return $row; // 返回数据处理好的数据
    }





    /**
     * 更新方法
     * @return bool
     */
    public function updateMenu(){
        $data=$this->data;
        $parent_id=$this->getFieldById($this->data['id'],'parent_id');

        // 由于点选加入了另外的分类，父亲id和以前数据库里的相比较就有所改动，
        // 依照此方式来判断是否改动过树，有改动则修改
        if($parent_id!=$this->data['parent_id']){

            $db_mysql_logic=new \Admin\Logic\DbMySqlLogic();
            $nested_sets=new \Admin\Service\NestedSets(
                $db_mysql_logic,$this->trueTableName,'lft','rght','parent_id','id','level'
            );
            // 执行接口里的移动方法它会自动编排改变后的lr值（其他数据不会保存），
            // 参数1：自己id，参数2：移动到id，参数3：嵌套集合排列方式（一般bottom）
            // 判断移动时不能移动到自己晚辈的层级里（它会自动判断）
            if($nested_sets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom')===false){
                $this->error='不能移动到子级菜单'; // 控制器的getError()方法手动设定一个反馈
                return false;
            }
        }

        // 无论是否更改过分类，都会保存一下数据
        if($this->save()===false){
            return false;
        }
        if($this->_savePermission($data['id'],false)){
            $this->_success = '保存成功！';
        }else{
            $this->_success = '保存成功，但是没有人能看到它！';
        }

        return true;
    }

    /**
     * 逻辑删除方法
     * @param $id
     * @return bool
     */
    public function removeMenu($id){
        // 得到id等于当前id的id,lft,rght,name四个字段值（数组）
        $categorys = $this->where(array('id'=>$id))->getField('id,lft,rght,name');
        // 拼接条件lft与rght之间的所有数据（包括边界）
        $conditions=array(
            'lft'=>array('egt',$categorys[$id]['lft']),
            'rght'=>array('elt',$categorys[$id]['rght']),
        );
        // 传入条件执行修改，将名字追加_del，并且status修改为0
        $categorys[$id]['name'].='_del';
        $data=array(
            'name'=>$categorys[$id]['name'],
            'status'=>0
        );
        return $this->where($conditions)->save($data);
    }


    private function _getNestedsets(){
        // 数据库操作对象 Service层需要的Logic层的对象
        $db_mysql_logic=new \Admin\Logic\DbMySqlLogic();
        // $db_mysql_logic=D('DbMysql','Logic'); // 要去查询数据表，没有数据表
        return new \Admin\Service\NestedSets(
        // 传入Logic层对象，表名，相关构造方法需要的字段（参照接口那边的构造方法）
            $db_mysql_logic,$this->trueTableName,'lft','rght','parent_id','id','level'
        );
    }

    /**
     * 权限关联数据保存
     * @param string $role_id 角色id
     * @param bool $is_new 新增数据还是修改数据的开关
     * @return bool|string
     */
    private function _savePermission($menu_id,$is_new=true){
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
                'menu_id'=>$menu_id,
                'permission_id'=>$perm,
            );
        }
        $model = M('MenuPermission');
        // 判断是更新还是插入，如果是更新还需要先删除一道数据
        if(!$is_new){
            $model->where(array('menu_id'=>$menu_id))->delete();
        }

        // 当权限全部为空时会返回false
        return M('MenuPermission')->addAll($data);
    }

    /**
     * 获取当前登录用户所能看到的菜单列表.
     */
    public function getMenuList(){
        //获取用户权限id列表
        $permission_ids = session('PERM_IDS');
        // session中没有保存过权限列表，返回空数组。
        if(empty($permission_ids)){
            return [];
        }
        //SELECT path,NAME,LEVEL FROM menu_permission AS mp LEFT JOIN menu AS m ON mp.`menu_id`=m.`id` WHERE permission_id IN (6,16,17,28)
        $cond = [
            'permission_id'=>['in',$permission_ids],
        ];
        $menus = $this->distinct(true)->alias('m')->field('id,path,name,level,parent_id')->join('__MENU_PERMISSION__ as mp ON mp.`menu_id`=m.`id`')->where($cond)->select();
        //dump($permission_ids);exit;
        return $menus;
    }

}