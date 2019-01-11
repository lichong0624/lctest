<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 */
class Admin_Plugin_Table_DataGrid extends Admin_Plugin_Abstract
{
    /**
     * 单例
     */
    protected static $_instance = null;
    protected        $_tpl      = 'Plugin/Table/DataGrid';

    protected $_attributes = [];
    protected $_options    = [];
    protected $_cols       = [];


    /**
     * @param array $config
     * @return Admin_Plugin_Table_DataGrid
     *
     * @throws Q_Exception
     */
    public static function instance($config = null)
    {
        if (isset(self::$_instance)) {
            $obj = self::$_instance;
        } else {
            $obj             = new self();
            self::$_instance = $obj;
        }
        if (!empty($config) && is_array($config)) {
            $obj->init($config);
        }

        return $obj;
    }


    /**
     * 初始化配置参数
     *
     * @param $config
     * @return $this
     * @throws Q_Exception
     */
    public function init($config)
    {
        try {
            foreach ($config as $_key => $_val) {
                $_funName = 'set' . ucfirst($_key);
                if (method_exists($this, $_funName)) {
                    $this->$_funName($_val);
                }
            }
        } catch (Exception $ex) {
            throw new Q_Exception($ex->getMessage(), -1);
        }

        return $this;
    }


    public function setAttributes(array $attributes = [])
    {
        $this->_attributes = $attributes;
        return $this;
    }


    public function setOptions(array $options = [])
    {
        $this->_options = $options;
        return $this;
    }


    public function setCols(array $cols = [])
    {
        if (!empty($cols)) {
            foreach ($cols as $_field => &$_col) {
                $_col['field'] = $_field;
            }
        }

        $this->_cols = $cols;
        return $this;
    }

    /**
     * 获取表格
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public function getHtml(Q_Request $input, Q_Response $output)
    {
        $data = [
            'attributes' => $this->_attributes,
            'options'    => $this->_options,
            'cols'       => $this->_cols,
        ];

        return $output->fetchCol($this->_tpl, $data);
    }

}