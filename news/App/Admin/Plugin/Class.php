<?php
/**
 * 侧边栏
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-13
 * @version     : $id$
 */
class Admin_Plugin_Class extends Admin_Plugin_Abstract
{

    /**
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     * @throws Q_Exception
     */
    public static function getClass(Q_Request $input, Q_Response $output)
    {
        return $output->fetchCol('Plugin/Class/Index');
    }
}
