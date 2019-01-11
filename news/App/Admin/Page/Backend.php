<?php

class Admin_Page_Backend extends Admin_Page_Abstract
{
    public function doIndex(Q_Request $input, Q_Response $output)
    {

        $articleNum = Q_DAL_Client::instance()->call('List', 'Count');
        $userNum    = Q_DAL_Client::instance()->call('Login', 'Count');
        $collectNum = Q_DAL_Client::instance()->call('Collect', 'Count');

        $output->colnum = $collectNum;
        $output->artnum = $articleNum;
        $output->usrnum = $userNum;
        $output->clearLayout()->setTemplate();
    }


}
