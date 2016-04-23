<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING'=>array(
        '__IMG__'=>'http://www.jx.com/Public/Images/',
        '__CSS__'=>'http://www.jx.com/Public/Css/',
        '__JS__'=>'http://www.jx.com/Public/Js/',
        '__EXT__'=>'http://www.jx.com/Public/ext/',
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
    // 验证码配置
    'CAPTCHA'=>array(
        'length'=>4,
    ),
    // 阿里大鱼配置
    'ALIDAYU_SETTING'=>[
        'ak'=>'23350841',
        'sk'=>'60eb8a32f6cca884ea4969b89726b579',
    ],

    // 验证邮箱配置
    'EMAIL_SETTING' => [
        'host'       => 'smtp.126.com',
        'username'   => 'dreamknitjohn@126.com', // 本邮箱地址
        'password'   => 'ZyH123456', // 授权码
        'smtpsecure' => 'ssl',
        'port'       => 465,
    ],
    //Redis Session配置
    'SESSION_AUTO_START' => true, // 是否自动开启Session
    'SESSION_TYPE'       => 'Redis', //session类型
    'SESSION_PERSISTENT' => 1, //是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME' => 1, //连接超时时间(秒)
    'SESSION_EXPIRE'     => 0, //session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX'     => 'sess_', //session前缀
    'SESSION_REDIS_HOST' => '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT' => '6379', //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH' => '', //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔
    //页面静态缓存
    'HTML_CACHE_ON'    => true, // 开启静态缓存
    'HTML_CACHE_TIME'  => 60, // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.shtml', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array( // 定义静态缓存规则
        'goods'=>['goods_info_{id}',60],//所有的控制器的goods方法都缓存成'goods_info_' . $_GET['id'] . '.shtml 缓存60秒
        'Index:goods'=>['goods/goods_{id}',60],//缓存Index控制器的goods操作,生成的文件放在goods目录下
        'Index:index'=>['{:action}',3600],//缓存Index控制器的index操作,文件名是index.shtml,缓存1小时
    ),
    // 启用redis
    'DATA_CACHE_TYPE' => 'Redis', //数据缓存机制
    'REDIS_HOST'      => '127.0.0.1', //redis服务器的地址
    'REDIS_PORT'      => 6379, //redis服务器的端口
);