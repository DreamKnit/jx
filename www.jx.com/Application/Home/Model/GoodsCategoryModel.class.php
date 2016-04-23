<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/22
 * Time: 15:09
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model{
    /**
     * 获取商品的分类列表
     * @return mixed
     */
    public function getList(){
        $cond = [
            'status'=>1,
        ];
        return $this->where($cond)->select();
    }
}