<?php
/**
 * 导航栏
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-13
 * @version     : $id$
 */
class Admin_Plugin_NavBar extends Admin_Plugin_Abstract
{

    /**
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     * @throws Q_Exception
     */
    public static function getNavBar(Q_Request $input, Q_Response $output)
    {
        return $output->fetchCol('Plugin/NavBar/NavBar');
    }
}
