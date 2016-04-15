<?php


namespace Admin\Controller;

/**
 * Class GoodsGalleryController
 * @package Admin\Controller
 */
class GoodsGalleryController extends \Think\Controller{

    /**
     * 删除一张图片（利用ajax）
     * @param string $path 由于相册表没有主键，只有用path作为条件
     */
    public function delete($path){
        $gallery_model = M('GoodsGallery'); // 相册表
        if($gallery_model->where(array('path'=>$path))->delete() === false){ // 根据传过来的id执行删除
            $this->error($gallery_model->getError());
        }else{
            $this->success('删除成功');
        }
    }
}
