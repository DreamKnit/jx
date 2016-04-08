<?php

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define('APP_DEBUG',True);
define('BIND_MODULE','Admin');
// 目录
define('APP_PATH','./Application/');
// 引入ThinkPHP
require './ThinkPHP/ThinkPHP.php';
