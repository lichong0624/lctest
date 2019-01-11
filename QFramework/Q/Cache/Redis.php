<?php
/**
 *
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 15-10-28
 * @version     : $id$
 */
class Q_Cache_Redis extends Q_Abstract_Cache
{
    const CONNECT_TYPE_MASTER = 'master';
    const CONNECT_TYPE_SLAVE  = 'slave';

    /**
     * @var Redis
     */
    protected $_masterObj;

    /**
     * @var Redis
     */
    protected $_slaveObj;


    /**
     * 初始化
     *
     * @param $conf
     * @return $this
     * @throws Q_Exception
     */
    public function init($conf)
    {
        if (empty($conf['master'])) {
            throw new Q_Exception('config is empty!');
        }

        if (!$this->_masterObj || !$this->_slaveObj) {
            $_master = $conf['master'];

            $_masterObj = self::_createConn($_master);


            if (!empty($conf['slaves'])) {
                $_slave    = count($conf['slaves']) > 1 ? array_rand($conf['slaves']) : reset($conf['slaves']);
                $_slaveObj = self::_createConn($_slave);
            } else {
                $_slaveObj = $_masterObj;
            }

            $this->_masterObj = $_masterObj;
            $this->_slaveObj  = $_slaveObj;
        }

        if (!empty($conf['options'])) {
            $this->_setOptions($conf['options']);
        }

        return $this;
    }

    /**
     * 清除所有数据
     *
     * @return bool
     */
    public function flush()
    {
        return $this->_masterObj->flushDB();
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
        $res  = $this->_slaveObj->get($_key);

        $res = unserialize($res);
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
     * @return null|array
     */
    public function gets(array $keys, $checkClear = true)
    {
        $data = array();
        if ($checkClear) {
            foreach ($keys as $_key) {
                $data[$_key] = $this->get($_key, $checkClear);
            }
        } else {
            $_keys = array();
            foreach ($keys as $_key) {
                $_keys[] = $this->makeKey($_key);
            }

            $_data = $this->_slaveObj->mget($_keys);


            $_i = 0;
            foreach ($_data as $_v) {
                $data[$keys[$_i]] = isset($_v['value']) ? $_v['value'] : null;
                $_i++;
            }
        }

        return $data;
    }

    /**
     * 写入数据
     *
     * @param      $key
     * @param null $value
     * @param int  $expire
     * @return mixed
     */
    public function set($key, $value = null, $expire = 3600)
    {
        return $this->_masterObj->set($this->makeKey($key), serialize($this->_makeValue($value, $expire)), $expire);
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

    /**
     * 删除数据
     *
     * @param $key
     * @return void
     */
    public function delete($key)
    {
        $this->_masterObj->delete($this->makeKey($key));
    }

    /**
     * 创建redis对象
     *
     * @param  array $conf 配置 {host => string, port => int, timeout => float}
     * @return Redis
     */
    protected static function _createConn(array $conf)
    {
        $_obj = new Redis();

        $_connectFunc = empty($conf['pconnect']) ? 'connect' : 'pconnect';
        $_obj->$_connectFunc($conf['host'], $conf['port'], $conf['timeout']);
        if (!empty($conf['auth'])) {
            $_obj->auth($conf['auth']);
        }
        return $_obj;
    }

    protected function _setOptions(array $options)
    {
        foreach ($options as $_key => $_val) {
            $this->_masterObj->setOption($_key, $_val);
            $this->_slaveObj->setOption($_key, $_val);
        }
    }
}