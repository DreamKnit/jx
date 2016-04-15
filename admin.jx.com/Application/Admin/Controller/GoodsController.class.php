<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/14
 * Time: 11:20
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller{
    private $_model = null;

    /**
     * 初始化相关
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '商品管理',
            'add'    => '添加商品',
            'edit'   => '修改商品',
            'delete' => '删除商品',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Goods');
    }

    public function index(){
        $brands=M('brand')->where(array('status'=>1))->select(); //品牌数据
        $goods_category=M('goodsCategory')->where(array('status'=>1))->select(); //商品分类数据
        $conditions=I('get.'); // 接收搜索条件

        /*-----数据处理-----*/
        $is_on_sale=$conditions['is_on_sale']; // 下架参数是0，防止会被过滤掉
        $conditions=array_filter($conditions); // 过滤空参数
        if($is_on_sale==='0'){
            $conditions['is_on_sale']='0'; // 下架参数重新获得值
        }
        $goods_status=$conditions['goods_status']; // 得到推荐类型（二进制）
        if($goods_status){ // 有这个字段就执行条件重写
            unset($conditions['goods_status']); // 不能有多余条件
            $conditions[]="goods_status&'$goods_status'"; // 条件算法（二进制）
        }
        /*-----数据处理-----*/

        $rows = $this->_model->showPage($conditions); // 执行主页显示方法

        /*-----数据处理-----*/
        if($goods_status){
            $conditions['goods_status']=$goods_status; // 执行完条件后还原数据
        }
        /*-----数据处理-----*/

        $data=array(
            'brands'=>$brands,
            'goods_category'=>$goods_category,
        );
        $this->assign($data);
        $this->assign('conditions',$conditions); // 搜索功能结束后的回显
        $this->assign($rows);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if ($this->_model->create()) {
                if($this->_model->addGoods()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('添加成功',U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $brands=M('brand')->select(); //品牌数据
            $suppliers=M('supplier')->select(); //供货商数据
            $data=array(
                'brands'=>$brands,
                'suppliers'=>$suppliers,
            );
            $this->_showTree();
            $this->assign($data);
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
                if($this->_model->updateGoods()===false){
                    $this->error($this->_model->getError());
                }else{
                    $this->success('修改成功',U('index'));
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            // 传入id值查询相应的数据
            if(!$row = $this->_model->getGoodsInfo($id)){
                $this->error('商品id不正确！',U('index'));
            }
            $brands=M('brand')->select(); //品牌数据
            $suppliers=M('supplier')->select(); //供货商数据
            $data=array(
                'brands'=>$brands,
                'suppliers'=>$suppliers,
                'row'=>$row
            );
            $this->assign($data); // 回显相关数据
            $this->_showTree(); // 调用树状
            $this->display('add');
        }
    }

    /**
     * 显示树状结构的方法
     */
    private function _showTree(){
        // 得到id,name,parent_id字段
        $categories = $this->_model->getList('id,name,parent_id');
        // 将id=>0，name=>'顶级分类'，parent_id=>0添加到查询出数据的开头
        array_unshift($categories,array('id'=>0,'name'=>'顶级分类','parent_id'=>0));
        $this->assign('categories', json_encode($categories)); // 转换json并回显
    }
}