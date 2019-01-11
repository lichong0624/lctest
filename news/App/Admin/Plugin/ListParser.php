<?php

/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-11-7
 * @version     : $id$
 */
class Admin_Plugin_ListParser
{
    private static $_instance = [];
    protected      $_params   = [];

    public function __construct($name)
    {
    }


    /**
     * @param string $name
     * @return $this
     */
    public static function instance($name = 'default')
    {
        $class = get_called_class();
        if (isset(self::$_instance[$name])) {
            $class = self::$_instance[$name];
        } else {
            $class = new $class($name);

            self::$_instance[$name] = $class;
        }

        return $class;
    }


    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params = [])
    {
        $this->_params = $params;

        return $this;
    }


    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 格式化搜索参数
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return mixed
     */
    public function parse(Q_Request $input, Q_Response $output)
    {
        $data  = $input->getArray('data');
        $order = $input->getArray('order');
        $page  = $input->get('page');
        $page  = empty($page) ? 1 : max(1, $page);
        $conf  = Q_Config::get(array('Global', APP_NAME . '_Global'));

        $param  = $this->getParams();
        $_where = self::_parseWhere($data);
        $_order = self::_parseOrder($order);

        if ($_where) {
            $param['where'] = empty($param['where']) ? $_where : array_merge($param['where'], $_where);
        }

        if ($_order) {
            $param['order'] = empty($param['order']) ? $_order : join(',', [$param['order'], $_order]);
        }

        $param += [
            'page'     => $page,
            'pageSize' => empty($conf['LIST_PAGE_SIZE']) ? 10 : $conf['LIST_PAGE_SIZE']
        ];

        return $param;
    }

    public static function _parseWhere(array $data = array())
    {
        $items = Admin_Plugin_Search::instance()->getItems();
        $date  = empty($data['search_date']) ? [] : $data['search_date'];
        if (empty($data)) {
            return [];
        }

        $_where = [];

        foreach ($items as $_field => $_fieldConfig) {

            $_field = trim($_field);

            $_val = isset($data[$_field]) ? $data[$_field] : false;
            if ($_val === false || $_val == '') {
                continue;
            }

            if (!empty($date[$_field])) {
                if (empty($date[$_field]['_start_date']) || empty($date[$_field]['_end_date'])) {
                    continue;
                }

                $_start = $date[$_field]['_start_date'];
                $_end   = $date[$_field]['_end_date'];
                /*if ($_val == -1) {
                    $_end = date('Y-m-d', strtotime('+1 day', strtotime($_end)));
                }*/
                $_val = [$_start, $_end];
            }

            if (!empty($_fieldConfig['mapping']) && is_callable($_fieldConfig['mapping'])) {
                $_val   = $_fieldConfig['mapping']($_val);
                $_field = $_fieldConfig['field'];
            }

            if (!empty($_fieldConfig['match'])) {
                $_match = $_fieldConfig['match'];
                $_field .= ($_match == '=' ? '' : ' ' . $_match);

                if ($_match == 'LIKE') {
                    $_val = "%{$_val}%";
                }
            }

            $_where[$_field] = $_val;
        }

        return $_where;
    }

    public static function _parseOrder(array $order = array())
    {
        if (empty($order)) {
            return null;
        }

        $_order = [];
        foreach ($order as $_field => $_val) {
            if (empty($_val)) {
                continue;
            }

            $_field   = trim($_field);
            $_order[] = "{$_field} {$_val}";
        }

        $_order = join(',', $_order);

        return $_order;
    }
}