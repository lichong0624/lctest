<?php

/**
 * PDO数据连接类
 * <pre>
 * <b>主要功能:</b>
 * 支持主从(读写)数据库链接，
 * 单例模式
 * 支持断点重连
 * 支持SQL打印
 * 支持获取结果集影响行数(分页时会用到)
 * 支持设置字符集
 * 支持getAll方法回调处理每一行
 * 自动处理getAll中查询一列数据时数组的维数
 * 可获取成对数据（根据C++中的容器概念想到的）
 * </pre>
 * <code>
 * <?php
 * //实例类
 * $db = Q_Db::instance('Admin');
 * //获取一行数据
 * $data = $db->getRow($sql, $param);
 * //获取所有数据
 * $data = $db->getAll($sql, $param);
 * //获取一列数据
 * $data = $db->getCol($sql, $param);
 * //获取一个字段
 * $data = $db->getOne($sql, $param);
 * //保存数据
 * $db->save($tableName, )
 * </code>
 *
 * @package      Abstract
 * @author       wukun<charmfocus@gmail.com>
 * @copyright(c) 2010-09-13
 * @version      v1.0
 */
abstract class Q_Abstract_Db
{
    const DB_TYPE_MASTER = 'master';
    const DB_TYPE_SLAVE  = 'slave';

    /**
     * 连接名
     *
     * @var string
     */
    protected $_name = 'default';


    /**
     * 当前连接类型
     *
     * @var string
     */
    protected $_curConnType = self::DB_TYPE_SLAVE;
    /**
     * 数据库引擎
     *
     * @var string
     */
    protected $_engine = 'mysql';

    /**
     * 服务器列表
     *
     * @var array
     */
    protected $_servers;

    /**
     * 当前PDO或数据库链接
     *
     * @var PDO
     */
    protected $_conn;


    /**
     * 当前查询
     *
     * @var PDOStatement
     */
    protected $_res;

    /**
     * 主数据库链接
     *
     * @var PDO
     */
    protected $_master;

    /**
     * 从数据库链接
     *
     * @var PDO
     */
    protected $_slave;

    /**
     * PDO options配置
     *
     * @var array
     */
    protected $_options = array();

    /**
     * @var string 强制指定读写库类型  self::DB_TYPE_MASTER|self::DB_TYPE_MASTER
     */
    protected $_setForceConnType = null;

    /**
     * 是否强制主库
     *
     * @var boolean
     */
    protected $_forceReadMaster = false;

    /**
     * 数据库字符集
     *
     * @var string
     */
    protected $_charset = 'utf8';

    /**
     * 数据库用户名
     *
     * @var string
     */
    protected $_username = 'root';

    /**
     * 数据库密码
     *
     * @var string
     */
    protected $_password;

    /**
     * PDO DSN
     *
     * @var string
     */
    protected $_dsn;

    /**
     * SQL池，调试用
     */
    protected static $_sqlPool = array();

    /**
     * 数据库单例
     *
     * @var Q_Abstract_Db[]
     */
    protected static $_instance = array();

    /**
     * 调用开关
     */
    protected $_debug = IS_DEBUGGING;

    /**
     * 当前SQL
     *
     * @var string
     */
    protected $_sql = '';

    /**
     * 当前原始SQL
     *
     * @var string
     */
    protected $_realSql = '';

    /**
     * 预处理参数
     *
     * @var array
     */
    protected $_prepareOptions = array();

    /**
     * 当前SQL的参数
     *
     * @var array
     */
    protected $_param = array();

    /**
     * 安全模式,打开后,会自动转换插入的数据类型
     *
     * @var bool
     */
    protected $_safeMode = true;


    /**
     * 最大重连次数
     *
     * @var int
     */
    protected $_maxReconnectNum = 5;

    /**
     * 当前重连次数
     *
     * @var int
     */
    protected $_reconnectCount = 0;

    private static $_comparison = array(' NOT BETWEEN', ' BETWEEN', ' NOT IN', ' IN', ' NOT LIKE',
                                        ' IS NOT', ' LIKE', ' IS', ' NOT REGEXP', ' REGEXP', ' RLIKE',
                                        '>=', '<=', '<>', '!=', '>', '<', '=',
    );

    const DUPLICATE_UPDATE_MODE_REPLACE        = 1;//替换
    const DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = 2;//+=
    const DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = 3;//*=
    const DUPLICATE_UPDATE_MODE_APPEND         = 4;//字符串合并


    private static $_cache = array();

    protected $_transactionCount = 0;

    protected $_useBuffer = true;

    /**
     * @return boolean
     */
    public function hasUseBuffer()
    {
        return $this->_useBuffer;
    }

    /**
     * 设置缓冲
     *
     * @param boolean $useBuffer
     * @return $this
     */
    public function setUseBuffer($useBuffer = true)
    {
        $this->_useBuffer = $useBuffer;

        return $this;
    }

    /**
     * 启用缓冲
     *
     * @return $this
     */
    public function useBuffer()
    {
        return $this->setUseBuffer(true);
    }

    /**
     * 禁用缓冲
     *
     * @return $this
     */
    public function disuseBuffer()
    {
        return $this->setUseBuffer(false);
    }

    /**
     * 数据库单例
     *
     * @param string|array|bool $connInfo string:数据库连接名
     *                                    array:数据库链接信息['username'=>用户名，'password'=>密码，'host'=>IP，'port'=>端口,'database'=>库名,'charset'=>字符集]
     *                                    bool:是否重新创建连接类,此时使用default连接类
     * @param bool              $renew
     * @return Q_Abstract_Db
     * @throws Q_Exception
     * @internal param bool $reuse 是否重新创建连接类
     */
    public static function instance($connInfo = 'default', $renew = false)
    {
        if (is_bool($connInfo)) {
            $renew    = $connInfo;
            $connInfo = 'default';
        }

        if (is_string($connInfo)) {
            $conf = Q_Config::get('Db', $connInfo);
        } else if (is_array($connInfo)) {
            $conf     = $connInfo;
            $connInfo = !empty($conf['name']) ? $conf['name'] : 'default';
        }

        if (empty($conf)) {
            throw new Q_Exception("The '{$connInfo}' database config does not exist!");
        }

        if (!isset(self::$_instance[$connInfo]) || $renew) {
            $dbo          = new Q_Db();
            $conf['name'] = $connInfo;
            $dbo->init($conf);
            if (!$renew) {
                self::$_instance[$connInfo] = $dbo;
            }
        } else {
            $dbo = self::$_instance[$connInfo];
        }

        if (IS_DEBUGGING) {
            $dbo->setDebug(true);
        }

        return $dbo;
    }

    /**
     * 动态建立新PDO,和当前的链接属性一致
     *
     * @return Q_Db
     */
    public function renew()
    {
        $conf = $this->_servers;
        $dbo  = new Q_Db();
        $dbo->init($conf);

        return $dbo;
    }

    /**
     * 初始化数据库
     *
     * @param array $conf 配置
     * @return void
     */
    public function init(array $conf = array())
    {
        $this->_servers = $conf;
        foreach (array('engine', 'charset', 'name', 'username', 'password', 'dsn', 'options') as $key) {
            $defName = 'DB_' . strtoupper($key);
            $_var    = '_' . $key;
            defined($defName) && $this->$_var = constant($defName);
            isset($conf[$key]) && $this->$_var = $conf[$key];
        }
    }

    /**
     * 强制数据库读写类型
     *
     * @param string $connType self::DB_TYPE_MASTER|self::DB_TYPE_SLAVE|null
     * @return $this
     * @throws Q_Exception
     */
    public function setForceConnType($connType = null)
    {
        if ($connType !== self::DB_TYPE_MASTER && $connType !== self::DB_TYPE_SLAVE && $connType !== null) {
            throw new Q_Exception('db type is error!');
        }

        $this->_setForceConnType = $connType;

        return $this;
    }

    public function getForceConnType()
    {
        return $this->_setForceConnType;
    }

    /**
     * 设置为自动连接类型
     *
     * @return $this
     */
    public function autoConnType()
    {
        $this->setForceConnType(null);

        return $this;
    }

    /**
     * 强制从写库读取
     *
     * @param bool $readMaster
     * @return Q_Abstract_Db
     */
    public function forceReadMaster($readMaster = true)
    {
        $connType = $readMaster ? self::DB_TYPE_MASTER : null;
        $this->setForceConnType($connType);

        return $this;
    }

    /**
     * 检查数据库是否存在
     *
     * @param string $dbName 数据库名称
     * @return bool
     */
    public function checkDBIsCreated($dbName)
    {
        if (empty($dbName)) {
            return false;
        }

        return (bool)$this->count(array('table' => 'information_schema.SCHEMATA', 'where' => array('SCHEMA_NAME' => $dbName)));
    }

    /**
     * 设置SQL语句
     *
     * @param      $sql
     * @param bool $comment
     * @return $this
     */
    public function setSql(&$sql, $comment = true)
    {
        $_sql = trim($sql);
        if ($comment) {
            $sqlId = uniqid('sql_', true);
            $sql   = $_sql . "/*{$sqlId}*/";
        }

        $this->_realSql = $_sql;
        $this->_sql     = $sql;

        return $this;
    }

    /**
     * 创建PDO或数据库链接
     *
     * @param string $connType {master|slave}
     * @throws \Q_Exception
     * @return $this
     */
    public function createConn($connType = self::DB_TYPE_MASTER)
    {
        $server = isset($this->_servers[$connType]) ? $this->_servers[$connType] : $this->_servers;

        $_connType          = '_' . $connType;
        $this->_curConnType = $connType;

        if (!empty($this->$_connType)) {
            $this->_conn = $this->$_connType;

            return $this;
        }

        if (empty($this->_dsn)) {
            $dsn        = $this->_engine . ':host=' . $server['host'];
            $dsn        .= isset($server['port']) ? ';port=' . $server['port'] : '';
            $dsn        .= isset($server['database']) ? ';dbname=' . $server['database'] : '';
            $dsn        .= $this->_charset ? ";charset={$this->_charset}" : '';
            $this->_dsn = $dsn;
        } else {
            $dsn = $this->_dsn;
        }


        try {
            $this->close($_connType);
            $opt = array(
                PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES  => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                //                PDO::ATTR_PERSISTENT        => true,
            );

            if ($this->_charset) {
                $opt[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $this->_charset;
            }

            if ($this->_options) {
                $opt += $this->_options;
            }

            $conn = new PDO($dsn, $this->_username, $this->_password, $opt);//$opt长连接加上报错，未知问题

        } catch (Exception $ex) {
              return $this->_reconnect($ex);
        }

        $this->_conn = $this->$_connType = $conn;

        $this->_reconnectCount = 0;

        return $this;
    }

    private function _reconnect(Exception $ex, $connType = null)
    {
        is_null($connType) && $connType = $this->_curConnType;
        $code = $ex->getCode();
        if (($code === 8 || $code === 2) && $this->_reconnectCount < $this->_maxReconnectNum) {
            ++$this->_reconnectCount;

            return $this->createConn($connType);
        }

        throw new Q_Exception($ex->getMessage(), $ex->getCode());
    }


    /**
     * 根据SQL选择不同的数据库连接
     *
     * @param string $connType self::DB_TYPE_MASTER|self::DB_TYPE_SLAVE SQL语句
     * @return $this
     * @throws \Q_Exception
     */
    public function chooseDbConn($connType = '')
    {
        $connType || $connType = $this->_sql;

        if ($connType !== self::DB_TYPE_MASTER && $connType !== self::DB_TYPE_SLAVE) {
            if ($this->_setForceConnType) {
                $connType = $this->_setForceConnType;
            } else {
                //检查SQL是否是select查询
                $isSelect = (stripos($connType, 'SELECT') === 0);
                if (!$isSelect) {
                    $connType = self::DB_TYPE_MASTER;
                } else {
                    $connType = self::DB_TYPE_SLAVE;
                }
            }
        }

        $this->createConn($connType);

        if (empty($this->_conn)) {
            throw new Q_Exception('Dose not exist instance of DBConn server!');
        }

        $this->_conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $this->hasUseBuffer());

        return $this;
    }

    /**
     * 是否是安全模式
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
    public function setSafeMode($safeMode = true)
    {
        $this->_safeMode = $safeMode;

        return $this;
    }


    /**
     * 查询
     *
     * @param string $sql SQL语句
     * @return bool|PDOStatement
     * @throws Q_Exception
     */
    public function query($sql = '')
    {
        if (empty($sql)) {
            return false;
        }

        $this->setSql($sql);
        $sql = $this->_sql;

        $this->chooseDbConn();
        //$this->_debug && $_queryStartTime = microtime(true);
        $_queryStartTime = microtime(true);

        try {
            $this->_res = $this->_conn->query($sql);
        } catch (Exception $ex) {
            //   return $this->_reconnect($ex)->query($this->_realSql);
        }

        //        if ($this->_debug) {
        //        if ($this->_debug) {
        self::$_sqlPool[$this->_name][] = $sql
                                          . "\r\n name:{$this->_name}"
                                          . "\r\n dsn:{$this->_dsn}"
                                          . "\r\n connType:{$this->_curConnType},
                                              query time:" . (microtime(true) - $_queryStartTime);
        //        }

        return $this->_res;
    }

    /**
     * 预处理
     *
     * @param  string $sql SQL语句
     * @param array   $opt 参数
     * @return bool|PDOStatement
     * @throws Q_Exception
     */
    public function prepare($sql, array $opt = array())
    {
        if (empty($sql)) {
            return false;
        }

        $this->setSql($sql);
        $sql                   = $this->_sql;
        $this->_prepareOptions = $opt;

        $this->chooseDbConn();

        try {
            $this->_res = $this->_conn->prepare($sql, $opt);
        } catch (Exception $ex) {
            //  return $this->_reconnect($ex)->prepare($this->_realSql, $opt);
        }

        return $this->_res;
    }


    /**
     * 获取一个字段
     *
     * @param string     $sql    SQL语句
     * @param array      $params 预处理execute参数
     * @param string|int $column 获取哪个字段，为数字则按下标提取，为字符则按字段名提取
     * @return string|bool
     */
    public function getOne($sql, array $params = null, $column = 0)
    {
        $fetchStyle = is_numeric($column) ? PDO::FETCH_NUM : PDO::FETCH_ASSOC;
        $row        = $this->getRow($sql, $params, $fetchStyle);

        return $row ? $row[$column] : null;
    }

    /**
     * 获取一列
     *
     * @param string   $sql      SQL语句
     * @param array    $params   预处理execute参数
     * @param int      $column   获取第几个字段,以0开始
     * @param callable $callback 回调函数
     * @return array|bool
     */
    public function getCol($sql, array $params = null, $column = 0, $callback = null)
    {
        $res = $this->prepare($sql);
        $this->execute($params);

        $results = $res->fetchAll(PDO::FETCH_COLUMN, $column);

        //回调处理每一行
        if (is_array($results) && $callback) {
            return array_map($callback, $results);
        }

        return $results ? $results : null;
    }

    /**
     * 获取一行
     *
     * @param string $sql        SQL语句
     * @param array  $params     预处理execute参数
     * @param int    $fetchStyle 获取数据返回格式 PDO::FETCH_ASSOC|PDO::FETCH_NUM|PDO::FETCH_BOTH
     * @return array
     */
    public function getRow($sql, array $params = null, $fetchStyle = PDO::FETCH_ASSOC)
    {
        $res = $this->prepare($sql);
        $this->execute($params);

        $row = $res->fetch($fetchStyle);

        return $row ? $row : null;
    }

    /**
     * 获取所有数据
     *
     * @param string   $sql        SQL语句
     * @param array    $params     预处理execute参数
     * @param string   $hash       重组的字段名,重组数组的KEY
     * @param callback $callback   回调函数，处理每行数据
     * @param int      $fetchStyle 查询结果类型 PDO::FETCH_ASSOC|PDO::FETCH_NUM|PDO::FETCH_BOTH
     * @param bool     $fast       是否启用快速模式
     * @return array
     */
    public function getAll($sql, array $params = null, $hash = '', $callback = null, $fetchStyle = PDO::FETCH_ASSOC, $fast = false)
    {
        if ($fast) {
            return $this->getAllFast($sql, $params, $hash, $callback);
        }

        $this->useBuffer();
        $res = $this->prepare($sql);
        $this->execute($params);

        $results = null;
        //先获取一行
        if ($hash) {
            while ($row = $res->fetch($fetchStyle)) {
                if (isset($row[$hash])) {
                    $results[$row[$hash]] = $row;
                } else {
                    $results[] = $row;
                }
            }
        } else {
            while ($row = $res->fetch($fetchStyle)) {
                $results[] = $row;
            }
        }
        //回调处理每一行
        if (is_array($results) && $callback) {
            return array_map($callback, $results);
        }

        return $results;
    }

    /**
     * 快速获取所有数据
     *
     * @param string   $sql        SQL语句
     * @param array    $params     预处理execute参数
     * @param string   $hash       重组的字段名,重组数组的KEY
     * @param callback $callback   回调函数，处理每行数据
     * @param int      $fetchStyle 查询结果类型 PDO::FETCH_ASSOC|PDO::FETCH_NUM|PDO::FETCH_BOTH
     * @return array
     */
    public function getAllFast($sql, array $params = null, $hash = '', $callback = null, $fetchStyle = PDO::FETCH_ASSOC)
    {
        $this->disuseBuffer();
        $res = $this->prepare($sql);
        $this->execute($params);

        //先获取一行
        if ($hash) {
            while ($row = $res->fetch($fetchStyle)) {
                if ($callback) {
                    $row = $callback($row);
                }

                if (isset($row[$hash])) {
                    yield $row[$hash] => $row;
                } else {
                    yield $row;
                }
            }
        } else {
            while ($row = $res->fetch($fetchStyle)) {
                if ($callback) {
                    $row = $callback($row);
                }

                yield $row;
            }
        }
    }

    /**
     * 根据每一行数据执行回调，比如发布程序
     *
     * @param string   $sql      SQL语句
     * @param array    $params   预处理execute参数
     * @param callable $callback 回调函数 如果回调函数返回false,则break出while循环
     * @return bool
     * @throws Q_Exception
     */
    public function execAll($sql, array $params = null, $callback)
    {
        $res = $this->prepare($sql);
        $this->execute($params);

        if (!$res) {
            return false;
        }

        if (!$callback) {
            return false;
        }

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $_callback = $callback($row);
            if ($_callback === false) {
                $this->kill();
                break;
            }
        }

        return true;
    }

    /**
     * 根据指定的SQL或部分SQL查找pid
     *
     * @param string|string[]|int|int[] $sql
     * @param Q_Db                      $db
     * @param string                    $connType
     * @return int|int[]
     */
    protected function _getPid($sql = '', $db = null, $connType = self::DB_TYPE_SLAVE)
    {
        if (is_array($sql)) {
            foreach ($sql as &$_sql) {
                if (!is_scalar($_sql)) {//非标量,不做处理
                    continue;
                }

                $_pid = $this->_getPid($_sql, $db, $connType);
                if ($_pid && is_int($_pid)) {
                    $_sql = $_pid;
                }
                continue;
            }

            return $sql;
        }

        if (is_int($sql)) {
            return $sql;
        }

        $db || $db = $this;

        if ($connType) {
            $db->setForceConnType($connType);
        }

        $pid = 0;
        if (empty($sql)) {
            $pid = (int)$db->getOne('SELECT CONNECTION_ID()');
        } elseif (is_string($sql)) {
            $pidSql = 'SELECT id FROM information_schema.processlist WHERE info LIKE :INFO';
            $pid    = $db->getCol($pidSql, array(':INFO' => $sql));
            $pid    = array_map('intval', $pid);
        }

        //还原连接类型
        if ($connType) {
            $db->autoConnType();
        }

        return $pid;
    }

    /**
     * 杀死指定语句
     *
     * @param string|int|string[]|int[] $sql SQL或ID
     * @param array                     $param
     * @param string                    $connType
     * @return bool
     */
    public function kill($sql = '', $param = array(), $connType = self::DB_TYPE_SLAVE)
    {
        $sql || $sql = $this->_sql;
        $param || $param = $this->_param;

        if ($param) {
            $sql = $this->_interpolateQuery($sql, $param);
        }

        if (empty($sql)) {
            return false;
        }

        $db   = $this->renew();
        $pids = $this->_getPid($sql, $db, $connType);//这里会还原$connType,所以需要在下面重新设置连接类型

        $db->setForceConnType($connType);

        if (is_int($pids)) {
            $db->query("KILL " . $pids);
        } elseif (is_array($pids)) {
            foreach ($pids as $_pid) {
                $db->query("KILL " . $_pid);
            }
        }

        $db->close();

        return true;
    }

    /**
     * 获取成对数据
     *
     * @param string $sql     SQL语句
     * @param array  $params  预处理execute参数
     * @param string $keyName 用于数组KEY的字段名
     * @param string $valName 用于数组value的字段名
     * @param bool   $fast    是否启动快速模式
     * @return array ($keyName => $valName)
     */
    public function getPairs($sql, array $params = null, $keyName = '', $valName = '', $fast = false)
    {
        if ($fast) {
            return $this->getPairsFast($sql, $params, $keyName, $valName);
        }

        $this->useBuffer();
        $res = $this->prepare($sql);
        $this->execute($params);

        $pairs = null;
        if (!($keyName && $valName)) {
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                $pairs[$row[0]] = $row[1];
            }
        } else {
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $pairs[$row[$keyName]] = $row[$valName];
            }
        }

        return $pairs;
    }

    /**
     * 快速获取成对数据
     *
     * @param string $sql     SQL语句
     * @param array  $params  预处理execute参数
     * @param string $keyName 用于数组KEY的字段名
     * @param string $valName 用于数组value的字段名
     * @return array ($keyName => $valName)
     */
    public function getPairsFast($sql, array $params = null, $keyName = '', $valName = '')
    {
        $this->disuseBuffer();
        $res = $this->prepare($sql);
        $this->execute($params);

        if (!($keyName && $valName)) {
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                yield $row[0] => $row[1];
            }
        } else {
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                yield $row[$keyName] = $row[$valName];
            }
        }
    }

    /**
     * 统计总数
     *
     * @param array $option
     * @return int
     * @throws Q_Exception
     */
    public function count(array $option = array())
    {
        $options = array(
            'field' => '',
            'table' => '',
            'where' => '',
        );
        $options = array_merge($options, $option);

        $this->chooseDbConn('SELECT');


        if (empty($options['field'])) {
            $field = '1';
        } else {
            $field = self::formatField($options['field']);
        }

        $where = $options['where'];
        if (is_array($where)) {
            $where = $this->parseWhere($where);
        }

        $table = $this->formatTable($options['table']);

        $sql = "SELECT COUNT({$field}) FROM {$table}";
        $sql .= $where ? " WHERE {$where}" : '';

        return (int)$this->getOne($sql);
    }

    /**
     * 表格配置数据获取
     *
     * @param array      $option
     * @param array|null $params
     * @param null       $callback
     * @param int        $fetchStyle
     * @return array|null
     * @throws Q_Exception
     */
    public function getFullColumns(array $option = array(), array $params = null, $callback = null, $fetchStyle = PDO::FETCH_ASSOC)
    {
        $options = array(
            'table'    => '',
            'callback' => null,
        );
        $options = array_merge($options, $option);
        $table   = $this->formatTable($options['table']);
        $sql     = "SHOW FULL COLUMNS FROM {$table}";

        $res = $this->prepare($sql);
        $this->execute($params);

        $results = null;
        $results = $res->fetchAll($fetchStyle);

        //回调处理每一行
        if (is_array($results) && $callback) {
            return array_map($callback, $results);
        }

        return $results ? $results : null;
    }

    /**
     * 通过源数据过滤获取安全数据
     *
     * @param       $table
     * @param array $data
     * @return mixed
     */
    private function _getSafeData($table, array $data)
    {
        $_dataTypeArray = array(
            'int'       => Q_Helper_Array::VAL_TYPE_FUNC_INT,
            'mediumint' => Q_Helper_Array::VAL_TYPE_FUNC_INT,
            'smallint'  => Q_Helper_Array::VAL_TYPE_FUNC_INT,
            'tinyint'   => Q_Helper_Array::VAL_TYPE_FUNC_INT,
            'timestamp' => Q_Helper_Array::VAL_TYPE_FUNC_DATETIME,
            'varchar'   => Q_Helper_Array::VAL_TYPE_FUNC_STRING,
            'char'      => Q_Helper_Array::VAL_TYPE_FUNC_STRING,
            'text'      => Q_Helper_Array::VAL_TYPE_FUNC_STRING,
            'decimal'   => Q_Helper_Array::VAL_TYPE_FUNC_FLOAT,
            'json'      => Q_Helper_Array::VAL_TYPE_FUNC_JSON,
        );

        $_fieldInfos = $this->getFieldInfo($table);

        $dataObj = Q_Helper_Array::instance($data);

        foreach ($data as $_key => $_val) {

            $_key   = trim($_key);
            $_field = $_key;

            $_exp = self::_expField($_field);

            $_isStringExpField = ($_field === '_string');
            if (!$_isStringExpField && empty($_fieldInfos[$_field])) {
                continue;
            }

            if ($_isStringExpField) {
                $_fieldInfo = array(
                    'COLUMN_DEFAULT' => '',
                    'DATA_TYPE'      => 'varchar',
                );
            } else {
                $_fieldInfo = $_fieldInfos[$_field];
            }


            if ($_exp) {//表达式,不做处理
                $_type = Q_Helper_Array::VAL_TYPE_FUNC_STRING;
            } else {
                $_type = isset($_dataTypeArray[$_fieldInfo['DATA_TYPE']])
                    ? $_dataTypeArray[$_fieldInfo['DATA_TYPE']]
                    : Q_Helper_Array::VAL_TYPE_FUNC_STRING;
            }

            if ($_type === Q_Helper_Array::VAL_TYPE_FUNC_STRING && is_array($_val)) {
                $dataObj->setArrValue($_key, join(',', $_val));
            }


            $dataObj->setRule($_key, $_type, $_fieldInfo['COLUMN_DEFAULT'], Q_Helper_Array::CK_TYPE_ISSET);
        }

        $safeData = $dataObj->getSafeArr();

        return $safeData;
    }

    /**
     * 保存数据
     *
     * @param string $table                    表名
     * @param array  $data                     数据键值对
     * @param bool   $replace                  是否替换，或指定替换模式
     *                                         self::DUPLICATE_UPDATE_MODE_REPLACE        = 1;//替换
     *                                         self::DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = 2;//+=
     *                                         self::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = 3;//*=
     *                                         self::DUPLICATE_UPDATE_MODE_APPEND         = 4;//字符串合并
     *                                         string:配合field为exp字段的话,可设置自定义更新语句
     *
     * @param array  $replaceFields            如果重复值的话，
     *                                         设置需要替换的字段，要替换的字段设置为[field => true],
     *                                         不替换的字段设置为[field => false],如果设置为空，
     *                                         默认替换data中所有的字段；
     *                                         注：不支持data中含有_string属性的字段
     * @return bool|int
     */
    public function save($table, array $data, $replace = false, $replaceFields = array())
    {
        if (empty($table) || empty($data)) {
            return false;
        }

        if (!is_array($data)) {
            return false;
        }

        if ($this->isSafeMode()) {
            $data = $this->_getSafeData($table, $data);
        }

        $_cols   = $_param = array();
        $_fields = array_keys($data);

        $_fieldsStr = '`' . join('`,`', $_fields) . '`';

        foreach ($data as $_col => $_val) {
            if (self::_expField($_col)) {//表达式方式
                $_cols[] = $_val;//直接使用表达式
                continue;
            }

            $_colStr = ":{$_col}";
            //预处理
            $_cols[]             = $_colStr;
            $_param[':' . $_col] = is_array($_val) ? join(',', $_val) : $_val;
        }
        $_cols = join(',', $_cols);

        $table = self::formatTable($table);

        $ignore = $replace ? '' : 'IGNORE';//不替换重复值的话,就忽略掉重复的值
        $sql    = "INSERT {$ignore} INTO {$table} ({$_fieldsStr}) VALUES ({$_cols}) ";

        if ($replace) {
            $_upFields = self::_getDuplicateUpFields($_fields, $replace, $replaceFields);
            $sql       .= " ON DUPLICATE KEY UPDATE {$_upFields}";
        }

        if ($this->prepare($sql)) {
            $this->execute($_param);
            $id = (int)$this->lastInsertId();

            return $id ? $id : true;
        }

        return false;
    }

    /**
     * 保存多行数据，成功返回影响行数
     *
     * @param string $table                         表名
     * @param array  $data                          多行数据
     * @param bool   $replace                       是否替换重复数据，可以指定替换模式DUPLICATE_UPDATE_MODE_*
     *                                              self::DUPLICATE_UPDATE_MODE_REPLACE        = 1;//替换
     *                                              self::DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = 2;//+=
     *                                              self::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = 3;//*=
     *                                              self::DUPLICATE_UPDATE_MODE_APPEND         = 4;//字符串合并
     *                                              string:配合field为exp字段的话,可设置自定义更新语句
     * @param array  $replaceFields                 如果重复值的话，
     *                                              设置需要替换的字段，要替换的字段设置为[field => true],
     *                                              不替换的字段设置为[field => false],如果设置为空，
     *                                              默认替换data中所有的字段
     * @param bool   $resetAutoIncrement            reset auto increment
     * @return bool
     * @throws Q_Exception
     */
    public function saveAll($table, array $data, $replace = false, $replaceFields = array(), $resetAutoIncrement = false)
    {
        if (empty($table) || empty($data)) {
            return false;
        }

        $this->chooseDbConn('INSERT');

        $_safeMode = $this->isSafeMode();

        $_cols = array();
        $_row  = array();
        foreach ($data as $_row) {
            if ($_safeMode) {
                $_row = $this->_getSafeData($table, $_row);
            }

            $_row    = $this->quote($_row);
            $_cols[] = '(' . join(',', $_row) . ')';
        }

        $_fields = array_keys($_row);

        foreach ($_fields as &$_field) {
            self::_expField($_field);
        }

        $_fieldsStr = '`' . join('`,`', $_fields) . '`';

        $_cols = join(',', $_cols);

        $table = self::formatTable($table);

        $ignore = $replace ? '' : 'IGNORE';//不替换重复值的话,就忽略掉重复的值
        $sql    = "INSERT {$ignore} INTO {$table} ({$_fieldsStr}) VALUES {$_cols} ";

        if ($replace) {
            $_upFields = self::_getDuplicateUpFields($_fields, $replace, $replaceFields);
            $sql       .= " ON DUPLICATE KEY UPDATE {$_upFields}";
        }

        $res = $this->query($sql);

        if ($resetAutoIncrement) {
            $this->_resetAutoIncrement($table);
        }

        if ($res) {
            return true;
        }

        return false;
    }

    /**
     * @param string       $table 表名
     * @param array        $data  更新的数据
     * @param array|string $where WHERE条件
     * @return bool|int 如果更新成功，则返回影响行数
     * @throws Q_Exception
     */
    public function update($table, array $data, $where = array(), $replace = false)
    {
        $set = $_param = array();

        if ($this->isSafeMode()) {

            $data = $this->_getSafeData($table, $data);
        }

        foreach ($data as $_k => $_v) {
            if ($_k == '_string') {
                $set[] = $_v;
                continue;
            }

            if ($exp = self::_expField($_k)) {
                $set[] = "`{$_k}`={$_v}";
                continue;
            }

            $set[]             = "`{$_k}`=:{$_k}";
            $_param[':' . $_k] = is_array($_v) ? join(',', $_v) : $_v;
        }

        //先选择连接类型
        $this->chooseDbConn('UPDATE');

        $set = join(',', $set);

        $table = self::formatTable($table);

        $ignore = $replace ? '' : 'IGNORE';//不替换重复值的话,就忽略掉重复的值

        $sql = "UPDATE {$ignore} {$table} SET {$set}";

        if ($where) {

            $sql .= ' WHERE ' . $this->parseWhere($where);

        }

        if ($this->prepare($sql) && $this->execute($_param)) {
            return $this->affectedRows();
        }

        return false;
    }


    /**
     * 根据主键更新指定列
     *
     * @param string $table 表名
     * @param string $field 要更新的 $string $field 字段
     * @param array  $data  数据键值对
     * @param string $pk    主键名
     * @param  array $where 限定条件
     * @return bool
     */
    public function updateCol($table, $field, array $data = array(), $pk = 'id', $where = array())
    {
        if (empty($data)) {
            return false;
        }
        $this->chooseDbConn('UPDATE');

        $when = array();
        foreach ($data as $key => $val) {
            $val    = $this->quote($val);
            $key    = $this->quote($key);
            $val    = is_array($val) ? join(',', $val) : $val;
            $when[] = "WHEN {$key} THEN {$val}";
        }

        $when = join("\r\n", $when);

        $where[$pk] = array_keys($data);
        $where      = $this->parseWhere($where);
        $table      = self::formatTable($table);

        $sql = "UPDATE {$table} SET {$field}=(CASE `{$pk}` {$when} END) WHERE {$where}";
        if ($this->query($sql)) {
            return true;
        }

        return false;
    }

    /**
     * 删除数据
     *
     * @param string $table 表名
     * @param array  $where 查询条件
     * @return bool
     */
    public function delete($table, $where = array())
    {
        if (empty($where)) {
            return false;
        }

        $this->chooseDbConn('DELETE');
        if (is_array($where)) {
            $where = $this->parseWhere($where);
        }

        $table = self::formatTable($table);

        $sql = "DELETE FROM {$table} WHERE {$where}";
        if ($this->query($sql)) {
            return true;
        }
    }

    /**
     * 清空表
     *
     * @param string $table 表名
     * @return bool
     */
    public function truncate($table)
    {
        if ($this->query("TRUNCATE TABLE `{$table}`")) {
            return true;
        }

        return false;
    }

    /**
     * 根据参数查找数据,如果有分页,可处理分页页码
     *
     * @param array $option
     * @return array
     */
    public function findData($option = array())
    {
        $options = array(
            'field'      => '*',
            'table'      => '',
            'forceIndex' => '',
            '_pk'        => 'id',
            'where'      => '',
            'group'      => '',
            'having'     => '',
            'order'      => '',
            'page'       => 0,
            'url'        => '',
            'num'        => 10,
            'target'     => '',#分页打开方式
            'pageBar'    => '{FIRST:首页}{PREV:&lt; 上一页}{BAR:[NUM]:5:2}{NEXT:下一页 &gt;}{LAST:尾页}',
            'hash'       => '',
            'callback'   => null,
            'fast'       => false,
            'union'      => false
        );

        $options = array_merge($options, $option);

        $this->chooseDbConn('SELECT');

        if (empty($options['field'])) {
            $field = '*';
        } else {
            $field = self::formatField($options['field']);
        }

        $where = $options['where'];
        if (is_array($where)) {
            $where = $this->parseWhere($where);
        }

        $having = $options['having'];
        if (is_array($having)) {
            $having = $this->parseWhere($having);
        }

        $forceIndex = '';
        if (!empty($options['forceIndex'])) {
            $forceIndex = $this->getForceIndex($options['forceIndex']);
        }

        $num    = (int)$options['num'];
        $offset = $options['page'] > 0 ? ($options['page'] - 1) * $num : 0;
        $limit  = $num ? " LIMIT {$offset}, {$num}" : '';

        $group   = $options['group'] ? " GROUP BY {$options['group']}" : '';
        $having  = $options['having'] ? " HAVING {$having}" : '';
        $orderBy = $options['order'] ? " ORDER BY {$options['order']}" : '';

        if (isset($options['hash']) && $options['hash'] === false) {
            $hash = '';
        } else {
            $hash = empty($options['hash']) ? $options['_pk'] : $options['hash'];
        }

        $options['table'] = self::formatTable($options['table']);

        $where = $where ? " WHERE {$where}" : '';

        //使用union查询进行分表查询
        if (!empty($options['union']) && is_array($options['union'])) {
            foreach ($options['union'] as $_table) {
                $_sql[] = "( SELECT {$field} 
                             FROM {$_table} {$forceIndex} 
                             {$where} {$group} {$having} )";
            }

            $sql = implode(" UNION ALL ", $_sql) . " {$orderBy} {$limit}";
        } else {
            $sql = "SELECT {$field}
                FROM {$options['table']} {$forceIndex}
                {$where} {$group} {$having} {$orderBy}
                {$limit}";
        }

        $params = array();

        if (empty($options['fast'])) {
            $data = $this->getAll($sql, $params, $hash, $options['callback']);
        } else {
            $data = $this->getAllFast($sql, $params, $hash, $options['callback']);
        }

        if ($options['page']) {
            if ($data) {
                $_field = $having ? $field : 'COUNT(1)';

                //使用union查询进行分表查询
                if (!empty($options['union']) && is_array($options['union'])) {
                    $_field = $having ? $field : 'COUNT(1) b';
                    $_sql   = [];
                    foreach ($options['union'] as $_table) {
                        $_sql[] = "( SELECT {$_field} FROM {$_table} {$where} {$group} {$having} )";
                    }

                    $_totalSql = implode(" UNION ALL ", $_sql);

                    if (empty($group) && empty($having)) {
                        $_totalSql = "SELECT SUM(t.b) FROM ({$_totalSql}) t";
                    }
                } else {
                    $_totalSql = "SELECT {$_field} FROM {$options['table']} {$where} {$group} {$having}";
                }

                if ($group || $having) {
                    $_totalSql = "SELECT COUNT(1) FROM ({$_totalSql}) t";
                }

                $total = $this->getOne($_totalSql);

                $data = array(
                    'data'      => $data,
                    'total'     => $total,//返回数据总数
                    'totalPage' => ceil($total / $options['num']),
                );

                if (!empty($options['pageBar'])) {
                    $cfg             = array(
                        'total'  => $total,
                        'rownum' => $options['num'],
                        'page'   => $options['page'],
                        'url'    => $options['url'],
                        'target' => $options['target'],
                    );
                    $pageObj         = new Q_Pagination($cfg);
                    $data['pageBar'] = $pageObj->display($options['pageBar']);
                }
            }
        }
        return $data;
    }

    /**
     * 解析WHERE语句
     *
     * @param array $where
     * @return array|string
     */
    public function parseWhere($where)
    {
        if (empty($where)) {
            return '';
        }

        if (is_string($where)) {
            return $where;
        }

        if (is_array($where)) {
            $_logic = empty($where['_logic']) ? 'AND' : $where['_logic'];
            $_where = array();

            empty($where['_complex']) || $_where[] = $this->parseWhere($where['_complex']);

            if (!empty($where['_complexes']) && is_array($where['_complexes'])) {
                foreach ($where['_complexes'] as $_complex) {
                    $_where[] = $this->parseWhere($_complex);
                }
            }

            empty($where['_string']) || $_where[] = "({$where['_string']})";
            unset($where['_complex'], $where['_complexes'], $where['_logic'], $where['_string']);

            foreach ($where as $_field => $_val) {
                $_field      = trim($_field);
                $_comparison = '=';
                foreach (self::$_comparison as $_cval) {
                    $_cOffset = strripos($_field, $_cval);
                    if ($_cOffset > 0) {//找到对应的运算符，
                        $_field      = trim(substr($_field, 0, $_cOffset));
                        $_comparison = trim($_cval);
                        break;
                    }
                }

                //                $_field = strpos($_field, '.') ? str_replace('.', '`.`', $_field) : $_field;

                $_field = self::formatField($_field);

                $_val = $this->quote($_val);

                if (!empty($_val) && is_array($_val)) {
                    $_comparison = $_comparison == '=' ? 'IN' : $_comparison;//替换默认的=号为IN查询

                    if ($_comparison == 'BETWEEN' || $_comparison == 'NOT BETWEEN') {
                        $_where[] = "({$_field} {$_comparison} " . join(' AND ', $_val) . ")";
                    } else {
                        $_where[] = "({$_field} {$_comparison} (" . join(',', $_val) . "))";
                    }
                } else if (is_string($_val)) {
                    $_where[] = "({$_field} {$_comparison} {$_val})";
                }
            }
            $where = join(" {$_logic} ", $_where);
        }

        return $where ? "({$where})" : '';
    }

    /**
     * 最后插入的ID
     *
     * @example ../Examples/lastInsertId.php
     * @return int
     */
    public function lastInsertId()
    {
        return $this->_conn->lastInsertId();
    }

    /**
     * 结果集行数
     *
     * @example ../Examples/numRows.php
     * @return int
     */
    public function numRows()
    {
        return $this->_res->rowCount();
    }

    /**
     * 影响行数
     *
     * @example ../Examples/affectedRows.php
     * @return int
     */
    public function affectedRows()
    {
        return $this->numRows();
    }


    /**
     * 获取表字段信息
     *
     * @param string       $table  表名
     * @param string       $dbName 数据库名
     * @param string|array $cols   字段名
     * @return array
     * @throws Q_Exception
     */
    public function getFieldInfo($table, $dbName = '', $cols = '*')
    {
        if (!empty($cols) && $cols != '*') {
            $cols = $this->quote($cols);
        }

        if (is_array($cols)) {
            $cols = join(',', $cols);
        }

        $cols = empty($cols) ? '*' : $cols;

        empty($dbName) && isset($this->_servers[self::DB_TYPE_MASTER]['database']) && $dbName = $this->_servers[self::DB_TYPE_MASTER]['database'];

        if (empty($dbName)) {
            throw new Q_Exception('Not have chosen to link the database!');
        }

        $_cacheKey = __METHOD__ . '|' . $table . '|' . $dbName . '|' . $cols;

        if (!empty(self::$_cache[$_cacheKey])) {
            return self::$_cache[$_cacheKey];
        }

        $sql = "SELECT * FROM information_schema.COLUMNS WHERE `TABLE_NAME`='{$table}' AND `TABLE_SCHEMA`='{$dbName}' ";
        if ($cols && $cols != '*') {
            $sql .= " AND COLUMN_NAME IN({$cols})";
        }

        $data                     = $this->getAll($sql, null, 'COLUMN_NAME');
        self::$_cache[$_cacheKey] = $data;

        return $data;
    }

    /**
     * 获取数据库所有表表名
     *
     * @param string $dbName 数据库名
     * @param string $connType
     * @param int    $fetchStyle
     * @return array|false
     * @throws Q_Exception
     */
    public function getDbTableNames($dbName = '', $connType = self::DB_TYPE_MASTER, $fetchStyle = PDO::FETCH_ASSOC)
    {
        empty($dbName) && isset($this->_servers[$connType]['database']) && $dbName = $this->_servers[$connType]['database'];

        if (empty($dbName)) {
            throw new Q_Exception('Not have chosen to link the database!');
        }

        $sql         = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='{$dbName}' AND TABLE_TYPE='base table'";
        $_tableNames = $this->query($sql)->fetchAll($fetchStyle);
        $tableNames  = array();

        if (empty($_tableNames)) {
            return false;
        }

        foreach ($_tableNames as $_val) {
            $tableNames[$_val['TABLE_NAME']] = $_val['TABLE_NAME'];
        }

        return $tableNames;
    }

    /**
     * 获取所有数据库名称
     *
     * @return array|bool
     * @throws Q_Exception
     */
    public function getDbNames()
    {
        $sql = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA";

        return $this->getCol($sql);
    }

    /**
     * 对字符串安全处理
     *
     * @param mixed $val 值
     * @param int   $type
     * @return string[]|string
     */
    public function quote($val, $type = PDO::PARAM_STR)
    {
        //配置默认数据链接为从库
        if (!$this->_conn) {
            $this->createConn(self::DB_TYPE_SLAVE);
        }
        if (is_array($val)) {
            foreach ($val as $_field => &$_val) {
                if (self::_expField($_field)) {//表达式字段不做处理
                    continue;
                }

                $_val = $this->_conn->quote($_val, $type);
            }
        } else {
            $val = $this->_conn->quote($val, $type);
        }

        return $val;
    }

    /**
     * 释放资源
     *
     * @param bool $res
     * @example ../Examples/free.php
     * @return bool
     */
    public function free($res = null)
    {
        $res || ($res = $this->_res);

        return $res->closeCursor();
    }

    /**
     * 关闭数据库链接
     *
     * @return void
     */
    public function close($connType = null)
    {
        if ($connType) {
            $this->$connType = null;
            $this->_conn = $this->_master = $this->_slave = null;
        } else {
            $this->_conn = $this->_master = $this->_slave = null;
        }

    }

    /**
     * 设置调试开关
     *
     * @param bool $debug 是否调试
     * @return Q_Abstract_Db
     */
    public function setDebug($debug = true)
    {
        $this->_debug = $debug;

        return $this;
    }


    /**
     * @param array|null $param
     * @return bool
     * @throws Q_Exception
     */
    public function execute(array $param = null)
    {
        $_queryStartTime = microtime(true);
        try {
            $res = $this->_res->execute($param);
        } catch (Exception $ex) {
            /*
            if ($this->_reconnect($ex) && $this->prepare($this->_realSql, $this->_prepareOptions)) {
                return $this->execute($param);
            }
            */
        }

        $this->_param = $param;

        //        if ($this->_debug) {
        self::$_sqlPool[$this->_name]['sql'][]
            = $this->_sql . "\r\n" .
              'params:' . var_export($param, true)
              . "\r\n connType:{$this->_curConnType},
          query time:" . (microtime(true) - $_queryStartTime);

        //        }

        return $res;
    }

    /**
     * 获取数据库错误信息
     *
     * @return array|false
     */
    public function dbErrorInfo()
    {
        return $this->_conn ? $this->_conn->errorInfo() : false;
    }

    /**
     * 获取数据库查询错误信息
     *
     * @return array|false
     */
    public function resErrorInfo()
    {
        return $this->_res ? $this->_res->errorInfo() : false;
    }


    /**
     * 获取SQL
     *
     * @param string $connName 数据库链接名 为空时返回所有的已查询过的SQL
     * @param bool   $dump     是否直接打印
     * @return array|string|null
     * @example ../Examples/setDebug.php
     */
    public static function dumpSql($connName = '', $dump = true)
    {

        $sql = $connName
            ? (empty(self::$_sqlPool[$connName]) ? false : self::$_sqlPool[$connName])
            : self::$_sqlPool;


        $dump && Q::debug($sql, false);

        return $sql;
    }

    /**
     * 获取SQL中的ON DUPLICATE KEY UPDATE 字段
     *
     * @param array $fields
     * @param int   $updateMode         self::DUPLICATE_UPDATE_MODE_REPLACE        = 1;//替换
     *                                  self::DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = 2;//+=
     *                                  self::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = 3;//*=
     *                                  self::DUPLICATE_UPDATE_MODE_APPEND         = 4;//字符串合并
     *                                  string:配合field为exp字段的话,可设置自定义更新语句
     * @param array $replaceFields      设置替换字段规则
     * @return string
     */
    protected static function _getDuplicateUpFields($fields, $updateMode = self::DUPLICATE_UPDATE_MODE_REPLACE, array $replaceFields = array())
    {
        $fields = (array)$fields;


        if (empty($replaceFields)) {
            foreach ($fields as &$_field) {
                $_field = self::_getDuplicateField($_field, $updateMode);
            }
        } else {
            $fields = array();
            foreach ($replaceFields as $_field => $_updateMode) {
                $fields[] = self::_getDuplicateField($_field, $_updateMode);
            }
        }

        return join(',', array_filter($fields));
    }

    /**
     * 生成重复键SQL
     *
     * @param string     $field         字段,支持EXP字段
     * @param int|string $updateMode    冲突解决方式:
     *                                  self::DUPLICATE_UPDATE_MODE_REPLACE        = 1;//替换
     *                                  self::DUPLICATE_UPDATE_MODE_COMPOUND_ADD   = 2;//+=
     *                                  self::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI = 3;//*=
     *                                  self::DUPLICATE_UPDATE_MODE_APPEND         = 4;//字符串合并
     *                                  string:配合field为exp字段的话,可设置自定义更新语句
     * @return string
     */
    private static function _getDuplicateField($field, $updateMode = self::DUPLICATE_UPDATE_MODE_REPLACE)
    {
        if (!$updateMode) {
            return '';
        }

        if ($updateMode === true) {
            $updateMode = self::DUPLICATE_UPDATE_MODE_REPLACE;
        }

        $exp = self::_expField($field);

        if ($exp && is_string($updateMode)) {
            return $field = "`{$field}`={$updateMode}";
        }

        switch ($updateMode) {
            case self::DUPLICATE_UPDATE_MODE_COMPOUND_ADD:
                $field = "`{$field}`=`{$field}`+VALUES(`{$field}`)";
                break;
            case self::DUPLICATE_UPDATE_MODE_COMPOUND_MULTI:
                $field = "`{$field}`=`{$field}`*VALUES(`{$field}`)";
                break;
            case self::DUPLICATE_UPDATE_MODE_APPEND:
                $field = "`{$field}`=CONCAT(`{$field}`, VALUES(`{$field}`))";
                break;
            case self::DUPLICATE_UPDATE_MODE_REPLACE:
            default:
                $field = "`{$field}`=VALUES(`{$field}`)";
                break;
        }


        return $field;
    }

    /**
     * 检查表名是否合法
     *
     * @param string $field 以字母开头，可以包含下划线，可以是表名.字段名格式
     * @return bool
     */
    protected static function _checkFieldName($field)
    {
        return (bool)preg_match('/^([a-zA-Z_]+[a-zA-Z0-9_]*)(\.[a-zA-Z_]+[a-zA-Z0-9_]*)*$/', $field);
    }

    /**
     * 格式化字段
     *
     * @param string|array $field 字段名
     * @return string
     * @throws Q_Exception
     */
    public static function formatField($field)
    {
        if (is_array($field)) {
            foreach ($field as &$_field) {
                $_field = self::formatField($_field);
            }

            return join(',', $field);
        }

        if (self::_checkFieldName($field)) {
            if (strpos($field, '.')) {
                $field = str_replace('.', '`.`', $field);
            }

            $field = '`' . $field . '`';
        }

        return $field;

    }

    /**
     * 是否是表达式字段 表达式字段：['field EXP' => 'field + 1']
     *
     * @param string $field 字段,引用方式,如果是表达式的话,自动转换成正确的字段,并返回值为true,否则不做变动,返回false
     * @return bool
     */
    private static function _expField(&$field)
    {
        if (!is_string($field)) {
            return false;
        }

        if ($field === '_string') {
            return true;
        }

        if (strlen($field) > 4 && strpos($field, ' ') && strtoupper(substr($field, -4)) === ' EXP') {//表达式方式
            $field = substr($field, 0, -4);

            return true;
        }

        return false;
    }

    /**
     * 格式化表名 TODO:未完成智能解析多表结构
     *
     * @param string $table 表名
     * @return string
     */
    public static function formatTable($table)
    {
        $table = trim($table);
        if (empty($table)) {
            return false;
        }
        if ($table[0] == '`' && $table[strlen($table) - 1] == '`') {
            return $table;
        }

        if (strpos($table, ' ') || strpos($table, ',')) {
            return $table;
        }

        if (strpos($table, '.')) {
            $table = str_replace('.', '`.`', $table);
        }

        $table = "`{$table}`";

        return $table;
    }

    /**
     * reset auto increment
     *
     * @param $table
     * @throws Q_Exception
     */
    private function _resetAutoIncrement($table)
    {
        $this->query("ALTER TABLE {$table} AUTO_INCREMENT=1");
    }

    /**
     * 事务是否已经激活
     *
     * @return bool
     */
    protected function _isTransactionActive()
    {
        return $this->_transactionCount > 0 && $this->_conn;
    }

    /**
     * 多库开始事务
     *
     */
    public function beginAll()
    {
        foreach (self::$_instance as $_db) {
            $_db->begin();
        }
    }

    /**
     * 开始事务
     *
     * @return bool
     */
    public function begin()
    {
        $this->createConn(self::DB_TYPE_MASTER);

        if (!$this->_transactionCount++) {
            return $this->_master->beginTransaction();
        }

        $this->_master->exec('SAVEPOINT trans' . $this->_transactionCount);

        return $this->_transactionCount > 0;
    }

    /**
     * 多库提交事务
     *
     * @throws Q_Exception
     */
    public function commitAll()
    {
        foreach (self::$_instance as $_db) {
            $_db->commit();
        }
    }

    /**
     * 提交事务
     *
     * @return bool
     * @throws Q_Exception
     */
    public function commit()
    {
        if (!$this->_isTransactionActive()) {
            throw new Q_Exception('Failed to commit transaction: transaction was inactive.');
        }

        if (!--$this->_transactionCount) {
            return $this->_master->commit();
        }

        return $this->_transactionCount >= 0;
    }

    /**
     * 多库回滚事务
     *
     */
    public function rollBackAll()
    {
        foreach (self::$_instance as $_db) {
            $_db->rollBack();
        }
    }

    /**
     * 回滚事务
     *
     * @return bool|void
     */
    public function rollBack()
    {
        if (!$this->_isTransactionActive()) {
            return false;
        }

        if (--$this->_transactionCount) {
            $this->_conn->exec('ROLLBACK TO trans' . ($this->_transactionCount + 1));

            return true;
        }

        return $this->_master->rollBack();
    }

    public function getForceIndex($forceIndex)
    {
        if (!empty($forceIndex)) {
            $forceIndex = is_array($forceIndex) ? join(',', $forceIndex) : $forceIndex;
            $forceIndex = " FORCE INDEX({$forceIndex})";
        }

        return $forceIndex;
    }

    /**
     * Replaces any parameter placeholders in a query with the value of that
     * parameter. Useful for debugging. Assumes anonymous parameters from
     * $params are are in the same order as specified in $query
     *
     * @param string $query  The sql query with parameter placeholders
     * @param array  $params The array of substitution parameters
     * @return string The interpolated query
     */
    protected function _interpolateQuery($query = '', $params = array())
    {
        $keys = array();

        $query || $query = $this->_sql;
        $params || $params = $this->_param;


        # build a regular expression for each parameter
        foreach ($params as $key => &$value) {
            if (is_string($key)) {
                $keys[] = '/:' . ltrim($key, ':') . '/';
            } else {
                $keys[] = '/[?]/';
            }
            $value = $this->quote($value);
        }

        $query = preg_replace($keys, $params, $query, 1, $count);

        return $query;
    }
}
