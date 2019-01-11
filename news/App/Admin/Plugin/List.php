<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 */
class Admin_Plugin_List extends Admin_Plugin_Abstract
{

    protected static $_tpl = 'Plugin/List/Default';


    /**
     * 获取list
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public static function getList(Q_Request $input, Q_Response $output)
    {
        $data = (array)$input->data;
        $cols = (array)$input->cols;
        $conf = (array)$input->config;

        $defaultConf = [
            'colsCtl' => false,
            'pageBar' => true,
        ];

        $conf      = array_merge($defaultConf, $conf);
        $tableData = empty($data['data']) ? [] : $data['data'];
        $total     = empty($data['total']) ? 0 : $data['total'];
        $totalPage = empty($data['totalPage']) ? 1 : $data['totalPage'];
        $pageBar   = empty($data['pageBar']) ? '' : $data['pageBar'];

        $pageBarHtml   = $tableColsCtlHtml = '';
        $actionHtml    = Admin_Plugin_Button::getGroupHtml($input, $output);
        $listTableHtml = Admin_Plugin_Table_Lay::instance('list-table')->setCols($cols)->setData($tableData)->getHtml($input, $output);

        empty($conf['colsCtl']) || $tableColsCtlHtml = Admin_Plugin_Table_ColsCtl::getHtml($input, $output);
        empty($conf['pageBar']) || $pageBarHtml = Admin_Plugin_PageBar::instance($total, $totalPage, $pageBar)->getHtml($input, $output);

        $_tpl = self::$_tpl;
        $list = $output->fetchCol($_tpl, [
            'searchHtml'       => $output->searchHtml,
            'actionHtml'       => $actionHtml,
            'tableColsCtlHtml' => $tableColsCtlHtml,
            'tableHtml'        => $listTableHtml,
            'pageBarHtml'      => $pageBarHtml
        ]);

        return $list;
    }
}