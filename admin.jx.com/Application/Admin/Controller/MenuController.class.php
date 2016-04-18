<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/17
 * Time: 18:29
 */

namespace Admin\Controller;


use Think\Controller;

class MenuController extends Controller{
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '菜单管理',
            'add'    => '添加菜单',
            'edit'   => '修改菜单',
            'delete' => '删除菜单',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Menu');
    }

    public function index(){
        $this->assign('rows', $this->_model->getList());
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->_model->create()){
                if($this->_model->addMenu()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success($this->_model->_success,U('index'),3);
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
                if($this->_model->updateMenu()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success($this->_model->_success,U('index'),3);
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row=$this->_model->getMenuInfo($id);
            $this->showTree();
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    public function remove($id){
        if($this->_model->removeMenu($id)===false){
            $this->error($this->_model->getError());
        }
        $this->success('删除成功',U('index'));
    }

    /**
     * 显示树状结构的方法
     */
    private function showTree(){
        // 权限表，得到权限表中id,name,parent_id字段
        $categories = D('Permission')->getList('id,name,parent_id');
        $this->assign('categories', json_encode($categories)); // 转换json并回显
        // 菜单表中的数据
        $menus = $this->_model->getList('id,name,parent_id');
        array_unshift($menus, ['id'=>0,'parent_id'=>0,'name'=>'顶级菜单']);
        $this->assign('menus', json_encode($menus));
    }
}