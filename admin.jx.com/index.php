<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define('APP_DEBUG',True);
define('BIND_MODULE','Admin');
// 目录
define('ROOT_PATH',__DIR__.'/'); // 等于E:\tp\project\admin.jx.com/
define('APP_PATH',ROOT_PATH.'Application/');
// 引入ThinkPHP
require ROOT_PATH.'../ThinkPHP/ThinkPHP.php';
