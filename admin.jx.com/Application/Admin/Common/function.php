<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/14
 * Time: 11:23
 */

    /**
     * 生成select标签
     * @param string $name 表单选项名
     * @param array $for_name 循环的数据
     * @param string $id 回显时的id值
     * @param string $default 默认第一个option选项
     * @return string html格式
     */
    function selectList($name,$for_name,$id='',$default='请选择...'){
        $html="<select name='$name'>";
        $html.="<option value=''>$default</option>";
        foreach($for_name as $key=>$val){
            // 如果查找到是回显的id就默认选中它
            if($val['id']==$id){
                $html.="<option selected='selected' value='{$val['id']}'>{$val['name']}</option>";
            }else{
                $html.="<option value='{$val['id']}'>{$val['name']}</option>";
            }
        }
        $html.="</select>";
        return $html;
    }