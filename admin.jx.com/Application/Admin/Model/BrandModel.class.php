<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/10
 * Time: 9:26
 */

namespace Admin\Model;
use Think\Model;

class BrandModel extends Model{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('name','require','品牌名字不能为空！',self::EXISTS_VALIDATE,'',1),
        array('name','','品牌已经存在！',self::EXISTS_VALIDATE,'unique',1)
    );

    /**
     * 显示分页及相关数据
     * @param array $conditions
     * @return array
     */
    public function showPage(array $conditions=array()){
        $conditions+=array(
            'status'=>1,
        );
        $page_size=C('PAGE_SIZE');
        $count=$this->where($conditions)->count();
        $page=new \Think\Page($count,$page_size);
        $show_page=$page->show();
        $rows=$this->where($conditions)->page(I('get.p'),$page_size)->select();
        return array(
            'show_page'=>$show_page,
            'rows'=>$rows,
        );
    }
}