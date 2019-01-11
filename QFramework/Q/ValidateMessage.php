<?php
/**
 * 验证消息类
 *
 * @author      : huxudong<huxudong@dalingpao.com>
 * @copyright(c): 16-1-25
 * @version     : $id$
 */
class Q_ValidateMessage implements Q_Interface_ValidateMessage
{
    //消息信息
    private static $message = '';

    //消息码
    private static $code = 0;

    public function __construct($message='', $code = 0)
    {
        self::$message = $message;
        self::$code    = $code;
    }

    /**
     * 获取消息信息
     * @return string
     */
    public function getMessage()
    {
        return self::$message;
    }

    /**
     * 获取消息码
     * @return int
     */
    public function getCode()
    {
        return self::$code;
    }
}