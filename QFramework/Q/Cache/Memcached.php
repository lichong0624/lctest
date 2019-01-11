<?php
/**
 * Class Q_Cache_Memcached
 */
class Q_Cache_Memcached extends Q_Abstract_Cache
{
    /**
     * @var Memcached
     */
    protected $_masterObj;

    public function init($conf)
    {
        if (empty($this->_masterObj)) {
            $this->_masterObj = new Memcached;

            if (!empty($conf['options'])) {
                $this->_masterObj->setOptions($conf['options']);
            }
            $_servers = $conf['servers'];

            $_hosts   = array_column($_servers, 'host');
            $_ports   = array_column($_servers, 'port');
            $_weights = array_column($_servers, 'weight');

            if ($_hosts) {
                $_servers = array_map(null, $_hosts, $_ports, $_weights);
            }


            $this->_masterObj->addServers($_servers);
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

    public function delete($key)
    {
        return $this->_masterObj->delete($this->makeKey($key));
    }

    public function set($key = '', $var = '', $expire = 3600)
    {
        return $this->_masterObj->set($this->makeKey($key), $this->_makeValue($var, $expire), $expire);
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
        if ($checkClear) {
            foreach ($keys as $_idx => $_key) {
                $data[$_key] = $this->get($_key, $checkClear);
            }
        } else {
            $_keys = array();
            foreach ($keys as $_key) {
                $_keys[$_key] = $this->makeKey($_key);
            }

            $_data = $this->_masterObj->getMulti($_keys);

            $__keys = array_flip($_keys);
            foreach ($_data as $_k => $_v) {
                $data[$__keys[$_k]] = isset($_v['value']) ? $_v['value'] : null;
            }
        }

        return $data;
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
        return $this->_masterObj->add($this->makeKey($key), $this->_makeValue($var, $expire), $expire);
    }
}

