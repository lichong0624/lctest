<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-12-4
 * @version     : $id$
 */

$stServer      = Q_Config::get('Global', 'ST_SERVER');
$globalServer  = Q_Config::get('Global', 'ST_GLOBAL_SERVER');
$easyPayServer = Q_Config::get('Global', 'EASY_PAY_SERVER');

return array(
    '*'             => [
        Q_ClientScript::FILE_TYPE_JS  => [
            Q_ClientScript::POS_HEADER => [
                "{$globalServer}layui/layui.js",
                "{$globalServer}jquery/jquery.min.js",
                "{$globalServer}jquery/plugins/jquery.tmpl.min.js",
                "{$globalServer}jquery/plugins/jquery.nestable.min.js",
                "{$globalServer}jquery/plugins/jquery.easypiechart.min.js",
                "{$globalServer}jquery/plugins/jquery.form.js",
                "{$globalServer}jquery/plugins/jquery.cookie.js",
                "{$globalServer}jquery/plugins/jquery.md5.js",
                "{$globalServer}jquery/plugins/jquery.json-2.2.min.js",
                "{$globalServer}jquery/plugins/monthPicker/jquery.monthpicker.js",
                "{$globalServer}jquery/plugins/jquery-migrate-1.2.1.min.js",
                "{$globalServer}image-upload/image-upload.js",
                "{$globalServer}echarts/3.8.4.min.js",
                "{$globalServer}q/q.js",
                "{$globalServer}q/plugins/validator.js",
                "{$globalServer}q/plugins/table.js",
                "{$globalServer}q/plugins/page.js",
                "{$globalServer}q/plugins/easyPieChart.js",
                "{$globalServer}q/plugins/resizeFont.js",
                "{$globalServer}q/plugins/laydate.js",
                "{$globalServer}validform/js/Validform_v5.3.2.js",
                "{$globalServer}validform/js/Validform_Datatype.js",
            ],
            Q_ClientScript::POS_FOOTER => [
                "{$globalServer}admin/js/global.js",
                "{$globalServer}admin/js/menu.js",
                "{$easyPayServer}js/global.js",
                "{$easyPayServer}js/common/common.js",
                "{$easyPayServer}js/page/admin.js",
                "{$easyPayServer}js/script2.js",
            ],
        ],
        Q_ClientScript::FILE_TYPE_CSS => [
            Q_ClientScript::POS_HEADER => [
                "{$globalServer}layui/css/layui.css",
                "{$globalServer}admin/css/reset.css",
                "{$globalServer}admin/images/bg.jpg",
                "{$globalServer}admin/css/style.css",
                "{$globalServer}admin/css/tabbar.css",
                "{$globalServer}admin/css/table.css",
                "{$globalServer}image-upload/image-upload.css",
                "{$globalServer}jquery/plugins/monthPicker/jquery.monthpicker.css",
                "{$easyPayServer}css/global.css",
                "{$easyPayServer}css/default.css",
                "{$globalServer}validform/css/style.css",
                "{$easyPayServer}css/index.css",
            ]
        ]
    ],
    'MENU|DEFAULT'  => [
        Q_ClientScript::FILE_TYPE_JS  => [
            Q_ClientScript::POS_HEADER => [
                "{$globalServer}jquery/plugins/jquery.nestable.min.js",
            ]
        ],
        Q_ClientScript::FILE_TYPE_CSS => [
            Q_ClientScript::POS_HEADER => [
                "{$globalServer}admin/css/menu.css",
            ]
        ]
    ],
    'LOGIN|DEFAULT' => [
        Q_ClientScript::FILE_TYPE_CSS => [
            Q_ClientScript::POS_HEADER => [
                "{$globalServer}admin/css/login.css",
                "{$easyPayServer}css/default_login.css",
                "{$easyPayServer}css/zw_pay_login.css",
                "{$easyPayServer}css/helpcenter/zw_style.css",
                "{$easyPayServer}css/login.css",
            ]
        ]
    ]
);
