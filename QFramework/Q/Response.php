<?php

/*
|---------------------------------------------------------------
| Response for output data
|---------------------------------------------------------------
| @package Q
|
*/

class Q_Response
{

    /*
    |---------------------------------------------------------------
    | Response data
    |---------------------------------------------------------------
    | @var array
    |
    */
    protected $_aProps;

    /*
    |---------------------------------------------------------------
    | HTTP status code
    |---------------------------------------------------------------
    | @var integer
    |
    */
    protected $_code;

    /*
    |---------------------------------------------------------------
    | Stores output string to be returned to user
    |---------------------------------------------------------------
    | @var string
    |
    */
    protected $_data;

    /*
    |---------------------------------------------------------------
    | List of messages to be returned to user
    |---------------------------------------------------------------
    | @var array
    |
    */
    protected $_aMessages;
    protected $_appName = APP_NAME;

    protected $_ctlName;
    protected $_actName;
    protected $_verName;
    protected $_curVerName;

    protected $_skinRoot = APP_SKIN_ROOT;

    protected $pageCharset = 'utf-8';

    /*
    |---------------------------------------------------------------
    | HTTP headers
    |---------------------------------------------------------------
    | @var array
    |
    */
    protected $aHeaders = array();

    protected $contentType = 'html';

    protected $_layout = array();

    protected $_template;
    protected $_colTemplate;


    /**
     * 设置数据返回后是否马上停止
     *
     * @var bool
     */
    protected $_returnEnd = true;

    /**
     * @return boolean
     */
    public function isReturnEnd()
    {
        return $this->_returnEnd;
    }

    /**
     * @param bool $returnEnd
     * @return $this
     */
    public function setReturnEnd($returnEnd = true)
    {
        $this->_returnEnd = $returnEnd;

        return $this;
    }


    /**
     * 状态码:错误
     */
    const STATUS_ERROR = 0;

    /**
     * 状态码:成功
     */
    const STATUS_SUCCESS = 1;

    /**
     * 单例
     *
     * @var Q_Response
     */
    protected static $_instance = null;

    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new Q_Response();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        if (defined('SYSTEM_CHARSET')) {
            $this->pageCharset = SYSTEM_CHARSET;
        }
    }

    /**
     * 获取应用名
     *
     * @return string
     */
    public function getAppName()
    {
        return $this->_appName;
    }

    /**
     * 设置应用名
     *
     * @param string $appName
     * @return $this
     */
    public function setAppName($appName = APP_NAME)
    {
        $this->_appName = $appName;

        //如果不是默认的appName，需要对skinRoot重置
        if ($appName != APP_NAME) {
            $this->setSkinRoot();
        }

        return $this;
    }


    public function setCtlName($ctlName)
    {
        $this->_ctlName = $ctlName;
        $this->set('ctlName', $ctlName);
        return $this;
    }

    public function getCtlName()
    {
        return $this->_ctlName;
    }

    public function setActName($actName)
    {
        $this->_actName = $actName;
        $this->set('actName', $actName);
        return $this;
    }

    public function getActName()
    {
        return $this->_actName;
    }

    public function setVerName($verName)
    {
        $this->_verName = $verName;
        $this->set('verName', $verName);
        return $this;
    }

    public function getVerName()
    {
        return $this->_verName;
    }

    public function setCurVerName($curVerName)
    {
        $this->_curVerName = $curVerName;
        $this->set('curVerName', $curVerName);
        return $this;
    }

    public function getCurVerName()
    {
        return $this->_curVerName;
    }

    /**
     * 获取模板根目录
     *
     * @return mixed
     */
    public function getSkinRoot()
    {
        return $this->_skinRoot;
    }

    /**
     * 获取模板根目录
     *
     * @return mixed
     */
    private function _parseTemplatePath($path = '')
    {
        if (empty($path) || $path[0] === '~') {//当前对应控制器下方法下的目录
            $_string = strtr($this->_ctlName, '_', '/') . '/';

            if (empty($path)) {
                $path = $_string . ucfirst($this->_actName);
            } else if ($path[0] === '~') {
                $path = $_string . ucfirst(substr($path, 2));
            }
        }

        if (substr($path, -8) != '.tpl.php') {
            $path .= '.tpl.php';
        }

        if ($path[0] !== '/') {
            $_skinRoot = '';
            $_skinName = '';

            $_protocolPos = strpos($path, '://');
            $_path        = substr($path, $_protocolPos + 3);

            $_skinNamePos    = strpos($_path, '@');
            $_skinNameEndPos = strpos($_path, '/');

            if ($_skinNamePos !== false) {

                $_skinName = substr($_path, $_skinNamePos, $_skinNameEndPos - $_skinNamePos);
            }

            if ($_protocolPos !== false) {
                $protocol  = substr($path, 0, $_protocolPos);
                $_skinRoot = Q::getNameSpacePath($protocol) . '/Skin/';
                $path      = $_path;

                if (!empty($_skinName)) {
                    $path = substr($path, $_skinNameEndPos + 1);
                }
            } else {

                if (empty($_skinName)) {
                    $_skinName = Q_Config::get($this->getAppName() . '_Global', 'SKIN_NAME');
                }
                $_skinRoot = $this->_skinRoot;
            }

            $_skinName = $_skinName ? $_skinName : 'Default';

            $tpl = $_skinRoot . '/' . $_skinName . '/' . $path;

            if ($_skinName != 'Default' && !is_file($tpl)) {//定义了其它模板，如果不存在某模板文件，就调用默认模板文件
                $tpl = $_skinRoot . 'Default/' . $path;
            }
        } else {
            $tpl = PRODUCTION_ROOT . $path;
        }

        return $tpl;
    }

    /**
     * 设置模板根目录
     *
     * @return $this
     */
    public function setSkinRoot()
    {
        $skinRoot        = APPS_ROOT . $this->getAppName() . '/Skin/';
        $this->_skinRoot = $skinRoot;
        return $this;
    }


    /**
     * 设置数据
     *
     * @param $k
     * @param $v
     * @return $this
     */
    public function set($k, $v)
    {
        $this->_aProps[$k] = $v;
        return $this;
    }

    /**
     * 添加数据
     *
     * @param array $aData
     * @return $this
     */
    public function add(array $aData)
    {
        foreach ($aData as $k => $v) {
            $this->_aProps[$k] = $v;
        }
        return $this;
    }

    /**
     * 清除数据
     *
     * @return $this
     */
    public function reset()
    {
        $this->_aProps = array();
        return $this;
    }


    public function __set($k, $v)
    {
        $this->set($k, $v);
    }

    public function __get($k)
    {
        if (isset($this->_aProps[$k])) {
            return $this->_aProps[$k];
        }
        return null;
    }

    public function isEmpty($name = '')
    {
        if (!empty($name)) {
            return empty($this->_aProps[$name]);
        } else {
            return empty($this->_aProps);
        }
    }

    public function exists($key)
    {
        if (!empty($key)) {
            return isset($this->_aProps[$key]);
        } else {
            return false;
        }
    }

    /**
     * 设置模板
     *
     * @param string $string
     * @return $this
     */
    public function setTemplate($string = '', $col = false)
    {
        $tpl = $this->_parseTemplatePath($string);

        if ($col) {
            $this->_colTemplate = $tpl;
        } else {
            $this->_template = $tpl;
        }

        return $this;
    }

    public function setColTemplate($tpl = '')
    {
        return $this->setTemplate($tpl, true);
    }

    public function getTemplate($col = false)
    {
        if (!$col) {
            $tpl = $this->_template;
        } else {
            $tpl = $this->_colTemplate;
        }

        return $tpl;
    }

    public function getColTemplate()
    {
        return $this->getTemplate(true);
    }

    /**
     *
     * @param array $aMessages
     * @return $this
     */
    public function setMessages(array $aMessages)
    {
        $this->_aMessages = $aMessages;
        return $this;
    }


    public function getHeaders()
    {
        return $this->aHeaders;
    }

    public function getBody()
    {
        return $this->_aProps;
    }

    public function getOutputBody()
    {
        return $this->_data;
    }

    /**
     * 设置body
     *
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->_data = $body;
        return $this;
    }

    /**
     * 添加header
     *
     * @param $header
     * @return $this
     */
    public function addHeader($header)
    {
        if (!in_array($header, $this->aHeaders)) {
            $this->aHeaders[] = $header;
        }

        return $this;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 添加Code
     *
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    public function __toString()
    {
        return $this->fetch();
    }

    /**
     * 设置session
     *
     * @param $key
     * @param $var
     * @return $this
     */
    public function session($key, $var)
    {
        session_id() || session_start();
        $_SESSION[$key] = $var;
        return $this;
    }

    public function cookie($name, $value = null, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public function buildStaticPage(array $data, $template, $filePath)
    {
        if (empty($data)) {
            trigger_error('$data dose not empty!');

            return false;
        }
        if (empty($template)) {
            trigger_error('$template dose not empty!');

            return false;
        }
        if (empty($filePath)) {
            trigger_error('$filePath dose not empty!');

            return false;
        }
        $output = new Q_Response();
        $output->add($data);
        $output->template = $template;
        $view             = new Q_View_Simple($output);

        Q_File::write($view->render(), $filePath);

        return false;
    }

    public function fetch()
    {
        $tpl = $this->getTemplate();
        if (!$tpl) {
            if ($data = $this->getOutputBody()) {
                return $data;
            }
        }

        $content = null;
        if ($tpl) {
            $view    = new Q_View_Simple($this);
            $content = $view->render();
        }

        while ($layout = $this->_shiftLayout()) {
            is_null($content) || $this->_content = $content;
            $content = $this->fetchCol($layout);
        }

        return $content;
    }

    /**
     * 获取部分模板
     *
     * @param string $template 模板路径
     * @param array  $data     要处理的数据
     * @param bool   $newData  是否使用全新的数据,不采用上下文中的数据
     * @return string html
     */
    public function fetchCol($template = '', $data = array(), $newData = false)
    {
        if ($newData) {
            $output = new Q_Response();
        } else {
            $output = $this;
        }

        if (!empty($data)) {
            $output->add($data);
        }
        $output->setColTemplate($template);
        $view = new Q_View_Simple($output);

        return $view->renderCol();
    }

    public function display()
    {
        $string = ob_get_clean();

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=' . $this->pageCharset);

            foreach ($this->getHeaders() as $header) {
                header($header);
            }
        }

        if (IS_DEBUGGING && $string) {
            throw new Q_Exception("output string '$string' before send header!");
        }
        $html = $this->fetch();
        echo $html;
        return $html;
    }

    /**
     * 返回JSON数据到浏览器
     *
     * @param        $data
     * @param int    $status
     * @param string $msg
     * @param null   $end
     * @return string
     */
    public function jsonReturn($data, $status = Q_Response::STATUS_SUCCESS, $msg = '', $end = null)
    {
        if (!isset($end)) {
            $end = $this->isReturnEnd();
        }

        Q_Http::sendHeader('json');
        $json = json_encode(array('status' => $status, 'data' => $data, 'msg' => $msg));

        if ($end) {
            echo $json;
            Q::end();
        }

        $this->setBody($json);
        return $json;
    }

    /**
     * 返回JSON数据到浏览器
     *
     * @deprecated Use jsonReturn() instead
     * @param        $data
     * @param int    $status
     * @param string $msg
     * @param null   $end
     * @return string
     */
    public function ajaxReturn($data, $status = Q_Response::STATUS_SUCCESS, $msg = '', $end = null)
    {
        return $this->jsonReturn($data, $status, $msg, $end);
    }

    /**
     * 设置layout
     *
     * @param $layout
     * @return $this
     */
    public function layout($layout)
    {
        $file            = 'Layout/' . $layout;
        $this->_layout[] = $file;
        return $this;
    }

    public function getLayout()
    {
        return $this->_layout;
    }

    protected function _shiftLayout()
    {
        return array_shift($this->_layout);
    }

    /**
     * 清除布局
     *
     * @return $this
     */
    public function clearLayout()
    {
        $this->_layout = array();
        return $this;
    }
}
