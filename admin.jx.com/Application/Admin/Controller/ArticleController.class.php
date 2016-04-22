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
class ArticleController extends Controller{
    protected $_model=null;

    /**
     * 初始化方法
     */
    public function _initialize(){
        $meta_titles=array(
            'Index'=>'文章管理',
            'add'=>'添加文章',
            'edit'=>'修改文章',
            'remove'=>'删除文章',
        );

        $this->assign('meta_title',$meta_titles[ACTION_NAME]);
        $this->_model=D('article');
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
     * 添加文章
     */
    public function add(){
        if(IS_POST){
            $content=I('post.content'); //得到文章内容
            if($this->_model->create()){
                if($this->_model->add()===false){
                    $this->error($this->_model->getError());
                }else{
                    $id=$this->_model->order('id desc')->getField("id"); //得到刚才文章表插入ID
                    //另外添加相关数据到文章内容表
                    if(M('article_content')->add(array('article_id'=>$id,'content'=>$content))){
                        $this->success('添加成功！',U('Index'));
                    }else{
                        $this->error(M('article_content')->getError());
                    }
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $cate_gorys=$this->_model->articleCategory();
            $this->assign('cate_gorys',$cate_gorys);
            $this->display();
        }
    }

    /**
     * 文章修改
     */
    public function edit(){
        $id=I('get.id');
        if(IS_POST){
            $content=I('post.content'); // 得到文章内容
            $id=I('post.id'); // 得到修改数据的id
            if($this->_model->create()){
                if($this->_model->save()===false){
                    $this->error($this->_model->getError());
                }else{
                    // 另外修改相关数据到文章内容表
                    if($this->_model->articleContentEdit($id,$content)){
                        $this->success('数据内容修改成功！',U('Index'),3);
                    }
                }
            }else{
                $this->error($this->_model->getError());
            }
        }else{
            $content=$this->_model->articleContent($id); // 得到对应内容
            $cate_gorys=$this->_model->articleCategory(); // 得到分类下拉
            $row=$this->_model->find($id); // 得到对应数据
            // 把对应的分类id赋值true给option数组，便于前端先提取selected
            $cate_gorys=$this->_model->cateGorySelected($cate_gorys,$row);
            $data=array(
                'content'=>$content,
                'cate_gorys'=>$cate_gorys,
                'row'=>$row,
            );
            $this->assign($data);
            $this->display('add');
        }
    }

    /**
     * 文章逻辑删除
     */
    public function remove(){
        $id=I('get.id');
        $data=array(
            'status'=>-1, // status修改为-1
            'name'=>array('exp',"CONCAT(name,'_del')"), // 名字追加'_del'
        );
        if($this->_model->where(array('id'=>$id))->setField($data)){
            $this->success('删除成功！');
        }else{
            $this->error($this->_model->getError());
        }
    }
}