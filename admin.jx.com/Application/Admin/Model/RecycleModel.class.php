<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/17
 * Time: 13:30
 */

namespace Admin\Model;


use Think\Model;

class RecycleModel extends Model{
    // 虚拟模型，没有实际的表，避开tp基础模型的数据库操作
    Protected $autoCheckFields = false;
    /**
     * 显示分页及相关数据
     * @param array $conditions
     * @return array
     */
    public function showPage(array $conditions=array()){
        $conditions+=array(
            'status'=>0, // 在回收站的商品都是状态为0的
        );
        $page_size=C('PAGE_SIZE');
        $count=$this->table('Goods')->where($conditions)->count();
        $page=new \Think\Page($count,$page_size);
        $show_page=$page->show();
        $rows=$this->table('Goods')->where($conditions)->page(I('get.p'),$page_size)->select();
        return array(
            'show_page'=>$show_page,
            'rows'=>$rows,
        );
    }

    /**
     * 恢复商品，逻辑恢复
     * @param $id
     * @return bool
     */
    public function restoreGoods($id){
        return $this->table('Goods')->where(['id'=>$id,'status'=>0])->setField(['status'=>1]);
    }
}