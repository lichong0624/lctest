<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 18-12-12
 * @version     : $id$
 */
class DAL_Collect extends Q_DAL_Module
{
    protected $_modelName = 'Model_Collect';

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

        $userId = $paramObj->getInt('userId');
        $newId  = $paramObj->getInt('newId');
        $time   = date('Y-m-d');
        $option = [

            'user_id' => $userId,
            'new_id'  => $newId,
            'state'   => 1,
            'time'    => $time
        ];

        return $this->getModel()->save($option);
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
//        Q::debug($param);
        return Model_Collect::instance()->getRow($param);
    }

    public function callCount(array $param = array())
    {
        return $this->getModel()->count($param);
    }


    public function callGets(array $param = array())
    {
        return $this->getModel()->gets($param);
    }

    /**
     *
     * 我的收藏
     */
    public function callGetCollect(array $param = array())
    {

        $table = 'test.new_collect AS C LEFT JOIN test.news AS N ON ( C.new_id = N.id)';
        $where = [
            'where' => [
                'C.user_id' => $param['where']['userId'],
            ],
            'num'   => 5,
            'field' => 'C.* , N.news_name',
            'table' => $table,
        ];
        return $this->getModel()->gets($where);
    }

    /**
     *
     * 删除手藏
     */

    public function callDelete(array $param = array())
    {
        return $this->getModel()->delete($param);
    }
}
