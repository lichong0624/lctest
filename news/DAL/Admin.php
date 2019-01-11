<?php

class DAL_Admin extends Q_DAL_Module
{
    protected $_modelName = 'Model_Admin';

    /**
     * 获取一行数据
     *
     * @param array $param
     * @return array|mixed
     * @throws Q_Exception
     */
    public function callGetRow(array $param = array())
    {
        return  Model_Admin::instance()->getRow($param);
    }

    public function callUpdateByPwd(array $param = array())
    {
        $data  = [
            'password' => md5($param['password'].'/!@#$%^&*()')
        ];

        $where = [
            'admin'   => $param['admin']
        ];
        return  $this->getModel()->updateByWhere($data,$where);
    }

    public function callSave(array $param = array())
    {
        $paramObj = Q_Helper_Array::instance($param);

        $nickname    = $paramObj->getString('nickname');
        $sex         = $paramObj->getInt('sex');
        $mobile      = $paramObj->getString('mobile');
        $desc        = $paramObj->getString('desc');
        $id          = $paramObj->getInt('id');

        $option = [
            'nickname'    => $nickname,
            'sex'         => $sex,
            'mobile'      => $mobile,
            'desc'        => $desc,
            'id'          => $id
        ];
        return $this->getModel()->save($option,true);
    }

//    public function callUpdate(array $param = array())
//    {
//        $data = [
//            'nickname' => $param['nickname'],
//            'sex'      => $param['sex'],
//            'mobile'   => $param['mobile'],
//            'desc'     => $param['desc']
//        ];
//
//        $where = [
//            'id'    => $param['id']
//        ];
//
//        return $this->getModel()->updateByWhere($data,$where);
//    }
}
