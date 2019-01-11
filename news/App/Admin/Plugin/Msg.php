<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-11-22
 * @version     : $id$
 */
class Admin_Plugin_Msg extends Admin_Plugin_Abstract
{
    /**
     * 显示提示信息
     *
     * @param array|string $param ['msg' => array|string, 'status' => Q_Abstract_Page::STATUS_*, 'timeout' => 5, 'callbackUrl' =>
     *                            ''] \ msg
     * @param string       $status
     * @param int          $timeout
     * @param string       $callbackUrl
     * @throws \Q_Exception
     */
    public static function showMsg($param = array(), $status = Q_Abstract_Page::STATUS_DANGER, $timeout = 2, $callbackUrl = '')
    {
        $params = array(
            'msg'         => '',
            'status'      => Q_Abstract_Page::STATUS_DANGER,
            'timeout'     => 2,
            'callbackUrl' => Q_Http::getReferer(),
        );

        if (is_string($param)) {
            $param = array(
                'msg'     => $param,
                'status'  => $status,
                'timeout' => $timeout,
            );
            if (!empty($callbackUrl)) {
                $param['callbackUrl'] = $callbackUrl;
            }
        }

        $params = array_merge($params, $param);

        $currUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $length  = strpos($currUrl, '&jumpUrl=');
        if (!empty($length)) {
            $_jumpUrlBase64        = substr($currUrl, ($length + 9));
            $params['callbackUrl'] = base64_decode($_jumpUrlBase64);
        }

        if (Q_Request::resolveType() == Q_Request::CLI) {
            echo join(PHP_EOL, (array)$params['msg']) . PHP_EOL;
            Q::end();
        }
        /**
         * @var $output Q_Response
         */
        $output = Q_Registry::get('Q_Response');

        if (Q_Request::resolveType() == Q_Request::AJAX) {
            $output->jsonReturn('', $params['status'] == Q_Response::STATUS_SUCCESS
                ? Q_Response::STATUS_SUCCESS : Q_Response::STATUS_ERROR, $params['msg']);
        }

        $html = $output->fetchCol('Plugin/ShowMsg', $params, true);
        echo $output->fetchCol('Layout/Default', array('_content' => $html));
        Q::end();
    }
}