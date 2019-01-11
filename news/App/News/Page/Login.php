<?php

class News_Page_Login extends News_Page_Abstract
{
    public function doDefault(Q_Request $input, Q_Response $output)
    {
        $output->clearLayout()->setTemplate();
    }

    /*
     * 登录
     * */

    public function doLogin(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray();

        if (empty($data['username']) || empty($data['password'])) {
            self::showMsg(array('msg' => '账号密码不能为空', 'callbackUrl' => '/?c=Login&a=Default'));
        }

        $where = [
            'where' => [
                'username' => $data['username']
            ]
        ];

        $user = Q_DAL_Client::instance()->call('Login', 'GetRow', $where);
        $password = md5($data['password'].'/!@#$%^&*()');
        if (isset($user) && $user['password'] == $password ) {
           self::$_userId=$user['id'];
           $this->_cacheLoginInfo($input, $output);
           self::showMsg(array('msg' => '登录ok!', 'callbackUrl' => '/?c=Default&a=Default'));
        }
        self::showMsg(array('msg' => '账号或密码错误', 'callbackUrl' => '/?c=Login&a=Default'));
    }
    /**
     * 缓存登录信息
     *s
     * @param Q_Request  $input
     * @param Q_Response $output
     */
    private static function _cacheLoginInfo(Q_Request $input, Q_Response $output)
    {

        $allowRemember = $input->postArray('username');

        $expire        = $allowRemember ? 86400 * 30 : null;

        session_id() || session_start();
        Q_Cookie::setKey(session_id());

        Q_Cookie::set('userId', self::$_userId, $expire);
        $output->session('userId', self::$_userId);
    }

    /*
     * 退出
     * */
    public function doUp(Q_Request $input, Q_Response $output)
    {
        session_id() || session_start();
        session_regenerate_id(true);
        Q_Cookie::del('userId');
        self::showMsg(array('msg' => '退出成功!', 'callbackUrl' => '/?c=Default&a=Default'));

    }

    /*
     * 注册
     * */
    public function doEnroll(Q_Request $input, Q_Response $output)
    {
        $data = $input->postArray();
        if (empty($data['username']) || empty($data['password']) || empty($data['passwordt'])) {
            self::showMsg(array('msg' => '注册信息不能为空', 'callbackUrl' => '/?c=Login&a=Default'));
        }
        if ($data['password'] != $data['passwordt']) {
            self::showMsg(array('msg' => '两次密码不一致', 'callbackUrl' => '/?c=Login&a=Default'));
        }
        $_state = Q_DAL_Client::instance()->call('Login', 'save', $data);
        if ($_state) {
            self::showMsg(array('msg' => '注册成功.请登录', 'callbackUrl' => '/?c=Login&a=Default'));
        }
    }
    /*
     * username判断
     * */
    public function doUserEnroll(Q_Request $input, Q_Response $output)
    {
        $data = $input ->postArray();
        if (empty($data)) {
            return $output->jsonReturn(null, Q_Response::STATUS_SUCCESS, '用户为空');
        }

        $where = [
            'where' => [
                'username' => $data['username']
            ]
        ];

        $admin = Q_DAL_Client::instance()->call('Login', 'GetRow', $where);

        if ($admin) {
            return $output->jsonReturn(null, Q_Response::STATUS_SUCCESS, '用户名已存在');
        }else{
            return $output->jsonReturn(null, Q_Response::STATUS_ERROR, '用户名可用');
        }
}
//
//        $admin = $this -> doEnrollValidation($data);
//        if ($admin) {
//
//            $state = Q_DAL_Client::instance()->call('Login', 'save', $data);
//            if ($state) {
//                self::showMsg(array('msg' => '注册成功.请登录', 'callbackUrl' => '/?c=Login&a=Default'));
//            }
//        }



//        news = [
//            'guonei'=>[
//                [
//                    'title'=>'嘻嘻哒',
//                    'url'=>'http://www.baidu.com/?id=2'
//                ],
//                [
//                    'title'=>'嘻嘻哒',
//                    'url'=>'http://www.baidu.com'
//                ]
//            ],
//            'guowai'=>[
//
//            ]
//        ]



    }
    /*
     * 注册信息校验
     * */
//    public function doEnrollValidation($data)
//    {
//        $where = [
//            'where' => [
//                'username' => $data['userName']
//            ]
//        ];
//
//        $admin = Q_DAL_Client::instance()->call('Login', 'GetRow', $where);
//        if ($admin) {
//            return $output->jsonReturn(null, Q_Response::STATUS_SUCCESS, '操作成功');
//        }
//
//       return true;
//    }

