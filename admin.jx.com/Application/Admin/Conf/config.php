<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING'=>array(
        '__IMG__'=>'http://admin.jx.com/Public/Images/',
        '__CSS__'=>'http://admin.jx.com/Public/Css/',
        '__JS__'=>'http://admin.jx.com/Public/Js/',
        '__EXT__'=>'http://admin.jx.com/Public/ext/',
        '__UPLOADURL__'=>'http://7xsuff.com2.z0.glb.clouddn.com/',
    ),
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'jx',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8

    'PAGE_SIZE'             =>  '3',

    //上传文件配置
    'UPLOAD_CONFIG'=>array(
        'maxSize'      => 0, //上传的文件大小限制 (0-不做限制)
        'exts'         => array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
        'autoSub'      => true, //自动子目录保存文件
        'subName'      => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'     => './Uploads/', //保存根路径
        'savePath'     => '', //保存路径
        'saveName'     => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'      => '', //文件保存后缀，空则使用原后缀
        'replace'      => false, //存在同名是否覆盖
        'hash'         => true, //是否生成hash编码
        'callback'     => false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver'       => 'Qiniu', // 文件上传驱动
        'driverConfig' => array(
            'secrectKey' => '2vL3czhNluZcsksu4bNyCHWY4RtcVGDuqQO-U_rS', //七牛sk
            'accessKey'  => 'i7Pr-9KuuD1X_B1N2XYGLgogqE6eLbkHDxHNY6ly', //七牛ak
            'domain'     => '7xsuff.com1.z0.glb.clouddn.com', //空间域名
            'bucket'     => 'jx-brand', //空间名称
            'timeout'    => 30, //超时时间
        ), // 上传驱动配置
    ),

    // 验证码配置
    'CAPTCHA'=>array(
        'length'=>4,
    ),

    // 管理员权限的忽略路径
    'IGNORE_PATHS'=>[
        'Admin/Login/Index',
        'Admin/Login/captcha',
        'Admin/Index/Index',
        'Admin/Index/top',
        'Admin/Index/menu',
        'Admin/Index/main',
    ],
);