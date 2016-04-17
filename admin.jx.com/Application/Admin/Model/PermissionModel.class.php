<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/16
 * Time: 9:00
 */

namespace Admin\Model;


use Think\Model;

class PermissionModel extends Model{
    /**
     * 获取符合条件的数据
     * @param string $field
     * @return mixed
     */
    public function getList($field = '*'){
        // 根据圈图排序应该用lft
        return $this->field($field)->order('lft')->where(array('status' => 1))->select();
    }

    /**
     * 添加权限数据
     * @return mixed
     */
    public function addPermission(){
        // 数据库操作对象 Service层需要的Logic层的对象
        $db_mysql_logic=new \Admin\Logic\DbMySqlLogic();
        // $db_mysql_logic=D('DbMysql','Logic'); // 要去查询数据表，没有数据表
        $nested_sets=new \Admin\Service\NestedSets(
        // 传入Logic层对象，表名，相关构造方法需要的字段（参照接口那边的构造方法）
            $db_mysql_logic,$this->trueTableName,'lft','rght','parent_id','id','level'
        );
        // 执行接口的插入方法，将自动分配写入嵌套集合的左右边界
        return $nested_sets->insert($this->data['parent_id'],$this->data,'bottom');
    }

    /**
     * 更新方法
     * @return bool
     */
    public function updatePermission(){
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
                $this->error='不能移动到子级分类'; // 控制器的getError()方法手动设定一个反馈
                return false;
            }
        }
        return $this->save(); // 无论是否更改过分类，都会保存一下数据
    }

    /**
     * 逻辑删除方法
     * @param $id
     * @return bool
     */
    public function removePermission($id){
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
}