<?php

/**
 * 验证类
 * 例子：
 * //配置验证规则
 * $rules = array(
 * 'field' => array(
 * 'name' => '字段名',
 * 'rules' => array(
 * 'required' => array('messge' => '请输入 {name} '),
 * 'number' => array('min' => 10, 'max' => 100, 'message' => '{name} 的值为 {value}, 必须为大于等于{min}小于等于{max}的数字'),
 * 'length' => array('min' => 10, 'max' => 100, 'message' => '{name} 的值为 {value}, 长度必须为大于等于{min}小于等于{max}'),
 * 'length' => array('length' => 100, 'message' => '{name} 的值为 {value}, 长度必需为{length}'),
 * 'enum' => array('enum' => [1,2,3,4], 'message' => '{name} 的值为 {value}, 不丰枚举中'),
 * )
 * )
 * );
 * $vail = new Q_Validate(); // 初始化验证类
 * $vail->setRules($rules); // 设置验证规则到类中
 * $vail->setParams(array('field'=>'123')); // 设置待验证值到类中
 * if ($vail->validate()) {
 * // 验证成功
 * $output->msg = 'ok';
 * } else {
 * // 验证失败
 * $output->msg = var_export($vail->getErrorMessage(), true);
 * }
 * 目前支持以下的可使用的验证方式
 *  - required 必填
 *  - number   验证数字
 *  - email    验证邮箱格式
 *  - url      验证网址
 *  - reg      验证正式表达式
 *  - length   验证字符长度
 *  - compare  比较2个字段是否相待
 *  - mobile   验证手机号
 *  - chinese  验证中文
 *  - idcard   验证身份证号，15位或18位，简单验证，不能保证绝对真实性，需要真实性需要对接公安系统
 *  - custom   自定义验证函数
 *  - range 　　范围
 * 自定义验证函数字法如下：
 *
 * @param string     $field    字段名
 * @param array      $rule     验证规则
 * @param Q_Validate $validate 验证类操作实例
 *                             function func(&$field, array &$rule, &$validate) {
 *                             }
 * @author  wiki<wukun@charmfocus.com>
 * @date    2014-11-12
 * @version $id$
 */
class Q_Validate
{

    /**
     * @var $this
     */
    protected static $_instance = null;

    /**
     * 默认信息验证失败信息
     *
     * @var string
     */
    public $defaultErrorMessage = '{name} 信息验证失败';

    /**
     * 验证规则
     *
     * @var array
     */
    private $_rules = array();

    /**
     * 待验证数据
     *
     * @var array
     */
    private $_params = array();

    /**
     * 错误信息
     *
     * @var array
     */
    private $_errorMessage = array();


    /**
     * 表单验证动态表单生成
     *
     * @var Q_ValidateForm
     */
    public $form;


    /**
     * 单例
     *
     * @param $valiName
     * @return $this
     */
    public static function instance($valiName = 'data')
    {

        if (isset(self::$_instance[$valiName])) {
            $obj = self::$_instance[$valiName];
        } else {
            $obj                        = new self($valiName);
            self::$_instance[$valiName] = $obj;
        }
        return $obj;
    }

    public function __construct($valiName = 'data')
    {
        $valiForm = new Q_ValidateForm($valiName);

        /**
         * @var $form Q_ValidateForm
         */
        $form = $valiForm->setValidate($this);

        $this->form = $form;
    }

    /**
     * @return Q_ValidateForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Q_ValidateForm $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }


    /**
     * 设置验证规则
     *
     * @param array $rules 验证规则
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->_rules = $rules;
        return $this;
    }

    /**
     * 获取验证规则
     *
     * @param string $field 需要获取的字段
     * @return array|null
     */
    public function getRules($field = null)
    {
        if (isset($field)) {
            return isset($this->_rules[$field]) ? $this->_rules[$field] : null;
        }
        return $this->_rules;
    }

    /**
     * 批量设置待验证数据
     *
     * @param array $data 待验证数据
     * @return $this
     */
    public function setParams(array $data = null)
    {

        $this->_params = $data;
        return $this;
    }

    /**
     * 设置待验证数据单条
     *
     * @param $k
     * @param $v
     * @return $this
     * @internal param array $data 待验证数据
     */
    public function setParam($k, $v)
    {
        $this->_params[$k] = $v;
        return $this;
    }

    /**
     * 获取验证数据
     *
     * @param string $key 需要获取的key
     * @return mixed
     */
    public function getParams($key = null)
    {
        if (isset($key)) {
            if (isset($this->_params[$key])) {
                return $this->_params[$key];
            } else {
                return null;
            }
        }
        return $this->_params;
    }

    /**
     * 开始验证
     */
    public function validate(bool $isReset = true)
    {
        if (empty($this->_rules)) {
            $this->autoCheckToken($isReset);
            return !$this->hasError();
        }

        foreach ($this->_rules as $k => $v) {
            if (isset($v['rules']) && is_array($v['rules']) && !empty($v['rules'])) {
                foreach ($v['rules'] as $key => $val) {

                    if (empty($val)) {
                        $val = array();
                    }

                    //                    if ($key == 'custom') {
                    //                        if (!empty($val['func']) && is_callable($val['func'])) {
                    //                            $valiResult = call_user_func_array($val['func'], array(&$k, &$val, &$this));
                    //                            if (!$valiResult) {
                    //                                break;
                    //                            }
                    //                        }
                    //                    }

                    //用于前台AJA验证
                    if ($key == 'ajax') {
                        continue;
                    }

                    $funcName = '_vali' . ucfirst($key);
                    if (method_exists($this, $funcName)) {
                        $valiResult = call_user_func_array(array(&$this, $funcName), array(&$k, &$val));
                        if (!$valiResult) {
                            break;
                        }
                    }

                }
            }
        }

        $this->autoCheckToken($isReset);

        if ($this->hasError()) {
            return false;
        }

        return true;
    }

    /**
     * 令牌验证
     */
    public function autoCheckToken(bool $isReset = true)
    {
        //令牌需要验证
        if (Q_Config::get(APP_NAME . '_Global', 'TOKEN_ON')) {
            $_input = Q_Request::instance();
            // 支持使用token(false) 关闭令牌验证
            $_onceOn = empty($_input->request(Q_ValidateForm::TOEKN_KEY)) ? null : strtolower($_input->request(Q_ValidateForm::TOEKN_KEY));

            if ((!empty($_input->request('e')) || !empty($_input->request('exec')) || $_input->getType() == $_input::AJAX) && $_onceOn !== 'false') {
                $name   = Q_Config::get(APP_NAME . '_Global', 'TOKEN_NAME');
                $_token = empty($_input->request($name)) ? '' : $_input->request($name);

                if (empty($_token) || !isset($_COOKIE[$name])) { // 令牌数据无效
                    $this->addErrorMessage($name, ['message' => 'form token is error', 'code' => -100]);
                }

                // 令牌验证
                if (isset($_COOKIE[$name])) { // 防止重复提交
                    if ($_COOKIE[$name] != $_token) {
                        $this->addErrorMessage($name, ['message' => 'form token is error', 'code' => -100]);
                    }

                    if ($isReset) {
                        unset($_COOKIE[$name]); // 验证完成销毁session
                    }
                }
            }
        }

        return;
    }

    /**
     * 是否存在错误
     *
     * @param string $field 字段名
     * @return boolean
     */
    public function hasError($field = '')
    {
        if (!empty($field)) {
            if (isset($this->_errorMessage[$field])) {
                return true;
            }
        } else {
            if (!empty($this->_errorMessage)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 添加错误信息
     *
     * @param string       $field  字段名
     * @param string|array $rule   $rule='错误信息'; | $rule = ['message'=>'错误信息','code'=>-100];
     * @param string       $defTpl 要添加的消息
     * @return $this
     */
    public function addErrorMessage($field, $rule, $defTpl = '请输入{name}')
    {

        if (empty($rule)) {
            return $this;
        }
        if (is_string($rule)) {
            $rule = array('message' => $rule);
        }

        $code = 0;
        if (isset($rule['code'])) {
            $code = $rule['code'];
        }
        $this->_errorMessage[$field][$code] = $this->formatMessage($field, $rule, $defTpl);

        return $this;
    }

    /**
     * 获取错误消息
     *
     * @param string $field 字段名
     * @return array|bool
     */
    public function getErrorMessage($field = '')
    {
        if (!empty($field)) {
            if (isset($this->_errorMessage[$field])) {
                return $this->_errorMessage[$field];
            } else {
                return false;
            }
        }
        return $this->_errorMessage;
    }

    /**
     * 获取第一个字段的错误消息
     *
     * @return mixed
     */
    public function getFirstErrorMessage()
    {
        return reset($this->_errorMessage);
    }

    /**
     * 获取第一条错误消息,如果指定字段获取到指定字段的第一个错误，如果没有指定，获取第一个字段的错误
     *
     * @param string $field 字段名
     * @return false|Q_ValidateMessage
     * @throws Q_Exception
     */
    public function getFirstErrorMessageObj($field = '')
    {
        $_error = self::getErrorMessage($field);
        if (!$_error) {
            throw new Q_Exception('message not found', 0);
        }

        if (!$field) {
            $_error = reset($_error);
        }
        foreach ($_error as $key => $val) {
            $_error['key']   = $key;
            $_error['value'] = $val;
        }
        return new Q_ValidateMessage($_error['value'], $_error['key']);
    }

    /**
     *
     */
    public function getFirstFieldError()
    {
        if (empty($this->_errorMessage)) {
            return false;
        }

        $_errMsg = reset($this->_errorMessage);
        return array('code' => array_keys($_errMsg), 'message' => array_values($_errMsg));
    }

    /**
     * 格式化消息
     *
     * @param string $field               字段名
     * @param array  $rule                规则
     * @param string $defaultErrorMessage 默认的错误提示消息
     * @return string
     */
    public function formatMessage(&$field, array &$rule, $defaultErrorMessage = '')
    {

        $name  = $this->getFieldName($field);
        $value = $this->getParams($field);

        if (empty($rule['message'])) {
            $msg = empty($defaultErrorMessage) ? $this->defaultErrorMessage : $defaultErrorMessage;
        } else {
            $msg = $rule['message'];
        }

        $length = isset($rule['length']) ? $rule['length'] : '';
        $min    = isset($rule['min']) ? $rule['min'] : '';
        $max    = isset($rule['max']) ? $rule['max'] : '';
        $value  = is_array($value) ? json_encode($value) : $value;

        return str_replace(array('{name}', '{value}', '{length}', '{min}', '{max}'), array($name, $value, $length, $min, $max), $msg);
    }

    /**
     * 获取字段的名字
     *
     * @param string $field 字段名
     * @return string
     */
    public function getFieldName(&$field)
    {
        if (isset($this->_rules[$field]['name'])) {
            return $this->_rules[$field]['name'];
        }
        return $field;
    }

    /**
     * 获取字段的验证规则
     *
     * @param string $field 字段名
     * @return array|boolean
     */
    public function getFieldRules(&$field)
    {
        if (isset($this->_rules[$field]['rules'])) {
            return $this->_rules[$field]['rules'];
        }
        return false;
    }

    /**
     * 验证自定义函数
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiCustom(&$field, array &$rule)
    {
        if (!empty($rule['func']) && is_callable($rule['func'])) {
            $valiFlag = $rule['func']($field, $rule, $this);
            if (!$valiFlag) {
                $this->addErrorMessage($field, $rule);
            }
            return $valiFlag;
        }
        return true;
    }

    /**
     * 验证必填项
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiRequired(&$field, array &$rule)
    {
        $data = $this->getParams($field);

        if ($data === false) {
            $this->addErrorMessage($field, $rule, '请输入{name}');
            return false;
        }

        if (!is_numeric($data) && empty($data)) {
            $this->addErrorMessage($field, $rule, '请输入{name}');
            return false;
        }

        return true;
    }

    /**
     * 验证数字
     * 本验证函数规则中支持3个额外参数
     *  - min     最小数字
     *  - max     最大数字
     *  - message 报错信息，可通过 {name} 来替换为字段名
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiNumber(&$field, array &$rule)
    {

        $data = $this->getParams($field);

        if ($data !== 0 && empty($data)) {
            return false;
        }

        if (!is_numeric($data)) {
            $this->addErrorMessage($field, $rule, '{name}必须为数字');
            return false;
        }

        if (isset($rule['min'])) {
            if ($data < $rule['min']) {
                $this->addErrorMessage($field, $rule, '{name}必须大于等于{min}');
                return false;
            }
        }

        if (isset($rule['max'])) {
            if ($data > $rule['max']) {
                $this->addErrorMessage($field, $rule, '{name}必须小于等于{max}');
                return false;
            }
        }

        return true;
    }

    /**
     * 验证邮箱地址是否正确
     * 本函数规则中支持1个额外的参数
     *  - message 报错信息，可通过 {name} 来替换为字段名
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiEmail(&$field, array &$rule)
    {
        if (($data = $this->getParams($field)) && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $data)) {
            $this->addErrorMessage($field, $rule, '{value}不是正确的邮箱格式');
            return false;
        }
        return true;
    }

    /**
     * 正式表达式验证
     * 本函数规则中支持2个额外的参数
     *  - reg     验证用的正则表达式
     *  - message 报错信息，可通过 {name} 来替换为字段名
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiReg(&$field, array &$rule)
    {
        if (($data = $this->getParams($field)) && !preg_match($rule['reg'], $data)) {
            $this->addErrorMessage($field, $rule, '{name}没有通过验证');
            return false;
        }
        return true;
    }

    /**
     * 网址验证
     * 本函数规则中支持1个额外的参数
     *  - message 报错信息，可通过 {name} 来替换为字段名
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiUrl(&$field, array &$rule)
    {
        if (($data = $this->getParams($field)) && !preg_match('/^(http[s]?:\/\/)?[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/i', $data)) {
            $this->addErrorMessage($field, $rule, '{value}不是正确的网址');
            return false;
        }
        return true;
    }

    /**
     * 比较验证
     * 比较2个字段的值是否相同
     * 本函数规则中支持2个额外的参数
     *  - field   要比较的字段名
     *  - message 报错信息，可通过 {name} 来替换为字段名
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiCompare(&$field, array &$rule)
    {
        $data  = $this->getParams($field);
        $data2 = $this->getParams($rule['field']);

        if ($data != $data2) {
            $this->addErrorMessage($field, $rule, '{name}与' . $this->getFieldName($rule['field']) . '不相同');
            return false;
        }
        return true;
    }

    /**
     * 字段长度验证
     * 本函数规则中支持4个额外的参数
     *  - length  要比较的字段名
     *  - max     最大数量
     *  - min     最小数量
     *  - message 报错信息，可通过 {name} 来替换为字段名
     *
     * @param string $field 字段名
     * @param array  $rule  验证规则
     * @return boolean
     */
    private function _valiLength(&$field, array &$rule)
    {
        $data = $this->getParams($field);

        if (empty($data)) {
            return false;
        }

        $num = strlen($data);
        if (isset($rule['length']) && $num != $rule['length']) {
            $this->addErrorMessage($field, $rule, '{name}长度不等于{length}');
            return false;
        } else {
            if (isset($rule['min']) && $num < $rule['min']) {
                $this->addErrorMessage($field, $rule, '{name}长度不能小于{min}');
                return false;
            }

            if (isset($rule['max']) && $num > $rule['max']) {
                $this->addErrorMessage($field, $rule, '{name}长度不能大于{max}');
                return false;
            }
        }

        return true;
    }

    /**
     * 验证是否为手机号
     *
     * @param       $field
     * @param array $rule
     * @return boolean
     */
    private function _valiMobile(&$field, array &$rule)
    {
        $reg = '/^(1\d{10})$/';
        $msg = '手机号 {name} 格式不正确';
        if (($data = $this->getParams($field)) && !preg_match($reg, $data)) {
            $this->addErrorMessage($field, $rule, $msg);
            return false;
        }
        return true;
    }

    /**
     * 验证是否是中文
     *
     * @param       $field
     * @param array $rule
     * @return boolean
     */
    private function _valiChinese(&$field, array &$rule)
    {
        $reg = '/^[\x{4e00}-\x{9fa5}]+$/u';
        $msg = '{name} 不是中文';
        if (($data = $this->getParams($field)) && !preg_match($reg, $data)) {
            $this->addErrorMessage($field, $rule, $msg);
            return false;
        }
        return true;
    }

    /**
     * 验证是否是身份证
     *
     * @param       $field
     * @param array $rule
     * @return boolean
     */
    private function _valiIdcard(&$field, array &$rule)
    {
        $reg = '/^(\d{15}|\d{17}[\dXx])$/';
        $msg = '{name} 不正确';
        if (($data = $this->getParams($field)) && !preg_match($reg, $data)) {
            $this->addErrorMessage($field, $rule, $msg);
            return false;
        }
        return true;
    }

    /**
     * 验证枚举
     *
     * @param       $field
     * @param array $rule
     * @return bool
     */
    private function _valiEnum(&$field, array &$rule)
    {
        $data = $this->getParams($field);
        if (isset($data)) {
            if (!isset($rule['enum']) || !is_array($rule['enum']) || !in_array($data, $rule['enum'])) {
                $this->addErrorMessage($field, $rule, '{name}不在枚举中');
                return false;
            }
        }
        return true;
    }


    /**
     * 验证IP是否合法
     *  本函数规则中支持1个额外的参数
     *  - message 报错信息，可通过 {value} 来替换为字段值
     *
     * @param       $field
     * @param array $rule
     * @return boolean
     */
    private function _valiIp(&$field, array &$rule)
    {
        $reg = '/^((25[0-5]|2[0-4]\\d|[1]{1}\\d{1}\\d{1}|[1-9]{1}\\d{1}|\\d{1})($|(?!\\.$)\\.)){4}$/';

        if (($data = $this->getParams($field)) && !preg_match($reg, $data)) {
            $this->addErrorMessage($field, $rule, '{name}的值{value}不是正确的IP格式');
            return false;
        }
        return true;
    }


    /**
     * 验证字段是否整型
     * 本验证函数规则中支持3个额外参数
     *  - min     最小整型数字
     *  - max     最大整型数字
     *  - message 报错信息，可通过 {name} 来替换为字段名
     *
     * @param       $field
     * @param array $rule
     * @return boolean
     */
    private function _valiInt(&$field, array &$rule)
    {
        $data = $this->getParams($field);
        if ($data != 0 && empty($data)) {
            return false;
        }

        if (!is_int($data)) {
            $this->addErrorMessage($field, $rule, '{name}的值{value}不是整型');
            return false;
        }

        $num = strlen($data);
        if (isset($rule['length']) && $num != $rule['length']) {
            $this->addErrorMessage($field, $rule, '{name}长度不等于{length}');
            return false;
        } else {
            if (isset($rule['min'])) {
                if ($data < $rule['min']) {
                    $this->addErrorMessage($field, $rule, '{name}必须大于等于{min}');
                    return false;
                }
            }

            if (isset($rule['max'])) {
                if ($data > $rule['max']) {
                    $this->addErrorMessage($field, $rule, '{name}必须小于等于{max}');
                    return false;
                }
            }
        }
        return true;
    }

}
