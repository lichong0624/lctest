<?php

/**
 * @author       wiki <charmfocus@gmail.com>
 * @copyright(c) 14/11/12
 */
class Q_TipMsg
{
    /**
     * 提示消息
     * @var array
     */
    private static $_msg = array();

    /**
     * 消息类型：成功
     */
    const MSG_TYPE_SUCCESS = 'success';

    /**
     * 消息类型失败
     */
    const MSG_TYPE_ERROR = 'error';

    /**
     * 消息提示
     */
    const MSG_TYPE_INFO = 'info';

    public function __construct()
    {
        if ($msg = Q_Cookie::get('_MSG')) {
            self::add($msg['msg'], $msg['type']);
            Q_Cookie::del(_MSG);
        }
    }

    public static function add($msg, $type = self::MSG_TYPE_ERROR)
    {
        self::$_msg[] = array(
            'msg'  => $msg,
            'type' => $type,
        );
    }

    public static function get()
    {
        return self::$_msg;
    }


} 