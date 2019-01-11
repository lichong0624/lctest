<?php
class News_Page_Details extends News_Page_Abstract
{
    public function doDefault(Q_Request $input, Q_Response $output)
    {
        $id    = $input->getInt('id');
        $adminId = self::$_adminId;

        $where = [
            'where' => [
                'id' => $id,
            ],
            'order' => 'id DESC'
        ];
        $data  = Q_DAL_Client::instance()->call('List', 'GetRow', $where);

        $where = [
            'where' => [
                'id<' => $id,
            ],
            'order' => 'id DESC'
        ];
        $up    = Q_DAL_Client::instance()->call('List', 'GetRow', $where);

        $where   = [
            'where' => [
                'id>' => $id,
            ],
            'order' => 'id ASC'
        ];
        $down    = Q_DAL_Client::instance()->call('List', 'GetRow', $where);
        $statc=$this->doJudge($id, $adminId);

        $output->statc   = $statc;
        $output->adminId = $adminId;
        $output->up      = $up;
        $output->down    = $down;
        $output->data    = $data;
        $output->clearLayout()->setTemplate();
    }

    /*
     * 收藏
     * */
    public function doCollect(Q_Request $input, Q_Response $output)
    {
        $data  = $input->postArray();
        $statc = Q_DAL_Client::instance()->call('Collect', 'Save', $data);
        if (!$statc) {
            self::showMsg(array('msg' => '收藏失败,请稍后再试', 'callbackUrl' => '/?c=Details&a=Default'));
        }
        return $output->jsonReturn(null, Q_Response::STATUS_SUCCESS, '操作成功');
    }
    /*
     * 取消收藏
     * */
    public function doDeleCollect(Q_Request $input, Q_Response $output)
    {
        $data  = $input->postArray();

        $where = [
            'where' => [
                'adminid' =>$data['adminid'],
            ]
        ];
        $statc = Q_DAL_Client::instance()->call('Collect', 'GetRow', $where);

        if ($data['newid'] == $statc['newid']) {
            $where = [
                    'id' => $statc['id']
                ];

            $statc = Q_DAL_Client::instance()->call('Collect', 'Delete', $where);
            //Q::debug($statc);
            if ($statc) {
                return $output->jsonReturn(null, Q_Response::STATUS_SUCCESS, '操作成功');
            }
       }
    }
    /*
     * 收藏判断
     * */
    public function doJudge($id, $adminId)
    {
        $where = [
            'where' => [
                'newid' => $id,
                'adminid' => $adminId
            ]
        ];
       $statc = Q_DAL_Client::instance()->call('Collect', 'GetRow', $where);
       return $statc;
    }
}
