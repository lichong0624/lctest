<?php
class News_Page_Release extends News_Page_Abstract
{
    public function doIndex(Q_Request $input, Q_Response $output)
    {
        $userId         = self::$_userId;
        $class          = (array)Q_DAL_Client::instance()->call('Class', 'GetNames');
        $output->userId = $userId;
        $output->class  = $class;
        $output->vali   = Q_Validate::instance()->setRules($this->_getRules($input, $output))->setParams(['e' => 'exec']);
        $output->clearLayout()->setTemplate();
    }

    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    private static function _getRules(Q_Request $input, Q_Response $output)
    {
        $_rules = [
            'news_name'    => [
                'name'  => '标题',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                ],
            ],
            'news_content' => [
                'name'  => '内容',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                ],
            ],
            'class_id'     => [
                'name'  => '分类',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                ],
                'data'  => ['' => '请选择'],
            ],
        ];
        return $_rules;
    }

    public function execIndex(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray('data');
        $vali = Q_Validate::instance();
        $vali->setRules($this->_getRules($input, $output))->setParams($data);
        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=Release&a=Index');
        }
        $state = Q_DAL_Client::instance()->call('List', 'SaveRelease', $data);
        Q::debug(1224);
    }
}
