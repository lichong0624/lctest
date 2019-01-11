<?php
/**
 * 缓存工厂类
 * <pre>
 * //用例
 * $cacheObj = Q_Cache::instance('memcache')->setModule('myModule');
 * $cacheObj->set(array('id' => 1), array('name' => 'wiki'), 3600);//添加数据
 * $cacheObj->get(array('id' => 1));//获取数据
 * $cacheObj->delete(array('id' => 1));//删除数据
 * $cacheObj->increment(array('id' => 1), 1);//增加数量
 * $cacheObj->decrement(array('id' => 1), 1);//减少数量
 * </pre>
 *
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 15/1/7
 * @version     : $id$
 */
class Q_Cache
{
    const MANAGER_MODULE_NAME = '_MANAGER_';
    const MANAGER_CLEAR_NAME  = 'CLEAR_PARAMS';

    protected static $_clearParams = array();

    /**
     * @var Q_Abstract_Cache[] ;
     */
    protected static $_instance;

    /**
     * @var Q_Abstract_Cache
     */
    protected static $_curCacheObj;


    /**
     * @var Q_Abstract_Cache
     */
    protected static $_cacheMgr;

    /**
     * @return Q_Abstract_Cache
     */
    public static function getCurCacheObj()
    {
        return self::$_curCacheObj;
    }

    /**
     * @param Q_Abstract_Cache $curCacheObj
     */
    public static function setCurCacheObj($curCacheObj)
    {
        self::$_curCacheObj = $curCacheObj;
    }

    /**
     * 单例
     *
     * @param null $type
     * @return \Q_Abstract_Cache
     * @throws \Q_Exception
     */
    public static function instance($type = null)
    {
        $_type = $type;
        $_manager = false;
        if ($_type == 'MANAGER') {
            $_manager = true;
            $_type     = Q_Config::get('Cache', $_type);
        }

        if (empty($_type)) {
            $_type = Q_Config::get('Cache', 'DEFAULT');
        }

        if (!$_type) {
            throw new Q_Exception("The type can not be set to empty!");
        }

        if (!isset(self::$_instance[$type])) {
            $conf = Q_Config::get('Cache', $_type);

            if (empty($conf)) {
                throw new Q_Exception("The '{$_type}' type cache config does not exists!");
            }

            $class = 'Q_Cache_' . ucfirst($_type);
            $obj   = new $class();

            if (!$obj instanceof Q_Abstract_Cache) {
                throw new Q_Exception("The '{$class}' not instanceof Q_Abstract_Class!");
            }

            $obj->init($conf);
            self::$_instance[$type] = $obj;

        } else {
            $obj = self::$_instance[$type];
        }

        if (!$_manager) {
            self::setCurCacheObj($obj);
        }

        return $obj;
    }

    /**
     * 初始化管理器缓存
     *
     * @return Q_Abstract_Cache
     * @throws Q_Exception
     */
    public static function initManagerCache()
    {
        $obj = self::instance('MANAGER');
        $obj->setModuleName(self::MANAGER_MODULE_NAME);
        return $obj;
    }

    public static function getClearParams()
    {
        if (!empty(self::$_clearParams)) {
            return self::$_clearParams;
        }

        $data               = self::initManagerCache()->get(self::MANAGER_CLEAR_NAME, false);
        self::$_clearParams = $data;

        return $data;
    }

    protected static function _setClearParam($param)
    {
        $_key = self::initManagerCache()->makeKey($param);
        self::$_clearParams[$_key] = array(
            'param' => $param,
            'time'  => SYSTEM_TIME,
        );
    }

    /**
     * 设置清除规则
     *
     * @param string|array $param  参数
     * @param int          $expire 规则有效期
     */
    public static function clear($param, $expire = 3600)
    {
        self::_setClearParam($param);
        self::initManagerCache()->set(self::MANAGER_CLEAR_NAME, self::getClearParams(), $expire);
    }

    /**
     * 删除清除规则
     *
     * @param array|string $param
     */
    public static function emptyClear($param = array())
    {
        $mgrCacheObj = self::initManagerCache();
        $_key        = $mgrCacheObj->makeKey($param);
        $data        = $mgrCacheObj->get(self::MANAGER_CLEAR_NAME, false, $time, $expire);
        unset($data[$_key]);
        self::$_clearParams = $data;
        $mgrCacheObj->set(self::MANAGER_CLEAR_NAME, $data, $expire);
    }

    /**
     * 删除所有清除规则
     *
     */
    public static function emptyClears()
    {
        $mgrCacheObj = self::initManagerCache();

        self::$_clearParams = array();
        $mgrCacheObj->delete(self::MANAGER_CLEAR_NAME);
    }

    /**
     * 检查要清除的数据，如果需要清除，直接删除数据，需要清除返回true,否则返回false
     *
     * @param array|string $param 要检查的KEY
     * @param int          $time  数据创建时间
     * @return bool
     */
    public static function checkClear($param, $time = 0)
    {
        $clearParams = self::getClearParams();
        $_key        = self::initManagerCache()->makeKey($param);
        if (!$clearParams) {
            return false;
        }



        //完整匹配
        if ($clearParams && !empty($clearParams[$_key]) && $clearParams[$_key]['time'] >= $time) {
            self::getCurCacheObj()->delete($param);
            return true;
        }

        if ($clearParams && is_array($param)) {
            foreach ($clearParams as $_param) {
                $_time  = $_param['time'];
                $_param = $_param['param'];

                if (empty($_param) || !is_array($_param) || $_time < $time) {
                    continue;
                }

                //存在交集
                if ($_param == array_intersect_assoc($_param, $param)) {
                    self::getCurCacheObj()->delete($param);
                    return true;
                }
            }

        }

        return false;

    }
}