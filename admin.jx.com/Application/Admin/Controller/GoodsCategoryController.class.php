<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/11
 * Time: 21:02
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsCategoryController extends Controller{
    private $_model = null;

    /**
     * 初始化相关
     */
    protected function _initialize() {
        $meta_titles  = array(
            'Index'  => '商品分类管理',
            'add'    => '添加商品分类',
            'edit'   => '修改商品分类',
            'delete' => '删除商品分类',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('GoodsCategory');
    }

    /**
     * 显示主页的方法
     */
    public function index(){
        $rows = $this->_model->getList();
        $this->assign('rows', $rows);
        $this->display();
    }

    /**
     * 添加数据的方法
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create()) {
                if($this->_model->addCategory()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('添加成功',U('Index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $this->showTree(); // 调用树状
            $this->display();
        }
    }

    /**
     * 修改当前数据
     * @param $id
     */
    public function edit($id) {
        if(IS_POST){
            if ($this->_model->create()) {
                if($this->_model->updateCategory()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('修改成功',U('Index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row = $this->_model->find($id); // 查找当前数据
            $this->assign('row', $row); // 回显相关数据
            $this->showTree(); // 调用树状
            $this->display('add');
        }
    }

    /**
     * 逻辑删除分类的方法
     */
    public function remove(){
        $id=I('get.id');
        if($this->_model->removeCategory($id)){
            $this->success('删除成功',U('Index'));
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
