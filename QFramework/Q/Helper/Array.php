<?php
/**
 * 数组帮助类
 *
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 15/12/17
 * @version     : $id$
 */
class Q_Helper_Array
{
    const CK_TYPE_ISSET = 'isset';
    const CK_TYPE_EMPTY = 'empty';

    const VAL_TYPE_STRING   = 'strval';
    const VAL_TYPE_INT      = 'intval';
    const VAL_TYPE_DATETIME = 'date';
    const VAL_TYPE_ARRAY    = 'array';
    const VAL_TYPE_BOOL     = 'boolval';
    const VAL_TYPE_FLOAT    = 'floatval';
    const VAL_TYPE_JSON     = 'json_encode';
    const VAL_TYPE_MIXED    = 'mixed';

    const VAL_TYPE_FUNC_STRING   = 'getString';
    const VAL_TYPE_FUNC_JSON     = 'getJSON';
    const VAL_TYPE_FUNC_INT      = 'getInt';
    const VAL_TYPE_FUNC_UINT     = 'getUint';
    const VAL_TYPE_FUNC_DATETIME = 'getDateTime';

    const VAL_TYPE_FUNC_RANGE = 'getRange';
    const VAL_TYPE_FUNC_ARRAY = 'getArray';
    const VAL_TYPE_FUNC_BOOL  = 'getBool';
    const VAL_TYPE_FUNC_FLOAT = 'getFloat';
    const VAL_TYPE_FUNC_MIXED = 'getMixed';

    const RULE_FIELD_TYPE_FUNC_IDX = 0;
    const RULE_FIELD_DEFAULT_IDX   = 1;
    const RULE_FIELD_CKFUNC_IDX    = 2;

    protected $_arr     = array();
    protected $_safeArr = array();
    protected $_rules   = array();

    /**
     * 返回未处理的数据
     *
     * @param string $key 要取数据的键名
     * @return mixed
     */
    public function getArr($key = '')
    {
        $arr = $this->_arr;

        if (!empty($key) && isset($arr[$key])) {
            return $arr[$key];
        }

        return $arr;
    }

    /**
     * 设置原数据
     *
     * @param array $arr
     * @return $this
     */
    public function setArr(array $arr)
    {
        $this->_arr = $arr;
        return $this;
    }

    /**
     * 设置原数组对应KEY的VALUE值
     *
     * @param $value
     * @param $key
     * @return $this
     * @throws Q_Exception
     */
    public function setArrValue($key = null, $value = '')
    {
        $array = $this->getArr();

        if (isset($array[$key])) {
            $array[$key] = $value;
            $this->setArr($array);
        }

        return $this;
    }

    /**
     * 返回安全处理过的数据
     *
     * @param string $key 要取数据的键名
     * @return mixed
     */
    public function getSafeArr($key = '')
    {
        $arr = $this->_safeArr;

        if (!empty($key) && isset($arr[$key])) {
            return $arr[$key];
        }

        return $arr;
    }

    /**
     * 设置安全数据
     *
     * @param array $safeArr
     * @return $this
     */
    protected function _setSafeArr($safeArr)
    {
        $this->_safeArr = $safeArr;
        return $this;
    }

    protected function _setSafeArrItem($key, $val)
    {
        $this->_safeArr[$key] = $val;
        return $this;
    }


    /**
     * 获取所有规则
     *
     * @return array
     */
    public function getRules()
    {
        return $this->_rules;
    }

    /**
     * 批量设置规则
     *
     * @param array $rules
     * @return $this
     */
    public function setRules($rules)
    {
        $this->_rules = $rules;
        if ($rules) {
            foreach ($rules as $_key => $_val) {

                $_def    = $_ckFUnc = null;
                $_params = array();
                //格式化数据
                if (is_string($_val) && method_exists($this, $_val)) {
                    $_params = array($_key, $_val);
                } elseif (is_array($_val)) {
                    $_params = array($_key);
                    $_params = array_merge($_params, $_val);
                }


                call_user_func_array(array($this, 'setRule'), $_params);
            }
        }
        return $this;
    }

    /**
     * 设置数组单个元素规则并格式化
     *
     * @param string $key
     * @param string $funcType
     * @param string $def
     * @param string $ckFunc
     * @return $this
     */
    public function setRule($key, $funcType = self::VAL_TYPE_FUNC_STRING, $def = '', $ckFunc = self::CK_TYPE_EMPTY)
    {
        $this->_rules[$key] = array($funcType, $def, $ckFunc);

        $_params = array($key);

        if ($def || $ckFunc == self::CK_TYPE_ISSET) {
            $_params[1] = $def;

            if ($ckFunc) {
                $_params[2] = $ckFunc;
            }
        }

        $val = call_user_func_array(array($this, $funcType), $_params);
        $this->_setSafeArrItem($key, $val);
        return $this;
    }

    /**
     * @var $this
     */
    protected static $_instance;

    public static function instance(array $array = array())
    {
        self::$_instance = new self();

        if ($array) {
            self::$_instance->setArr($array);
        }

        return self::$_instance;
    }

    public function __construct(array $array = null)
    {
        if ($array) {
            $this->setArr($array);
        }
    }

    /**
     * 判断key是否设置
     * @param $key
     * @return bool
     */
    public function isset($key)
    {
        $arr = $this->getArr();
        return isset($arr[$key]);
    }

    /**
     * 判断对应key的值是否为empty
     * @param $key
     * @return bool
     */
    public function isEmpty($key)
    {
        $arr = $this->getArr();
        return empty($arr[$key]);
    }

    /**
     * 判断对应的key是否存在
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        $arr = $this->getArr();
        return array_key_exists($key, $arr);
    }

    public function get($key, $type = self::VAL_TYPE_STRING, $def = '', $ckFunc = self::CK_TYPE_EMPTY)
    {
        $arr = $this->getArr();

        $val = null;

        if ($ckFunc === self::CK_TYPE_EMPTY) {
            $val = empty($arr[$key]) ? $def : $arr[$key];
        } else if ($ckFunc === self::CK_TYPE_ISSET) {
            $val = !isset($arr[$key]) ? $def : $arr[$key];
        } else if (is_callable($ckFunc)) {
            $val = !$ckFunc($arr[$key]) ? $def : $arr[$key];
        }

        if ($type === self::VAL_TYPE_ARRAY) {
            return (array)$val;
        }

        /**
         * 混合类型，不做处理
         */
        if ($type == self::VAL_TYPE_MIXED) {
            return $val;
        }

        /**
         * json类型,只处理数组
         */
        if ($type === self::VAL_TYPE_JSON) {
            if (is_array($val)) {
                return json_encode($val);
            } else if (is_string($val)) {
                return $val;
            } else {
                return null;
            }
        }

        /**
         * 时间类型
         */
        if ($type === self::VAL_TYPE_DATETIME) {
            if (is_numeric($val)) {
                return date('Y-m-d H:i:s', $val);
            } else if (is_string($val)) {
                return date('Y-m-d H:i:s', strtotime($val));
            } else {
                return null;
            }
        }

        if (!is_null($val) && is_callable($type)) {
            return $type($val);
        }

        return $val;
    }

    public function getInt($key, $def = 0, $ckFunc = self::CK_TYPE_EMPTY)
    {
        return $this->get($key, self::VAL_TYPE_INT, $def, $ckFunc);
    }

    /**
     * 取正数,如果是非法的数字,返回默认def值
     *
     * @param        $key
     * @param int    $def
     * @param string $ckFunc
     * @return mixed
     */
    public function getUint($key, $def = 0, $ckFunc = self::CK_TYPE_EMPTY)
    {
        $val = self::getInt($key, $def, $ckFunc);
        return max(0, $val);
    }

    /**
     * 指定范围取值,如果数值不在范围内,返回最近的范围边界
     *
     * @param        $key
     * @param array  $def
     * @param string $ckFunc
     * @return int
     */
    public function getRange($key, $def = array(), $ckFunc = self::CK_TYPE_EMPTY)
    {
        $def = (array)$def;
        $min = isset($def['min']) ? $def['min'] : (isset($def[0]) ? $def[0] : null);
        $max = isset($def['max']) ? $def['max'] : (isset($def[1]) ? $def[1] : null);

        $val = self::getInt($key, 0, $ckFunc);

        if (isset($min)) {
            $val = max($min, $val);
        }

        if (isset($max)) {
            $val = min($max, $val);
        }


        return $val;
    }


    public function getString($key, $def = '', $ckFunc = self::CK_TYPE_EMPTY)
    {
        return $this->get($key, self::VAL_TYPE_STRING, $def, $ckFunc);
    }

    public function getJSON($key, $def = array(), $ckFunc = self::CK_TYPE_EMPTY)
    {
        return $this->get($key, self::VAL_TYPE_JSON, $def, $ckFunc);
    }

    public function getArray($key, $def = array(), $ckFunc = self::CK_TYPE_EMPTY)
    {
        return $this->get($key, self::VAL_TYPE_ARRAY, $def, $ckFunc);
    }

    public function getBool($key, $def = false, $ckFunc = self::CK_TYPE_EMPTY)
    {
        $_val = $this->get($key, self::VAL_TYPE_BOOL, $def, $ckFunc);

        if ($_val === null) {
            $_val = $def;
        } else {
            if (empty($_val) || $_val === 'false') {
                $_val = false;
            } else {
                $_val = boolval($_val);
            }
        }

        return $_val;
    }

    public function getFloat($key, $def = 0.0, $ckFunc = self::CK_TYPE_EMPTY)
    {
        return $this->get($key, self::VAL_TYPE_FLOAT, $def, $ckFunc);
    }

    public function getDateTime($key, $def = null, $ckFunc = self::CK_TYPE_EMPTY)
    {
        return $this->get($key, self::VAL_TYPE_DATETIME, $def, $ckFunc);
    }


    public function getMixed($key, $def = null, $ckFunc = self::CK_TYPE_EMPTY)
    {
        return $this->get($key, self::VAL_TYPE_MIXED, $def, $ckFunc);
    }

}