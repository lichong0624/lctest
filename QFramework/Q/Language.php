<?php
/**
 * 语言检测 并自动加载语言包
 *
 * @author      : hanqiang<hanqiang@dalingpao.com>
 * @copyright(c): 16-3-7
 * @version     : $id$
 *
 *              语言包路径结构为 /Lang/语言种类/Global.php (|CName/AName.php)
 */
class Q_Language
{
    //config 的配偶项KEY
    const CONFIG_KEY               = 'LANGUAGE';
    const OPTION_AUTO_DETECT       = 'LANG_AUTO_DETECT';// 自动侦测语言 开启多语言功能后有效
    const OPTION_LIST              = 'LANG_LIST';// 允许切换的语言列表 用逗号分隔
    const OPTION_DEFAULT           = 'LANG_DEFAULT';// 默认语言
    const OPTION_VAR               = 'LANG_VAR';// 默认语言切换变量
    const OPTION_PLACEHOLDER_START = 'PLACEHOLDER_START';// 默认开始占位符
    const OPTION_PLACEHOLDER_END   = 'PLACEHOLDER_END';// 默认结束占位符

    //语言系
    const LANG_ZH_CN = 'zh-cn';//中文
    const LANG_ZH_TW = 'zh-tw';//台湾
    const LANG_EN    = 'en';//英文
    const LANG_AR    = 'ar';//阿拉伯
    const LANG_CS    = 'cs';//捷克
    const LANG_DE    = 'de';//捷克
    const LANG_EL    = 'el';//希腊
    const LANG_ES    = 'es';//西班牙
    const LANG_FI    = 'fi';//芬兰
    const LANG_FR    = 'fr';//法国
    const LANG_JA    = 'ja';//日本
    const LANG_kO    = 'ko';//韩国
    const LANG_IT    = 'it';//意大利
    const LANG_NL    = 'nl';//荷兰
    const LANG_NO    = 'no';//挪威
    const LANG_PL    = 'pl';//波兰
    const LANG_PT    = 'pt';//葡萄牙
    const LANG_RU    = 'ru';//俄国
    const LANG_SV    = 'sv';//瑞典
    const LANG_TH    = 'th';//泰国
    const LANG_TR    = 'tr';//土耳其
    const LANG_VI    = 'vi';//越南
    //语言包功能开关
    protected static $_enable = false;

    protected static $_options = array(
        self::OPTION_AUTO_DETECT       => true,      // 自动侦测语言 开启多语言功能后有效
        self::OPTION_LIST              => '*',       // 允许切换的语言列表 用逗号分隔
        self::OPTION_DEFAULT           => self::LANG_ZH_CN,  // 默认语言
        self::OPTION_VAR               => '',     // 默认语言切换变量
        self::OPTION_PLACEHOLDER_START => '{',     // 默认开始占位符
        self::OPTION_PLACEHOLDER_END   => '}',     // 默认结束占位符
    );

    //语言系列表
    protected static $_languageGroup = array(
        self::LANG_ZH_CN => 'zh-cn',
        self::LANG_ZH_TW => 'zh-tw',
        self::LANG_EN    => 'en',
        self::LANG_AR    => 'ar',
        self::LANG_CS    => 'cs',
        self::LANG_DE    => 'de',
        self::LANG_EL    => 'el',
        self::LANG_ES    => 'es',
        self::LANG_FI    => 'fi',
        self::LANG_FR    => 'fr',
        self::LANG_JA    => 'ja',
        self::LANG_kO    => 'ko',
        self::LANG_IT    => 'it',
        self::LANG_NL    => 'nl',
        self::LANG_NO    => 'no',
        self::LANG_PL    => 'pl',
        self::LANG_PT    => 'pt',
        self::LANG_RU    => 'ru',
        self::LANG_SV    => 'sv',
        self::LANG_TH    => 'th',
        self::LANG_TR    => 'tr',
        self::LANG_VI    => 'vi',
    );

    //语言包数据缓存
    private static $_cache = array();

    //文件路径名
    private static $_pathRootName = 'Lang';

    //当前语言
    protected static $_curLanguage = '';

    /**
     * 单例
     *
     * @var Q_Language
     */
    protected static $_instance = array();

    /**
     * 获取配置项参数
     *
     * @return array
     */
    public static function getOptions()
    {
        return self::$_options;
    }

    /**
     * 设置配置项参数
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (!empty($options)) {
            self::$_options = array_merge(self::$_options, $options);
        }
        return $this;
    }

    /**
     * 获取开启状态
     *
     * @return mixed
     */
    public static function getEnable()
    {
        return self::$_enable;
    }

    /**
     * 设置开启状态
     *
     * @param mixed $enable
     * @return $this
     */
    public function setEnable($enable = false)
    {
        self::$_enable = $enable;
        return $this;
    }

    /**
     * 获取当前设置语言
     *
     * @return string
     */
    public static function getCurLanguage()
    {
        return self::$_curLanguage;
    }

    /**
     * 设置当前语言
     *
     * @param string $curLanguage
     */
    public static function setCurLanguage($curLanguage)
    {
        self::$_curLanguage = $curLanguage;
    }

    /**
     * 获取语言系列表
     *
     * @param string $key
     * @return array
     */
    public static function getLanguageList($key = '')
    {
        return !empty($key) && isset(self::$_languageGroup[$key]) ? self::$_languageGroup[$key] : self::$_languageGroup;
    }

    /**
     * 单例
     *
     * @param string $language
     * @return Q_Language
     */
    public static function instance($language = 'default')
    {
        if (!isset(self::$_instance[$language])) {
            $lang                       = new Q_Language();
            self::$_instance[$language] = $lang;
        } else {
            $lang = self::$_instance[$language];
        }

        return $lang;
    }

    public function __construct()
    {
        //获取config的语言配置
        $config = Q_Config::get('Global', self::CONFIG_KEY);
        !empty($config) && $this->setOptions($config);
    }

    /**
     * 设置语言包数据内容
     *
     * @param string $path         路径
     * @param array  $languageData 设置的语言数据
     * @param bool   $clean        是否覆盖模式
     * @return bool|int
     */
    public static function set($path = '', array $languageData = array(), $clean = false)
    {
        if (is_file($path)) {
            //非覆盖模式
            if (!$clean) {
                $_data = include $path;
                if (is_array($languageData)) {
                    $languageData = Q_Array::arrayMergeRecursiveUnique($_data, $languageData);
                }
            }

            $data = '<?php' . PHP_EOL . 'return ' . var_export($languageData, true) . ';';
            return Q_File::write($data, $path);//写入文件
        }

        return false;
    }

    /**
     * 获取语言包数组
     *
     * @param string|array $key     需要获取的KEY
     * @param string|array $replace 对应替换的数据
     * @return array|bool|mixed|string
     */
    public static function get($key = null, $replace = null)
    {
        //加载语言包配置
        if (!self::_setCurLanguage()) {
            return false;
        }
        //读取数据
        $data = self::_getData();

        //传进获取条件
        if (!empty($key)) {
            return self::_parserKey($data, $key, $replace);
        }

        return $data;
    }

    /**
     * 自定义路径获取语言包
     *
     * @param string $path    加载路径
     * @param string $key     需要获取的KEY
     * @param null   $replace 需要替换的数据
     * @param bool   $merge   合并模式
     * @return array|bool|mixed|string
     */
    public static function getByPath($path = '', $key = '', $replace = null, $merge = false)
    {
        //加载语言包配置
        if (!self::_setCurLanguage()) {
            return false;
        }

        $_data = array();
        //加载路径文件数据
        if (is_string($path)) {
            $_data = self::_getByPath($path);
        } else if (is_array($path)) {
            foreach ($path as $_pathValue) {
                $_data = array_merge($_data, self::_getByPath($_pathValue));
            }
        }

        //合并模式
        if ($merge) {
            //自动读取数据
            $dataAuto = self::_getData();
            $_data    = array_merge($dataAuto, $_data);
        }

        if (!empty($key)) {
            return self::_parserKey($_data, $key, $replace);
        }

        return $_data;
    }

    /**
     * 根据路径加载语言包
     *
     * @param string $path 路径
     * @return bool|mixed
     * @throws Q_Exception
     */
    protected static function _getByPath($path)
    {
        if (isset(self::$_cache[$path])) {
            return self::$_cache[$path];
        }

        if (empty($path) || $path[0] == '~') {//当前对应控制器下方法下的目录
            $input   = Q_Request::instance();
            $_string = strtr($input->getControllerName(), '_', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            if (empty($path)) {
                $path = $_string . ucfirst($input->getActionName());
            } else if ($path[0] == '~') {
                $path = $_string . ucfirst(substr($path, 2));
            }
            $filePath = APP_PATH . self::$_pathRootName . DIRECTORY_SEPARATOR . self::getCurLanguage() . DIRECTORY_SEPARATOR;
            $path     = $filePath . $path . '.php';
        }

        if (!is_file($path)) {//定义了路径，如果不存在该文件，返回空数组
            return array();
        }

        self::$_cache[$path] = include $path;

        return self::$_cache[$path];
    }

    /**
     * 解析语言包数据返回相应的值
     *
     * @param array          $data    源数据
     * @param   string|array $key     传入需要获取的KEY
     * @param string|array   $replace 传入需要替换的值
     * @return array|bool
     */
    protected static function _parserKey(array $data = array(), $key, $replace = null)
    {
        if (empty($data)) {
            return false;
        }
        $keys     = (array)$key;
        $replaces = (array)$replace;

        $returns = array();

        foreach ($keys as $_idx => $_key) {
            $_key     = strtoupper($_key);
            $_replace = empty($replaces[$_idx]) ? null : (array)$replaces[$_idx];

            if (!isset($data[$_key])) {
                $returns[$_key] = $_key;
                continue;
            }

            $format = $data[$_key];
            $option = self::getOptions();

            //占位符替换
            if (strpos($format, $option[self::OPTION_PLACEHOLDER_START]) && strpos($format, $option[self::OPTION_PLACEHOLDER_END])) {
                foreach ($replaces as $_subKey => $_subReplace) {
                    $returns[$_key] = str_replace("{$option[self::OPTION_PLACEHOLDER_START]}{$_subKey}{$option[self::OPTION_PLACEHOLDER_END]}", $_subReplace, $format);
                }
                continue;
            }

            if ($_replace === false || $_replace === null) {
                $returns[$_key] = $format;
                continue;
            }

            //sprintf替换
            $returns[$_key] = call_user_func_array('sprintf', array_merge((array)$format, $_replace));
        }

        return count($returns) < 2 ? reset($returns) : $returns;
    }

    /**
     * 加载语言配置 设置当前语言环境
     * 检查浏览器支持语言，并自动加载语言包
     *
     * @access private
     * @return bool
     */
    protected static function _setCurLanguage()
    {
        $option = self::getOptions();

        // 不开启语言包功能，仅仅加载框架语言包直接返回
        if (!self::getEnable()) {
            return false;
        }

        $input   = Q_Request::instance();
        $langSet = $option[self::OPTION_DEFAULT];
        // 启用了语言包功能
        // 根据是否启用自动侦测设置获取语言选择
        if ($option[self::OPTION_AUTO_DETECT]) {

            if (!empty($option[self::OPTION_VAR])) {
                $_langSet = $input->get($option[self::OPTION_VAR]);// url中设置了语言变量
                !empty($_langSet) && $langSet = $_langSet;
                Q_Cookie::set('__LANGUAGE__', $langSet, 3600);
            } elseif (Q_Cookie::get('__LANGUAGE__')) {// 获取上次用户的选择
                $langSet = Q_Cookie::get('__LANGUAGE__');
            } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {// 自动侦测浏览器语言
                preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                $langSet = $matches[1];
                Q_Cookie::set('__LANGUAGE__', $langSet, 3600);
            }

            if ($option[self::OPTION_LIST] != '*' && false === stripos($option[self::OPTION_LIST], $langSet)) { // 非法语言参数,设置默认语言
                $langSet = $option[self::OPTION_DEFAULT];
            }
        }
        // 定义当前语言
        self::setCurLanguage(strtolower($langSet));

        return true;
    }


    /**
     *  读取语言包数据
     *
     * @return mixed | array
     */
    protected static function _getData()
    {
        $group    = '';
        $input    = Q_Request::instance();
        $cacheKey = self::getCurLanguage() . $input->getControllerName() . $input->getActionName();

        if (!isset(self::$_cache[$cacheKey])) {

            $path = APP_PATH . self::$_pathRootName . DIRECTORY_SEPARATOR . self::getCurLanguage() . DIRECTORY_SEPARATOR;

            // 读取项目公共语言包
            $_global = array();
            if (is_file(LANG_PATH . self::getCurLanguage() . DIRECTORY_SEPARATOR . 'Global.php')) {
                $_global = include LANG_PATH . self::getCurLanguage() . DIRECTORY_SEPARATOR . 'Global.php';
            }

            // 读取分组公共语言包
            $_appGlobal = array();
            if (is_file($path . 'Global.php')) { // 独立分组
                $file       = $path . 'Global.php';
                $_appGlobal = include $file;
            }

            // 模块分组
            $_appCtl  = array();
            $_ctlName = ucfirst($input->getControllerName());

            if (is_file($path . $_ctlName . '.php')) {
                $file    = $path . $_ctlName . '.php';
                $group   = $_ctlName . DIRECTORY_SEPARATOR;
                $_appCtl = include $file;
            }

            // 读取当前模块下action的语言包
            $_actName = ucfirst($input->getActionName());
            $_appAct  = array();
            if (is_file($path . $group . $_actName . '.php')) {
                $_appAct = include $path . $group . $_actName . '.php';
            }

            self::$_cache[$cacheKey] = array_merge($_global, $_appGlobal, $_appCtl, $_appAct);
        }

        return self::$_cache[$cacheKey];
    }
}