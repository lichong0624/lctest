<?php
/**
 * DAL客户端
 * 使用示例:
 * //调用单个方法
 * Q_DAL_Client::instance()->call('DAL_User', 'getInfo', array('id' => 1));
 * //调用多个方法
 * Q_DAL_Client::instance()->call('DAL_User', array('getInfo' => array('id' => 1), 'getEmail' => array('id' => 1)));
 * //多个方法共用同一参数
 * Q_DAL_Client::instance()->call('DAL_User', array('getInfo', 'getEmail'), array('id' => 1));
 * //调用多个方法时返回数据结构为['getInfo' => $data1, 'getEmail' => $data2];
 *
 * @author       : wiki <charmfocus@gmail.com>
 * @copyright(c) 14-10-4
 */
class Q_DAL_Client
{

    /**
     * 处理器类型：调用
     */
    const WORKER_CALL = 1;

    /**
     * 处理器类型：清除
     */
    const WORKER_CLEAR = 2;

    /**
     * 请求类型 HTTP方式
     *
     * @var string
     */
    const MODE_HTTP = 'HTTP';

    /**
     * 请求类型 NATIVE方式
     *
     * @var string
     */
    const MODE_NATIVE = 'NATIVE';

    const POST_KEY_MODULE  = '__MODULE__';
    const POST_KEY_METHOD  = '__METHOD__';
    const POST_KEY_PARAM   = '__PARAM__';
    const POST_KEY_EXPIRE  = '__EXPIRE__';
    const POST_KEY_FLUSH   = '__FLUSH__';
    const POST_KEY_WORKER  = '__WORKER__';
    const POST_KEY_SIGN    = '__SIGN__';
    const POST_KEY_APPID   = '__APPID__';
    const POST_KEY_APPKEY  = '__APPKEY__';
    const POST_KEY_VERSION = '__VERSION__';

    const CACHE_PARAM_KEY_MODULE        = '__DAL_MODULE__';
    const CACHE_PARAM_KEY_MODULE_METHOD = '__DAL_MODULE_METHOD__';

    /**
     * 立即清除
     */
    const FLUSH_NOW = true;

    /**
     * 延迟清除
     */
    const FLUSH_DEFER = 'DEFER';

    /**
     * 不清除
     */
    const FLUSH_NOT = false;

    /**
     * 应用名
     *
     * @var string
     */
    protected $_appName = '';


    /**
     * @var $this
     */
    protected static $_instance = null;

    /**
     * 数据运行时缓存
     *
     * @var array
     */
    protected $_cache = null;

    /**
     * @var array 配置
     */
    protected $_config;

    /**
     * @var array 缓存配置
     */
    protected $_cacheConfig;


    protected $_moduleInstance = null;

    protected $_flush = false;

    protected $_mode = null;

    /**
     * 用户angent
     *
     * @var string
     */
    protected $_userAgent = '';

    /**
     * COOKIE 存储文件
     *
     * @var string
     */
    protected $_cookieJar = '';

    /**
     * COOKIE 数组
     *
     * @var array
     */
    protected $_cookie = array();

    /**
     * @return bool|string
     */
    public function getFlush()
    {
        return $this->_flush;
    }

    /**
     * 设置清除缓存方式
     *
     * @param bool|string $flush 是否为被动（延迟）清除 self::FLASH_*
     * @return $this
     */
    public function setFlush($flush = self::FLUSH_NOW)
    {
        $this->_flush = $flush;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        if (!isset($this->_mode)) {
            $mode = empty($this->_config['MODE']) ? self::MODE_NATIVE : $this->_config['MODE'];
            $this->setMode($mode);
        }
        return $this->_mode;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }

    public function setModeNative()
    {
        $this->_mode = self::MODE_NATIVE;
        return $this;
    }

    public function setModeHttp()
    {
        $this->_mode = self::MODE_HTTP;
        return $this;
    }

    /**
     * 设置用户agent
     *
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
        return $this;
    }

    /**
     * 设置cookieJar文件
     *
     * @param string $cookieJar
     * @return $this
     */
    public function setCookieJar($cookieJar)
    {
        $this->_cookieJar = $cookieJar;
        return $this;
    }

    /**
     * 设置cookie内容
     *
     * @param array $cookie
     * @return $this
     */
    public function setCookie(array $cookie)
    {
        $this->_cookie = $cookie;
        return $this;
    }


    /**
     * 单例
     *
     * @param string $_nodeName 节点名，留空调用全局
     * @return $this
     */
    public static function instance($_nodeName = '')
    {
        if (empty(self::$_instance[$_nodeName])) {
            $obj = new self();

            $_conf = Q_Config::get(array('DAL', APP_NAME . '_DAL'));//加载DAL配置文件

            if (!empty($_conf['VERSION']) && (float)$_conf['VERSION'] > 1) {
                $_conf = empty($_conf[$_nodeName]) ? $_conf : $_conf[$_nodeName];
                if (!empty($_conf[$_nodeName])) {
                    $_conf = $_conf[$_nodeName];
                }
            }

            $obj->setConfig($_conf);
            $obj->setCacheConfig(Q_Config::get(array('DALCache', APP_NAME . '_DALCache')));
            self::$_instance[$_nodeName]    = $obj;
        }
        return self::$_instance[$_nodeName];
    }

    /**
     * 设置配置
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * 设置缓存配置
     *
     * @param array $cacheConfig
     * @return $this
     */
    public function setCacheConfig(array $cacheConfig)
    {
        $this->_cacheConfig = $cacheConfig;
        return $this;
    }

    /**
     * 设置应用名
     *
     * @param string $appName
     * @return $this
     */
    public function setAppName($appName = '')
    {
        $this->_appName = $appName;
        return $this;
    }


    /**
     * 调用模块
     *
     * @param string       $moduleName 模块名
     * @param string|array $methodName 方法名
     * @param array        $param      参数
     * @param array        $files      要上传的文件 [field => file]
     * @param bool         $flush      是否更新缓存
     * @param string       $mode       调用方式 Q_DAL_Client::MODE_NATIVE|Q_DAL_Client::MODE_HTTP
     * @throws \Q_Exception
     * @return mixed
     */
    public function call($moduleName, $methodName = 'Default', array $param = array(), array $files = array(), $flush = self::FLUSH_NOT, $mode = null)
    {
        $moduleName = Q_Inflector::formatName($moduleName);
        $methodName = Q_Inflector::formatName($methodName);

        is_array($param) && Q_Array::ksortDeep($param);

        $jsonName = json_encode($param);

        if (is_array($methodName)) {
            $methodName = array_map('ucfirst', $methodName);
            $_cacheKey  = md5($moduleName . '|' . join(',', $methodName) . $jsonName);
        } else {
            $methodName = empty($methodName) ? 'Default' : ucfirst($methodName);
            $_cacheKey  = md5($moduleName . '|' . $methodName . $jsonName);
        }


        if (isset($this->_cache[$_cacheKey])) {
            return $this->_cache[$_cacheKey];
        }

        if (!isset($mode)) {
            $mode = $this->getMode();
        }

        if ($mode == self::MODE_NATIVE) {
            return $this->_callByNative($moduleName, $methodName, $param, $files, $flush);
        } else if ($mode == self::MODE_HTTP) {
            return $this->_callByHttp($moduleName, $methodName, $param, $files, $flush);
        } else {
            throw new Q_Exception('the client mode is incorrect or not set DAL Config!');
        }
    }

    /**
     * 清除缓存，默认是主动(立即)清除，可配置被动(延迟)清除
     *
     * @param string      $moduleName 要清除的模块
     * @param string      $methodName 要清除的方法
     * @param array       $param      清除规则参数
     * @param int         $expire     清除规则有效期
     * @param bool|string $flush      是否更新缓存，false 不更新， true 立即更新，'defer' 延迟更新，null，自动根据getter获取
     * @param string      $mode       调用方式 Q_DAL_Client ::MODE_NATIVE|Q_DAL_Client::MODE_HTTP
     * @return $this
     * @throws Q_Exception
     */
    public function clearCache($moduleName, $methodName = '', array $param = array(), $expire = 3600, $flush = null, $mode = null)
    {

        if (!isset($flush)) {
            $flush = $this->getFlush();
        }
        isset($flush) || $flush = $this->getFlush();

        if (!$flush) {
            return $this;
        }

        if ($flush === self::FLUSH_NOW && empty($methodName)) {
            throw new Q_Exception('The methodName can not be set to empty!');
        }

        if (!isset($mode)) {
            $mode = $this->getMode();
        }

        if ($mode == self::MODE_NATIVE) {
            $this->_clearCacheByNative($moduleName, $methodName, $param, $expire, $flush);
        } else if ($mode == self::MODE_HTTP) {
            $this->_clearCacheByHttp($moduleName, $methodName, $param, $expire, $flush);
        }

        return $this;
    }

    /**
     * 发送数据到服务器
     *
     * @param array $data  要发送的数据
     * @param array $files 要上传的文件
     * @return mixed
     * @throws Q_Exception
     */
    protected function _post(array $data, array $files = array())
    {
        $data[self::POST_KEY_APPID] = $this->_config['APPID'];

        $_signData = $data;

        $_signData[self::POST_KEY_APPKEY] = $this->_config['APPKEY'];
        Q_Array::ksortDeep($_signData);

        $_sign = md5(urldecode(http_build_query($_signData)));


        $data[self::POST_KEY_SIGN] = $_sign;

        $url = $this->_config['SERVER'];

        $option = array();

        if (!empty($this->_cookie)) {
            $option[CURLOPT_COOKIE] = http_build_query($this->_cookie, '', '; ');
        }

        if (!empty($this->_userAgent)) {
            $option[CURLOPT_USERAGENT] = $this->_userAgent;
        }

        if (!empty($this->_cookieJar)) {
            $option[CURLOPT_COOKIEJAR]  = $this->_cookieJar;
            $option[CURLOPT_COOKIEFILE] = $this->_cookieJar;
        }

        $res = Q_Http::post($url, $data, $files, $option);

        if ($res) {
            $data = json_decode($res, true);

            if (is_array($data)) {
                if ($data['status'] < 1) {
                    throw new Q_Exception($data['msg'], $data['status']);
                }
                return $data['data'];
            }

            throw new Q_Exception('server response data is error! data:' . $res);
        }
        throw new Q_Exception('can not connect to the server! please check your network!');
    }

    /**
     * 清除缓存，默认是主动(立即)清除，可配置被动(延迟)清除
     *
     * @param string      $moduleName 要清除的模块
     * @param string      $methodName 要清除的方法
     * @param array       $param      清除规则参数
     * @param int         $expire     清除规则有效期
     * @param bool|string $flush      更新方式：self::FLUSH_*
     * @return $this
     * @throws Q_Exception
     */
    protected function _clearCacheByNative($moduleName, $methodName = '', array $param = array(), $expire = 3600, $flush = null)
    {
        if (!$flush) {
            throw new Q_Exception('The flush param can not be set to empty!');
        }

        $_cacheParam = $param;

        $_cacheParam[self::CACHE_PARAM_KEY_MODULE] = $moduleName;
        if ($methodName) {
            $_cacheParam[self::CACHE_PARAM_KEY_MODULE_METHOD] = $methodName;
        }

        if ($flush === self::FLUSH_DEFER) {
            Q_Cache::clear($_cacheParam, $expire);
        } else {
            $_moduleCacheCfg = $this->_getModuleCacheConfig($moduleName, $methodName);
            if ($_moduleCacheCfg) {
                $_cacheObj = Q_Cache::instance($_moduleCacheCfg['type']);
                $_cacheObj->delete($_cacheParam);
            }
        }


        return $this;
    }

    protected function _clearCacheByHttp($moduleName, $methodName = '', array $param = array(), $expire = 3600, $flush = null)
    {
        $data = array(
            self::POST_KEY_MODULE => $moduleName,
            self::POST_KEY_METHOD => $methodName,
            self::POST_KEY_PARAM  => $param,
            self::POST_KEY_EXPIRE => $expire,
            self::POST_KEY_FLUSH  => $flush,
            self::POST_KEY_WORKER => self::WORKER_CLEAR,
        );

        return $this->_post($data);
    }

    /**
     * 模块单例加载
     *
     * @param string $moduleName 模块名
     * @return Q_DAL_Module
     * @throws Q_Exception
     */
    protected function _moduleInstance($moduleName)
    {
        if (empty($this->_moduleInstance[$moduleName])) {
            if (!Q::classExists($moduleName)) {
                throw new Q_Exception('The module "' . $moduleName . '" does not exist!');
            }

            $module = new $moduleName;
            if (!($module instanceof Q_DAL_Module)) {
                throw new Q_Exception('The module "' . $moduleName . '" does not a module!');
            }
            $this->_moduleInstance[$moduleName] = $module;
        } else {
            $module = $this->_moduleInstance[$moduleName];
        }
        return $module;
    }

    /**
     * 原生方式调用模块
     *
     * @param string       $moduleName 模块名
     * @param string|array $methodName 方法
     * @param array        $param      参数
     * @param array        $files      要上传的文件 [field => file]
     * @param bool         $flush      是否更新缓存
     * @throws \Q_Exception
     * @return mixed
     */
    protected function _callByNative($moduleName, $methodName = 'Default', array $param = array(), array $files = array(), $flush = false)
    {
        /**
         * @var Q_DAL_Module
         */
        $module = null;

        if ($this->_appName) {
            #取公共下的APP模块
            try {
                $module = self::_moduleInstance('DAL_' . $this->_appName . '_' . $moduleName);
            } catch (Q_Exception $ex) {
                #取各自应用下的APP模块
                try {
                    $module = self::_moduleInstance($this->_appName . '_DAL_' . $moduleName);
                } catch (Q_Exception $ex) {
                    throw new Q_Exception($ex->getMessage(), $ex->getCode());
                }
            }
        } else {
            $module = self::_moduleInstance('DAL_' . $moduleName);
        }

        $module->setModuleName($moduleName);

        $data = null;
        if (is_array($methodName)) {
            foreach ($methodName as $_methodName => $_param) {
                if (is_string($_param)) {
                    $_methodName = $_param;
                    $_param      = $param;
                }
                $_data              = $this->_getAndSetCacheData($module, $moduleName, $_methodName, $_param, $flush);
                $data[$_methodName] = $_data;
            }
        } else {
            $data = $this->_getAndSetCacheData($module, $moduleName, $methodName, $param, $flush);
        }
        return $data;
    }


    /**
     * HTTP方式调用模块
     *
     * @param string       $moduleName 模块名
     * @param string|array $methodName 方法名
     * @param array        $param      参数
     * @param array        $files      要上传的文件 [field => file]
     * @param bool         $flush      是否更新缓存
     * @throws \Q_Exception
     * @return mixed
     */
    protected function _callByHttp($moduleName, $methodName = 'default', array $param = array(), array $files = array(), $flush = false)
    {
        $data = array(
            self::POST_KEY_MODULE => $moduleName,
            self::POST_KEY_METHOD => $methodName,
            self::POST_KEY_PARAM  => $param,
            self::POST_KEY_FLUSH  => $flush,
            self::POST_KEY_WORKER => self::WORKER_CALL,
        );

        return $this->_post($data, $files);
    }

    protected function _getModuleCacheConfig($moduleName, $methodName)
    {
        if (!isset($this->_cacheConfig[$moduleName][$methodName])) {
            return false;
        }

        $_moduleCacheCfg               = $this->_cacheConfig[$moduleName][$methodName];
        $this->_cacheConfig['DEFAULT'] = empty($this->_cacheConfig['DEFAULT'])
            ? array()
            : $this->_cacheConfig['DEFAULT'];

        if (empty($_moduleCacheCfg['type'])) {
            $_moduleCacheCfg['type'] = empty($this->_cacheConfig[$moduleName]['DEFAULT']['type'])
                ? (empty($this->_cacheConfig['DEFAULT']['type']) ? 'memcache' : $this->_cacheConfig['DEFAULT']['type'])
                : $this->_cacheConfig[$moduleName]['DEFAULT']['type'];
        }

        if (empty($_moduleCacheCfg['expire'])) {
            $_moduleCacheCfg['expire'] = empty($this->_cacheConfig[$moduleName]['DEFAULT']['expire'])
                ? (empty($this->_cacheConfig['DEFAULT']['expire']) ? 3600 : $this->_cacheConfig['DEFAULT']['expire'])
                : $this->_cacheConfig[$moduleName]['DEFAULT']['expire'];
        }

        return $_moduleCacheCfg;
    }

    /**
     * 读取并写入缓存
     *
     * @param \Q_DAL_Module $module     模块对象
     * @param string        $moduleName 模块名
     * @param string        $methodName 方法名
     * @param array         $param      参数
     * @param bool          $flush      是否更新缓存
     * @return mixed
     * @throws Q_Exception
     */
    protected function _getAndSetCacheData(Q_DAL_Module $module, $moduleName, $methodName, $param, $flush = false)
    {
        $methodName = ucfirst($methodName);
        $method     = 'call' . $methodName;

        if (!$module->validate($param)) {
            throw new Q_Exception('validate failed!', -104);
        }

        if (!method_exists($module, $method)) {
            throw new Q_Exception('The method ' . $method . ' does not exist!');
        }

        $module->setMethodName($methodName);

        $_moduleCacheCfg = $this->_getModuleCacheConfig($moduleName, $methodName);
        if (empty($_moduleCacheCfg)) {
            $data = $module->$method($param);
        } else {//有缓存配置
            $_cacheObj = Q_Cache::instance($_moduleCacheCfg['type']);

            $_cacheParam                                      = $param;
            $_cacheParam[self::CACHE_PARAM_KEY_MODULE]        = $moduleName;
            $_cacheParam[self::CACHE_PARAM_KEY_MODULE_METHOD] = $methodName;

            if ($flush) {
                $data = $module->$method($param);
                isset($data) && $_cacheObj->set($_cacheParam, $data, $_moduleCacheCfg['expire']);
            } else {
                $data = $_cacheObj->get($_cacheParam);
                //写入缓存
                if (empty($data)) {
                    $data = $module->$method($param);
                    isset($data) && $_cacheObj->set($_cacheParam, $data, $_moduleCacheCfg['expire']);
                }
            }
        }

        return $data;
    }
}