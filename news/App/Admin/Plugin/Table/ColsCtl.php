<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-11-01
 * @version     : $id$
 */
class Admin_Plugin_Table_ColsCtl extends Admin_Plugin_Abstract
{

    protected static $_tpl = 'Plugin/Table/ColsCtl';


    /**
     * 获取自定义list选项
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public static function getHtml(Q_Request $input, Q_Response $output)
    {
        $defaultConfig = [
            'colsCtl' => false,
        ];

        $cols   = (array)$input->cols;
        $config = (array)$input->config;
        $config = empty($config) ? $defaultConfig : array_merge($defaultConfig, $config);

        return $output->fetchCol(self::$_tpl, ['cols' => $cols, 'config' => $config]);
    }
}