<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/11
 * Time: 16:57
 */

namespace Admin\Controller;


use Think\Controller;

/**
 * Class UploadController
 * @package Admin\Controller
 */
class UploadController extends Controller{
    public function upload(){
        $config=C('UPLOAD_CONFIG'); // 获取自定义的配置信息
        $upload=new \Think\Upload($config);
        $upload_info=$upload->upload($_FILES); // 配置文件设置了七牛云就直接上传那里了
        if($config['driver']=='Qiniu'){
            $upload_url=$upload_info['Filedata']['savepath'].$upload_info['Filedata']['savename'];
            $upload_url=str_replace('/','_',$upload_url);
        }else{
            $upload_url=$upload_info['Filedata']['savepath'].$upload_info['Filedata']['savename'];
        }
        $data=array(
            'msg'=>$upload->getError(), // 上传失败报错信息
            'status'=>$upload_info?1:0, // 上传成功与否（便于前端判断）
            'file_url'=>$upload_url // 拼接的图片上传地址
        );
        $this->ajaxReturn($data); // 回显ajax数据
    }

}