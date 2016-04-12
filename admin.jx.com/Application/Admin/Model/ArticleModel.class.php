<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/10
 * Time: 9:26
 */

namespace Admin\Model;
use Think\Model;

class ArticleModel extends Model{
    /**
     * 自动验证内容
     * @var array
     */
    protected $_validate = array(
        array('name','require','文章名字不能为空！',self::EXISTS_VALIDATE,'',1),
    );

    /**
     * 显示主页内容及分页
     * @param array $conditions 搜索内容及拼接status值大于-1的记录
     * @return array 返回分页控件及数据表中数据
     */
    public function showPage(array $conditions=array()){
        $conditions+=array(
            'status'=>array('gt',-1),
        );
        $page_size=C('PAGE_SIZE');
        $count=$this->where($conditions)->count();
        $page=new \Think\Page($count,$page_size);
        $show_page=$page->show();
        $rows=$this->where($conditions)->page(I('get.p'),$page_size)->select();
        // 追加一个分类名的键值（分类名字是另一张表里面的）
        $cate_gory=M('article_category');
        foreach($rows as $key=>$row){
            $cate_gory_data=$cate_gory->getById($row['article_category_id']);
            $rows[$key]['article_category_name']=$cate_gory_data['name'];
        }
        return array(
            'show_page'=>$show_page,
            'rows'=>$rows,
        );
    }

    /**
     * 得到分类表中的数据
     * @return mixed 返回分类表中的内容
     */
    public function articleCategory(){
        return M('article_category')->select();
    }

    /**
     * 得到对应数据的文章内容
     * @param STRING $id 对应数据的ID值
     * @return mixed 返回对应数据的内容
     */
    public function articleContent($id){
        return M('article_content')->find($id);
    }

    /**
     * 修改对应数据的内容信息
     * @param STRING $id 对应数据的ID值
     * @param STRING $content 输入框的内容
     * @return bool 返回成功TRUE
     */
    public function articleContentEdit($id,$content){
        $content_model=M('article_content');
        if($content_model->find($id)){ // 内容有则修改，无则添加
            $content_model->save(array('article_id'=>$id,'content'=>$content));
        }else{
            $content_model->add(array('article_id'=>$id,'content'=>$content));
        }
        return true;
    }

    /**
     * 点击编辑时文章分类的默认选择
     * @param array $cate_gorys 文章分类数据
     * @param array $row 对应数据
     * @return array 新的文章分类数据
     */
    public function cateGorySelected($cate_gorys,$row){
        foreach($cate_gorys as $key=>$cate_gory){
            // 找到对应的默认分类值
            if($cate_gory['id']==$row['article_category_id']){
                $cate_gorys[$key]['selected']=true;
            }else{
                $cate_gorys[$key]['selected']=false;
            }
        }
        return $cate_gorys; //返回新拼装好的文章分类数据
    }
}