<?php

/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-12-4
 * @version     : $id$
 */
class News_Page_Abstract extends Q_Abstract_Page
{

    protected static $_sysConf = [];
    protected static $_ctlName = '';
    protected static $_actName = '';
    protected static $_userId  = 0;
    protected static $_user = [];


    /**
     * Admin_Page_Abstract constructor.
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @throws Q_Exception
     */
    public function __construct(Q_Request $input, Q_Response $output)
    {

        self::$_ctlName = $input->ctlName;
        self::$_actName = $input->actName;
        self::$_sysConf = Q_Config::get(array('Global', 'Admin_Global', 'Admin_Enum'));

        $input->conf = $output->conf = self::$_sysConf;

        //检测登录状态

        $isLogin        = $this->_checkIsLogin($input, $output);

        if (Q_Request::resolveType() !== Q_Request::AJAX) {
            $this->_loadClientScript();
            $output->layout('Default');
        } else {
            $output->layout('Ajax');
        }
    }

    /**
     * 加载客户端脚本
     */
    private function _loadClientScript()
    {
        $conf = (array)Q_Config::get('News_ClientScript');
        $data = empty($conf['*']) ? [] : $conf['*'];
        $key  = strtoupper(self::$_ctlName . "|" . self::$_actName);
        $keys = [];
        foreach ($conf as $_key => $_conf) {
            if ($_key != '*') {
                $keyArr = explode(',', $_key);
                if (in_array($key, $keyArr)) {
                    $keys[$_key] = $_key;
                }
            }
        }

        foreach ($keys as $_key) {
            $data = array_merge_recursive($data, $conf[$_key]);
        }

        foreach ($data as $fileType => $row) {
            foreach ($row as $pos => $file) {
                Q_ClientScript::addFile($file, $fileType, $pos);
            }
        }

        return true;
    }

    public static function showMsg($param = array(), $status = self::STATUS_DANGER, $timeout = 2, $callbackUrl = '')
    {
        $params = array(
            'msg'         => '',
            'status'      => self::STATUS_DANGER,
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

        $length = strpos($currUrl, '&jumpUrl=');


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
            $output->jsonReturn('', $params['status'] == self::STATUS_SUCCESS
                ? Q_Response::STATUS_SUCCESS : Q_Response::STATUS_ERROR, $params['msg']);
        }

        $html = $output->fetchCol('Plugin/ShowMsg', $params, true);
        echo $output->fetchCol('Layout/IFrame', array('_content' => $html));
        Q::end();
    }

    /**
     * 检验用户是否登录
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return bool
     * @throws Q_Exception
     */
    protected function _checkIsLogin(Q_Request $input, Q_Response $output)
    {


        $isLogin = false;
        $userId = self::_getAdminId($input, $output);
        self::$_userId = $userId;
        if ($userId) {
            $_user = Q_DAL_Client::instance()->call('Login', 'GetRow', ['where' => ['id' => $userId ]]);

            if ($_user) {
                self::$_user = $_user;
                $isLogin      = true;
            }
        }
        $output->userId= self::$_userId;
        $output->user = self::$_user;

        return $isLogin;
    }

    protected static function _getAdminId(Q_Request $input, Q_Response $output)
    {
        if (self::$_userId) {
            return self::$_userId;
        }

        session_id() || session_start();

        $sessionId = session_id();
        Q_Cookie::setKey($sessionId);
        $userId        = Q_Cookie::get('userId');
        $output->userId = $userId;

        return $userId;
    }
}
