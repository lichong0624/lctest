<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 18-11-28
 * Time: 下午2:43
 */
class DAL_List extends Q_DAL_Module
{
    protected $_modelName = 'Model_List';

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

        $newsName    = $paramObj->getString('news_name');
        $newsContent = $paramObj->getString('news_content');
        $newsAuthor  = $paramObj->getString('news_author');
        $newsTime    = $paramObj->getDateTime('news_time');
        $classId     = $paramObj->getInt('class_id');


        $option = [
            'news_time'    => $newsTime,
            'news_author'  => $newsAuthor,
            'news_content' => $newsContent,
            'news_name'    => $newsName,
            'class_id'     => $classId
        ];

        return $this->getModel()->save($option);
    }


    public function callSaveEdit(array $param = array())
    {
        $paramObj = Q_Helper_Array::instance($param);

        $newsName    = $paramObj->getString('news_name');
        $newsContent = $paramObj->getString('news_content');
        $newsAuthor  = $paramObj->getString('news_author');
        $newsTime    = $paramObj->getDateTime('news_time');
        $classId     = $paramObj->getInt('class_id');
        $id          = $paramObj->getInt('id');

        $option = [
            'news_time'    => $newsTime,
            'news_author'  => $newsAuthor,
            'news_content' => $newsContent,
            'news_name'    => $newsName,
            'class_id'     => $classId,
            'id'           => $id
        ];
        return $this->getModel()->save($option, true);
    }


    /**
     *
     * 保存发布新闻信息
     */

    public function callSaveRelease(array $param = array())
    {
        $paramObj = Q_Helper_Array::instance($param);

        $newsName    = (string)$paramObj->getString('news_name');
        $newsContent = $paramObj->getString('news_content');
        $newsAuthor  = $paramObj->getString('news_author');
        $classId     = $paramObj->getInt('class_id');

        $option = [
            'news_name' => $newsName,
            'news_author' => $newsAuthor,
            'news_content' => $newsContent,
            'class_id' => $classId,
            'state' => 0
        ];

        return $this->getModel()->save($option,true);
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
     * 根据条件更新数据
     *
     * @param array $param
     * @return bool
     * @throws Q_Exception
     */
    //    public function callUpdateByWhere(array $param = array())
    //    {
    //
    //        $data  = [
    //            'news_author'  => $param['news_author'],
    //            'news_content' => $param['news_content'],
    //            'news_name'    => $param['news_name'],
    //            'news_time'    => $param['news_time'],
    //            'class_id'     => $param['class_id']
    //        ];
    //        $where = [
    //            'id' => $param['id']
    //        ];
    //        $this->getModel()->updateByWhere($data, $where,true);
    //        $this->getModel()->dumpSql();
    //        Q::debug(11);
    //        return $this->getModel()->updateByWhere($data, $where,true);
    //    }


    /**
     * 获取所有数据
     *
     * @param array $param
     * @return array
     * @throws Q_Exception
     */
    public function callGets(array $param = array())
    {
        return Model_List::instance()->gets($param);
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
        return Model_List::instance()->getRow($param);
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
        $param = array_merge($param, ['field' => 'id']);

        return Model_List::instance()->getOne($param);
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
        $param = array_merge($param, ['valueName' => 'username', 'keyName' => 'id']);
        return Model_List::instance()->getPairs($param);
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
        $param = array_merge($param, ['page' => 1, 'pageSize' => 10]);
        return Model_List::instance()->getList($param);
    }

    public function callCount(array $param = array())
    {
        return $this->getModel()->count($param);
    }
}
