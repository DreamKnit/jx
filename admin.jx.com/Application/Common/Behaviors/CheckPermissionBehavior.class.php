<?php

namespace Common\Behaviors;
use Think\Behavior;

/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/19
 * Time: 15:47
 */
class CheckPermissionBehavior extends Behavior{
    public function run(&$params) {
        //// 拼凑模块/控制器/操作
        //$url    = implode('/', [MODULE_NAME, CONTROLLER_NAME, ACTION_NAME]);
        //$ignore = C('IGNORE_PATHS'); // 得到配置文件里的忽略路径
        //if(in_array($url, $ignore)){
        //    return true; // 如果所得路径是忽略路径就不用受限
        //}
        //$userinfo = session('USERINFO'); // 得到登录信息
        //
        ///*// 没有登录信息说明自动登录
        //if(empty($userinfo)){
        //    $admin_model = D('Admin');
        //    //自动登陆并保存用户的信息和权限信息
        //    $admin_model->autoLogin();
        //    //保存之后由于要判断是否是超级管理员,所以要再取一次用户信息
        //    $userinfo = session('USERINFO');
        //}*/
        //
        //
        //if($userinfo){ // 在有登录信息之后判断是不是超级管理员
        //    if($userinfo['username']=='admin'){
        //        return true; // 如果是超级管理员,就没有任何限制操作
        //    }
        //}
        //
        //$paths = session('PATHS'); // 得到管理员可以访问的路径（login模型层里获得）
        //
        //
        //if(!is_array($paths)){
        //    $paths = [];
        //}
        ////获取当前请求的路径
        //if (!in_array($url, $paths)) {
        //    $url = U('Admin/Login/index');
        //    redirect($url);
        //}
    }
}