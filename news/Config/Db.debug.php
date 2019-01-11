<?php
/**
 * 数据库链接
 *
 * @author       hanqiang <123456@qq.com>
 * @copyright(c) 2016-07-14 16:03:51
 */

$_host     = '192.168.0.27';
$_username = 'root';
$_password = '';

/* 数据库 */
$_dbTest    = 'test';

return array(
    'default'  => [
        'engine'   => 'mysql',
        'charset'  => 'utf8mb4',
        'username' => $_username,
        'password' => $_password,
        'master'   => array(
            'host'     => $_host,
            'database' => $_dbTest,
        ),
        'slave'    => array(
            'host'     => $_host,
            'database' => $_dbTest,
        ),
    ]
);
