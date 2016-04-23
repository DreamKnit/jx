<?php
/**
 * Created by PhpStorm.
 * User: z1133
 * Date: 2016/4/20
 * Time: 21:27
 */


/**
 * 加盐加密
 * @param string $password 原始密码
 * @param string $salt 盐（延）
 * @return string 新密码
 */
function salt_password($password,$salt){
    return md5(md5($password).$salt);
}

