<?php
/**
 * @author       hanqiang <123456@qq.com>
 * @copyright(c) 2016-07-14 16:03:51
 */
$domain         = 'easypay.com';
$adminDomain    = 'admin.' . $domain;
$mchDomain      = 'mch.' . $domain;
$agentDomain    = 'agent.' . $domain;
$frontDomain    = 'www.' . $domain;
$apiDomain      = 'api.' . $domain;
$notifyDomain   = 'notify.' . $domain;
$imgDomain      = 'img.' . $domain;
$downloadDomain = 'download.' . $domain;
$stDomain       = 'st.lp-cdn.dalingpao.com';

return array(
    'DOMAIN'          => $domain,//根域名stream
    'ADMIN_DOMAIN'    => $adminDomain,
    'MCH_DOMAIN'      => $mchDomain,
    'AGENT_DOMAIN'    => $agentDomain,
    'FRONT_DOMAIN'    => $frontDomain,
    'API_DOMAIN'      => $apiDomain,
    'NOTIFY_DOMAIN'   => $notifyDomain,
    'ST_DOMAIN'       => $stDomain,
    'IMG_DOMAIN'      => $imgDomain,
    'DOWNLOAD_DOMAIN' => $downloadDomain,

    'ADMIN_SERVER'     => Q_Http::getProtocol() . '://' . $adminDomain . '/',
    'MCH_SERVER'       => Q_Http::getProtocol() . '://' . $mchDomain . '/',
    'AGENT_SERVER'     => Q_Http::getProtocol() . '://' . $agentDomain . '/',
    'FRONT_SERVER'     => Q_Http::getProtocol() . '://' . $frontDomain . '/',
    'API_SERVER'       => Q_Http::getProtocol() . '://' . $apiDomain . '/',
    'NOTIFY_SERVER'    => Q_Http::getProtocol() . '://' . $notifyDomain . '/',
    'ST_SERVER'        => Q_Http::getProtocol() . '://' . $stDomain . '/',
    'ST_GLOBAL_SERVER' => Q_Http::getProtocol() . '://' . $stDomain . '/global/',
    'EASY_PAY_SERVER'  => Q_Http::getProtocol() . '://' . $stDomain . '/easypay/',
    'IMG_SERVER'       => Q_Http::getProtocol() . '://' . $imgDomain . '/',
    'DOWNLOAD_SERVER'  => Q_Http::getProtocol() . '://' . $downloadDomain . '/',

    'CAPTCHA_FONT_PATH' => VAR_PATH . 'fonts/fonts-japanese-gothic.ttf',
    'REWRITE'           => true,

    'ST_DIR'           => dirname(PRODUCTION_ROOT) . '/{__ST_DIR_NAME__}/',
    'ST_REVISION_FILE' => VAR_PATH . 'st_ver.json',#ST版本文件路径,配置了就自动给静态文件加版本号,不配置不进行处理

    'APP_NAME'              => '易支付',
    'IMG_DIR'               => dirname(PRODUCTION_ROOT) . '/img/',
    'SSO_KEY'               => 'scfq*se@127^23)93&^%_{23;d08sm',
    'LOGIN_USER_CENTER_KEY' => 'sc*()23;fq*se@127^23)93&*(Q3*5623LH&^%_{23;d08smSDF',
    'TOKEN_EXPIRE_TIME'     => 30,
    'ORDER_PREFIX_SIGN'     => 'LP01',
);
