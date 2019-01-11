<?php
if (PHP_SAPI !== 'cli') {
    ob_start('ob_gzhandler');
}

if (!defined('IN_PRODUCTION')) {
    die('Hacking attempt');
}

if (PHP_VERSION < '5.5.16') {
    die ('The PHP version is ' . PHP_VERSION . '! Plz upgrade it to 5.5.16 or newer version!');
}

if (!defined('PRODUCTION_ROOT') || !defined('APP_NAME')) {
    die('please define "PRODUCTION_ROOT" and "APP_NAME" !');
}

/*
|---------------------------------------------------------------
| For security
|---------------------------------------------------------------
*/
ini_set('allow_url_fopen', 0);

defined('IMAGETYPE_WEBP') || define('IMAGETYPE_WEBP', 18);

date_default_timezone_set('PRC');
define('SYSTEM_TIME', isset($_SERVER['REQUEST_TIME']) ? (int)$_SERVER['REQUEST_TIME'] : time());
define('SYSTEM_DATE', date('Y-m-d H:i:s', SYSTEM_TIME));

define('PAGE_REQUEST_TIME', microtime(true));
define('SYSTEM_PATH', __DIR__);
defined('COOKIE_DOMAIN') || define('COOKIE_DOMAIN', '');
defined('SYSTEM_CHARSET') || define('SYSTEM_CHARSET', 'UTF-8');
//定义系统首页
defined('SYSTEM_HOMEPAGE') || define('SYSTEM_HOMEPAGE', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');

//定义产品相关目录
define('APPS_ROOT', PRODUCTION_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR);//所有应用根目录
define('APP_PATH', APPS_ROOT . APP_NAME . DIRECTORY_SEPARATOR);//应用目录
define('LIB_PATH', PRODUCTION_ROOT . DIRECTORY_SEPARATOR . 'Lib' . DIRECTORY_SEPARATOR);//LIB目录
define('DAL_PATH', PRODUCTION_ROOT . DIRECTORY_SEPARATOR . 'DAL' . DIRECTORY_SEPARATOR);//DAL目录
define('MODEL_PATH', PRODUCTION_ROOT . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR);//Model目录
define('EXTENSION_PATH', PRODUCTION_ROOT . DIRECTORY_SEPARATOR . 'Extension' . DIRECTORY_SEPARATOR);//Extension目录
define('VAR_PATH', PRODUCTION_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR);//VAR目录
define('TMP_PATH', VAR_PATH . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR);//VAR目录
define('SKIN_ROOT', APP_PATH . 'Skin' . DIRECTORY_SEPARATOR);//模板根目录
define('LANG_PATH', PRODUCTION_ROOT . DIRECTORY_SEPARATOR . 'Lang' . DIRECTORY_SEPARATOR);//语言包目录
define('APP_PAGE_ROOT', APP_PATH . 'Page' . DIRECTORY_SEPARATOR);//应用page目录
define('APP_PLUGIN_ROOT', APP_PATH . 'Plugin' . DIRECTORY_SEPARATOR);//应用Plugin目录
define('APP_SKIN_ROOT', SKIN_ROOT);//应用模板目录

define('IS_DEBUGGING', is_file(VAR_PATH . 'DEBUG') || is_file(VAR_PATH . APP_NAME . '.DEBUG'));//调试模式
define('IS_PRODUCTION', is_file(VAR_PATH . 'PRODUCT') || is_file(VAR_PATH . APP_NAME . '.PRODUCT'));//生产模式

$versionFile = VAR_PATH . 'VERSION';
define('VERSION', file_exists($versionFile) ? file_get_contents($versionFile) : '');//定义版本
define('LAST_UPDATE_TIME', is_file($versionFile) ? filemtime($versionFile) : SYSTEM_TIME);//定义最后更新时间

//注册命名空间
Q::setNameSpace(LIB_PATH);#注册类库
Q::setNameSpace(APP_PATH);#注册应用
Q::setNameSpace(DAL_PATH);#注册DAL实例
Q::setNameSpace(MODEL_PATH);#注册Model实例

spl_autoload_register(array('Q', 'autoload'));

Q_Exception::register();
Q_Error::register();

class Q
{
    const NAMESPACE_TYPE_ANALOG = 0;
    const NAMESPACE_TYPE_MIXED  = 1;
    const NAMESPACE_TYPE_REAL   = 2;

    /**
     * 已加载的类
     *
     * @var array
     */
    private static $_loadedClass = array();

    private static $_namespace = array();

    private static $_namespaceType = self::NAMESPACE_TYPE_ANALOG;

    public static function setNamespaceType($type = self::NAMESPACE_TYPE_ANALOG)
    {
        self::$_namespaceType = $type;
    }

    public static function formatClassName($name)
    {
        if (version_compare(PHP_VERSION, '5.5.16', '>=')) {
            $name = ucwords($name, '_');
        } else {
            $name = strtr(ucwords(strtr($name, '_', ' ')), ' ', '_');
        }
        return $name;
    }

    public static function classExists(&$class, $autoload = true)
    {
        if (!class_exists($class, $autoload)) {
            if (false !== strpos($class, '_')) {
                $class = strtr($class, '_', '\\');
                return class_exists($class, $autoload);
            }
        }
        return true;
    }

    public static function interfaceExists(&$class, $autoload = true)
    {
        if (!interface_exists($class, $autoload)) {
            if (false !== strpos($class, '_')) {
                $class = strtr($class, '_', '\\');
                return interface_exists($class, $autoload);
            }
        }
        return true;
    }

    /*
    |---------------------------------------------------------------
    |  Loads a class or interface file from the include_path.
    |---------------------------------------------------------------
    | @param string $name A Q (or other) class or interface name.
    | @return void
    */
    /**
     * @param $name
     * @throws Q_Exception
     */
    public static function autoload($name)
    {
        if (trim($name) == '') {
            new Q_Exception('No class or interface named for loading');
        }

        //处理目录名大写开头

        if (strpos($name, '.')) {
            $name = strtr($name, '.', '_');
        }

        $name = self::formatClassName($name);

        if (isset(self::$_loadedClass[$name])) {
            self::$_loadedClass[$name]++;
            return;
        }

        $namespace = strstr($name, '_', true);

        $file         = '';
        $_nsSeparator = '_';
        // 对Q一种处理
        if ($namespace == 'Q') {
            $file = SYSTEM_PATH . DIRECTORY_SEPARATOR . strtr($name, ['_' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR]) . '.php';
        } elseif (!empty(self::$_namespace[DIRECTORY_SEPARATOR . $namespace])) {// 对个性的命名空间做处理
            $_nsSeparator = strpos($name, '\\') !== false ? '\\' : '_';
            $file         = self::$_namespace[DIRECTORY_SEPARATOR . $namespace] . strtr(strstr($name, $_nsSeparator), ['_' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR]) . '.php';
        }


        if (!$file || !is_file($file)) {
            return;
        }

        include $file;
        if (!self::classExists($name, false) && !self::interfaceExists($name, false)) {
            throw new Q_Exception('Class or interface does not exist in loaded file');
        }

        if (empty(self::$_loadedClass[$name])) {
            self::$_loadedClass[$name] = 1;
        }
    }

    /**
     * 使用namespace方法实现每个实例的命名空间映射
     *
     * @param string|array $path
     * @param string $alias
     */
    public static function setNameSpace($path, $alias = '')
    {
        if (empty($path)) {
            return;
        }

        if (is_array($path)) {
            foreach ($path as $_path) {
                self::setNameSpace($_path);
            }
            return;
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR);

        if ($alias) {
            $alias = DIRECTORY_SEPARATOR . $alias;
        }

        $namespace = $alias ? $alias : strrchr($path, DIRECTORY_SEPARATOR);#namespace = /Namespace

        self::$_namespace[$namespace] = $path;
    }

    public static function getNameSpacePath($alias)
    {
        if ($alias) {
            $alias = DIRECTORY_SEPARATOR . $alias;
        }

        return empty(self::$_namespace[$alias]) ? null : self::$_namespace[$alias];
    }

    public static function getLoadedClass()
    {
        return self::$_loadedClass;
    }

    public static function debug($var, $end = true, $highlight = true)
    {
        if (IS_DEBUGGING) {
            $trace = debug_backtrace();
            $echo  = "<pre>file:{$trace[0]['file']}\n<br>line:{$trace[0]['line']}\n<br><hr>output:\n<br>";
            $echo  = Q_Request::resolveType() == Q_Request::CLI ? Q_String::clean($echo) : $echo;
            echo $echo;
            if (Q_Request::resolveType() != Q_Request::CLI && $highlight) {
                Q_VarDumper::dump($var, 10, $highlight);
            } else {
                var_dump($var);
            }
            $end && self::end();
        }
    }

    /**
     * 加载第三方扩展
     *
     * @param $name
     * @param $initFileName
     */
    public static function loadExtension($name, $initFileName = 'init')
    {
        $_path = EXTENSION_PATH . $name . DIRECTORY_SEPARATOR;
        if (is_array($initFileName)) {
            foreach ($initFileName as $_file) {
                $_file = $_path . $_file . '.php';
                if (is_file($_file)) {
                    require_once $_file;
                }
            }
        } else {
            $_file = $_path . $initFileName . '.php';
            if (is_file($_file)) {
                require_once $_file;
            }
        }

    }

    /**
     * 终止运行
     */
    public static function end()
    {
        exit();
    }
}

