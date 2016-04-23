<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/22
 * Time: 15:10
 */

namespace Home\Model;


use Think\Model;

class ArticleModel extends Model{
    /**
     * 得到帮助文章的首页显示数据，并按前端方便排版的数据返回
     * @return mixed
     */
    public function getHelpArticleList(){
        $help_categories = M('ArticleCategory')->where(['is_help'=>1,'status'=>1])->limit(5)->getField('id,name');
        foreach ($help_categories as $key=>$value){
            $value = [
                'name'=>$value,
                'list'=>$this->field('id,name')->where(['status'=>1,'article_category_id'=>$key])->limit(6)->select(),
            ];
            $help_categories[$key] = $value;
        }
        return $help_categories;
    }
}