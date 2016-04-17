<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/16
 * Time: 8:58
 */

namespace Admin\Controller;


use Think\Controller;

class PermissionController extends Controller{
    protected $_model=null;

    protected function _initialize() {
        $meta_titles = array(
            'index'  => '权限管理',
            'add'    => '添加权限',
            'edit'   => '修改权限',
            'remove' => '删除权限',
        );
        $meta_title  = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Permission');
    }

    /**
     * 显示权限的主页
     */
    public function index(){
        $rows=$this->_model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }

    /**
     * 添加权限
     */
    public function add(){
        if(IS_POST){
            if ($this->_model->create()) {
                if($this->_model->addPermission()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('添加成功',U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $this->showTree();
            $this->display();
        }
    }

    /**
     * 修改当前权限数据
     * @param $id
     */
    public function edit($id) {
        if(IS_POST){
            if ($this->_model->create()) {
                if($this->_model->updatePermission()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('修改成功',U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row = $this->_model->where(array('status'=>1))->find($id); // 查找当前数据
            $this->assign('row', $row); // 回显相关数据
            $this->showTree(); // 调用树状
            $this->display('add');
        }
    }

    /**
     * 逻辑删除权限的方法
     */
    public function remove(){
        $id=I('get.id');
        if($this->_model->removePermission($id)){
            $this->success('删除成功',U('index'));
        }else{
            $this->error($this->_model->getError());
        }

    }

    /**
     * 显示树状结构的方法
     */
    private function showTree(){
        // 得到id,name,parent_id字段
        $categories = $this->_model->getList('id,name,parent_id');
        // 将id=>0，name=>'顶级分类'，parent_id=>0添加到查询出数据的开头
        array_unshift($categories,array('id'=>0,'name'=>'顶级分类','parent_id'=>0));
        $this->assign('categories', json_encode($categories)); // 转换json并回显
    }
}