<?php

/**
 *
 * 多输入框增删操作插件
 *
 * @author       hanqiang <123456@qq.com>
 * @copyright(c) 2016-07-14 16:03:51
 */
class Admin_Plugin_MultiInputOpt extends Admin_Plugin_Abstract
{
    protected static $_instance = null;
    protected        $_name     = '';
    protected        $_title    = '';
    protected        $_cols     = [];
    protected        $_data     = [];
    protected        $_tpl      = 'Plugin/MultiInputOpt/Table';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $name
     * @return  Admin_Plugin_MultiInputOpt
     */
    public function setName(string $name): Admin_Plugin_MultiInputOpt
    {
        $this->_name = $name;
        return $this;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->_title;
    }

    /**
     * @param string $title
     * @return Admin_Plugin_MultiInputOpt
     */
    public function setTitle(string $title): Admin_Plugin_MultiInputOpt
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getCols(): array
    {
        return $this->_cols;
    }

    /**
     * @param array $cols
     * @return Admin_Plugin_MultiInputOpt
     */
    public function setCols(array $cols): Admin_Plugin_MultiInputOpt
    {
        $this->_cols = $cols;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->_data;
    }

    /**
     * @param array $data
     * @return Admin_Plugin_MultiInputOpt
     */
    public function setData(array $data): Admin_Plugin_MultiInputOpt
    {
        $this->_data = $data;
        return $this;
    }


    /**
     * @return string
     */
    public function getTpl(): string
    {
        return $this->_tpl;
    }

    /**
     * @param string $tpl
     * @return Admin_Plugin_MultiInputOpt
     */
    public function setTpl(string $tpl): Admin_Plugin_MultiInputOpt
    {
        $this->_tpl = $tpl;
        return $this;
    }


    /**
     * @param string $name
     * @return Admin_Plugin_MultiInputOpt
     */
    public static function instance($name = 'default')
    {
        $_instanceKey = $name;
        if (empty(self::$_instance[$_instanceKey])) {
            $obj                            = new self();
            self::$_instance[$_instanceKey] = $obj;
        } else {
            $obj = self::$_instance[$_instanceKey];
        }

        $obj->setName($name);
        return $obj;
    }


    /**
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public function getHtml(Q_Request $input, Q_Response $output)
    {
        $data = [
            '_name'  => $this->getName(),
            '_title' => $this->getTitle(),
            '_cols'  => $this->getCols(),
            '_data'  => $this->getData(),
        ];

        return $output->fetchCol($this->getTpl(), $data);
    }
}