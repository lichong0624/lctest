<?php

class Admin_Page_User extends Admin_Page_Abstract
{

    public function doIndex(Q_Request $input, Q_Response $output)
    {
        $data = Q_DAL_Client::instance()->call('Login', 'Gets',['order' => 'id ASC']);
        $output->data = $data;
        $output->clearLayout()->setTemplate();
    }
}
