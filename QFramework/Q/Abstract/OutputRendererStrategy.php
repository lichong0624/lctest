<?php

/*
|---------------------------------------------------------------
| output renderer strategy
|---------------------------------------------------------------
| @package Q
|
*/

abstract class Q_Abstract_OutputRendererStrategy
{
    protected $_blockName = array();

    public function __construct()
    {
    }

    abstract public function render(Q_Abstract_View $view, $col = false);
    abstract public function renderCol(Q_Abstract_View $view);

    public function getBlockName()
    {
        return array_shift($this->_blockName);
    }

    public function setBlockName($name)
    {
        $this->_blockName[] = $name;
        return $this;
    }

    protected function _initEngine(Q_Response $response)
    {
        $this->_engine = $response;
        return $response;
    }

    /**
     * @return Q_Response
     */
    public function getEngine()
    {
        return $this->_engine;
    }


    /**
     * 设置区块开始，此标签后至结束标签的HTML将会被设置到$name这个output变量中
     *
     * @param $name
     */
    public function blockBegin($name)
    {
        $this->setBlockName($name);
        ob_start();
    }

    /**
     * 结束区块，把获取的内容加入output中的blockName变量中
     *
     * @return string
     */
    public function blockEnd()
    {
        $_content = ob_get_clean();
        $this->getEngine()->set($this->getBlockName(), $_content);
        return $_content;
    }
}

