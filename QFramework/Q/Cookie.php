<?php

/**
 * @author       wiki <charmfocus@gmail.com>
 * @copyright(c) 14/11/12
 */
class Q_Cookie
{

    /**
     * cookie加密因子
     *
     * @var string
     */
    private static $_key = '12312cuiq3kcm12KORdlwiLSJCR';

    // 数据类型为字符串
    const DATA_TYPE_STRING = 0;
    // 数据类型为数组
    const DATA_TYPE_ARRAY = 1;

    /**
     * cookie中的值，缓存
     *
     * @var array
     */
    private static $_var = array();

    /**
     * 设置cookie
     *
     * @param string  $name   键名
     * @param mixed   $value  值
     * @param integer $expire 过期时间，以当前时间为起点计时
     * @param string  $domain 域
     * @return boolean
     */
    public static function set($name, $value, $expire = null, $domain = null)
    {


        self::$_var[$name] = $value;
        $value             = self::encode($value);
        if ($expire) {
            $expire = SYSTEM_TIME + $expire;
        }
        return setcookie($name, $value, $expire, '/', $domain);
    }

    /**
     * 获取cookie
     *
     * @param string $name 键名
     * @return mixed
     */
    public static function get($name)
    {
        if (isset(self::$_var[$name])) {
            return self::$_var[$name];
        }
        if ($value = Q_Registry::get('Q_Request')->cookie($name)) {
            if ($value = self::decode($value)) {
                self::$_var[$name] = $value;
                return $value;
            }
        }
        return null;
    }

    /**
     * 删除cookie
     *
     * @param string $name 键名
     * @param string $domain 域名
     * @return boolean
     */
    public static function del($name, $domain = null)
    {
        if (isset(self::$_var[$name])) {
            unset(self::$_var[$name]);
        }
        return setcookie($name, '', time() - 3600, '/', $domain);
    }

    /**
     * 生成数据加密因子
     *
     * @param string $value 需要生成因子的数据
     * @return string
     */
    public static function encode($value)
    {

        $dataType = self::DATA_TYPE_STRING;
        if (is_array($value)) {
            $dataType = self::DATA_TYPE_ARRAY;
            $value    = serialize($value);
        }

        return $dataType . $value . self::sign($value);
    }

    /**
     * 解密COOKIE
     *
     * @param string $value 需要解密的值
     * @return mixed
     */
    public static function decode($value)
    {
        $tmpValue = substr($value, 1, strlen($value) - 33);
        $dataType = substr($value, 0, 1);
        $sign     = substr($value, -32);

        if (self::sign($tmpValue) != $sign) {
            return false;
        }

        if ($dataType == self::DATA_TYPE_ARRAY) {
            $tmpValue = unserialize($tmpValue);
        }

        return $tmpValue;
    }


    /**
     * 设置密钥
     *
     * @param $key
     */
    public static function setKey($key)
    {
        if (!empty($key)) {
            self::$_key = $key;
        }
    }

    /**
     * 安全签名
     *
     * @param string $value 需要进行签名的数据
     * @return string
     */
    public static function sign($value)
    {
        $num = strlen($value);
        return md5(md5($value) . substr(self::$_key, $num, $num % strlen(self::$_key)));
    }
} 