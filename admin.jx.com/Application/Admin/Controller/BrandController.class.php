<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/10
 * Time: 9:09
 */

namespace Admin\Controller;
use Think\Controller;

/**
 * Class BrandController
 * @package Admin\Controller
 */
class BrandController extends Controller{
    protected $_model=null;

    /**
     * 初始化方法
     */
    public function _initialize(){
        $meta_titles=array(
            'index'=>'品牌管理',
            'add'=>'品牌添加',
            'edit'=>'品牌修改',
            'remove'=>'品牌删除',
        );

        $this->assign('meta_title',$meta_titles[ACTION_NAME]);
        $this->_model=D('brand');
    }

    /**
     * 主页显示方法
     */
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

    /**
     * 添加品牌
     */
    public function add(){
        if(IS_POST){
            if($this->_model->create()){
                if($this->_model->add()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('添加成功！',U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $this->display();
        }
    }

    /**
     * 品牌修改
     */
    public function edit(){
        $id=I('get.id');
        if(IS_POST){
            if($this->_model->create()){
                if($this->_model->save()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('修改成功！',U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row=$this->_model->find($id);
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    /**
     * 品牌删除
     */
    public function remove(){
        $id=I('get.id');
        $data=array(
            'status'=>-1,
            'name'=>array('exp',"CONCAT(name,'_del')"),
        );
        if($this->_model->where(array('id'=>$id))->setField($data)){
            $this->success('删除成功！');
        }else{
            $this->error($this->_model->getError());
        }
    }
}