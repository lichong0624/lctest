<?php
/**
 *
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 16-11-16
 * @version     : $id$
 */

class Q_Date
{
    public static function gmdateStr($time = SYSTEM_TIME)
    {
        return gmdate('D, d M Y H:i:s', $time) . ' GMT';
    }
}