<?php

class Q_Request
{
    const BROWSER = 1;
    const CLI     = 2;
    const AJAX    = 4;
    const XMLRPC  = 8;
    const AMF     = 16;
    const NATIVE  = 32;


    protected static $_runtimeType;

    protected $_aClean = array();

    const METHOD_GET     = 'get';
    const METHOD_POST    = 'post';
    const METHOD_REQUEST = 'request';
    const METHOD_FILES   = 'files';
    const METHOD_COOKIE  = 'cookie';
    const METHOD_SESSION = 'session';

    protected $_a = array(
        self::METHOD_GET     => array(),
        self::METHOD_POST    => array(),
        self::METHOD_REQUEST => array(),
        self::METHOD_FILES   => array(),
        self::METHOD_COOKIE  => array(),
    );

    const RAW_DATA_TYPE_STRING       = 'string';
    const RAW_DATA_TYPE_QUERY_STRING = 'query';
    const RAW_DATA_TYPE_JSON         = 'json';

    protected $_type;
    /**
     * 执行名
     *
     * @var string
     */
    public $execName = null;

    /**
     * 事件名
     *
     * @var string
     */
    public $actName = null;

    /**
     * 控制器名
     *
     * @var string
     */
    public $ctlName = null;

    /**
     * 版本名
     *
     * @var string
     */
    public $verName = null;

    /**
     * 当前版本
     *
     * @var string
     */
    public $curVerName = null;

    /**
     * 单例
     *
     * @var Q_Request
     */
    protected static $_instance = null;

    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new Q_Request();
        }

        return self::$_instance;
    }

    /**
     * Q_Request constructor.
     */
    public function __construct()
    {
        if ($this->isEmpty()) {
            $type = self::resolveType();
            $this->setType($type);
            if ($type == Q_Request::CLI) {
                $this->_a[self::METHOD_GET] = $this->getCliOpt();
            } elseif ($type == Q_Request::BROWSER || $type == Q_Request::AJAX) {
                $this->_a[self::METHOD_GET]     = empty($_GET) ? array() : $_GET;
                $this->_a[self::METHOD_POST]    = empty($_POST) ? array() : $_POST;
                $this->_a[self::METHOD_REQUEST] = empty($_REQUEST) ? array() : $_REQUEST;
                $this->_a[self::METHOD_FILES]   = empty($_FILES) ? array() : $_FILES;
                $this->_a[self::METHOD_COOKIE]  = empty($_COOKIE) ? array() : $_COOKIE;
                unset($_GET, $_FILES, $_POST, $_REQUEST);
            }

            $this->ctlName  = $this->getControllerName();
            $this->actName  = $this->getActionName();
            $this->execName = $this->getExecName();
            $this->verName  = $this->getVerName();
        }
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    protected function _getTypeName()
    {
        $class                = new ReflectionClass(get_class($this));
        $aConstants           = $class->getConstants();
        $aConstantsIntIndexed = array_flip($aConstants);
        $const                = $aConstantsIntIndexed[$this->_type];
        $name                 = ucfirst(strtolower($const));
        return $name;
    }

    /**
     * 根据系统环境自动判断运行环境类型
     *
     * @return int
     */
    public static function resolveType()
    {
        if (!empty(self::$_runtimeType)) {
            return (int)self::$_runtimeType;
        }

        if (PHP_SAPI == 'cli') {
            $ret = self::CLI;
        } elseif (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
        ) {
            $ret = self::AJAX;
        } elseif (isset($_SERVER['CONTENT_TYPE']) &&
                  $_SERVER['CONTENT_TYPE'] == 'application/x-amf'
        ) {
            $ret = self::AMF;
        } else if (self::$_runtimeType == self::NATIVE) {
            $ret = self::NATIVE;
        } else {
            $ret = self::BROWSER;
        }

        self::setRuntimeType($ret);
        return $ret;
    }

    /**
     * 设置运行时类型
     *
     * @param $type
     */
    public static function setRuntimeType($type)
    {
        self::$_runtimeType = $type;
    }

    /**
     * 获取运行时类型
     *
     * @return int
     */
    public static function getRuntimeType()
    {
        return self::resolveType();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isEmpty($name = '')
    {
        if (!empty($name)) {
            return empty($this->_aClean[$name]);
        } else {
            return empty($this->_aClean);
        }
    }

    /*
    |---------------------------------------------------------------
    | Retrieves values from Request object.
    |---------------------------------------------------------------
    | @param   mixed   $paramName  Request param name
    | @param   string  $method     Method of request
    | @param   boolean $allowTags  If html/php tags are allowed or not
    | @return  mixed               Request param value or null if not exists
    | @todo make additional arg for defalut value
    */
    public function getByMethod($key = '', $method = self::METHOD_GET, $default = null, $conv = '', $allowTags = false)
    {
        $ret = $default;
        if (empty($key)) {
            if (false === $allowTags) {
                $ret = Q_String::clean($this->_a[$method]);
            } else {
                $ret = $this->_a[$method];
            }
        } elseif (isset($this->_a[$method][$key])) {
            //  don't operate on reference to avoid segfault :-(
            $ret = $this->_a[$method][$key];

            //  if html not allowed, run an enhanced strip_tags()
            if (false === $allowTags) {
                $ret = Q_String::clean($ret);
            }
        }

        if (!empty($ret)) {
            if (self::AJAX == $this->getType() && strcasecmp(SYSTEM_CHARSET, 'utf-8')) {
                if (in_array(strtolower($method), array(self::METHOD_GET, self::METHOD_POST)) && !empty($ret)) {
                    $ret = Q_String::convertEncodingDeep($ret, SYSTEM_CHARSET, 'utf-8');
                }
            }
        }

        empty($ret) && isset($default) && $ret = $default;
        $conv && $ret = $conv($ret);
        return $ret;
    }

    public function rawData($type = self::RAW_DATA_TYPE_JSON)
    {
        $data = file_get_contents('php://input');

        if (!isset($data)) {
            return null;
        }

        switch ($type) {
            case self::RAW_DATA_TYPE_JSON:
                $data = json_decode($data, true);
                break;
            case self::RAW_DATA_TYPE_QUERY_STRING:
                parse_str($data, $data);
                break;
        }

        return $data;
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param string     $conv
     * @param bool|false $allowTags
     * @return array|bool|null|string
     */
    public function get($key = '', $default = null, $conv = '', $allowTags = false)
    {
        return $this->getByMethod($key, self::METHOD_GET, $default, $conv, $allowTags);
    }

    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @return int
     */
    public function getRange($key, $def = array())
    {
        return $this->_range($key, $def, self::METHOD_GET);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return int
     */
    public function getInt($key = '', $default = null)
    {
        return $this->get($key, $default, 'intval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return float
     */
    public function getFloat($key = '', $default = null)
    {
        return $this->get($key, $default, 'floatval', true);
    }

    /**
     * @param string    $key
     * @param null|bool $default
     * @return bool
     */
    public function getBool($key = '', $default = false)
    {
        $_val = $this->get($key);

        if ($_val === null) {
            $_val = $default;
        } else {
            if (empty($_val) || $_val === 'false') {
                $_val = false;
            } else {
                $_val = boolval($_val);
            }
        }

        return $_val;
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param bool|false $allowTags
     * @return array
     */
    public function getArray($key = '', $default = null, $allowTags = false)
    {
        $ret = $this->get($key, $default, '', $allowTags);
        return (array)$ret;
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param string     $conv
     * @param bool|false $allowTags
     * @return array|bool|null|string
     */
    public function post($key = '', $default = null, $conv = '', $allowTags = false)
    {
        return $this->getByMethod($key, self::METHOD_POST, $default, $conv, $allowTags);
    }

    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @return int
     */
    public function postRange($key, $def = array())
    {
        return $this->_range($key, $def, self::METHOD_POST);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return int
     */
    public function postInt($key = '', $default = null)
    {
        return $this->post($key, $default, 'intval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return float
     */
    public function postFloat($key = '', $default = null)
    {
        return $this->post($key, $default, 'floatval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return bool
     */
    public function postBool($key = '', $default = null)
    {
        return filter_var($this->post($key, $default, '', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param bool|false $allowTags
     * @return array
     */
    public function postArray($key = '', $default = null, $allowTags = false)
    {
        $ret = $this->post($key, $default, '', $allowTags);
        return (array)$ret;
    }

    /**
     * @param string     $key
     * @param string     $convType
     * @param null       $default
     * @param bool|false $allowTags
     * @return array|bool|null|string
     */
    public function cookie($key = '', $convType = '', $default = null, $allowTags = false)
    {
        return $this->getByMethod($key, self::METHOD_COOKIE, $default, $convType, $allowTags);
    }

    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @return int
     */
    public function cookieRange($key, $def = array())
    {
        return $this->_range($key, $def, self::METHOD_COOKIE);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return int
     */
    public function cookieInt($key = '', $default = null)
    {
        return $this->cookie($key, $default, 'intval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return float
     */
    public function cookieFloat($key = '', $default = null)
    {
        return $this->cookie($key, $default, 'floatval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return bool
     */
    public function cookieBool($key = '', $default = null)
    {
        return filter_var($this->cookie($key, $default, 'boolval', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param bool|false $allowTags
     * @return array
     */
    public function cookieArray($key = '', $default = null, $allowTags = false)
    {
        $ret = $this->cookie($key, $default, '', $allowTags);
        return (array)$ret;
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param string     $conv
     * @param bool|false $allowTags
     * @return array|bool|null|string
     */
    public function session($key = '', $default = null, $conv = '', $allowTags = false)
    {
        session_id() || session_start();
        $this->_a['session'] = $_SESSION;//用时再放入过滤器
        return $this->getByMethod($key, 'session', $default, $conv, $allowTags);
    }

    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @return int
     */
    public function sessionRange($key, $def = array())
    {
        return $this->_range($key, $def, self::METHOD_SESSION);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return int
     */
    public function sessionInt($key = '', $default = null)
    {
        return $this->session($key, $default, 'intval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return float
     */
    public function sessionFloat($key = '', $default = null)
    {
        return $this->session($key, $default, 'floatval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return bool
     */
    public function sessionBool($key = '', $default = null)
    {
        return filter_var($this->session($key, $default, 'boolval', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param bool|false $allowTags
     * @return array
     */
    public function sessionArray($key = '', $default = null, $allowTags = false)
    {
        $ret = $this->session($key, $default, '', $allowTags);
        return (array)$ret;
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param string     $conv
     * @param bool|false $allowTags
     * @return array|bool|null|string
     */
    public function request($key = '', $default = null, $conv = '', $allowTags = false)
    {
        return $this->getByMethod($key, self::METHOD_REQUEST, $default, $conv, $allowTags);
    }

    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @return int
     */
    public function requestRange($key, $def = array())
    {
        return $this->_range($key, $def, self::METHOD_REQUEST);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return int
     */
    public function requestInt($key = '', $default = null)
    {
        return $this->request($key, $default, 'intval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return float
     */
    public function requestFloat($key = '', $default = null)
    {
        return $this->request($key, $default, 'floatval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return bool
     */
    public function requestBool($key = '', $default = null)
    {
        return filter_var($this->request($key, $default, 'boolval', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param bool|false $allowTags
     * @return array
     */
    public function requestArray($key = '', $default = null, $allowTags = false)
    {
        $ret = $this->request($key, $default, '', $allowTags);
        return (array)$ret;
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param string     $conv
     * @param bool|false $allowTags
     * @return array|bool|null|string
     */
    public function files($key = '', $default = null, $conv = '', $allowTags = false)
    {
        return $this->getByMethod($key, self::METHOD_FILES, $default, $conv, $allowTags);
    }

    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @return int
     */
    public function filesRange($key, $def = array())
    {
        return $this->_range($key, $def, self::METHOD_FILES);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return int
     */
    public function filesInt($key = '', $default = null)
    {
        return $this->files($key, $default, 'intval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return float
     */
    public function filesFloat($key = '', $default = null)
    {
        return $this->files($key, $default, 'floatval', true);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return bool
     */
    public function filesBool($key = '', $default = null)
    {
        return filter_var($this->files($key, $default, 'boolval', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string     $key
     * @param null       $default
     * @param bool|false $allowTags
     * @return array
     */
    public function filesArray($key = '', $default = null, $allowTags = false)
    {
        $ret = $this->files($key, $default, '', $allowTags);
        return (array)$ret;
    }

    /*
    |---------------------------------------------------------------
    | Set a value for Request object.
    |---------------------------------------------------------------
    | @access  public
    | @param   mixed   $name   Request param name
    | @param   mixed   $value  Request param value
    | @return  $this
    */
    public function set($key, $value)
    {
        $this->_aClean[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->_aClean[$key])) {
            return $this->_aClean[$key];
        }
        return null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        if (!empty($key)) {
            return isset($this->_aClean[$key]);
        } else {
            return false;
        }
    }

    /**
     * @param array  $aParams
     * @param string $method
     * @return $this
     */
    public function add(array $aParams, $method = self::METHOD_GET)
    {
        $this->_a[$method] = isset($this->_a[$method]) ? Q_Array::arrayMergeRecursiveUnique($this->_a[$method], $aParams) : $aParams;

        //给request中也加入数据
        if ($method != self::METHOD_REQUEST) {
            $this->_a[self::METHOD_REQUEST] = isset($this->_a[self::METHOD_REQUEST]) ? Q_Array::arrayMergeRecursiveUnique($this->_a[self::METHOD_REQUEST], $aParams) : $aParams;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->_aClean = array();
        return $this;
    }

    /**
     * 清除原始数据
     *
     * @param string $method
     * @param string $key
     * @return $this
     */
    public function removeSourceData($method = '', $key = '')
    {
        if ($method) {
            if ($key) {
                unset($this->_a[$method][$key]);
            } else {
                unset($this->_a[$method]);
            }
        } else {
            $this->_a = array();
        }
        return $this;
    }

    /*
    |---------------------------------------------------------------
    | Return an array of all filtered Request properties.
    |---------------------------------------------------------------
    | @access  public
    | @return  array
    */
    public function getClean()
    {
        return $this->_aClean;
    }

    /**
     * 获取控制器名
     *
     * @return string
     */
    public function getControllerName()
    {
        $ret = $this->ctlName;
        $ret || $ret = $this->get('controller');
        $ret || $ret = $this->post('controller');
        $ret || $ret = $this->get('c');
        $ret || $ret = $this->post('c');
        $ret || $ret = 'default';

        $ret = Q::formatClassName($ret);

        $ret = Q_Inflector::formatName($ret);

        $_map = Q_Config::get(APP_NAME . '_ControllerMap');

        if ($_map && !empty($_map[$ret])) {
            $ret = $_map[$ret];
        }

        $this->ctlName || $this->setControllerName($ret);
        return $ret;
    }

    /**
     * 获取ctlName
     *
     * @param $ctlName
     * @return $this
     */
    public function setControllerName($ctlName)
    {
        $this->ctlName = ucfirst($ctlName);
        return $this;
    }

    /**
     * 获取action名
     *
     * @return string
     */
    public function getActionName()
    {
        $ret = $this->actName;
        $ret || $ret = $this->get('action');
        $ret || $ret = $this->get('a');
        $ret || $ret = $this->post('a');
        $ret || $ret = 'default';

        $ret = Q_Inflector::formatName($ret);

        $this->actName || $this->setActionName($ret);
        return $ret;
    }

    /**
     * @param string $actName
     * @return $this
     */
    public function setActionName($actName)
    {
        $this->actName = ucfirst($actName);
        return $this;
    }

    /**
     * 获取执行名
     *
     * @return string
     */
    public function getExecName()
    {
        $ret = $this->get('exec');
        $ret || $ret = $this->post('exec');
        $ret || $ret = $this->get('e');
        $ret || $ret = $this->post('e');

        $this->set('exec', $ret);
        return $ret;
    }

    /**
     * @param string $execName
     * @return $this
     */
    public function setExecName($execName)
    {
        $this->execName = $execName;
        return $this;
    }

    /**
     * 获取版本名
     *
     * @return string
     */
    public function getVerName()
    {
        $ret = $this->get('ver');
        $ret || $ret = $this->post('ver');
        $ret || $ret = $this->get('v');
        $ret || $ret = $this->post('v');

        if (Q_Inflector::formatVersionNo($ret)) {
            $this->set('ver', $ret);
        } else {
            $ret = null;
        }

        return $ret;
    }

    /**
     * 设置verName
     *
     * @param string $verName
     * @return $this
     */
    public function setVerName($verName)
    {
        if (Q_Inflector::formatVersionNo($verName)) {
            $this->verName = $verName;
        }
        return $this;
    }

    /**
     * 获取版本名
     *
     * @return string
     */
    public function getCurVerName()
    {
        return $this->curVerName;
    }

    /**
     * 设置verName
     *
     * @param string $curVerName
     * @return $this
     */
    public function setCurVerName($curVerName)
    {
        if (Q_Inflector::formatVersionNo($curVerName)) {
            $this->curVerName = $curVerName;
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function getCliOpt()
    {
        $ret  = array();
        $args = $this->readPHPArgv();
        if (!empty($args)) {
            foreach ($args as $val) {
                if (isset($val{2})) {
                    if ($val{0} == '-' && $val{1} == '-') {
                        $exp                     = explode('=', $val, 2);
                        $ret[substr($exp[0], 2)] = isset($exp[1]) ? $exp[1] : NULL;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * @return array|bool
     */
    public function readPHPArgv()
    {
        global $argv;
        if (!is_array($argv)) {
            if (!@is_array($_SERVER['argv'])) {
                if (!@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
                    trigger_error("Console_Getopt: Could not read cmd args (register_argc_argv=Off?)");

                    return false;
                }
                return $GLOBALS['HTTP_SERVER_VARS']['argv'];
            }
            return $_SERVER['argv'];
        }
        return $argv;
    }


    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @param string $method
     * @return int
     */
    private function _range($key, $def = array(), $method = self::METHOD_GET)
    {
        $def = (array)$def;
        $min = isset($def['min']) ? $def['min'] : (isset($def[0]) ? $def[0] : null);
        $max = isset($def['max']) ? $def['max'] : (isset($def[1]) ? $def[1] : null);

        $method = ucfirst($method) . 'Int';
        $val    = $this->$method($key, 0);

        if (isset($min)) {
            $val = max($min, $val);
        }

        if (isset($max)) {
            $val = min($max, $val);
        }

        return $val;
    }
}

