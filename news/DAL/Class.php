<?php

class DAL_Class extends Q_DAL_Module
{
    protected $_modelName = 'Model_Class';

    /**
     * 获取一行数据
     *
     * @param array $param
     * @return array|mixed
     * @throws Q_Exception
     */
    public function callGetRow(array $param = array())
    {
        return  Model_Class::instance()->getRow($param);
    }

    public function callSave(array $param = array())
    {
        $paramObj = Q_Helper_Array::instance($param);

        $class    = $paramObj->getString('class_name');

        $option =[
            'class_name'    => $class
        ];

        return $this->getModel()->save($option);
    }

    public function callUpdateByWhere(array $param = array())
    {
        $data = [
            'class_name'  => $param['class_name'],
        ];

        $where = [
            'id' =>$param['id']
        ];

        return $this->getModel()->updateByWhere($data,$where);
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
     * 获取所有数据
     *
     * @param array $param
     * @return array
     * @throws Q_Exception
     */
    public function callGets(array $param = array())
    {
        return  Model_Class::instance()->gets($param);
    }

//    public function callGetName(array $param = array())
//    {
//
//        return $this->getModel()->getPairs($param);
//
//    }

    public function callGetNames(array $param = array())
    {
        $paramObj = Q_Helper_Array::instance($param);

        $id       = $paramObj->getMixed('id');

        $option   = [
            'valueName' => 'class_name'
        ];

        $id && $option['where']['id'] = $id;

        return $this->getModel()->getPairs($option);

    }
}
