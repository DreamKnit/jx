<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING'=>array(
        '__IMG__'=>'http://www.jx.com/Public/Images/',
        '__CSS__'=>'http://www.jx.com/Public/Css/',
        '__JS__'=>'http://www.jx.com/Public/Js/',
        '__EXT__'=>'http://www.jx.com/Public/ext/',
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
);