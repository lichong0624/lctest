<?php

class Q_Log
{
    const TYPE_ERROR     = 1;
    const TYPE_EXCEPTION = 2;
    protected static $type = array(
        self::TYPE_ERROR     => 'error',
        self::TYPE_EXCEPTION => 'exception',
    );


    /**
     * @param $message
     * @param $type
     * @return bool|int
     */
    public static function write($message, $type)
    {
        if (empty($message)) {
            trigger_error('$message dose not empty! ');

            return false;
        }

        if (empty($type)) {
            trigger_error('$type dose not empty! ');

            return false;
        }

        if (isset(self::$type[$type])) {
            $type = self::$type[$type];
        }

        $var  = defined('LOG_PATH') ? (LOG_PATH . basename(PRODUCTION_ROOT) . '/') : (VAR_PATH . 'log/');
        $path = $var . APP_NAME . '/' . $type . '/' . date('Ymd') . '.log';

        $mark = "\n\n===========================================================================\n";
        $mark .= 'TIME:' . date('Y-m-d H:i:s') . "\n";

        return Q_File::write($mark . $message, $path, (FILE_APPEND | LOCK_EX));
    }
}
