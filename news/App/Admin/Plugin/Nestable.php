<?php

/**
 *
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 14-11-14
 * @version     : $id$
 */
class Admin_Plugin_Nestable extends Admin_Plugin_Abstract
{
    protected static $_cache = array();

    public static function getList(array $param = array())
    {
        $data      = empty($param['data']) ? [] : $param['data'];
        $fieldName = empty($param['fieldName']) ? 'data' : $param['fieldName'];

        if (empty($data)) {
            return false;
        }
        $html      = '';
        foreach ($data as $_row) {
            if (!empty($_row['son'])) {
                $param['data'] = $_row['son'];
                $child         = self::getList($param);
            } else {
                $child = null;
            }

            $_fieldName          = str_replace('${id}', $_row['id'], $fieldName);
            $_param              = $param;
            $_param['row']       = $_row;
            $_param['child']     = $child;
            $_param['fieldName'] = $_fieldName;
            $html .= self::getRow($_param);

        }
        return $html;
    }

    public static function getRow(array $param = array())
    {
        if (empty($param['row'])) {
            return false;
        }
        $tpl       = empty($param['tpl']) ? 'Plugin/NestableItem' : $param['tpl'];
        $fieldRule = empty($param['fieldRule']) ? [] : $param['fieldRule'];
        $fieldName = empty($param['fieldName']) ? 'data' : $param['fieldName'];
        $row       = empty($param['row']) ? [] : $param['row'];
        $child     = empty($param['child']) ? '' : $param['child'];

        $vali = Q_Validate::instance('nestable-row');

        $vali->setParams($row)->setRules($fieldRule);

        $vali->form->setInputName($fieldName);
        $data = array(
            '_row'  => $row,
            'vali'  => $vali,
            'child' => $child,
        );

        return Q_Response::instance()->fetchCol($tpl, $data, true);
    }

}