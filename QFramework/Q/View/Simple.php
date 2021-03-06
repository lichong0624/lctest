<?php

/*
|---------------------------------------------------------------
| Wrapper for simple HTML views.
|---------------------------------------------------------------
| @package Q
|
*/

class Q_View_Simple extends Q_Abstract_View
{
    /*
    |---------------------------------------------------------------
    | HTML renderer decorator
    |---------------------------------------------------------------
    | @param Q_Response $data
    | @param string $templateEngine
    |
    */
    public function __construct(Q_Response $response, $templateEngine = null)
    {
        //  prepare renderer class
        if (is_null($templateEngine)) {
            $templateEngine = 'php';
        }
        $templateEngine =  ucfirst($templateEngine);
        $rendererClass  = 'Q_OutputRenderer_' . $templateEngine . 'Strategy';

        parent::__construct($response, new $rendererClass);
    }

    public function postProcess(Q_View $view)
    {
        // do nothing
    }
}
