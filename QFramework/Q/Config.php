<?php

/**
 * 获取配置
 *
 * @author        wiki<wu.kun@zol.com.cn>
 * @copyright (c) 2010-06-22
 * @version       v1.0
 */
class Q_Config
{
    /**
     * 配置缓存
     *
     * @var array
     */
    private static $_cache;

    /**
     * 获取配置
     *
     * @param mixed $key        配置地址
     * @param mixed $arrKeyName 配置子键，可多维，用.号分隔
     * @return array|false
     */
    public static function get($key, $arrKeyName = '')
    {
        if (is_array($key)) {
            $data = array();
            foreach ($key as $_key) {
                $_data = self::get($_key, $arrKeyName);
                if (is_array($_data)) {
                    $data = Q_Array::arrayMergeRecursiveUnique($data, $_data);
                }
            }
            return $data;
        }

        $debugPath = self::_getConfigFilePath($key, true);
        $path      = self::_getConfigFilePath($key);

        if (isset(self::$_cache[$path])) {
            $config = self::$_cache[$path];
        } else if (IS_DEBUGGING && isset(self::$_cache[$debugPath])) {
            $config = self::$_cache[$debugPath];
        } else {

            $config = null;

            if (Q_File::exists($path)) {
                $config = self::_loadConf($path);
            }

            if (IS_DEBUGGING) {
                if (Q_File::exists($debugPath)) {
                    $debugConfig = self::_loadConf($debugPath);
                    if ($config) {
                        $config = Q_Array::arrayMergeRecursiveUnique($config, $debugConfig);
                    }
                }
            }
            self::$_cache[$path] = $config;
        }

        if ($arrKeyName) {
            if (strpos($arrKeyName, '.')) {
                $keyArr = explode('.', $arrKeyName);
                foreach ($keyArr as $key) {
                    if (!$config) {
                        break;
                    }
                    $config = isset($config[$key]) ? $config[$key] : null;
                }
            } else {
                $config = isset($config[$arrKeyName]) ? $config[$arrKeyName] : null;
            }
        }

        return $config;
    }

    /**
     * 写入配置
     *
     * @param string $key   要设置的KEY
     * @param array  $data
     * @param bool   $clean 是否清除原有数据
     * @return bool
     */
    public static function set($key, array $data, $clean = true)
    {
        if (!$clean) {
            $_data = Q_Config::get($key);
            if (is_array($_data)) {
                $data = Q_Array::arrayMergeRecursiveUnique($_data, $data);
            }
        }

        $conf = '<?php' . PHP_EOL . 'return ' . var_export($data, true) . ';';

        $path = self::_getConfigFilePath($key, IS_DEBUGGING);
        return Q_File::write($conf, $path);
    }

    /**
     * 加载配置
     *
     * @param        $path
     * @return array|mixed
     */
    private static function _loadConf($path)
    {
        return include($path);
    }

    private static function _getConfigFilePath($key, $debug = false)
    {
        if (!defined('CONFIG_PATH')) {
            define('CONFIG_PATH', PRODUCTION_ROOT . '/Config');
        }

        $pathPrefx = CONFIG_PATH . '/' . strtr($key, '_', '/');

        return $debug ? $pathPrefx . '.debug.php' : $pathPrefx . '.php';
    }
}
