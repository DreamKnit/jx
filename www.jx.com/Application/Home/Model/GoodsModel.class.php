<?php

/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/22
 * Time: 15:09
 */

namespace Home\Model;

/**
 * Description of GoodsModel
 *
 * @author qingf
 */
class GoodsModel extends \Think\Model{
    
    /**
     * 得到推荐商品的数据
     * @param integer $goods_status
     * @param integer $limit        取出记录数.
     * @return array
     */
    public function getGoodsListByGoodsStatus($goods_status,$limit=5){
        $condition = [
            'goods_status & ' . $goods_status,
            'status'=>1,
            'is_on_sale'=>1,
        ];
        return $this->field('id,name,logo,shop_price')->where($condition)->select();
    }
    
    /**
     * 商品的信息
     * @param integer $id 商品id.
     * @return array|bool
     */
    public function getGoodsInfo($id){
        // 匹配条件
        $condition = [
            'status'=>1,
            'is_on_sale'=>1,
            'id'=>$id,
        ];
        $row = $this->where($condition)->find();
        if(!$row){
            $this->error = '没有这样商品';
            return false;
        }
        // 得到商品的品牌，详细内容，相册
        $row['brand_name'] = M('Brand')->where(['id'=>$row['brand_id']])->getField('name');
        $row['content'] = M('GoodsIntro')->where(['goods_id'=>$id])->getField('content');
        $row['galleries'] = M('GoodsGallery')->where(['goods_id'=>$id])->getField('path',true);
        return $row;
    }

    /**
     * 获取商品点击数,并且保存到数据库中
     * @param integer $goods_id 商品id.
     * @return int
     */
    public function getGoodsClick($goods_id){
        $goods_click_model = M('GoodsClick');
        $count =$goods_click_model->getFieldByGoodsId($goods_id,'click_times');
        if(empty($count)){
            $goods_click_model->add(['goods_id'=>$goods_id,'click_times'=>1]);
            return 1;
        } else{
            $goods_click_model->where(['goods_id'=>$goods_id])->setInc('click_times', 1);
            return $count;
        }
    }

    /**
     * 从redis中获取商品的点击数,商品的点击数存放在goods_click的hash中
     * @param integer $goods_id 商品id
     */
    public function getGoodsClickFromRedis($goods_id){
        $key = 'goods_click';
        $redis = get_redis();
        return $redis->hIncrBy($key,$goods_id,1);
    }
}
