<?php
/**
 * 应用配置
 *
 * @author       hanqiang <123456@qq.com>
 * @copyright(c) 2016-07-14 16:03:51
 */
return array(
    'SKIN_NAME'          => 'Default',#模板风格
    'COOKIE_KEY'         => 'also20324(82&%2)032lm+A',
    'APP_DOMAIN'         => Q_Config::get('Global', 'ADMIN_DOMAIN'),
    'APP_NAME'           => Q_Config::get('Global', 'APP_NAME') . '管理平台',
    'IGNORE_CTL'         => array(
        'Login'   => '*',
        'Captcha' => '*',
        'Test_Test'    => '*',
    ),
    'OPT_LOG_IGNORE_CTL' => array(
        '*' => [
            'Default' => true,
            'View'    => true,
        ],
    ),
    'LIST_PAGE_SIZE'     => 15,
    'IMG_WIDTH'          => 200,
    'IMG_HEIGHT'         => 200,
    'LIST_TOP_NUM'       => 10
);