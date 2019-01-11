<?php
/**
 * 验证表单生成类
 *
 * @author       wiki <charmfocus@gmail.com>
 * @copyright(c) 14/11/12
 */
class Q_ValidateForm
{
    const TOEKN_KEY = 'token';
    /**
     * @var string
     */
    private $_inputName = 'data';

    /**
     * 验证规则类
     *
     * @var Q_Validate
     */
    private $_obj;

    private $_jsValidate = false;

    /**
     *  表单令牌
     */

    private $_tokenOn    = false;
    private $_tokenName  = '__hash__';
    private $_tokenType  = 'md5';
    private $_tokenReset = true;
    private $_tokenValue = '';

    /**
     * @param $obj
     * @return Q_ValidateForm
     */
    public function setValidate(&$obj)
    {
        $this->_obj = $obj;
        return $this;
    }

    /**
     * 设置JS验证开关
     *
     * @param bool|true $validate 验证开关
     * @return $this
     */
    public function setJsValidate($validate = true)
    {
        $this->_jsValidate = $validate;

        return $this;
    }

    /**
     * 获取JS验证开关状态
     *
     * @return bool
     */
    public function getJsValidate()
    {
        return $this->_jsValidate;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setInputName($name = 'data')
    {
        $name             = empty($name) ? 'data' : $name;
        $this->_inputName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        return $this->_inputName;
    }

    public function __construct($inputName = 'data')
    {
        $this->setInputName($inputName);

        $_config = Q_Config::get(APP_NAME . '_Global');
        !empty($_config['TOKEN_ON']) && $this->_tokenOn = $_config['TOKEN_ON'];
        !empty($_config['TOKEN_NAME']) && $this->_tokenName = $_config['TOKEN_NAME'];
        !empty($_config['TOKEN_TYPE']) && $this->_tokenType = $_config['TOKEN_TYPE'];
        !empty($_config['TOKEN_RESET']) && $this->_tokenReset = $_config['TOKEN_RESET'];
    }

    public function __destruct()
    {
        //重置
        if ($this->_tokenOn && $this->_tokenReset) {
            unset($_COOKIE[$this->_tokenName]);
        }
    }

    /**
     * 获取字段名
     *
     * @param string $field  字段名
     * @param bool   $hasTag 是否有标签
     * @return string
     */
    public function name($field, $hasTag = true)
    {
        $name = $this->_obj->getFieldName($field);

        if (!$hasTag) {
            return $name;
        }

        $required = false;
        if ($rules = $this->_obj->getFieldRules($field)) {
            if (isset($rules['required'])) {
                $required = true;
            }
        }

        if ($required) {
            $name = '<span class="required">*</span>' . $name;
        }
        return $this->_obj->hasError($field) ? '<span class="error">' . $name . '</span>' : $name;
    }

    /**
     * 获取对应字段的值
     *
     * @param string $field 字段名
     * @return mixed
     */
    public function value($field)
    {
        return $this->_obj->getParams($field);
    }

    /**
     * @param $field
     * @param $option
     */
    private function _jsValidateOption($field, &$option)
    {
        $_jsRules = $this->_obj->getRules($field);

        if (empty($_jsRules['rules'])) {
            return;
        }

        $option['data-field'] = $field;
        $option['data-name']  = $this->_obj->getFieldName($field);
        $option['data-valid'] = json_encode($_jsRules['rules']);
    }

    /**
     * 单行输入框
     *
     * @param string    $field      字段名
     * @param array     $options    属性配置
     * @param string    $type       输入框的类型，默认为text,不填写时也为text
     * @param bool|null $jsValidate 是否开启JS验证
     * @return string
     */
    public function input($field, $options = array(), $type = 'text', $jsValidate = null)
    {
        if ($this->_obj->hasError($field)) {
            if (isset($options['class'])) {
                $options['class'] .= ' error';
            } else {
                $options['class'] = 'error';
            }
        }

        if (!isset($options['placeholder'])) {
            $_fieldName = $this->_obj->getFieldName($field);
            if (!is_numeric($_fieldName)) {
                $options['placeholder'] = '请输入' . $_fieldName;
            }
        }

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $this->_jsValidateOption($field, $options);
        }
        return Q_Form::input($this->_inputName . '[' . $field . ']', $this->_obj->getParams($field), $options, $type);
    }

    /**
     * 隐藏表单
     *
     * @param string $field      字段名
     * @param array  $options    配置
     * @param null   $jsValidate 是否打开JS验证
     * @return string
     */
    public function hidden($field, $options = array(), $jsValidate = null)
    {
        return $this->input($field, $options, 'hidden', $jsValidate);
    }

    /**
     * 字数表单
     *
     * @param string $field      字段名
     * @param array  $options    配置
     * @param null   $jsValidate 是否打开JS验证
     * @return string
     */
    public function number($field, $options = array(), $jsValidate = null)
    {
        return $this->input($field, $options, 'number', $jsValidate);
    }

    /**
     * 密码输入框
     *
     * @param string    $field      字段名
     * @param array     $options    属性配置
     * @param bool|null $jsValidate 是否开启JS验证
     * @return string
     */
    public function password($field, $options = array(), $jsValidate = null)
    {
        if ($this->_obj->hasError($field)) {
            if (isset($options['class'])) {
                $options['class'] .= ' error';
            } else {
                $options['class'] = 'error';
            }
        }
        if (!isset($options['placeholder'])) {
            $_fieldName = $this->_obj->getFieldName($field);
            if (!is_numeric($_fieldName)) {
                $options['placeholder'] = '请输入' . $_fieldName;
            }
        }

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $this->_jsValidateOption($field, $options);
        }

        return Q_Form::password($this->_inputName . '[' . $field . ']', $this->_obj->getParams($field), $options);
    }

    /**
     * 复选框
     *
     * @param string       $field        字段名
     * @param string|array $data         数据[value => text]|string
     * @param array        $labelOptions label的属性 如果为false，不生成label
     * @param array        $options      属性配置
     * @param bool|null    $jsValidate   是否开启JS验证
     * @return string
     */
    public function checkbox($field, $data = '', $labelOptions = array(), $options = array(), $jsValidate = null)
    {
        $_val = is_array($data) ? key($data) : $data;
        if ($this->_obj->getParams($field) == $_val) {
            $options['checked'] = 'checked';
        }

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            if ($labelOptions === false) {
                $this->_jsValidateOption($field, $options);
            } else {
                $this->_jsValidateOption($field, $labelOptions);
            }
        }

        return Q_Form::checkbox($this->_inputName . '[' . $field . ']', $data, $labelOptions, $options);
    }

    /**
     * 批量生成复选框
     *
     * @param string    $field        字段名 最终生成的字段名如:data[field][k]
     * @param array     $data         字段可用选项
     * @param array     $labelOptions label的属性配置
     * @param array     $options      checkbox的属性配置
     * @param bool|null $jsValidate   是否开启JS验证
     * @return string
     */
    public function checkboxList($field, $data, $labelOptions = array(), $options = array(), $jsValidate = null)
    {
        $html = Q_Form::checkboxList($this->_inputName . '[' . $field . ']', $data, $this->_obj->getParams($field), $labelOptions, $options);

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $validateOption = array(
                'class'     => 'valid-group',
                'date-type' => 'checkboxList'
            );
            $this->_jsValidateOption($field, $validateOption);
            $html = Q_Form::tag('span', $validateOption, $html);
        }
        return $html;
    }

    /**
     * 单选框
     *
     * @param string    $field      字段名
     * @param string    $value      值
     * @param array     $options    属性配置
     * @param bool|null $jsValidate 是否开启JS验证
     * @return string
     */
    public function radio($field, $value = '', $options = array(), $jsValidate = null)
    {
        if ($this->_obj->getParams($field) == $value) {
            $options['checked'] = 'checked';
        }

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $this->_jsValidateOption($field, $options);
        }

        return Q_Form::radio($this->_inputName . '[' . $field . ']', $this->_obj->getParams($field), $options);
    }

    /**
     * 批量生成复选框
     *
     * @param string    $field        字段名
     * @param array     $data         字段可用选项
     * @param array     $labelOptions label的属性配置
     * @param array     $options      checkbox的属性配置
     * @param bool|null $jsValidate   是否开启JS验证
     * @return string
     */
    public function radioList($field, $data, $labelOptions = array(), $options = array(), $jsValidate = null)
    {
        $html = Q_Form::radioList($this->_inputName . '[' . $field . ']', $data, $this->_obj->getParams($field), $labelOptions, $options);

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $validateOption = array(
                'class'     => 'valid-group',
                'date-type' => 'radioList',
            );
            $this->_jsValidateOption($field, $validateOption);
            $html = Q_Form::tag('span', $validateOption, $html);
        }
        return $html;
    }

    /**
     * 生成下拉框
     *
     * @param string    $field      字段名
     * @param array     $data       可用选项
     * @param array     $options    select属性配置
     * @param bool|null $jsValidate 是否开启JS验证
     * @return string
     */
    public function select($field, $data, $options = array(), $jsValidate = null)
    {
        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $this->_jsValidateOption($field, $options);
        }

        $name = $this->_inputName . '[' . $field . ']';
        if (!empty($options['multiple'])) {
            $name .= '[]';
        }

        return Q_Form::select($name, $data, $this->_obj->getParams($field), $options);
    }

    /**
     * 多行文本输入框
     *
     * @param string    $field      字段名
     * @param array     $options    属性配置
     * @param bool|null $jsValidate 是否开启JS验证
     * @return string
     */
    public function textArea($field, $options = array(), $jsValidate = null)
    {
        if ($this->_obj->hasError($field)) {
            if (isset($options['class'])) {
                $options['class'] .= ' error';
            } else {
                $options['class'] = 'error';
            }
        }

        if (!isset($options['placeholder'])) {
            $_fieldName = $this->_obj->getFieldName($field);
            if (!is_numeric($_fieldName)) {
                $options['placeholder'] = '请输入' . $_fieldName;
            }
        }

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $this->_jsValidateOption($field, $options);
        }

        return Q_Form::textArea($this->_inputName . '[' . $field . ']', $this->_obj->getParams($field), $options);
    }

    /**
     * 多行文本输入框
     *
     * @param string    $field      字段名
     * @param array     $options    属性配置
     * @param bool|null $jsValidate 是否开启JS验证
     * @return string
     */
    public function file($field, $options = array(), $jsValidate = null)
    {
        if ($this->_obj->hasError($field)) {
            if (isset($options['class'])) {
                $options['class'] .= ' error';
            } else {
                $options['class'] = 'error';
            }
        }

        $jsValidate = isset($jsValidate) ? $jsValidate : $this->getJsValidate();
        if ($jsValidate) {
            $this->_jsValidateOption($field, $options);
        }

        return Q_Form::file($this->_inputName . '[' . $field . ']', $options);
    }

    /**
     * 表单开始函数
     * 表单的开始结构体，需要结束表单时需要调用CHtml::endForm();
     *
     * @param string $action  提交的地址
     * @param string $method  提交的方式
     * @param array  $options 扩展属性
     * @return string
     */
    public function beginForm($action = '', $method = 'post', $options = array())
    {
        $form = Q_Form::beginForm($action, $method, $options);

        if ($this->_tokenOn && $this->_tokenOn == true) {
            $tokenName  = $this->_tokenName;
            $tokenValue = $this->_createFormToken();
            $form       .= Q_Form::hidden($tokenName, $tokenValue);
        }

        return $form;
    }

    private function _createFormToken()
    {
        // 开启表单验证自动生成表单令牌
        if (!$this->_tokenOn) {
            return;
        }

        $tokenName = $this->_tokenName;
        $tokenType = $this->_tokenType;

        $tokenValue        = $tokenType(microtime(TRUE));
        $this->_tokenValue = $tokenValue;

        setcookie($tokenName, $tokenValue);
        return $tokenValue;
    }

    /**
     * 表单结束
     * 输出表单的结束符号
     *
     * @return string
     */
    public function endForm()
    {
        return Q_Form::endForm();
    }

    /**
     * 获取指定字段的单条错误
     *
     * @param string $field  字段名
     * @param bool   $always 是否直接输出容器，不管是否有错误
     * @return string
     */
    public function error($field, $always = false)
    {
        if ($this->_obj->hasError($field)) {
            if ($errorMessage = $this->_obj->getErrorMessage($field)) {
                return '<span class="valid-msg valid-msg-' . $field . ' error valid-error">' . reset($errorMessage) . '</span>';
            }
        } else if ($always) {
            return '<span class="valid-msg valid-msg-' . $field . ' " style="display:none"></span>';
        }
        return '';
    }

    /**
     * 获取所有字段的错误
     *
     * @return string
     */
    public function errorSum()
    {
        $error = $this->_obj->getErrorMessage();
        if ($error) {
            $errorMessage = '<ul class="errorSum">';
            foreach ($error as $k => $v) {
                if (is_array($v) && !empty($v)) {
                    foreach ($v as $b) {
                        $errorMessage .= '<li>' . $b . '</li>';
                    }
                }
            }
            return $errorMessage . '</ul>';
        }
        return '';
    }
}
