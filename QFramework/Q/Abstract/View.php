<?php

/*
|---------------------------------------------------------------
| Container for output data and renderer strategy.
|---------------------------------------------------------------
| @package Q
|
*/

abstract class Q_Abstract_View
{

    /*
    |---------------------------------------------------------------
    | Response object.
    |---------------------------------------------------------------
    | @var Q_Response
    */
    public $data;

    /*
    |---------------------------------------------------------------
    | Reference to renderer strategy.
    |---------------------------------------------------------------
    | @var Q_OutputRendererStrategy
    */
    protected $_rendererStrategy;

    /*
    |---------------------------------------------------------------
    | Constructor.
    |---------------------------------------------------------------
    | @param Q_Response $data
    | @param Q_OutputRendererStrategy $rendererStrategy
    | @return Q_View
    */
    public function __construct(Q_Response $response, Q_Abstract_OutputRendererStrategy $rendererStrategy)
    {
        $this->data = $response;
        $this->_rendererStrategy = $rendererStrategy;
    }

    /*
    |---------------------------------------------------------------
    | Post processing tasks specific to view type.
    |---------------------------------------------------------------
    | @param Q_View $view
    | @return boolean
    */
    abstract public function postProcess(Q_View $view);

    /*
    |---------------------------------------------------------------
    | Delegates rendering strategy based on view.
    |---------------------------------------------------------------
    | @param Q_View $this
    | @return string   Rendered output data
    */
    public function render()
    {
        return $this->_rendererStrategy->render($this);
    }

    public function renderCol()
    {
        return $this->_rendererStrategy->renderCol($this);
    }
}
