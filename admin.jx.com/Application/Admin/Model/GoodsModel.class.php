<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/14
 * Time: 15:08
 */

namespace Admin\Model;


use Think\Model;

class GoodsModel extends Model{
    /**
     * 自动验证功能
     * @var array
     */
    protected $_validate = array(
        array('name', 'require', '商品名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('goods_category_id', 'require', '商品分类不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('stock', 'require', '商品库存不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('shop_price', 'require', '市场价不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('market_price', 'require', '售价不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
    );

    /**
     * 自动完成功能
     * @var array
     */
    protected $_auto = array(
        // 数组求和会自动让二进制数据按位或
        //array('goods_status', 'array_sum',self::MODEL_INSERT, 'function'),
        array('goods_status', 'array_sum',self::MODEL_BOTH, 'function'), // 插入时也要验证
        array('inputtime', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 获取符合条件的数据
     * @param string $field
     * @return mixed
     */
    public function getList($field = '*'){
        // 根据圈图排序应该用lft
        return M('goodsCategory')->field($field)->order('lft')->where(array('status' => 1))->select();
    }

    public function addGoods(){
        unset($this->data['id']);
        $this->_calcSn(); // 自动添加货号，返回新的 $this->data['sn']
        if(($goods_id = $this->add())===false){ // 保存商品的基本信息，成功将返回id
            return false;
        }

        /*----------内容和相册单独保存----------*/
        if ($this->_saveGoodsContent($goods_id)===false) { // 商品的描述内容保存
            $this->error = '商品简单描述保存失败！';
            return false;
        }

        if($this->_savePhoto($goods_id)===false){ // 相册信息的保存
            $this->error = '保存商品图片失败!';
            return false;
        }

        return true; // 都保存成功反馈true
    }

    /**
     * 货号自动编辑
     */
    private function _calcSn() {
        $sn=$this->data['sn']; // 接收前端货号
        if(empty($sn)){ // 判断是否有填写货号，没有执行自动填写功能
            $day=date('Ymd'); // 得到当天的年月日
            $goods_count_model = M('GoodsDayCount'); // 日期货号表
            if(!($count=$goods_count_model->getFieldByDay($day,'count'))){ // 若是当天第一件商品就执行插入功能
                $count=1; // 因为是当天的第一件所有数量就是1
                $data=array(
                    'day'=>$day,
                    'count'=>$count,
                );
                $goods_count_model->add($data); // 给日期货号表插入当天时间与数量1
            }else{ // 若不是当天第一件商品则执行更新功能，只修改count值
                $count++; // 合乎情理的在已有数量上新增一件
                // setInc也是加法运算更新，只有int型字段生效
                $goods_count_model->where(array('day'=>$day))->setInc('count',1);
            }
        }
        // 按自定的格式将货号拼接起来，不够的位数用0填充
        $this->data['sn']='SN'.$day.str_pad($count,5,'0',STR_PAD_LEFT);
        // 注意：日期值（$day）数量值（$count）在手动填写货号的时候是没有的
    }

    /**
     * 保存商品的描述
     * @param string $goods_id 传入的id值
     * @param bool $is_new 判断执行更新还是添加的开关值，默认是添加
     * @return bool|mixed
     */
    private function _saveGoodsContent($goods_id, $is_new=true){
        $content=I('post.content','',false); // 获得内容，默认为空，不过滤
        $data=array(
            'goods_id'=>$goods_id,
            'content'=>$content,
        );
        // 判断是更新还是插入
        if($is_new){
            return M('GoodsIntro')->add($data);
        }else{
            return M('GoodsIntro')->save($data);
        }
    }

    /**
     * 保存商品的图片
     * @param string $goods_id 传入的id
     * @return bool|string
     */
    private function _savePhoto($goods_id){
        $paths=I('post.path'); // 得到图片路径（可能是多张图片）
        if(!$paths){
            return true; // 没有上传图片也可以
        }
        $gallery_model = M('GoodsGallery'); // 商品画册表
        $data=array(); // 拼装插入数据用于保存所有的图片信息（数组）
        foreach ($paths as $path){
            $data[] = array(
                'goods_id'=>$goods_id,
                'path'=>$path,
            );
        }
        return $gallery_model->addAll($data); // 执行保存
    }

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
        // 将精品，新品，热销让前端正确显示
        foreach ($rows as $key => $value) {
            $rows[$key]['is_best'] = $value['goods_status'] & 1 ? 1 : 0;
            $rows[$key]['is_new']  = $value['goods_status'] & 2 ? 1 : 0;
            $rows[$key]['is_hot']  = $value['goods_status'] & 4 ? 1 : 0;
        }
        return array(
            'show_page'=>$show_page,
            'rows'=>$rows,
        );
    }

    /**
     * @param $goods_id
     * @return bool|mixed
     */
    public function getGoodsInfo($goods_id){
        // 根据id查询出相应的记录
        $row=$this->where(array('status'=>1))->find($goods_id);
        if (empty($row)) { // 判断是否有该商品
            $this->error = '该商品不存在！';
            return false;
        }
        // 由于状态是二进制将goods_status数据处理后返回给模板
        $tmp_status=$row['goods_status'];
        $row['goods_status']=array();
        if ($tmp_status & 1){
            $row['goods_status'][]=1;
        }
        if ($tmp_status & 2){
            $row['goods_status'][]=2;
        }
        if ($tmp_status & 4){
            $row['goods_status'][]=4;
        }
        $row['goods_status']=json_encode($row['goods_status']); // 将状态转换为json数据

        $content=M('GoodsIntro')->getFieldByGoodsId($goods_id, 'content'); // 得到详细描述
        $row['content']=$content?$content:''; // 给内容赋值，没有值默认为空

        // 得到相册表内的内容
        $paths = M('GoodsGallery')->where(array('goods_id'=>$goods_id))->getField('goods_id,goods_id,path',true);
        $row['paths']=$paths?$paths:array(); // 给路径赋值，没有值默认为空
        return $row;
    }

    /**
     * 修改商品
     * @return bool
     */
    public function updateGoods() {
        $request_data = $this->data; // 获取基本信息
        if($this->save()===false){ // 保存基本信息（注意：返回值是影响的行数）
            $this->error = '商品修改失败！';
            return false;
        }else{
            // 更新描述内容（传入false为更新操作）
            if($this->_saveGoodsContent($request_data['id'],false)===false){
                $this->error = '保存商品简单描述失败！';
                return false;
            }
        }
        // 保存相册信息
        if($this->_savePhoto($request_data['id'])===false){
            $this->error = '保存商品图片失败！';
            return false;
        }
        return true;
    }
}