<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/17
 * Time: 13:29
 */

namespace Admin\Controller;


use Think\Controller;

class RecycleController extends Controller{
    private $_model = null;

    /**
     * 初始化相关
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '商品回收站管理',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Recycle');
    }
    public function index(){
        $rows=$this->_model->showPage();
        $this->assign($rows);
        $this->display();
    }

    /**
     * 商品逻辑恢复
     */
    public function restore(){
        $id=I('get.id');
        if($this->_model->restoreGoods($id)){
            $this->success('商品已恢复！');
        }else{
            $this->error('商品恢复失败！');
        }
    }
}