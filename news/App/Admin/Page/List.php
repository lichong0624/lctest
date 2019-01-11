<?php

class Admin_Page_List extends Admin_Page_Abstract
{

    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    private static function _getRules(Q_Request $input, Q_Response $output)
    {
        $class = (array)Q_DAL_Client::instance()->call('Class', 'GetNames');

        $_rules = array(
            'id'           => array(
                'name'  => '序号',
                'rules' => array(
                    'required' => ['message', '{name}不能为空'],
                ),
            ),
            'news_name'    => array(
                'name'  => '标题',
                'rules' => array(
                    'required' => ['message', '{name}不能为空'],
                ),
            ),
            'news_content' => array(
                'name'  => '内容',
                'rules' => array(
                    'required' => ['message', '{name}不能为空'],
                ),
            ),
            'news_author'  => array(
                'name'  => '作者',
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
            'class_id'     => array(
                'name'  => '分类',
                'rules' => array(
                    'required' => ['message', '{name}不能为空'],
                ),
                'data'  => array('' => '请选择') + $class,
            ),
        );
        return $_rules;
    }

    private static function _getAddRules(Q_Request $input, Q_Response $output)
    {

        $class = (array)Q_DAL_Client::instance()->call('Class', 'GetNames');

        $_rules = array(
            'news_name'    => array(
                'name'  => '标题',
                'rules' => array(
                    'required' => ['message', '{name}不能为空'],
                ),
            ),
            'news_content' => array(
                'name'  => '内容',
                'rules' => array(
                    'required' => ['message', '{name}不能为空'],
                ),
            ),
            'news_author'  => array(
                'name'  => '作者',
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
            'class_id'     => array(
                'name'  => '分类',
                'rules' => array(
                    'required' => ['message', '{name}不能为空'],
                ),
                'data'  => array('' => '请选择') + $class,
            ),
        );

        return $_rules;
    }

    /**
     * 新闻列表
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @throws Q_Exception
     */
    public function doAdmin(Q_Request $input, Q_Response $output)
    {
        $data = (array)Q_DAL_Client::instance()->call('List', 'Gets', ['order' => 'id ASC']);

        $ids   = array_column($data, 'class_id');
        $where = [
            'where' => [
                'id' => $ids
            ]
        ];
        $class = Q_DAL_Client::instance()->call('Class', 'Gets', $where);

        foreach ($data as $k => &$v) {
            $v['class_name'] = empty($class[$v['class_id']]['class_name']) ? '' : $class[$v['class_id']]['class_name'];
        }
        $output->data = $data;
        $output->clearLayout()->setTempLate();
    }

    /**
     *
     * 编辑
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @throws Q_Exception
     */

    public function doEdit(Q_Request $input, Q_Response $output)
    {

        $id    = $input->get('id');
        $where = [
            'where' => [
                'id' => $id
            ]
        ];
        $class = (array)Q_DAL_Client::instance()->call('Class', 'GetNames');
        $data  = Q_DAL_Client::instance()->call('List', 'GetRow', $where);

        $output->class = $class;
        $output->vali  = Q_Validate::instance()->setRules($this->_getRules($input, $output))->setParams($data);
        $output->clearLayout()->setTemplate();
    }

    public function execEdit(Q_Request $input, Q_Response $output)
    {

        $data = $input->postArray('data');

        $vali = Q_Validate::instance();
        $vali->setRules($this->_getRules($input, $output))->setParams($data);
        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=List&a=Edit&id=' . $data['id']);
        }

        $state = Q_DAL_Client::instance()->call('List', 'SaveEdit', $data);
        if (!$state) {
            self::showMsg('修改失败', self::STATUS_ERROR, 2, '/?c=List&a=Admin');
        }
        self::showMsg('修改成功', self::STATUS_SUCCESS, 2, '/?c=List&a=Admin');

    }

    /**
     *
     * 删除
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @throws Q_Exception
     */

    public function doDele(Q_Request $input, Q_Response $output)
    {
        $id     = $input->get('id');
        $where  = [
            'id' => $id
        ];
        $_state = Q_DAL_Client::instance()->call('List', 'Delete', $where);

        if ($_state) {
            self::showMsg('删除成功', self::STATUS_SUCCESS, 2, '/?c=List&a=Admin');
        }
        self::showMsg('删除失败', self::STATUS_ERROR, 2, '/?c=List&a=Admin');
    }

    /**
     * 添加
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     */
    public function doAdd(Q_Request $input, Q_Response $output)
    {
        $output->vali  = Q_Validate::instance()->setRules($this->_getAddRules($input, $output))->setParams(['e' => 'exec']);
        $class         = (array)Q_DAL_Client::instance()->call('Class', 'GetNames');
        $output->class = $class;
        $output->clearLayout()->setTemplate();
    }

    public function execAdd(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray('data');
        $vali = Q_Validate::instance();
        $vali->setRules($this->_getAddRules($input, $output))->setParams($data);
        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=List&a=Add');
        }

        $_state = Q_DAL_Client::instance()->call('List', 'Save', $data);
        if ($_state) {
            self::showMsg('保存成功', self::STATUS_SUCCESS, 2, '/?c=List&a=Admin');
        }
        self::showMsg('保存失败', self::STATUS_ERROR, 2, '/?c=List&a=Add');
    }
}
