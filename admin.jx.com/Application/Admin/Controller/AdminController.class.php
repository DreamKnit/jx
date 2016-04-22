<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/17
 * Time: 12:04
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends Controller{
    private $_model = null;

    /**
     * 初始化相关
     */
    protected function _initialize() {
        $meta_titles  = array(
            'Index'  => '管理员管理',
            'add'    => '添加管理员',
            'edit'   => '管理员编辑',
            'remove' => '管理员删除',
            'tips' => '重置密码',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Admin');
    }
    public function index(){
        $this->assign('rows',$this->_model->getList());
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->_model->create()){
                if($this->_model->addAdmin()===false){
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
                if($this->_model->updateAdmin()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success($this->_model->_success,U('Index'),3);
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row=$this->_model->getAdminInfo($id);
            $this->showTree();
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    public function remove($id){
        if($this->_model->removeAdmin($id)===false){
            $this->error($this->_model->getError());
        }
        $this->success('删除成功',U('Index'));
    }

    /**
     * 显示树状结构的方法
     */
    private function showTree(){
        // 权限表，得到权限表中id,name,parent_id字段
        $categories = D('Permission')->getList('id,name,parent_id');
        $this->assign('categories', json_encode($categories)); // 转换json并回显
        // 角色表中的数据
        $roles = D('Role')->getList('id,name');
        $this->assign('roles', $roles);
    }

    public function resetPwd(){
        $id=I('get.id');
        if(IS_POST){
            if($this->_model->create('','reset_pwd')){ // 自定义的自动完成
                if(($password=$this->_model->resetPwd())===false){
                    $this->error($this->_model->getError());
                }else{
                    // 密码重置成功跳转到提示页，传入新密码
                    $this->success('重置密码成功！',U('tips',['password'=>$password]),3);
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row = $this->_model->field('id,username,email')->find($id);
            $this->assign('row',$row);
            $this->display();
        }
    }

    public function tips(){
        $password=I('get.password');
        $this->assign('password',$password);
        $this->display();
    }
}