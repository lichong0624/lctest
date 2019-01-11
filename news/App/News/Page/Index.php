<?php

class News_Page_Index extends News_Page_Abstract
{
    public function doIndex(Q_Request $input, Q_Response $output)
    {
     $output->clearLayout()->setTemplate();
    }

}
