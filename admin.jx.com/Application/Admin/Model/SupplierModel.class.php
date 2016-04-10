<?php
namespace Admin\Model;
use Think\Model;
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/9
 * Time: 13:07
 */
class SupplierModel extends Model{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('name','require','供应商名字不能为空！',self::EXISTS_VALIDATE,'',1),
        array('name','','供货商已经存在！',self::EXISTS_VALIDATE,'unique',1)
    );

    /**
     * @param array $conditions 搜索参数及status>-1的条件
     * @return array
     */
    public function showPage(array $conditions=array()){
        // 条件是status大于-1的记录
        $conditions+=array(
            'status'=>array('gt',-1),
        );
        $count = $this->where($conditions)->count(); // 得到数据的总记录数
        $page_size=C('PAGE_SIZE'); // 得到配置文件设置的每页显示记录条数
        $page=new \Think\Page($count,$page_size);
        $show_page=$page->show();
        //得到条件是status大于-1的记录，并分页查询
        $rows = $this->where($conditions)->page(I('get.p'),$page_size)->select();
        return array('rows'=>$rows,'show_page'=>$show_page);
    }
}