<?php


class News_Page_New extends News_Page_Abstract
{

//    /**
//     *
//     * 首页
//     */
//    public function doIndex(Q_Request $input, Q_Response $output)
//    {
//        $_class = Q_DAL_Client::instance()->call('Class', 'GetPairs', [
//            'valueName' => 'class_name'
//        ]);
//        $collect = $this->domyConllect();
//        $news   = Q_DAL_Client::instance()->call('List', 'Gets');
//        $_data  = [];
//        foreach ($news as $key => $val) {
//            if (!empty($_data[$val['class_id']]['content']) && count($_data[$val['class_id']]['content']) > 12) {
//                continue;
//            }
//            if (strlen($val['news_name']) > 54) {
//                $val['news_name'] = substr($val['news_name'], 0, 54) . '...';
//            }
//            $_data[$val['class_id']]['class_name'] = empty($_class[$val['class_id']]) ? '-' : $_class[$val['class_id']];
//            $_data[$val['class_id']]['content'][]  = $val;
//
//        }
//
//        $output->collect = $collect;
//        $output->class   = $_class;
//        $output->data    = $_data;
//        $output->clearLayout()->setTemplate();
//    }

    /**
     *
     * 详情页
     */
    public function doContent(Q_Request $input, Q_Response $output)
    {
        $id     = $input->getInt('id');
        $userId = self::$_userId;

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

        $where = [
            'where' => [
                'id>' => $id,
            ],
            'order' => 'id ASC'
        ];
        $down  = Q_DAL_Client::instance()->call('List', 'GetRow', $where);
        $statc = $this->doJudge($id, $userId);

        $_class = Q_DAL_Client::instance()->call('Class', 'Gets');

        $output->class  = $_class;
        $output->statc  = $statc;
        $output->userId = $userId;
        $output->up     = $up;
        $output->down   = $down;
        $output->data   = $data;
        $output->clearLayout()->setTemplate();
    }


    /*
     * 收藏
     * */
    public function doCollect(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray();

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

        $data = $input->postArray();
      // Q::debug($data,false);
        $where = [
            'where' => [
                'user_id' => $data['userId'],
            ]
        ];
        $statc = Q_DAL_Client::instance()->call('Collect', 'GetRow', $where);
       //Q::debug($statc,false);
        if ($data['newId'] == $statc['new_id']) {
            $where = [
                'id' => $statc['id']
            ];

            $statc = Q_DAL_Client::instance()->call('Collect', 'Delete', $where);
           // Q::debug($statc);
            if ($statc) {
                return $output->jsonReturn(null, Q_Response::STATUS_SUCCESS, '操作成功');
            }
        }
    }

    /*
     * 收藏判断
     * */
    public function doJudge($id, $userId)
    {
        $where = [
            'where' => [
                'new_id'  => $id,
                'user_id' => $userId
            ]
        ];
        $statc = Q_DAL_Client::instance()->call('Collect', 'GetRow', $where);
        return $statc;
    }

}
