<?php

/*
|---------------------------------------------------------------
| Various static string helper methods.
|---------------------------------------------------------------
| @package Q
|
*/

class Q_String
{

    public static function trimWhitespace($var)
    {
        if (!isset($var)) {
            return false;
        }

        if (is_array($var)) {
            $newArray = array();
            foreach ($var as $key => $value) {
                $newArray[$key] = self::trimWhitespace($value);
            }
            return $newArray;
        } else {
            return trim($var);
        }
    }

    /*
    |---------------------------------------------------------------
    | Returns cleaned user input.
    |---------------------------------------------------------------
    | @access  public
    | @param   string $var  The string to clean.
    | @return  string       $cleaned result.
    */
    public static function clean($var)
    {
        if (!isset($var)) {
            return false;
        }
        $var = self::trimWhitespace($var);
        if (is_array($var)) {
            $newArray = array();
            foreach ($var as $key => $value) {
                $newArray[$key] = self::clean($value);
            }
            return $newArray;
        } else {
            return strip_tags($var);
        }
    }

    public static function removeJs($var)
    {
        if (!isset($var)) {
            return false;
        }
        $var = self::trimWhitespace($var);
        if (is_array($var)) {
            $newArray = array();
            foreach ($var as $key => $value) {
                $newArray[$key] = self::removeJs($value);
            }
            return $newArray;
        } else {
            $search  = "/<script[^>]*?>.*?<\/script\s*>/i";
            $replace = '';
            $clean   = preg_replace($search, $replace, $var);
            return $clean;
        }
    }

    public static function toValidVariableName($str)
    {
        //  remove illegal chars
        $search  = '/[^a-zA-Z0-9_]/';
        $replace = '';
        $res     = preg_replace($search, $replace, $str);
        //  ensure 1st letter is lc
        $firstLetter = strtolower($res[0]);
        $final       = substr_replace($res, $firstLetter, 0, 1);
        return $final;
    }

    public static function toValidFileName($origName)
    {
        return self::dirify($origName);
    }

    //  from http://kalsey.com/2004/07/dirify_in_php/
    public static function dirify($s)
    {
        $s = self::_convertHighAscii($s);     ## convert high-ASCII chars to 7bit.
        $s = strtolower($s);                       ## lower-case.
        $s = strip_tags($s);                       ## remove HTML tags.
        // Note that &nbsp (for example) is legal in HTML 4, ie. semi-colon is optional if it is followed
        // by a non-alphanumeric character (eg. space, tag...).
        //         $s = preg_replace('!&[^;\s]+;!','',$s);    ## remove HTML entities.
        $s = preg_replace('!&#?[A-Za-z0-9]{1,7};?!', '', $s);    ## remove HTML entities.
        $s = preg_replace('![^\w\s-]!', '', $s);    ## remove non-word/space chars.
        $s = preg_replace('!\s+!', '_', $s);        ## change space chars to underscores.
        return $s;
    }

    protected static function _convertHighAscii($s)
    {
        // Seems to be for Latin-1 (ISO-8859-1) and quite limited (no ae/oe, no y:/Y:, etc.)
        $aHighAscii = array(
            "!\xc0!" => 'A',    # A`
            "!\xe0!" => 'a',    # a`
            "!\xc1!" => 'A',    # A'
            "!\xe1!" => 'a',    # a'
            "!\xc2!" => 'A',    # A^
            "!\xe2!" => 'a',    # a^
            "!\xc4!" => 'A',    # A:
            "!\xe4!" => 'a',    # a:
            "!\xc3!" => 'A',    # A~
            "!\xe3!" => 'a',    # a~
            "!\xc8!" => 'E',    # E`
            "!\xe8!" => 'e',    # e`
            "!\xc9!" => 'E',    # E'
            "!\xe9!" => 'e',    # e'
            "!\xca!" => 'E',    # E^
            "!\xea!" => 'e',    # e^
            "!\xcb!" => 'E',    # E:
            "!\xeb!" => 'e',    # e:
            "!\xcc!" => 'I',    # I`
            "!\xec!" => 'i',    # i`
            "!\xcd!" => 'I',    # I'
            "!\xed!" => 'i',    # i'
            "!\xce!" => 'I',    # I^
            "!\xee!" => 'i',    # i^
            "!\xcf!" => 'I',    # I:
            "!\xef!" => 'i',    # i:
            "!\xd2!" => 'O',    # O`
            "!\xf2!" => 'o',    # o`
            "!\xd3!" => 'O',    # O'
            "!\xf3!" => 'o',    # o'
            "!\xd4!" => 'O',    # O^
            "!\xf4!" => 'o',    # o^
            "!\xd6!" => 'O',    # O:
            "!\xf6!" => 'o',    # o:
            "!\xd5!" => 'O',    # O~
            "!\xf5!" => 'o',    # o~
            "!\xd8!" => 'O',    # O/
            "!\xf8!" => 'o',    # o/
            "!\xd9!" => 'U',    # U`
            "!\xf9!" => 'u',    # u`
            "!\xda!" => 'U',    # U'
            "!\xfa!" => 'u',    # u'
            "!\xdb!" => 'U',    # U^
            "!\xfb!" => 'u',    # u^
            "!\xdc!" => 'U',    # U:
            "!\xfc!" => 'u',    # u:
            "!\xc7!" => 'C',    # ,C
            "!\xe7!" => 'c',    # ,c
            "!\xd1!" => 'N',    # N~
            "!\xf1!" => 'n',    # n~
            "!\xdf!" => 'ss'
        );
        $find       = array_keys($aHighAscii);
        $replace    = array_values($aHighAscii);
        $s          = preg_replace($find, $replace, $s);
        return $s;
    }

    protected function _to7bit($text)
    {
        if (!function_exists('mb_convert_encoding')) {
            return $text;
        }
        $text = mb_convert_encoding($text, 'HTML-ENTITIES', mb_detect_encoding($text));
        $text = preg_replace(
            array('/&szlig;/', '/&(..)lig;/',
                  '/&([aouAOU])uml;/', '/&(.)[^;]*;/'),
            array('ss', "$1", "$1" . 'e', "$1"),
            $text);
        return $text;
    }

    /*
    |---------------------------------------------------------------
    | Replaces accents in string.
    |---------------------------------------------------------------
    | @todo make it work with cyrillic chars
    | @todo make it work with non utf-8 encoded strings
    | @see Q_String::isCyrillic()
    | @param string $str
    | @return string
    */
    public static function replaceAccents($str)
    {
        if (!self::_isCyrillic($str)) {
            $str = self::_to7bit($str);
            $str = preg_replace('/[^A-Z^a-z^0-9()]+/', ' ', $str);
        }
        return $str;
    }

    /*
    |---------------------------------------------------------------
    | Checks if strings has cyrillic chars.
    |---------------------------------------------------------------
    | @param string $str
    | @return boolean
    */
    protected function _isCyrillic($str)
    {
        $ret = false;
        if (function_exists('mb_convert_encoding') && !empty($str)) {
            // codes for Russian chars
            $aCodes = range(1040, 1103);
            // convert to entities
            $encoded = mb_convert_encoding($str, 'HTML-ENTITIES',
                mb_detect_encoding($str));
            // get codes of the string
            $aChars = explode(';', str_replace('&#', '', $encoded));
            array_pop($aChars);
            $aChars = array_unique($aChars);
            // see if cyrillic chars there
            $aNonCyrillicChars = array_diff($aChars, $aCodes);
            // if string is the same -> no cyrillic chars
            $ret = count($aNonCyrillicChars) != count($aChars);
        }
        return $ret;
    }

    /*
    |---------------------------------------------------------------
    | Removes chars that are illegal in ini files.
    |---------------------------------------------------------------
    | @param string $string
    | @return string
    */
    public static function stripIniFileIllegalChars($string)
    {
        return preg_replace("/[\|\&\~\!\"\(\)]/i", "", $string);
    }

    /*
    |---------------------------------------------------------------
    | Converts strings representing constants to int values.
    | Used for when constants are stored as strings in config.
    |---------------------------------------------------------------
    | @param string $string
    | @return integer
    */
    public static function pseudoConstantToInt($string)
    {
        $ret = 0;
        if (is_int($string)) {
            $ret = $string;
        }
        if (is_numeric($string)) {
            $ret = (int)$string;
        }
        if (Q_Inflector::isConstant($string)) {
            $const = str_replace("'", '', $string);
            if (defined($const)) {
                $ret = constant($const);
            }
        }
        return $ret;
    }

    /*
    |---------------------------------------------------------------
    | Esacape single quote.
    |---------------------------------------------------------------
    | @param string $string
    | @return  string
    */
    public static function escapeSingleQuote($string)
    {
        $ret = str_replace('\\', '\\\\', $string);
        $ret = str_replace("'", '\\\'', $ret);
        return $ret;
    }


    /*
    |---------------------------------------------------------------
    | Escape single quotes in every key of given array.
    |---------------------------------------------------------------
    | @param   array $array
    | @static
    */
    public static function escapeSingleQuoteInArrayKeys($array)
    {
        $ret = array();
        foreach ($array as $key => $value) {
            $k       = self::escapeSingleQuote($key);
            $ret[$k] = is_array($value)
                ? self::escapeSingleQuoteInArrayKeys($value)
                : $value;
        }
        return $ret;
    }

    /*
    |---------------------------------------------------------------
    | 将一个字串中含有全角或半角的数字字符、字母、空格或'%+-()'字符互换
    |---------------------------------------------------------------
    | @static
    | @access  public
    | @param   string       $str         待转换字串
    | @param   boolean      $reverse     默认true为全角转半角, false为半角转全角
    | @return  string       $str         处理后字串
    */

    public static function convertSemiangle($str, $reverse = true)
    {
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
                     '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
                     'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
                     'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
                     'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
                     'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
                     'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
                     'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
                     'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
                     'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
                     'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
                     'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
                     'ｙ' => 'y', 'ｚ' => 'z',
                     '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
                     '】' => ']', '〖' => '[', '〗' => ']', '｛' => '{', '｝' => '}',
                     '《' => '<', '》' => '>',
                     '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
                     '：' => ':', '。' => '.', '，' => '.', '、' => '.',
                     '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
                     '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
                     '　' => ' ');
        if (false === $reverse) {
            $arr = array_flip($arr);
        }
        return strtr($str, $arr);
    }

    /**
     * convert utf-8 encoding data to other encodings
     *
     * @param mixed  $input
     * @param string $encoding
     * @return mixed
     */
    public static function u8conv($input, $encoding = 'GBK')
    {
        if (is_string($input)) {
            if (strtoupper($encoding) == 'UTF-8') {
                return $input;
            }
            return mb_convert_encoding($input, $encoding, 'UTF-8');
        } else {
            $output = array();
            foreach ((array)$input as $k => $v) {
                $output[$k] = self::u8conv($v, $encoding);
            }
            return $output;
        }
    }

    public static function stripslashes($val)
    {
        if (get_magic_quotes_gpc()) {
            return stripslashes($val);
        } else {
            return $val;
        }
    }

    public static function addslashes($val)
    {
        if (!get_magic_quotes_gpc()) {
            return addslashes($val);
        } else {
            return $val;
        }
    }

    public static function convertEncodingDeep($value, $target_lang, $source_lang)
    {
        if (empty($value)) {
            return $value;
        } else {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $value[$k] = self::convertEncodingDeep($source_lang, $target_lang, $v);
                }
                return $value;
            } elseif (is_string($value)) {
                return mb_convert_encoding($value, $target_lang, $source_lang);
            } else {
                return $value;
            }
        }
    }

    public static function addslashesDeep($value)
    {
        if (empty($value) || get_magic_quotes_gpc()) {
            return $value;
        } else {
            if (is_array($value)) {
                foreach ($value as &$val) {
                    $val = self::addslashesDeep($val);
                }
                return $value;
            } elseif (is_string($value)) {
                return addslashes($value);
            } else {
                return $value;
            }
        }
    }

    public static function substr($str, $len, $charset = 'utf-8')
    {
        if (!function_exists('cnsubstr_ext') || 'utf-8' == strtolower($charset)) {
            return self::substr_php($str, $len, $charset);
        } else {
            return cnsubstr_ext($str, $len);
        }
    }

    public static function substr_php($str, $len, $charset = 'gbk')
    {
        if (empty($str)) {
            return false;
        }
        if ($len >= strlen($str) || $len < 1) {
            return $str;
        }

        //        $str = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $str);

        $strcut   = array();
        $temp_str = '';
        $sublen   = (strtolower($charset) == 'utf-8') ? 3 : 2;
        for ($i = 0; $i < $len; ++$i) {
            $temp_str = substr($str, 0, 1);

            if (ord($temp_str) > 127) {
                ++$i;
                if ($sublen == 3) {
                    ++$i;
                }
                if ($i < $len) {
                    $strcut[] = substr($str, 0, $sublen);
                    $str      = substr($str, $sublen);
                }
            } else {
                if ($i < $len) {
                    $strcut[] = substr($str, 0, 1);
                    $str      = substr($str, 1);
                }
            }
        }
        if (!empty($strcut)) {
            $strcut = join($strcut);
            //            $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

            return $strcut;
        } else {
            return '';
        }
    }

    /**
     * 加密中文 同JS同名函数功能
     *
     * @param string $str      要转码的字符
     * @param string $encoding 字符的编码方式
     * @param string $prefix
     * @return string 回返已转码的字符
     */
    public static function escape($str, $encoding = 'GBK', $prefix = '%')
    {
        $return = '';
        for ($x = 0; $x < mb_strlen($str, $encoding); $x++) {
            $s = mb_substr($str, $x, 1, $encoding);
            if (strlen($s) > 1) {//多字节字符
                $return .= $prefix . 'u' . strtoupper(bin2hex(mb_convert_encoding($s, 'UCS-2', $encoding)));
            } else {
                $return .= $prefix . strtoupper(bin2hex($s));
            }
        }
        return $return;
    }

    /**
     * 解析JS的escape编码
     *
     * @param string $str
     * @param string $encoding
     * @param string $prefix
     * @return mixed
     */
    public static function unescape($str, $encoding = 'GBK', $prefix = '%')
    {
        $prefix != '%' && $str = str_replace($prefix, '%', $str);
        $str  = rawurldecode($str);
        $text = preg_replace_callback("/%u[0-9A-Za-z]{4}/", array(__CLASS__, 'unicode2Utf8'), $str);
        return self::u8conv($text, $encoding);
    }

    /**
     * 处理HTML中的转义字符
     *
     * @param string $str      要处理的转义字符
     * @param string $encoding 转换后的编码方式
     * @return string
     */
    public static function recode($str, $encoding = 'GBK')
    {
        if (function_exists('recode')) {
            return recode("html..{$encoding}", $str);
        } else {
            return self::phprecode($str, $encoding);
        }
    }

    /**
     * PHP版的recode，只处理HTML中的转义字符
     *
     * @param string $str      要处理的转义字符
     * @param string $encoding 转换后的编码方式xx
     * @return string
     */
    public static function phprecode($str, $encoding = 'GBK')
    {
        $text = preg_replace_callback("/&#[0-9]{1,5}/", array(__CLASS__, 'htmlDecode'), $str);
        return self::u8conv($text, $encoding);
    }

    public static function htmlDecode($ar)
    {
        $str = '';
        foreach ($ar as $val) {
            $c = substr($val, 2);
            if ($c < 0x80) {
                $str .= chr($c);
            } else if ($c < 0x800) {
                $str .= chr(0xC0 | $c >> 6);
                $str .= chr(0x80 | $c & 0x3F);
            } else if ($c < 0x10000) {
                $str .= chr(0xE0 | $c >> 12);
                $str .= chr(0x80 | $c >> 6 & 0x3F);
                $str .= chr(0x80 | $c & 0x3F);
            } else if ($c < 0x200000) {
                $str .= chr(0xF0 | $c >> 18);
                $str .= chr(0x80 | $c >> 12 & 0x3F);
                $str .= chr(0x80 | $c >> 6 & 0x3F);
                $str .= chr(0x80 | $c & 0x3F);
            }
        }
        return $str;
    }


    /**
     * 转换UNICODE编码为UTF8
     *
     * @param mixed $ar
     * @return string
     */
    public static function unicode2Utf8($ar)
    {
        $c = '';
        foreach ($ar as $val) {
            $val = intval(substr($val, 2), 16);
            if ($val < 0x7F) {        // 0000-007F 单字节
                $c .= chr($val);
            } elseif ($val < 0x800) { // 0080-0800 双字节
                $c .= chr(0xC0 | ($val / 64));
                $c .= chr(0x80 | ($val % 64));
            } else {                // 0800-FFFF 三字节
                $c .= chr(0xE0 | (($val / 64) / 64));
                $c .= chr(0x80 | (($val / 64) % 64));
                $c .= chr(0x80 | ($val % 64));
            }
        }
        return $c;
    }

    public static function utf82Unicode($c)
    {
        $n = '';
        switch (strlen($c)) {
            case 1:
                $n = ord($c);
                break;
            case 2:
                $n = (ord($c[0]) & 0x3f) << 6;
                $n += ord($c[1]) & 0x3f;
                break;
            case 3:
                $n = (ord($c[0]) & 0x1f) << 12;
                $n += (ord($c[1]) & 0x3f) << 6;
                $n += ord($c[2]) & 0x3f;
                break;
            case 4:
                $n = (ord($c[0]) & 0x0f) << 18;
                $n += (ord($c[1]) & 0x3f) << 12;
                $n += (ord($c[2]) & 0x3f) << 6;
                $n += ord($c[3]) & 0x3f;
                break;
        }
        return $n;
    }

    /**
     * 驼峰写法转下划线连接
     *
     * @param $str
     * @return string
     */
    public static function camelToUnderline($str)
    {
        if (empty($str) || !is_string($str)) {
            return $str;
        }

        $len = strlen($str);

        $_str = '';

        $offset = 0;
        /**
         * 首字母不检查
         */
        for ($i = 1; $i < $len; ++$i) {
            $_char = $str[$i];
            if ($_char == strtoupper($_char)) {
                $_str .= substr($str, $offset, $i - $offset) . '_';
                $offset = $i;
            }
        }
        if ($offset > 0) {
            $_str .= substr($str, $offset, $i - $offset);
        }


        return strtolower($_str);
    }


    public static function isBase64Encoded($code)
    {
        return preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $code);
    }
}


