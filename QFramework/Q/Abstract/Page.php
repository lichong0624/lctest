<?php

abstract class Q_Abstract_Page
{
    /*
    |---------------------------------------------------------------
    | Array of action permitted by mgr subclass.
    |---------------------------------------------------------------
    | @access  private
    | @var     array
    |
    */

    /**
     * 缓存时间，为false不缓存，为0永久缓存
     *
     * @var false|int|[false]|[int]
     */
    protected $_cache = false;


    protected $_url;

    /**
     * 输入流对象
     *
     * @var Q_Request
     */
    public $request;

    /**
     * 输出流对象
     *
     * @var Q_Response
     */
    public $response;

    const STATUS_SUCCESS = 'success';
    const STATUS_INFO    = 'info';
    const STATUS_WARNING = 'warning';
    const STATUS_DANGER  = 'danger';
    const STATUS_ERROR   = 'error';

    private static $_optId = 0;

    /**
     * 获取当前链接
     *
     * @return mixed
     */
    public function getUrl()
    {
        if (empty($this->_url)) {
            $this->_setUrl();
        }

        return $this->_url;
    }

    /**
     * 设置当前链接
     *
     * @param mixed $url
     * @return $this
     */
    protected function _setUrl($url = '')
    {
        $url        = $url ? $url : Q_Http::currentUrl();
        $this->_url = $url;
        return $this;
    }


    /*
    |---------------------------------------------------------------
    | Specific validations are implemented in sub classes.
    |---------------------------------------------------------------
    | @param   Q_Request     $req    Q_Request object received from user agent
    | @return  boolean
    |
    */
    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    /**
     * 页面是否缓存，返回false,不缓存，返回0，永久缓存,大于0,返回缓存周期时间
     *
     * @return false|int
     */
    public function getExpire()
    {
        $execName = $this->request->getExecName();
        if (!empty($execName)) {
            return false;
        }


        if (!is_array($this->_cache)) {
            return is_numeric($this->_cache) ? (int)$this->_cache : false;
        }

        $_act = $this->request->getActionName();

        if (is_array($this->_cache) && isset($this->_cache[$_act])) {

            return is_numeric($this->_cache[$_act]) ? (int)$this->_cache[$_act] : false;
        }

        return false;
    }


    public function getCache()
    {
        if (!$this->_isExpired()) {
            $key  = $this->getCacheKey();
            $data = Q_File::get($key);
            return gzinflate($data);
        }
        return false;
    }

    public function setCache($html)
    {
        $key = $this->getCacheKey();


        if (!$html) {
            return false;
        }
        $html = gzdeflate($html, 9);
        return Q_File::write($html, $key);
    }

    public function getCacheKey(array $param = null)
    {
        isset($param) || $param = $this->request->get();

        unset($param['controller'], $param['action']);

        $param['c'] = $this->request->getControllerName();
        $param['a'] = $this->request->getActionName();
        ksort($param);
        $key = md5(http_build_query($param));
        $key = VAR_PATH . 'html/' . APP_NAME . '/'
               . chunk_split(substr($key, 0, 4), 2, '/')
               . substr($key, 4) . '.html';
        return $key;
    }

    /**
     * 获取对象人ID
     *
     * @return int
     */
    public static function getOptId()
    {
        return self::$_optId;
    }

    public static function setOptId(int $id)
    {
        if (!empty($id)) {
            self::$_optId = $id;
        }
    }

    /**
     * 检查缓存是否过期
     *
     * @return bool true 过期| false没过期
     * @throws Q_Exception
     */
    protected function _isExpired()
    {
        $expire = $this->getExpire();
        $key    = $this->getCacheKey();

        if (empty($key)) {
            throw new Q_Exception('the cache key is empty!');
        }

        return $expire === false || !file_exists($key) || ($expire !== 0 && filemtime($key) + $expire < SYSTEM_TIME);
    }
}
