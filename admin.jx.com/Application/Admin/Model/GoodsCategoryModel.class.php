<?php


namespace Admin\Model;

class GoodsCategoryModel extends \Think\Model {


    /**
     * 获取符合条件的数据
     * @param string $field
     * @return mixed
     */
    public function getList($field = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }

    /**
     * 添加分类数据
     * @return mixed
     */
    public function addCategory() {
        return $this->add();
    }

}
