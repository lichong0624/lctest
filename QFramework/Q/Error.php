<?php

class Q_Error
{
    private static $_show = true;

    public static function register()
    {
        set_error_handler(array(__CLASS__, 'handler'));
    }

    public static function setShow($show = true)
    {
        self::$_show = $show;
    }

    public static function handler($level, $errorMsg, $file, $line)
    {
        if ('.tpl.php' == substr($file, -8)) {
            return;
        }
        $debugging  = defined('IS_DEBUGGING') ? IS_DEBUGGING : false;
        $production = defined('IS_PRODUCTION') ? IS_PRODUCTION : false;
        $exception  = new Q_Exception($errorMsg, $level, $file, $line);
        $exception->setIsFromError(true);

        Q_Http::sendHeader(500);
        if ($production) {
            Q_Log::write(Q_String::clean($exception), Q_Log::TYPE_ERROR);
        }

        if ($debugging && self::$_show) {
            throw $exception;
        }
    }
}

