<?php

class Admin_Page_News extends Admin_Page_Abstract
{

    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    protected $_rules = [
        'news_name' => array(
            'name'  => '新闻标题',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'news_content'=>array(
            'name'  => '新闻内容',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'news_author'=>array(
            'name'  => '新闻作者',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'news_time'=>array(
            'name'  => '发布时间',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
    ];


    public function doAdd(Q_Request $input, Q_Response $output)
    {
        $output->vali = Q_Validate::instance('news')->setRules($this->_rules)->setParams(['e'=>'exec']);
        $output->clearLayout()->setTempLate();
    }


    /*
     *添加新闻
     * */
    public function execAdd(Q_Request $input, Q_Response $output)
    {

        $data = $input->postArray('news');

        $vali = Q_Validate::instance();

        $vali->setRules($this->_rules)->setParams($data);
        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=News&a=Add');
        }
        $res = Q_DAL_Client::instance()->call('List', 'Save', $data);

        if (!$res) {
            self::showMsg('保存失败', self::STATUS_ERROR, 2, '/?c=List&a=Admin');
        }
        //  2  跳转时间
        self::showMsg('保存成功', self::STATUS_SUCCESS, 2, '/?c=List&a=Admin');
    }


}
