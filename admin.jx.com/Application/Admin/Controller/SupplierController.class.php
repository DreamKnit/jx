<?php
namespace Admin\Controller;
use Think\Controller;
class SupplierController extends Controller {
    protected $_model=null;

    protected function _initialize() {
        $meta_titles = array(
            'index'  => '供货商管理',
            'add'    => '添加供货商',
            'edit'   => '修改供货商',
            'remove' => '删除供货商',
        );
        $meta_title  = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Supplier');
    }

    public function index(){
        $conditions = array(); //默认让搜索为空数组
        $search = I('get.search'); //得到前端的搜索值
        if($search){ //是否有搜索值
            $conditions['name'] = array('like','%'.$search.'%'); //搜索条件
        }
        $this->assign($this->_model->showPage($conditions)); //得到相应的数据与分页（传入搜索条件）
        $this->display();
    }

    /**
     * 添加供货商
     */
    public function add(){
        if(IS_POST){ // 是否有POST传值
            if($this->_model->create()){
                if($this->_model->add() === false) {
                    $this->error($this->_model->getError());
                }else{
                    $this->success('添加成功', U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $this->display();
        }
    }

    /**
     * 修改供货商
     */
    public function edit(){
        $id=I('get.id'); // 得到对应ID值
        if(IS_POST){ // 是否有POST传值
            if($this->_model->create()){
                if ($this->_model->save() === false) {
                    $this->error($this->_model->getError());
                } else {
                    $this->success('修改成功', U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $row = $this->_model->find($id); // 显示该数据
            $this->assign('row', $row);
            $this->display('add');
        }
    }

    /**
     * 逻辑删除
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