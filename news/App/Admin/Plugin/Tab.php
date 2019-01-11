<?php
/**
 * 侧边栏
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-13
 * @version     : $id$
 */
class Admin_Plugin_Tab extends Admin_Plugin_Abstract
{

    /**
     * 获取侧边栏
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public static function getTab(Q_Request $input, Q_Response $output)
    {
        return $output->fetchCol('Plugin/Tab/Tab');
    }
}
