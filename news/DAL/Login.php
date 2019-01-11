<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 18-11-28
 * Time: 下午2:43
 */
class DAL_Login extends Q_DAL_Module
{

    protected $_modelName = 'Model_Login';


    public function callCount(array $param = array())
    {
        return $this->getModel()->count($param);
    }
    /**
     * 保存一条数据
     *
     * @param array $param
     * @return bool|int
     * @throws Q_Exception
     */
    public function callSave(array $param = array())
    {
        $paramObj = Q_Helper_Array::instance($param);

        $userName    = $paramObj->getString('username');
        $password    = $paramObj->getString('password');

        $option =[
            'username' => $userName,
            'password' => md5($password.'/!@#$%^&*()')
        ];

        return $this->getModel()->save($option);
    }


    /**
     * 保存多条数据
     *
     * @param array $param
     * @return bool|int
     * @throws Q_Exception
     */
    public function callSaveAll(array $param = array())
    {
        return $this->getModel()->saveAll($param);
    }

    /**
     * 根据主键删除数据
     *
     * @param array $param
     * @return bool|resource
     * @throws Q_Exception
     */
    public function callDelete(array $param = array())
    {

        $id = $param['id'];
        if ($id) {
            return $this->getModel()->delete($param);
        }


    }

    /**
     * 根据条件删除数据
     *
     * @param array $param
     * @return bool|resource
     * @throws Q_Exception
     */
    public function callDeleteByWhere(array $param = array())
    {
        return $this->getModel()->deleteByWhere($param);
    }

    /**
     * 获取所有数据
     *
     * @param array $param
     * @return array
     * @throws Q_Exception
     */
    public function callGets(array $param = array())
    {
        return  Model_Login::instance()->gets($param);
    }

    /**
     * 获取一行数据
     *
     * @param array $param
     * @return array|mixed
     * @throws Q_Exception
     */
    public function callGetRow(array $param = array())
    {
        return  Model_Login::instance()->getRow($param);
    }

    /**
     * 获取一个字段
     *
     * @param array $param
     * @return mixed
     * @throws Q_Exception
     */
    public function callGetUserName(array $param = array())
    {
        $param =array_merge($param,['field'=>'id']);

        return  Model_Admin::instance()->getOne($param);
    }

    /**
     * 获取以键值对形式的数据
     *
     * @param array $param
     * @return array
     * @throws Q_Exception
     */
    public function callGetPairs(array $param = array())
    {
        $param =array_merge($param,['valueName'=>'username','keyName'=>'id']);
        return  Model_Admin::instance()->getPairs($param);
    }

    /**
     * 获取带有分页的数据
     *
     * @param array $param
     * @return array
     * @throws Q_Exception
     */
    public function callGetList(array $param = array())
    {
        $param =array_merge($param,['page'=>1,'pageSize'=>10]);
        return  Model_Admin::instance()->getList($param);
    }

    /**
     * 根据条件更新数据
     *
     * @param array $param
     * @return bool
     * @throws Q_Exception
     */
    public function callUpdateByWhere(array $param = array())
    {
//        $data = [
//            'news_author'  => $param['news_author'],
//            'news_content' => $param['news_content'],
//            'news_name'    => $param['news_name'],
//            'news_time'    => $param['news_time'],
//            'class'        => $param['class']
//        ];
//        $where = [
//            'id' =>$param['id']
//        ];

        return $this->getModel()->updateByWhere($data,$where);
    }
}
