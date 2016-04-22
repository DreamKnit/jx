<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/16
 * Time: 9:59
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends Controller{
    protected $_model=null;

    protected function _initialize() {
        $meta_titles = array(
            'Index'  => '角色管理',
            'add'    => '添加角色',
            'edit'   => '修改角色',
            'remove' => '删除角色',
        );
        $meta_title  = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Role');
    }

    public function index(){
        $search=I('get.search');
        $conditions=array();
        if($search){
            $conditions['name']=array('like',"%$search%");
        }
        $rows=$this->_model->showPage($conditions);
        $this->assign($rows);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->_model->create()){
                if($this->_model->addRole()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success($this->_model->_success,U('Index'),3);
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $this->showTree();
            $this->display();
        }
    }

    public function edit(){
        $id=I('get.id');
        if(IS_POST){
            if($this->_model->create()){
                if($this->_model->updateRole()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success($this->_model->_success,U('Index'),3);
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row=$this->_model->getRoleInfo($id);
            $this->showTree();
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    public function remove($id){
        if($this->_model->removeRole($id)===false){
            $this->error($this->_model->getError());
        }
        $this->success('删除成功',U('Index'));
    }

    /**
     * 显示树状结构的方法
     */
    private function showTree(){
        // 角色关联需要权限表，得到权限表中id,name,parent_id字段
        $categories = D('Permission')->getList('id,name,parent_id');
        $this->assign('categories', json_encode($categories)); // 转换json并回显
    }
}