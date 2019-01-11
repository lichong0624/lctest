<?php

class Admin_Page_Add extends Admin_Page_Abstract
{

    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    protected $_rules = [
        'id'          => array(
            'name'  => '新闻序号',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'news_name'    => array(
            'name'  => '新闻标题',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'news_content' => array(
            'name'  => '新闻内容',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'news_author'  => array(
            'name'  => '新闻作者',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'news_time'    => array(
            'name'  => '发布时间',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
    ];

    public function doIndex(Q_Request $input, Q_Response $output)
    {

        $id = $input->getInt('id', 0);
        if ($id < 0) {
            return false;
        }
        $data = Q_DAL_Client::instance()->call('List', 'GetRow', [
            'where' => [
                'id' => $id
            ]
        ]);

        $vali         = Q_Validate::instance();
        $output->vali = Q_Validate::instance()->setRules($this->_rules)->setParams($data);
        $output->vali = $vali;
        $output->clearLayout()->setTempLate();
    }

    /*
     * 编辑
     * */

    public function execIndex(Q_Request $input, Q_Response $output)
    {

        $data = $input->postArray('data');

        $vali = Q_Validate::instance();
        $vali->setRules($this->_rules)->setParams($data);

        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=List&a=Admin');
        }
        $res = Q_DAL_Client::instance()->call('List', 'UpdateByWhere', $data);

        if (!$res) {
            self::showMsg('保存失败', self::STATUS_ERROR, 2, '/?c=List&a=Admin');
        }
        //  2  跳转时间
        self::showMsg('保存成功', self::STATUS_SUCCESS, 2, '/?c=List&a=Admin');
    }

    /**
     *  删除
     * @param Q_Request  $input
     * @param Q_Response $output
     * @throws Q_Exception
     */

    public function doExid(Q_Request $input, Q_Response $output)
    {
       $id = $input->getInt('id');
       $data = [
           'id' => $id
       ];
       $res = Q_DAL_Client::instance()->call('List', 'Delete', $data);

        if (!$res) {
            self::showMsg('删除失败', self::STATUS_ERROR, 2, '/?c=List&a=Admin');
        }
        //  2  跳转时间
        self::showMsg('删除成功', self::STATUS_SUCCESS, 2, '/?c=List&a=Admin');
    }
}
