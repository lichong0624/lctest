<?php
/**
 * 模块抽象类
 *
 * @author        wiki <charmofcus@gmail.com>
 * @copyright (c) 2014-10-04
 */
abstract class Q_DAL_Module
{
    const PARAM_KEY_SAVE_REPLACE = '__RELACE__';

    /**
     * @var $this
     */
    protected static $_instance;

    /**
     * 模块名称
     *
     * @var
     */
    protected $_moduleName;

    /**
     * 当前方法名
     *
     * @var
     */
    protected $_methodName;

    /**
     * 模型名称
     *
     * @var string
     */
    protected $_modelName;

    /**
     * 数据库表名
     *
     * @var string
     */
    protected $_dbTableName;

    /**
     * 数据库名称
     *
     * @var string
     */
    protected $_dbConnName = 'default';

    /**
     * 主键字段名
     *
     * @var string
     */
    protected $_pkName = 'id';

    /**
     * 获取模块名
     *
     * @return mixed
     * @throws Q_Exception
     */
    public function getModuleName()
    {
        $moduleName = $this->_moduleName;
        if (!$moduleName) {
            $moduleName = $this->setModuleName()->getModuleName();
        }
        return $moduleName;
    }

    /**
     * 设置模块名
     *
     * @param string $moduleName
     * @return $this
     * @throws Q_Exception
     */
    public function setModuleName($moduleName = '')
    {
        if (!$moduleName) {
            $_className = get_called_class();
            $moduleName = substr($_className, 4);
        }

        if (!$moduleName) {
            throw new Q_Exception('moduleName is empty!');
        }

        $this->_moduleName = $moduleName;
        return $this;
    }

    /**
     * 设置当前调用的方法名
     *
     * @param string $methodName
     * @return $this
     */
    public function setMethodName($methodName = '')
    {
        $this->_methodName = $methodName;
        return $this;
    }

    /**
     * 获取当前方法名
     *
     * @return mixed
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * 获取当前模型名称
     *
     * @return string Q_Model
     */
    public function getModelName()
    {
        return $this->_modelName;
    }

    /**
     * 设置当前模型名称
     *
     * @param string $modelName
     * @return $this
     * @throws Q_Exception
     */
    public function setModelName($modelName = '')
    {
        if (!$modelName) {
            throw new Q_Exception('modelName is empty!');
        }

        $this->_modelName = $modelName;
        return $this;
    }

    /**
     * 获取当前数据库名称
     *
     * @return string
     */
    public function getDbConnName()
    {
        return $this->_dbConnName;
    }

    /**
     * 设置当前数据库名称
     *
     * @param string $dbConnName
     * @return $this
     */
    public function setDbConnName($dbConnName)
    {
        $this->_dbConnName = $dbConnName;
        return $this;
    }

    /**
     * 获取当前数据库该表表名
     *
     * @return string
     */
    public function getDbTableName()
    {
        return $this->_dbTableName;
    }

    /**
     * 设置当前数据库该表表名
     *
     * @param string $dbTableName
     * @return $this
     * @throws Q_Exception
     */
    public function setDbTableName($dbTableName = '')
    {
        if (!$dbTableName) {
            throw new Q_Exception('dbTableName is empty!');
        }

        $this->_dbTableName = $dbTableName;
        return $this;
    }

    /**
     * 获取主键字段名
     *
     * @return string
     */
    public function getPkName()
    {
        return $this->_pkName;
    }

    /**
     * 设置主键字段名
     *
     * @param string $pkName
     * @return $this
     */
    public function setPkName($pkName)
    {
        $this->_pkName = $pkName;
        return $this;
    }


    /**
     * @return $this
     */
    public static function instance()
    {
        $_className = get_called_class();

        if (isset(self::$_instance[$_className])) {
            return self::$_instance[$_className];
        }
        self::$_instance[$_className] = $_obj = new $_className();
        return $_obj;
    }

    /**
     * 验证方法
     *
     * @param array $param
     * @return bool
     */
    public function validate(array $param)
    {
        return true;
    }

    /**
     * 获取HTML代码内容
     *
     * @param        $tpl
     * @param array  $data
     * @param string $appName 应用名
     * @return string
     */
    public function rander($tpl, array $data = array(), $appName = APP_NAME)
    {
        $output = new Q_Response();
        $output->setAppName($appName)
            ->setCtlName($this->getModuleName())
            ->setActName($this->getMethodName());
        return $output->fetchCol($tpl, $data);
    }

    /**
     * 获取该模型
     *
     * @return Q_Model
     * @throws Q_Exception
     */
    public function getModel()
    {
        $dbConnName  = $this->getDbConnName();
        $dbTableName = $this->getDbTableName();

        $modelName = $this->getModelName();

        if ($modelName) {
            /**
             * @var $modelName Q_Model
             */
            return $modelName::instance();
        } else if ($dbConnName && $dbTableName) {
            return Q_Model::instance($dbTableName, $dbConnName);
        } else {
            throw new Q_Exception('model is empty!');
        }

    }

    /**
     * 获取一行数据
     *
     * @param array $param
     * @return array|bool
     * @throws Q_Exception
     */
    public function callGet(array $param = array())
    {
        $model = $this->getModel();

        $paramObj = Q_Helper_Array::instance($param);

        $option = $paramObj->setRule($this->_pkName, $paramObj::VAL_TYPE_FUNC_UINT)
            ->setRule('pk', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('fields', $paramObj::VAL_TYPE_FUNC_STRING)
            ->getSafeArr();

        if ($option[$this->_pkName] < 1) {
            throw new Q_Exception('Illegal ' . $this->_pkName, -1001);
        }

        return $model->get($option[$this->_pkName], $option['pk'], $option['fields']);
    }

    /**
     * 获取一行数据
     *
     * @param array $param
     * @return array|mixed
     */
    public function callGetRow(array $param = array())
    {
        $model    = $this->getModel();
        $paramObj = Q_Helper_Array::instance($param);

        $param = $paramObj->setRule('num', $paramObj::VAL_TYPE_FUNC_UINT)
            ->setRule('where', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->setRule('group', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('having', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('order', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('field', $paramObj::VAL_TYPE_FUNC_MIXED)
            ->setRule('hash', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('_string', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('union', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->setRule('_shardName', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->getSafeArr();

        return $model->getRow($param);
    }

    /**
     * 获取一个字段
     *
     * @param array $param
     * @return string|null
     */
    public function callGetOne(array $param = array())
    {
        $model = $this->getModel();

        return $model->getOne($param);
    }

    public function callGetPairs(array $param = array())
    {
        $model    = $this->getModel();
        $paramObj = Q_Helper_Array::instance($param);

        $option = $paramObj->setRule('table', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('keyName', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('valueName', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('field', $paramObj::VAL_TYPE_FUNC_MIXED)
            ->setRule('num', $paramObj::VAL_TYPE_FUNC_UINT)
            ->setRule('where', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->setRule('group', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('having', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('order', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('hash', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('_string', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('fast', $paramObj::VAL_TYPE_FUNC_BOOL, false)
            ->getSafeArr();

        return $model->getPairs($option);

    }

    /**
     * 获取所有
     *
     * @param  array $param
     * @return array
     */
    public function callGets(array $param = array())
    {
        $model    = $this->getModel();
        $paramObj = Q_Helper_Array::instance($param);

        $option = $paramObj->setRule('num', $paramObj::VAL_TYPE_FUNC_UINT)
            ->setRule('where', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->setRule('group', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('having', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('order', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('field', $paramObj::VAL_TYPE_FUNC_MIXED)
            ->setRule('hash', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('_string', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('fast', $paramObj::VAL_TYPE_FUNC_BOOL, false)
            ->setRule('callback', $paramObj::VAL_TYPE_FUNC_MIXED)
            ->setRule('union', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->setRule('_shardName', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->getSafeArr();

        return $model->gets($option);
    }

    /**
     * 获取列表
     *
     * @param array $param
     * @return array
     */
    public function callGetList(array $param = array())
    {
        $model = $this->getModel();

        $paramObj = Q_Helper_Array::instance($param);

        $option = $paramObj->setRule('page', $paramObj::VAL_TYPE_FUNC_RANGE, 1)
            ->setRule('table', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('pageSize', $paramObj::VAL_TYPE_FUNC_UINT, 20)
            ->setRule('where', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->setRule('group', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('having', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('order', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('field', $paramObj::VAL_TYPE_FUNC_MIXED)
            ->setRule('pageBar', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('hash', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->setRule('callback', $paramObj::VAL_TYPE_FUNC_MIXED)
            ->setRule('fast', $paramObj::VAL_TYPE_FUNC_BOOL, false)
            ->setRule('url', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('target', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('union', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->setRule('_shardName', $paramObj::VAL_TYPE_FUNC_MIXED, null, $paramObj::CK_TYPE_ISSET)
            ->getSafeArr();

        return $model->getList($option);
    }

    /**
     * 根据主键删除
     *
     * @param array $param
     * @return bool|resource
     * @throws Q_Exception
     */
    public function callDelete(array $param = array())
    {
        $model = $this->getModel();

        if (empty($param[$this->_pkName])) {
            throw new Q_Exception('Illegal' . $this->_pkName, -1001);
        }

        $ids = is_array($param[$this->_pkName]) ? array_map('intval', array_filter($param[$this->_pkName])) : (int)$param[$this->_pkName];
        $pk  = empty($param['pk']) ? '' : $param['pk'];

        return $model->delete($ids, $pk);
    }

    /*
     * 根据主键更新数据
     *
     * @param array $param
     * @return bool|resource
     * @throws Q_Exception
     */
    public function callUpdate(array $param = array())
    {
        $model = $this->getModel();

        $paramObj = Q_Helper_Array::instance($param);

        $option = $paramObj->setRule($this->_pkName, $paramObj::VAL_TYPE_FUNC_UINT)
            ->setRule('pk', $paramObj::VAL_TYPE_FUNC_STRING)
            ->setRule('data', $paramObj::VAL_TYPE_FUNC_ARRAY)
            ->getSafeArr();

        if (empty($option['data'])) {
            throw new Q_Exception('Illegal required value', -1002);
        }

        return $model->update($option['data'], $option[$this->_pkName], $option['pk']);
    }

    /*
     * 保存数据
     *
     * @param array $param
     * @return bool|Int
     */
    public function callSave(array $param = array())
    {
        $replace = true;
        if (!empty($param[self::PARAM_KEY_SAVE_REPLACE]) && $param[self::PARAM_KEY_SAVE_REPLACE] === false) {
            $replace = false;
        }
        unset($param[self::PARAM_KEY_SAVE_REPLACE]);
        $model = $this->getModel();
        return $model->save($param, $replace);
    }

    /*
     * 保存所有数据
     *
     * @param array $param
     * @return bool|Int
     */
    public function callSaveAll(array $param = array())
    {
        $replace = true;
        if (!empty($param[self::PARAM_KEY_SAVE_REPLACE]) && $param[self::PARAM_KEY_SAVE_REPLACE] === false) {
            $replace = false;
        }
        unset($param[self::PARAM_KEY_SAVE_REPLACE]);
        $model = $this->getModel();
        return $model->saveAll($param, $replace);
    }
}
