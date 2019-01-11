<?php

class Q_OutputRenderer_PhpStrategy extends Q_Abstract_OutputRendererStrategy
{

    /**
     * @var Q_Response
     */
    protected $_engine;

    public function render(Q_Abstract_View $view, $col = false)
    {
        $engine  = $this->_initEngine($view->data);
        $tplFile = $engine->getTemplate($col);

        if (!$tplFile) {
            return null;
        } elseif (!Q_File::exists($tplFile)) {
            throw new Q_Exception('The template dose not exist or is not readable: ' . $tplFile);
        }

        $variables = $engine->getBody();

        if (!empty($variables)) {
            extract($variables);
        }

        ob_start();
        include $tplFile;
        $content = ob_get_clean();
        return $content;
    }

    public function renderCol(Q_Abstract_View $view)
    {
        return $this->render($view, true);
    }
}


