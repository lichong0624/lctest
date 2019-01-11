<?php
/**
 * 缓存抽象类
 * Class Q_Abstract_Cache
 */
abstract class Q_Abstract_Cache
{
    /**
     */
    protected $_masterObj;

    /**
     */
    protected $_slaveObj;

    /**
     * 模块名
     *
     * @var string
     */
    protected $_moduleName = '';

    /**
     * 初始化
     *
     * @param $conf
     * @return $this
     */
    abstract public function init($conf);

    /**
     * 获取数据
     *
     * @param array|string $key
     * @param bool         $checkClear 检查是否被清除
     * @param int          $time       以引用的方式返回数据添加时间
     * @param int          $expire     以引用的方式返回当前值的过期时间
     * @return mixed
     */
    abstract public function get($key, $checkClear = true, &$time = null, &$expire = null);

    /**
     * 批量获取数据
     *
     * @param array $keys       由键组成的数组
     * @param bool  $checkClear 检查是否被清除
     * @return mixed
     */
    abstract public function gets(array $keys, $checkClear = true);

    /**
     * 写入数据
     *
     * @param      $key
     * @param null $value
     * @param int  $expire
     * @return mixed
     */
    abstract public function set($key, $value = null, $expire = 3600);

    /**
     * 批量写入数据
     *
     * @param array $items  键值对数组
     * @param int   $expire 过期时间
     * @return mixed
     */
    abstract public function sets(array $items, $expire = 3600);

    /**
     * 删除数据
     *
     * @param $key
     * @return mixed
     */
    abstract public function delete($key);

    /**
     * 删除所有缓存
     * @return bool
     */
    abstract public function flush();

    /**
     * 在原基础上增加数量
     *
     * @param     $key
     * @param int $value
     * @return mixed
     */
    public function increment($key, $value = 1)
    {
        $value = (int)$value ? $value : 1;
        $val   = (int)$this->get($key, true, $time, $expire);
        return $this->set($key, $val + $value, $expire);
    }

    /**
     * 在原基础上减少数量
     *
     * @param     $key
     * @param int $value
     * @return mixed
     */
    public function decrement($key, $value = 1)
    {
        $value = (int)$value ? $value : 1;
        $val   = (int)$this->get($key, true, $time, $expire);
        return $this->set($key, $val - $value, $expire);
    }


    /**
     * 生成KEY值
     *
     * @param array $param
     * @return string
     */
    public function makeKey($param = null)
    {
        if (empty($param)) {
            return '';
        }

        if (is_array($param)) {
            ksort($param);
            $param = http_build_query($param);
        }

        return md5($this->getModuleName() . $param);
    }

    /**
     * 设置模块名
     *
     * @param string $moduleName
     * @return $this
     */
    public function setModuleName($moduleName = '')
    {
        $this->_moduleName = $moduleName;
        return $this;
    }

    /**
     * 获取模块名
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->_moduleName;
    }

    /**
     * 包装VALUE
     *
     * @param mixed $val
     * @param int   $expire
     * @return array
     */
    final protected function _makeValue($val, $expire = 3600)
    {
        $val = array(
            'value'  => $val,
            'expire' => $expire,
            'time'   => SYSTEM_TIME,
        );
        return $val;
    }

    /**
     * 获取原始的缓存对象
     *
     * @param bool $master
     * @return mixed
     */
    final public function getOriginObj($master = true)
    {
        return $master ? $this->_masterObj : $this->_slaveObj;
    }

}
