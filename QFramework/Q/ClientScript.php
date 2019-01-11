<?php

/**
 * 客户端代码
 *
 * @author       wiki <charmfocus@gmail.com>
 * @copyright(c) 14/11/11
 */
class Q_ClientScript
{
    /**
     * 文件要
     */
    const POS_HEADER = 'header';
    const POS_FOOTER = 'footer';

    /**
     * js文件类型
     *
     * @var string
     */
    const FILE_TYPE_JS = 'js';

    /**
     * css文件类型
     *
     * @var string
     */
    const FILE_TYPE_CSS = 'css';

    const REVERSION_CTL_GRAINSIZE_COARSE = 1;
    const REVERSION_CTL_GRAINSIZE_FINE   = 2;

    private static $_reversionCtlGrainSize = self::REVERSION_CTL_GRAINSIZE_COARSE;

    private static $_urlPrefix = '';

    private static $_files = array();

    private static $_fileHtmlTpl = array(
        self::FILE_TYPE_JS  => '<script type="text/javascript" src="{URL}"></script>',
        self::FILE_TYPE_CSS => '<link href="{URL}" rel="stylesheet" type="text/css"/>',
    );

    private static $_useRevision = true;

    private static $_revision = null;

    /**
     * 设置是否使用Revision
     *
     * @param bool $rev
     */
    public static function useRevision($rev = true)
    {
        self::$_useRevision = $rev;
    }

    public static function setReversionCtlGrainSize($grainsize = self::REVERSION_CTL_GRAINSIZE_COARSE)
    {
        self::$_reversionCtlGrainSize = $grainsize;
    }

    /**
     * 获取文件版本号
     *
     * @param string $file
     * @return string|false
     */
    public static function getRevision($file = '')
    {
        if (self::$_reversionCtlGrainSize == self::REVERSION_CTL_GRAINSIZE_COARSE) {
            return VERSION;
        }

        if (empty(self::$_revision)) {
            $_revFile        = Q_Config::get('Global', 'ST_REVISION_FILE');
            $str             = Q_File::get($_revFile);
            self::$_revision = json_decode($str, true);
        }

        if (empty(self::$_revision[$file])) {
            return false;
        }

        arsort(self::$_revision[$file]);

        foreach (self::$_revision[$file] as $_time) {
            if ($_time > LAST_UPDATE_TIME) {
                continue;
            }
            return $_time;
        }
        return false;
    }

    /**
     * 设置URL前缀
     *
     * @param $url
     */
    public static function setUrlPrefix($url)
    {
        self::$_urlPrefix = $url;
    }

    /**
     * 获取URL前缀
     *
     * @return string
     */
    public static function getUrlPrefix()
    {
        return self::$_urlPrefix;
    }

    /**
     * 添加文件
     *
     * @param string $url  文件路径
     * @param string $type 文件类型 self::FILE_TYPE_*
     * @param string $pos  文件位置
     */
    public static function addFile($url, $type = null, $pos = self::POS_HEADER)
    {
        $url = (array)$url;

        $conf = Q_Config::get('Global');

        foreach ($url as $_url) {
            if (!$type) {
                $_type = strtolower(substr($_url, strrpos($_url, '.') + 1));
                if (empty(self::$_fileHtmlTpl[$_type])) {//如果系统定义没有此类型,就跳过
                    continue;
                }
            } else {
                $_type = $type;
            }

            if (strtolower(substr($_url, 0, 7)) != 'http://') {
                $_url = self::$_urlPrefix . $_url;
            }

            //使用revision,文件在st下,且有版本控制文件,则换成带版本号的文件
            if (self::$_useRevision && strpos($_url, $conf['ST_SERVER']) === 0) {
                $_file = substr($_url, strlen($conf['ST_SERVER']));
                $_rev  = self::getRevision($_file);

                if ($_rev) {
                    if (self::$_reversionCtlGrainSize == self::REVERSION_CTL_GRAINSIZE_COARSE) {
                        $_url .= '?' . $_rev;
                    } elseif (!empty($conf['ST_REVISION_FILE'])) {
                        $_ext = substr($_url, strrpos($_url, '.'));
                        $_url = substr($_url, 0, strrpos($_url, '.')) . '_' . $_rev . $_ext;
                    }
                }
            }

            self::$_files[$_type][$pos][] = $_url;
        }
    }

    /**
     * 获取设置的文件
     *
     * @param string $type 文件类型 self::FILE_TYPE_*
     * @param string $pos  要显示的位置
     * @param bool   $html 是否解析成HTML格式
     * @return array|string 如果是解析成HTML,输出为string类型,默认配置输出原数组
     */
    public static function getFile($type, $pos = self::POS_HEADER, $html = false)
    {

        $files = '';
        if (isset(self::$_files[$type][$pos])) {
            $files = self::$_files[$type][$pos];
            if ($html) {
                if (empty(self::$_fileHtmlTpl[$type])) {
                    return false;
                }
                $tpl = self::$_fileHtmlTpl[$type];
                foreach ($files as &$_file) {
                    $_file = str_replace('{URL}', $_file, $tpl);
                }
                $files = join("\r\n", $files);
            }
        }
        return $files;
    }

    /**
     * 清除数据
     *
     * @param string $type 要清除的文件类型 self::FILE_TYPE_*
     * @param string $pos  位置 self::POS_*
     * @return bool
     */
    public static function clear($type = null, $pos = null)
    {
        if ($type && $pos) {
            unset(self::$_files[$type][$pos]);
        } else if ($type) {
            unset(self::$_files[$type]);
        } else {
            self::$_files = array();
        }
        return true;
    }
}
