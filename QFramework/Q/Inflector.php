<?php

/*
|---------------------------------------------------------------
| Performs transformations on resource names, ie, urls, classes,
| methods, variables.
|---------------------------------------------------------------
| @package Q
|
*/

class Q_Inflector
{

    /*
    |---------------------------------------------------------------
    | Returns the full Manager name given the short name, ie,
    | faq becomes FaqMgr.
    |---------------------------------------------------------------
    | @param string $name
    | @return string
    |
    */
    public static function getControllerClassName($name)
    {
        //  if controller suffix has been left out, append it
        if (strtolower(substr($name, -3)) != 'Mgr') {
            $name .= 'Mgr';
        }
        return ucfirst($name);
    }

    /*
    |---------------------------------------------------------------
    | Converts "string with spaces" to "camelCase" string.
    |---------------------------------------------------------------
    | @param   string $s
    | @return  string $delimiters 正则元字符分隔符 如 \s表示空字符，
    |
    */
    public static function camelise($s, $delimiters = '\s')
    {
        $callback = function ($matches) {
            return strtoupper($matches[1]);
        };

        return preg_replace_callback('/[' . $delimiters . '](.?)/', $callback, $s);
    }

    public static function getTitleFromCamelCase($camelCaseWord)
    {
        if (!self::isCamelCase($camelCaseWord)) {
            return $camelCaseWord;
        }
        $ret = '';
        for ($x = 0; $x < strlen($camelCaseWord); $x++) {
            if (preg_match("/[A-Z]/", $camelCaseWord{$x})) {
                $ret .= ' ';
            }
            $ret .= $camelCaseWord{$x};
        }
        return ucfirst($ret);
    }

    public static function isCamelCase($str)
    {
        //  ensure no non-alpha chars
        if (preg_match("/[^a-z].*/i", $str)) {
            return false;
        }
        //  and at least 1 capital not including first letter
        for ($x = 1; $x < strlen($str) - 1; $x++) {
            if (preg_match("/[A-Z]/", $str{$x})) {
                return true;
            }
        }
        return false;
    }

    public static function isConstant($str)
    {
        if (empty($str)) {
            return false;
        }
        if (preg_match('/sessid/i', $str)) {
            return false;
        }
        $pattern = '@^[A-Z_\'][A-Z_0-9\']*$@';
        if (!preg_match($pattern, $str)) {
            return false;
        }
        return true;
    }


    /*
    |---------------------------------------------------------------
    | Returns a human-readable string from $lower_case_and_underscored_word,
    | by replacing underscores with a space, and by upper-casing the initial characters.
    |---------------------------------------------------------------
    | @param string $lower_case_and_underscored_word String to be
    |  made more readable
    | @return string Human-readable string
    |
    */
    public static function humanise($lowerCaseAndUnderscoredWord)
    {
        $replace = ucwords(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
        return $replace;
    }

    /**
     * 过滤及规范名称，替换了.为_
     *
     * @param string|array $name
     * @return string
     */
    public static function formatName($name)
    {
        if (is_array($name)) {
            foreach ($name as &$_name) {
                $_name = self::formatName($_name);
            }
        } else {
            if (strpos($name, '.')) {
                $name = strtr($name, '.', '_');
            }
        }

        return ucfirst($name);
    }

    /**
     * 是否是版本号格式 v1.0.0|v20160909|1.0.0|20160909
     *
     * @param $version
     * @return bool
     */
    public static function formatVersionNo(&$version)
    {
        $pattern = '@^(\w+_)?v?((\d+\.)+\d+|\d+)$@i';
        $res     = (bool)preg_match($pattern, $version, $matches);
        if ($res) {
            $version = $matches[1] . 'v' . $matches[2];
        }
        return $res;
    }
}
