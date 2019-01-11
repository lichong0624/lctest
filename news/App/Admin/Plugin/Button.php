<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 */

class Admin_Plugin_Button extends Admin_Plugin_Abstract
{

    /**
     * 默认一组button
     *
     * @var array
     */
    protected static $_defaultButtons = [
        'Del'  => [
            'name'    => '删除',
            'class'   => 'layui-btn layui-btn-xs layui-btn-danger',
            'action'  => '',
            'iconCls' => 'fa fa-trash-o',
        ],
        'Edit' => [
            'name'    => '编辑',
            'class'   => 'layui-btn layui-btn-xs',
            'iconCls' => 'fa fa-pencil',
        ]
    ];

    protected static $_defaultButtonTpl = 'Default';

    /**
     * 获取一组button
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public static function getGroupHtml(Q_Request $input, Q_Response $output)
    {
        $buttonConfig = (array)$input->buttonConfig;

        if (empty($buttonConfig)) {
            return '';
        }

        $btnGrp = [];

        foreach ($buttonConfig as $_key => $_item) {

            if (!is_array($_item)) {
                $_item = [
                    'action' => $_item
                ];
            }

            if (empty($_item['action'])) {
                $_item['action'] = $_key;
            }

            $input->buttonConfig = $_item;
            $btnGrp[$_key]       = self::getHtml($input, $output);
        }

        return $output->fetchCol('Plugin/Button/ButtonGrp', ['btnGrp' => $btnGrp]);
    }


    /**
     * 获取button
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public static function getHtml(Q_Request $input, Q_Response $output)
    {
        $buttonConfig = (array)$input->buttonConfig;

        if (empty($buttonConfig['action'])) {
            return '';
        }

        $input   = Q_Request::instance();
        $request = $input->requestArray();

        $_action      = $buttonConfig['action'];
        $request['a'] = ucfirst($_action);

        $_params          = empty(self::$_defaultButtons[$_action]) ? [] : self::$_defaultButtons[$_action];
        $_params['param'] = empty($buttonConfig['param']) ? $request : array_merge($request, $buttonConfig['param']);

        $_params = array_replace_recursive($_params, $buttonConfig);

        return self::_getButtonContent($_params);
    }


    /**
     * 获取button内容
     *
     * @param array $params
     * @return string
     */
    private static function _getButtonContent(array $params = [])
    {
        $output = Q_Response::instance();

        $_tpl = self::$_defaultButtonTpl;
        if (isset($params['tpl'])) {
            $_tpl = $params['tpl'];
        }
        $tpl = "Plugin/Button/Tpl/{$_tpl}";

        return $output->fetchCol($tpl, ['params' => $params]);
    }
}