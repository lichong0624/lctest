<?php

class Admin_Page_Admin extends Admin_Page_Abstract
{

    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    protected static function _getChangePwd(Q_Request $input, Q_Response $output)
    {
        $_rules = [
            'admin'  => [
                'name' => '账号',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                ],
            ],
            'old_password'     => [
                'name'  => '旧密码',
                'rules' => [
                    'required' => ['message' => '{name}不能为空'],
                ],
            ],
            'password' => [
                'name' => '新密码',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                ],
            ],
            'confirm_password' => [
                'name'  => '确认密码',
                'rules'  => [
                    'required' => ['message', '{name}不能为空'],
                    'compare'  => ['field' => 'password', 'message' => '两次新密码不一致'],
                ]
            ]
        ];
        return $_rules;
    }

    private static function _getRules(Q_Request $input, Q_Response $output)
    {
        $_rules = [
            'id'            => [
                'name'  => '管理员Id'
            ],
            'nickname'      => [
                'name'  => '昵称',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                ],
            ],
            'sex'           => [
                'name'  => '性别',
                'type'  => 'select',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                ],
            ],
            'mobile'           => [
                'name'  => '手机',
                'rules' => [
                    'required' => ['message', '{name}不能为空'],
                    'mobile'   => ['message', '手机号码格式错误'],
                ],
            ],
            'desc'           => [
                'name'  => '简介',
            ]
        ];
        return $_rules;
    }

    public function doIndex(Q_Request $input, Q_Response $output)
    {

        $data = Q_DAL_Client::instance()->call('Admin', 'Gets', ['order' => 'id ASC']);

        $output->data = $data;
        $output->clearLayout()->setTemplate();
    }

    /**
     *
     * 修改密码
     */

    public function doChangePwd(Q_Request $input, Q_Response $output)
    {
        $output->vali = Q_Validate::instance()->setRules($this->_getChangePwd($input, $output))->setParams(['e'=>'exec']);
        $output->clearLayout()->setTemplate();
    }

    public function execChangePwd(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray('data');
        $vali = Q_Validate::instance();
        $vali->setRules($this->_getChangePwd($input, $output))->setParams($data);
        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=Default&a=ChangePwd');
        }

        $admin  = $data['admin'];
        $oldPwd = md5($data['old_password'].'/!@#$%^&*()');
        $where = [
            'where' => [
                'admin' => $admin
            ]
        ];
        $adminData = Q_DAL_Client::instance()->call('Admin', 'GetRow', $where);

        if (!$adminData) {
            return $vali->addErrorMessage('admin', '账号不正确');
        }
        if ($oldPwd != $adminData['password']) {
            return $vali->addErrorMessage('old_password', '旧密码不正确');
        }
        if ($data['old_password'] == $data['password']) {
            return $vali->addErrorMessage('password', '新密码与旧密码重复');
        }
        if ($data['password'] != $data['confirm_password']) {
            return $vali->addErrorMessage('confirm_password', '两次密码不一致');
        }

        $_state = Q_DAL_Client::instance()->call('Admin', 'UpdateByPwd', $data);
        if ($_state) {
            self::showMsg('修改成功,请重新登录', self::STATUS_SUCCESS, 2, '/?c=Default&a=Default');
        }
        self::showMsg('修改失败', self::STATUS_ERROR, 2, '/?c=Default&a=ChangePwd');
    }

    /**
     *
     * 基本信息
     */

    public function doBasicData(Q_Request $input, Q_Response $output)
    {
        $_adminid = self::$_adminId;
        $where =[
            'where' => [
                'id' => $_adminid
            ]
        ];
        $data = Q_DAL_Client::instance()->call('Admin', 'GetRow', $where);
        $sex  = [ '0' => '未知' , '1' => '男' , '2' => '女' ];
        $output->sex = $sex;
        $output->vali = Q_Validate::instance()->setRules($this->_getRules($input, $output))->setParams($data);
        $output->clearLayout()->setTemplate();
    }

    public function execBasicData(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray('data');

        $vali = Q_Validate::instance();
        $vali->setRules($this->_getRules($input, $output))->setParams($data);
        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=Default&a=BasicData');
        }

        $_state = Q_DAL_Client::instance()->call('Admin', 'Save', $data);

        if ($_state) {
            self::showMsg('保存成功', self::STATUS_SUCCESS, 2, '/?c=Default&a=BasicData');
        }
        self::showMsg('保存失败', self::STATUS_ERROR, 2, '/?c=Default&a=BasicData');
    }
}
