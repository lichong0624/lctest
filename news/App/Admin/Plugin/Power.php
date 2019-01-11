<?php

/**
 *
 * @author       hanqiang <123456@qq.com>
 * @copyright(c) 2016-07-14 16:03:51
 * @version      : $id$
 */
class Admin_Plugin_Power extends Admin_Plugin_Abstract
{
    public static function getList(Q_Request $input, Q_Response $output)
    {
        $menuArr = $input->menuArr;

        if (empty($menuArr)) {
            return false;
        }
        $html = '';
        foreach ($menuArr as $_row) {
            if (!empty($_row['son'])) {
                $input->menuArr = $_row['son'];
                $output->child  = self::getList($input, $output);
            } else {
                $output->child = null;
            }
            $output->_row = $_row;

            $html .= $output->fetchCol('Plugin/Power/Item');

        }
        return $output->fetchCol('Plugin/Power/List', ['items' => $html]);
    }
}