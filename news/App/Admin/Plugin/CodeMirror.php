<?php
/**
 *
 * @author      : hanqiang<hanqiang@dalingpao.com>
 * @copyright(c): 17-1-19
 * @version     : $id$
 */
class Admin_Plugin_CodeMirror extends Admin_Plugin_Abstract
{
    /**
     * 字段名
     *
     * @var string
     */
    protected $_fieldName = '';

    /**
     * 设置字段名
     *
     * @param string $fieldName
     * @return $this
     */
    public function setFieldName($fieldName = '')
    {
        $this->_fieldName = $fieldName;
        return $this;
    }


    /**
     * 获取字段名
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->_fieldName;
    }

    /**
     * @var $this
     */
    protected static $_instance = null;

    public static function instance($fieldName = 'tpl')
    {
        $_instanceKey = $fieldName;
        if (empty(self::$_instance[$_instanceKey])) {
            $obj                            = self::$_instance[$_instanceKey] = new self();
            self::$_instance[$_instanceKey] = $obj;
        } else {
            $obj = self::$_instance[$_instanceKey];
        }

        $obj->setFieldName($fieldName);

        return $obj;
    }

    /**
     * textarea 插件
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public function getTextArea(Q_Request $input, Q_Response $output)
    {
        $validate = $input->validate;

        if (empty($validate)) {
            $validate = Q_Validate::instance();
        }

        $output->fieldName    = $this->getFieldName();
        $output->validate     = $validate;
        $output->globalServer = Q_Config::get('Global', 'ST_SERVER') . 'global/';

        return $output->fetchCol('Plugin/CodeMirror/TextArea');
    }

}