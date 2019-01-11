<?php

/**
 * 数据模型
 *
 * @package      Abstract
 * @author       wukun<charmfocus@gmail.com>
 * @copyright(c) 2014-11-09
 * @version      $id$
 */
abstract class Q_Abstract_Model
{
    const OPT_TYPE_INSERT        = 1;
    const OPT_TYPE_MULTI_INSERT  = 2;
    const OPT_TYPE_UPDATE_BEFORE = 3;
    const OPT_TYPE_UPDATE_AFTER  = 4;
    const OPT_TYPE_DELETE        = 5;
    /**
     * 数据表单例
     *
     * @var Q_Abstract_Model
     */
    protected static $_instance = array();

    /**
     * 数据库连接名
     *
     * @var string
     */
    protected $_dbConnName = 'default';

    /**
     * 数据库连接
     *
     * @var Q_Abstract_Db
     */
    protected $_db = null;

    /**
     * 表名
     *
     * @var string
     */
    protected $_tableName;

    /**
     * 主键
     *
     * @var string
     */
    protected $_pk = 'id';

    /**
     * 安全模式,打开后,会自动转换插入的数据类型,和db类中safeMode对应
     *
     * @var bool
     */
    protected $_safeMode = true;

    const DUPLICATE_UPDATE_MODE_REPLACE        = Q_Abstract_Db::DUPLICATE_UPDATE_MODE_REPLACE;//替换
    const DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = Q_Abstract_Db::DUPLICATE_UPDATE_MODE_COMPOUND_ADD;//+=
    const DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = Q_Abstract_Db::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI;//*=
    const DUPLICATE_UPDATE_MODE_APPEND         = Q_Abstract_Db::DUPLICATE_UPDATE_MODE_APPEND;//字符串合并

    public function renew()
    {
        $obj = clone $this;
        $obj->setDb($obj->getDb()->renew());
        return $obj;
    }

    /**
     * 单例
     *
     * @param string $tableName 表名
     * @param string $connName  连接名
     * @return $this
     * @throws Q_Exception
     */
    public static function instance($tableName = '', $connName = '')
    {
        $modelName    = get_called_class();
        $_instanceKey = $modelName . ':' . $connName . ':' . $tableName;

        if (!isset(self::$_instance[$_instanceKey])) {

            /**
             * 数据模型
             *
             * @var $model Q_Abstract_Model
             */
            $model = new $modelName();

            if (!empty($connName)) {
                $model->_dbConnName = $connName;
            }

            $model->setDb(Q_Db::instance($model->_dbConnName));

            empty($tableName) || $model->setTableName($tableName);

            self::$_instance[$_instanceKey] = $model;
        } else {
            $model = self::$_instance[$_instanceKey];
        }

        return $model;
    }

    /**
     * 设置数据库连接
     *
     * @param \Q_Abstract_Db $db
     * @return $this
     */
    public function setDb(Q_Abstract_Db $db)
    {
        $this->_db = $db;

        return $this;
    }

    /**
     * 获取数据库连接对象
     *
     * @return Q_Abstract_Db
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * 设置表名
     *
     * @param $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;

        return $this;
    }

    /**
     * 设置主键
     *
     * @param $pk
     * @return $this
     */
    public function setPk($pk)
    {
        $this->_pk = $pk;

        return $this;
    }

    /**
     * 获取安全模式
     *
     * @return boolean
     */
    public function isSafeMode()
    {
        return $this->_safeMode;
    }

    /**
     * 设置安全模式
     *
     * @param boolean $safeMode
     * @return $this
     */
    public function setSafeMode($safeMode)
    {
        $this->_safeMode = $safeMode;
        return $this;
    }


    /**
     * 根据指定字段名返回一行数据
     *
     * @param string|array    $fieldName  字段名,可以是多个字段，如果是数组的话，代表where，键代表字段，值代表字段的值
     * @param int|string      $fieldVal   字段值
     * @param string|array    $fields     要查询的字段
     * @param string          $tableName  表名
     * @param string|string[] $forceIndex 强制索引
     * @return array|bool
     */
    public function getByField($fieldName, $fieldVal = '', $fields = '*', $tableName = '', $forceIndex = '')
    {
        if (empty($fieldName)) {
            return false;
        }

        $conn = $this->_db->chooseDbConn('SELECT');
        if (is_array($fieldName)) {
            $where = $conn->parseWhere($fieldName);
        } else {
            $where = "`{$fieldName}`=" . $conn->quote($fieldVal);
        }

        $tableName = empty($tableName) ? $this->_tableName : $tableName;

        if (!empty($forceIndex)) {
            $forceIndex = $this->_db->getForceIndex($forceIndex);
        }

        $fields = empty($fields) ? '*' : $fields;

        if (is_array($fields)) {
            $fields = '`' . join('`,`', $fields) . '`';
        }

        $sql = "SELECT {$fields} FROM `{$tableName}` {$forceIndex} WHERE {$where}";

        return $this->_db->getRow($sql);
    }

    /**
     * 根据主键值获取一条数据
     *
     * @param int             $pkVal
     * @param string          $pk         主键名
     * @param string|array    $fields     要查询的字段
     * @param string          $tableName  表名
     * @param string|string[] $forceIndex 强制索引
     * @return array|bool
     */
    public function get($pkVal, $pk = '', $fields = '*', $tableName = '', $forceIndex = '')
    {
        if ($pkVal < 1) {
            return false;
        }

        $pk = empty($pk) ? $this->_pk : $pk;

        return $this->getByField($pk, $pkVal, $fields, $tableName, $forceIndex);
    }


    /**
     * 获取一行数据，可自定义字段/条件和排序
     *
     * @param array $param
     * @return array|mixed
     */
    public function getRow(array $param = array())
    {

        $param['num'] = 1;
        $data         = $this->gets($param);
        if (!empty($data)) {
            $data = reset($data);
        }

        return $data;
    }

    /**
     * 获取一个字段
     *
     * @param array $param
     * @return mixed
     */
    public function getOne(array $param = array())
    {
        $param['order'] = isset($param['order']) ? $param['order'] : false;
        $data           = $this->getRow($param);
        if ($data) {
            $data = reset($data);
        }
        return $data;
    }

    /**
     * 统计总数
     *
     * @param array $param
     * @return int
     * @throws Q_Exception
     */
    public function count(array $param = array())
    {

        $param['table'] = empty($param['table']) ? $this->_tableName : $param['table'];

        return $this->_db->count($param);
    }

    /**
     * 获取表格配置数据
     *
     * @param array $param
     * @return array|null
     * @throws Q_Exception
     */
    public function getFullColumns(array $param = array())
    {
        $table    = empty($param['table']) ? $this->_tableName : $param['table'];
        $callback = !empty($param['callback']) && is_callable($param['callback']) ? $param['callback'] : null;

        $option = array(
            'table'    => $table,
            'callback' => $callback,
        );

        return $this->_db->getFullColumns($option);
    }

    /**
     * 获取数据结果集
     *
     * @param array $param
     * @return array
     */
    public function gets(array $param = array())
    {
       // Q::debug($param);
        $table      = empty($param['table']) ? $this->_tableName : $param['table'];
        $forceIndex = empty($param['forceIndex']) ? '' : $param['forceIndex'];
        $pk         = empty($param['_pk']) ? $this->_pk : $param['_pk'];
        $num        = empty($param['num']) ? 0 : (int)$param['num'];
        $where      = !empty($param['where']) ? $param['where'] : array();
        $group      = !empty($param['group']) ? $param['group'] : '';
        $having     = !empty($param['having']) ? $param['having'] : '';
        $order      = !isset($param['order']) ? ($pk . ' DESC') : $param['order'];
        $field      = !empty($param['field']) ? $param['field'] : '*';
        $callback   = !empty($param['callback']) && is_callable($param['callback']) ? $param['callback'] : null;
        $hash       = isset($param['hash']) ? $param['hash'] : $pk;
        $fast       = empty($param['fast']) ? false : (bool)$param['fast'];
        $union      = empty($param['union']) ? array() : $param['union'];

        $option = array(
            'table'      => $table,
            'forceIndex' => $forceIndex,
            'field'      => $field,
            '_pk'        => $pk,
            'where'      => $where,
            'group'      => $group,
            'having'     => $having,
            'order'      => $order,
            'num'        => $num,
            'hash'       => $hash,
            'callback'   => $callback,
            'fast'       => $fast,
            'union'      => $union,
        );

        return $this->_db->findData($option);
    }

    /**
     * 获取数据结果集
     *
     * @param array $param
     * @return array
     * @throws Q_Exception
     */
    public function getPairs(array $param = array())
    {
        $table      = empty($param['table']) ? $this->_tableName : $param['table'];
        $forceIndex = empty($param['forceIndex']) ? '' : $param['forceIndex'];
        $keyName    = empty($param['keyName']) ? $this->_pk : $param['keyName'];
        $valueName  = empty($param['valueName']) ? '' : $param['valueName'];
        $field      = !empty($param['field']) ? Q_Db::formatField($param['field']) : '';
        $num        = empty($param['num']) ? 0 : (int)$param['num'];
        $where      = !empty($param['where']) ? $param['where'] : array();
        $group      = !empty($param['group']) ? $param['group'] : '';
        $having     = !empty($param['having']) ? $param['having'] : '';
        $order      = !isset($param['order']) ? ($keyName . ' DESC') : $param['order'];
        $fast       = empty($param['fast']) ? false : (bool)$param['fast'];

        if (empty($valueName)) {
            throw new Q_Exception('valueName is empty!');
        }

        if (!empty($forceIndex)) {
            $forceIndex = $this->_db->getForceIndex($forceIndex);
        }

        $conn = $this->_db->chooseDbConn('SELECT');

        $field = $field ? $field : "`{$keyName}`, `{$valueName}`";

        $table   = $conn::formatTable($table);
        $field   = $conn::formatField($field);
        $where   = $conn->parseWhere($where);
        $_having = $conn->parseWhere($having);
        $where   = $where ? " WHERE {$where}" : '';
        $group   = $group ? " GROUP BY {$group}" : '';
        $having  = $having ? " HAVING {$_having}" : '';

        $orderBy = $order ? " ORDER BY {$order}" : '';
        $limit   = $num ? " LIMIT {$num}" : '';
        $sql     = "SELECT {$field} {$forceIndex}
                    FROM {$table} {$where} {$group} {$having} {$orderBy} {$limit}";

        return $this->_db->getPairs($sql, null, $keyName, $valueName, $fast);
    }

    /**
     * 获取列表数据
     *
     * @param array $param
     * @return array ['data' => array(), 'total' => int, 'pageBar' => string]
     */
    public function getList(array $param = array())
    {
        $table      = empty($param['table']) ? $this->_tableName : $param['table'];
        $forceIndex = empty($param['forceIndex']) ? '' : $param['forceIndex'];
        $pk         = !isset($param['_pk']) ? $this->_pk : $param['_pk'];
        $page       = !empty($param['page']) ? max((int)$param['page'], 1) : 1;
        $pageSize   = empty($param['pageSize']) ? 10 : (int)$param['pageSize'];
        $where      = !empty($param['where']) ? $param['where'] : array();
        $group      = !empty($param['group']) ? $param['group'] : '';
        $having     = !empty($param['having']) ? $param['having'] : '';
        $order      = !isset($param['order']) ? ($pk . ' DESC') : $param['order'];
        $field      = !empty($param['field']) ? $param['field'] : '*';
        $pageBar    = isset($param['pageBar']) ? $param['pageBar'] : null;

        $pageBar = is_null($pageBar) ? '{FIRST:首页}{PREV:&lt; 上一页}{BAR:[NUM]:5:2}{NEXT:下一页 &gt;}{LAST:尾页}' : $pageBar;

        $url      = !empty($param['url']) ? $param['url'] : '';
        $target   = !empty($param['target']) ? $param['target'] : '';
        $callback = !empty($param['callback']) && is_callable($param['callback']) ? $param['callback'] : null;
        $hash     = isset($param['hash']) ? $param['hash'] : $pk;
        $fast     = empty($param['fast']) ? false : (bool)$param['fast'];
        $union    = empty($param['union']) ? array() : $param['union'];

        $option = array(
            'table'      => $table,
            'forceIndex' => $forceIndex,
            'field'      => $field,
            '_pk'        => $pk,
            'where'      => $where,
            'group'      => $group,
            'having'     => $having,
            'order'      => $order,
            'hash'       => $hash,
            'page'       => $page,
            'num'        => $pageSize,
            'pageBar'    => $pageBar,
            'url'        => $url,
            'target'     => $target,
            'callback'   => $callback,
            'fast'       => $fast,
            'union'      => $union,
        );

        return $this->_db->findData($option);
    }

    /**
     * 保存
     *
     * @param array    $data            数据
     * @param bool|int $replace         是否替换，可以指定替换模式DUPLICATE_UPDATE_MODE_*
     *                                  self::DUPLICATE_UPDATE_MODE_REPLACE        = 1;//替换
     *                                  self::DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = 2;//+=
     *                                  self::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = 3;//*=
     *                                  self::DUPLICATE_UPDATE_MODE_APPEND         = 4;//字符串合并
     *                                  string:配合field为exp字段的话,可设置自定义更新语句
     * @param array    $replaceFields   设置替换字段,如果重复值的话，
     *                                  设置需要替换的字段，要替换的字段设置为[field => true],
     *                                  不替换的字段设置为[field => false],如果设置为空，
     *                                  默认替换data中所有的字段
     * @param string   $tableName       表名
     * @return bool|int 成功返回insertId
     */
    public function save(array $data, $replace = false, array $replaceFields = array(), $tableName = '')
    {
        $tableName = empty($tableName) ? $this->_tableName : $tableName;

        //清除为零的主键
        if (empty($data[$this->_pk])) {
            unset($data[$this->_pk]);
        }

        $res = $this->_db
            ->setSafeMode($this->isSafeMode())
            ->save($tableName, $data, $replace, $replaceFields);

        //保存记录
        $this->createLog($tableName, Q_Abstract_Model::OPT_TYPE_INSERT, $data);
        return $res;
    }

    /**
     * 保存所有
     *
     * @param array    $data               数据
     * @param bool|int $replace            是否替换，可以指定替换模式DUPLICATE_UPDATE_MODE_*
     *                                     self::DUPLICATE_UPDATE_MODE_REPLACE        = 1;//替换
     *                                     self::DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = 2;//+=
     *                                     self::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = 3;//*=
     *                                     self::DUPLICATE_UPDATE_MODE_APPEND         = 4;//字符串合并
     *                                     string:配合field为exp字段的话,可设置自定义更新语句
     * @param array    $replaceFields      设置替换字段,如果重复值的话，
     *                                     设置需要替换的字段，要替换的字段设置为[field => true],
     *                                     不替换的字段设置为[field => false],如果设置为空，
     *                                     默认替换data中所有的字段
     * @param string   $tableName          表名
     * @param bool     $resetAutoIncrement reset auto increment
     * @return bool
     */
    public function saveAll(array $data, $replace = false, $replaceFields = array(), $tableName = '', $resetAutoIncrement = false)
    {
        $tableName = empty($tableName) ? $this->_tableName : $tableName;
        $res       = $this->_db
            ->setSafeMode($this->isSafeMode())
            ->saveAll($tableName, $data, $replace, $replaceFields, $resetAutoIncrement);

        //保存记录
        $this->createLog($tableName, Q_Abstract_Model::OPT_TYPE_MULTI_INSERT, $data);
        return $res;
    }

    /**
     * 根据主键删除
     *
     * @param int|array $pkVal 主键值
     * @param string    $pk    主键名
     * @return bool|resource
     */
    public function delete($pkVal, $pk = '')
    {
        $pk    = empty($pk) ? $this->_pk : $pk;
        $where = array(
            $pk => $pkVal,
        );

        return $this->deleteByWhere($where);
    }

    /**
     * 根据指定条件删除
     *
     * @param array  $where     条件
     * @param string $tableName 表名
     * @return bool
     */
    public function deleteByWhere(array $where, $tableName = '')
    {
        $tableName = empty($tableName) ? $this->_tableName : $tableName;

        $res = $this->_db->delete($tableName, $where);

        //保存记录
        $this->createLog($tableName, Q_Abstract_Model::OPT_TYPE_DELETE, $where);

        return $res;
    }

    public function truncate($tableName = '')
    {
        $tableName = empty($tableName) ? $this->_tableName : $tableName;

        return $this->_db->truncate($tableName);
    }

    /**
     * 根据主键更新一行数据
     *
     * @param array     $data    要保存的一行数据
     * @param int|array $pkVal   主键值
     * @param string    $pk      主键名
     * @param bool      $replace 重复数据是否替换
     * @return int
     */
    public function update($data, $pkVal, $pk = '', $replace = false)
    {
        $pk    = empty($pk) ? $this->_pk : $pk;
        $where = array($pk => $pkVal);

        return $this->updateByWhere($data, $where, $replace);
    }

    /**
     * 更新所有行
     *
     * @param array $data    要保存的字段键值对:[field => val, field2 => val2]
     * @param bool  $replace 重复数据是否替换
     * @return bool
     */
    public function updateAll(array $data, $replace = false)
    {
        return $this->updateByWhere($data, array(), $replace);
    }

    /**
     * 根据主键更新指定的一列
     *
     * @param string $field     列名
     * @param array  $data      列表数据
     * @param string $tableName 表名
     * @param string $pk        主键名
     * @param array  $where     限定条件
     * @return bool
     */
    public function updateCol($field, array $data, $tableName = '', $pk = '', $where = array())
    {
        $tableName = empty($tableName) ? $this->_tableName : $tableName;
        $pk        = empty($pk) ? $this->_pk : $pk;

        $this->createLog($tableName, Q_Abstract_Model::OPT_TYPE_UPDATE_BEFORE, $where);

        $res = $this->_db->updateCol($tableName, $field, $data, $pk, $where);

        //保存记录
        $this->createLog($tableName, Q_Abstract_Model::OPT_TYPE_UPDATE_AFTER, $where);
        return $res;
    }

    /**
     * 根据指定条件更新
     *
     * @param array        $data      数据
     * @param array|string $where     WHERE条件
     * @param bool         $replace   重复数据是否替换
     * @param string       $tableName 表名
     * @return bool
     */
    public function updateByWhere(array $data, $where = array(), $replace = false, $tableName = '')
    {
        //TODO:兼容老代码，替换更新后去除
        if (is_string($replace)) {
            $tableName = $replace;
            $replace   = false;
        }

        $tableName = empty($tableName) ? $this->_tableName : $tableName;

        $this->createLog($tableName, Q_Abstract_Model::OPT_TYPE_UPDATE_BEFORE, $where);

        $res = $this->_db->update($tableName, $data, $where, $replace);

        //保存记录
        $this->createLog($tableName, Q_Abstract_Model::OPT_TYPE_UPDATE_AFTER, $where);
        return $res;
    }

    /**
     * 设置是否强制从主库读取
     *
     * @param bool $readMaster 是否从主库读取
     * @return $this
     */
    public function forceReadMaster($readMaster = true)
    {
        $this->_db->forceReadMaster($readMaster);

        return $this;
    }

    /**
     * 事务开始
     *
     * @return bool
     */
    public function begin()
    {
        return $this->_db->begin();
    }

    /**
     * 多库事务开始
     *
     * @return bool
     */
    public function beginAll()
    {
        return $this->_db->beginAll();
    }

    /**
     * 事务提交
     *
     * @return bool
     */
    public function commit()
    {
        return $this->_db->commit();
    }

    /**
     * 多库事务提交
     *
     * @return bool
     */
    public function commitAll()
    {
        return $this->_db->commitAll();
    }

    /**
     * 事务回滚
     *
     * @return bool|void
     */
    public function rollBack()
    {
        return $this->_db->rollBack();
    }

    /**
     * 多库事务回滚
     *
     * @return bool|void
     */
    public function rollBackAll()
    {
        return $this->_db->rollBackAll();
    }

    /**
     * 执行getXXXModel这类的方法,类中必须设置protected $_XXXTableName;
     *
     * @param $name
     * @param $arguments
     * @return $this
     * @throws Q_Exception
     */
    public function __call($name, $arguments)
    {
        $_prefix       = substr($name, 0, 3);
        $_stem         = substr($name, 3, -5);
        $_suffix       = substr($name, -5);
        $_getModelName = '_' . lcfirst($_stem) . 'TableName';

        if ($_prefix == 'get' && $_suffix == 'Model' && property_exists($this, $_getModelName)) {
            return Q_Model::instance($this->$_getModelName, $this->_dbConnName);
        }
        $_className = get_called_class();
        throw new Q_Exception('call method ' . $name . ' does not exists! Please check this "' . $_className . '" class is set "' . $_getModelName . '" property?');
    }

    /**
     * 设置db开关
     *
     * @param bool|true $debug
     * @return $this
     */
    public function setDbDebug($debug = true)
    {
        $this->_db->setDebug($debug);
        return $this;
    }

    /**
     * 打印SQL
     *
     * @param string    $connName
     * @param bool|true $dump
     * @return array|null|string
     */
    public function dumpSql($connName = '', $dump = true)
    {
        return $this->_db->dumpSql($connName, $dump);
    }

    public function createLog($tableName = '', $optType = 0, array $data = array())
    {
        return;
    }
}
