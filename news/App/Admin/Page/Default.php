<?php

class Admin_Page_Default extends Admin_Page_Abstract
{


    protected $_rules = [
        'admin'          => array(
            'name'  => '用户名',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        ),
        'password'       => array(
            'name'  => '密码',
            'rules' => array(
                'required' => ['message', '{name}不能为空'],
            ),
        )
    ];

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

    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    public function doDefault(Q_Request $input, Q_Response $output)
    {

        $output->vali = Q_Validate::instance()->setRules($this->_rules)->setParams(['e'=>'exec']);
        $output->clearLayout()->setTempLate();
    }

    public function execDefault(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray('data');
        $vali = Q_Validate::instance();
        $vali->setRules($this->_rules)->setParams($data);
        if (!$vali->validate()) {
            self::showMsg('验证失败', self::STATUS_ERROR, 2, '/?c=Default&a=Default');
        }
        $where = [
          'where' => [
              'admin' => $data['admin']
          ]
        ];

        $_state = Q_DAL_Client::instance()->call('Admin', 'GetRow', $where);
        $password = md5($data['password'].'/!@#$%^&*()');
        if ($_state && $_state['password'] == $password ) {

            self::$_adminId=$_state['id'];
            $this->_cacheLoginInfo($input, $output);

            self::showMsg(array('msg' => '登录ok!', 'callbackUrl' => '/?c=Backend&a=Index'));
        }
        self::showMsg(array('msg' => '账号或密码错误', 'callbackUrl' => '/?c=Default&a=Default'));
    }

    /**
     * 缓存登录信息
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     */
    private static function _cacheLoginInfo(Q_Request $input, Q_Response $output)
    {
        $allowRemember = $input->postArray('data');
        $expire        = $allowRemember ? 86400 * 30 : null;

        session_id() || session_start();
        Q_Cookie::setKey(session_id());

        Q_Cookie::set('adminId', self::$_adminId, $expire);
        $output->session('adminId', self::$_adminId);
    }



    public function doExit(Q_Request $input, Q_Response $output)
    {
        session_id() || session_start();
        session_regenerate_id(true);
        Q_Cookie::del('adminId');
        self::showMsg(array('msg' => '退出成功!', 'callbackUrl' => '/?c=Default&a=Default'));
    }






}
