<?php
class News_Page_Default extends News_Page_Abstract
{
    /**
     *
     * 首页
     */
    public function doDefault(Q_Request $input, Q_Response $output)
    {
        $_class = Q_DAL_Client::instance()->call('Class', 'GetPairs', [
            'valueName' => 'class_name'
        ]);
        $collect = $this->domyConllect();
        $where = [
          'where' => [
              'state' => 1
          ]
        ];
        $news   = Q_DAL_Client::instance()->call('List', 'Gets', $where);

        $_data  = [];
        if ($news) {
            foreach ($news as $key => $val) {
                if (!empty($_data[$val['class_id']]['content']) && count($_data[$val['class_id']]['content']) > 12) {
                    continue;
                }
                if (strlen($val['news_name']) > 54) {
                    $val['news_name'] = substr($val['news_name'], 0, 54) . '...';
                }
                $_data[$val['class_id']]['class_name'] = empty($_class[$val['class_id']]) ? '-' : $_class[$val['class_id']];
                $_data[$val['class_id']]['content'][]  = $val;

            }
        }
        $output->collect = $collect;
        $output->class   = $_class;
        $output->data    = $_data;
        $output->clearLayout()->setTemplate();
    }

    /**
     *
     * 获取收藏信息
     */
    public function domyConllect()
    {
        $userId = self::$_userId;
        if ($userId) {
            $where = [
                'where' => [
                    'userId' => $userId
                ]
            ];

            $data = Q_DAL_Client::instance()->call('Collect', 'GetCollect', $where);
            return $data;
        }
        return false;
    }
}
