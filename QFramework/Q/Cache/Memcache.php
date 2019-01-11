<?php
/**
 * Class Q_Cache_Memcache
 */
class Q_Cache_Memcache extends Q_Abstract_Cache
{
    protected static $_servers = array();

    /**
     * @var Memcache
     */
    protected $_masterObj;

    public function init($conf)
    {
        if (empty($this->_masterObj)) {
            $this->_masterObj = new Memcache;
            if (!empty($conf['servers'])) {
                self::$_servers = $conf['servers'];
            }
            foreach (self::$_servers as $_server) {
                $this->_masterObj->addServer($_server['host'], $_server['port']);
            }
            $this->_slaveObj = $this->_masterObj;
        }
        return $this;
    }

    public function flush()
    {
        return $this->_masterObj->flush();
    }

    /**
     * 获取数据
     *
     * @param array|string $key
     * @param bool         $checkClear 检查是否被清除
     * @param int          $time       以引用的方式返回数据添加时间
     * @param int          $expire     以引用的方式返回当前值的过期时间
     * @return mixed
     */
    public function get($key, $checkClear = true, &$time = null, &$expire = null)
    {
        $_key = $this->makeKey($key);
        $res  = $this->_masterObj->get($_key);

        if ($res && isset($res['value'])) {
            $time   = $res['time'];
            $expire = $res['expire'];
            if ($checkClear && Q_Cache::checkClear($key, $time)) {
                unset($res);
                return null;
            }
            return $res['value'];
        }

        return null;
    }


    /**
     * 批量获取数据
     *
     * @param array $keys       由键组成的数组
     * @param bool  $checkClear 检查是否被清除
     * @return mixed
     */
    public function gets(array $keys, $checkClear = true)
    {
        $data = array();
        foreach ($keys as $_key) {
            $data[$_key] = $this->get($_key, $checkClear);
        }
        return $data;
    }


    public function delete($key)
    {
        return $this->_masterObj->delete($this->makeKey($key));
    }

    public function set($key = '', $var = '', $expire = 3600)
    {
        return $this->_masterObj->set($this->makeKey($key), $this->_makeValue($var, $expire), 0, $expire);
    }

    /**
     * 批量写入数据
     *
     * @param array $items  键值对数组
     * @param int   $expire 过期时间
     * @return mixed
     */
    public function sets(array $items, $expire = 3600)
    {
        foreach ($items as $_key => $_val) {
            $this->set($_key, $_val, $expire);
        }

        return true;
    }


    public function add($key = '', $var = '', $expire = 3600)
    {
        return $this->_masterObj->add($this->makeKey($key), $this->_makeValue($var, $expire), 0, $expire);
    }

}

