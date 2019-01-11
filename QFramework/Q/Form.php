<?php

/**
 * @author       wiki <charmfocus@gmail.com>
 * @copyright(c) 14/11/12
 */
class Q_Form
{

    /**
     * 输入框
     * 生成input输入框的html代码
     *
     * @param string $name    输入框名
     * @param string $value   输入框值
     * @param array  $options 输入框可用值
     * @param string $type    输入框类型
     * @return string
     */
    public static function input($name, $value, $options = array(), $type = 'text')
    {
        if (empty($options['type'])) {
            $options['type'] = empty($type) ? 'text' : $type;
        }

        $options['name']  = $name;
        $options['value'] = $value;
        return self::tag('input', $options);
    }

    /**
     * 密码输入框
     * 生成input密码输入框的html代码
     *
     * @param string $name    输入框名
     * @param string $value   输入框值
     * @param array  $options 输入框可用值
     * @return string
     */
    public static function password($name, $value, $options = array())
    {
        $options['type']  = 'password';
        $options['name']  = $name;
        $options['value'] = $value;
        return self::tag('input', $options);
    }

    /**
     * 生成隐藏input
     * 生成隐藏input的html代码
     *
     * @param string $name    输入框名
     * @param string $value   输入框值
     * @param array  $options 输入框可用值
     * @return string
     */
    public static function hidden($name, $value, $options = array())
    {
        $options['type']  = 'hidden';
        $options['name']  = $name;
        $options['value'] = $value;
        return self::tag('input', $options);
    }

    /**
     * 生成文件选择框
     *
     * @param string $name    文件选择框名
     * @param array  $options 扩展配置
     * @return string
     */
    public static function file($name, $options = array())
    {
        $options['type'] = 'file';
        $options['name'] = $name;
        return self::tag('input', $options);
    }

    /**
     * 生成普通按钮
     *
     * @param string $name    按钮名
     * @param string $value   按钮值
     * @param string $text    按钮文字
     * @param array  $options 扩展配置
     * @return string
     */
    public static function button($name = '', $value = '', $text = '', $options = array())
    {
        $options['type']  = empty($options['type']) ? 'button' : $options['type'];
        $options['name']  = $name;
        $options['value'] = (!empty($value) ? $value : 'Button');
        $text             = (!empty($text) ? $text : $options['value']);

        unset($options['text']);

        return self::tag('button', $options, $text, true);
    }

    /**
     * 生成提交按钮
     *
     * @param string $name    按钮名
     * @param string $value   按钮值
     * @param array  $options 扩展配置
     * @return string
     */
    public static function submit($name = '', $value = '', $options = array())
    {
        $options['type']  = 'submit';
        $options['name']  = $name;
        $options['value'] = (!empty($value) ? $value : 'Submit');
        return self::tag('input', $options);
    }

    /**
     * 生成checkbox代码
     *
     * @param string       $name        复选框名
     * @param string|array $data        复选框值 [value => text]|string
     * @param array        $labelOption label配置
     * @param array        $options     扩展配置
     * @return string
     */
    public static function checkbox($name = '', $data = '', $labelOption = array(), $options = array())
    {
        if (is_array($data)) {
            $_val  = key($data);
            $_text = reset($data);
        } else {
            $_val  = $data;
            $_text = $data;
        }
        $options['type']  = 'checkbox';
        $options['name']  = $name;
        $options['value'] = $_val;
        $options['id']    = empty($options['id']) ? ($name . '_checkbox_') : $options['id'];
        $checkbox         = self::tag('input', $options);

        if ($labelOption !== false) {
            $labelOption['for'] = $options['id'];
            $tag                = self::tag('label', $labelOption, $checkbox . $_text);
        } else {
            $tag = $checkbox . $_text;
        }


        return $tag;
    }

    /**
     * 显示复选框列表
     * 可通过$data提供一个一维数组，程序会按一维数组生成一个checkbox列表
     * 一维数组的key将做为选项的值，值将做为显示名，显示给用户看。
     *
     * @param string       $name           复选框名
     * @param array        $data           选项
     * @param array|string $selected       默认选中
     * @param array        $labelOption    label的扩展配置
     * @param array        $checkboxOption checkbox的扩展配置
     * @return string
     */
    public static function checkboxList($name, $data, $selected = null, $labelOption = array(), $checkboxOption = array())
    {
        $html = '';

        if (!empty($data) && is_array($data)) {

            if (!is_null($selected)) {
                if (is_array($selected)) {
                    $selected = array_flip($selected);
                } elseif (is_numeric($selected) || is_string($selected)) {
                    $selected = array($selected => '');
                }
            }

            $checkboxOption['type'] = 'checkbox';

            foreach ($data as $k => $v) {
                if (isset($selected[$k])) {
                    $checkboxOption['checked'] = 'checked';
                } elseif (isset($checkboxOption['checked'])) {
                    unset($checkboxOption['checked']);
                }
                $checkboxOption['name']  = "{$name}[{$k}]";
                $checkboxOption['value'] = $k;
                $checkboxOption['id']    = $name . '_checkbox_' . $k;
                $checkbox                = self::tag('input', $checkboxOption);
                $labelOption['for']      = $checkboxOption['id'];
                $html                    .= self::tag('label', $labelOption, $checkbox . $v);
            }
        }

        return $html;
    }

    /**
     * 生成radio代码
     *
     * @param string $name    单选框名
     * @param string $value   单选框值
     * @param array  $options 扩展配置
     * @return string
     */
    public static function radio($name = '', $value = '', $options = array())
    {
        $options['type']  = 'radio';
        $options['name']  = $name;
        $options['value'] = $value;
        return self::tag('input', $options);
    }

    /**
     * 显示单选框列表
     * 可通过$data提供一个一维数组，程序会按一维数组生成一个checkbox列表
     * 一维数组的key将做为选项的值，值将做为显示名，显示给用户看。
     *
     * @param string $name        单选框名
     * @param array  $data        选项
     * @param string $selected    默认选中
     * @param array  $labelOption label的扩展配置
     * @param array  $radioOption radio的扩展配置
     * @return string
     */
    public static function radioList($name, $data, $selected = null, $labelOption = array(), $radioOption = array())
    {
        $html = '';

        if (!empty($data) && is_array($data)) {

            if (!is_null($selected)) {
                if (is_array($selected)) {
                    $selected = array_flip($selected);
                } elseif (is_numeric($selected) || is_string($selected)) {
                    $selected = array($selected => '');
                }
            }

            $radioOption['type'] = 'radio';
            $radioOption['name'] = $name;

            foreach ($data as $k => $v) {
                if (isset($selected[$k])) {
                    $radioOption['checked'] = 'checked';
                } elseif (isset($radioOption['checked'])) {
                    unset($radioOption['checked']);
                }
                $radioOption['value'] = $k;
                $radioOption['id']    = $name . '_radio_' . $k;
                $checkbox             = self::tag('input', $radioOption);
                $labelOption['for']   = $radioOption['id'];
                $html                 .= self::tag('label', $labelOption, $checkbox . $v);
            }
        }
        return $html;
    }

    /**
     * 生成select选择框
     * $data选择框选项为一维数组，数组的key做为选项的值，value做为选项说明显示给用户看。
     * 也可以为二维数组{key => {id => 'xx', name => 'xxx'}}，如果要自定义k和v，需要在options中配置valueField和textField
     *
     * @param string       $name     选择框名
     * @param array|null   $data     选项
     * @param array|string $selected 默认选中
     * @param array        $options  扩展配置
     * @return string
     */
    public static function select($name = '', array $data = null, $selected = array(), $options = array())
    {
        $html = '';
        $data = (array)$data;
        if (isset($selected)) {
            if (!is_array($selected)) {
                $selected = array($selected => '');
            } else {
                $selected = array_flip($selected);
            }
        }

        $options['name'] = $name;

        $valueField = empty($options['valueField']) ? 'id' : $options['valueField'];
        $textField  = empty($options['textField']) ? 'name' : $options['textField'];

        unset($options['valueField'], $options['textField']);

        //设置默认文字
        if (!empty($options['default'])) {
            $html .= self::tag('option', array('value' => ''), $options['default']);
            unset($options['default']);
        }

        if (!empty($data)) {
            $arr = array();
            foreach ($data as $k => $v) {
                $arr['value'] = $k;
                if (isset($selected[$k])) {
                    $arr['selected'] = 'selected';
                } elseif (isset($arr['selected'])) {
                    unset($arr['selected']);
                }
                if (is_array($v)) {
                    $v = isset($v[$textField]) ? $v[$textField] : '';
                }
                $html .= self::tag('option', $arr, $v);
            }
        }
        
        if ($html) {
            $html = self::tag('select', $options, $html, true);
        }

        return $html;
    }

    /**
     * 生成textarea多行输入框
     * 多行输入框的内容，是被html实例化过的，这是为了防止代码中出现一些HTML代码以影响到HTML的正常结构
     *
     * @param string $name    多行输入框名
     * @param string $content 内容
     * @param array  $options 扩展配置
     * @return string
     */
    public static function textArea($name = '', $content = '', $options = array())
    {
        $options['name'] = $name;
        return self::tag('textarea', $options, self::encode($content), true);
    }

    /**
     * 对显示出来的结果进行实例化
     *
     * @param string $content
     * @return string
     */
    public static function encode($content)
    {
        return htmlspecialchars($content, ENT_QUOTES);
    }

    /**
     * 生成标签html代码
     * 将传送过来的标签信息，生成标签对应的html代码
     *
     * @param string       $name     标签名
     * @param string|array $options  标签属性
     * @param string       $content  要显示的内容值
     * @param boolean      $closeTag 是否显示关闭标签
     * @return string
     */
    public static function tag($name, $options = array(), $content = '', $closeTag = false)
    {
        $html = '<' . $name . self::createOption($options);

        if (!empty($content)) {
            $html .= '>' . $content . '</' . $name . '>';
        } elseif (!empty($closeTag)) {
            $html .= '></' . $name . '>';
        } else {
            $html .= ' />';
        }

        return $html;
    }

    /**
     * 生成标签属性字符串
     * $option必须为一维数组，key为属性名，value为属性值
     *
     * @param array $options 属性数组
     * @return string
     */
    public static function createOption($options)
    {
        $html = '';

        if (!empty($options) && is_array($options)) {
            foreach ($options as $k => $v) {
                if (is_array($v)) {
                    $v = json_encode($v);
                }

                $html .= ' ' . $k . '="' . self::encode($v) . '"';
            }
        } elseif (!empty($options) && is_string($options)) {
            $html .= ' ' . $options;
        }
        return $html;
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
    public static function beginForm($action = '', $method = 'post', $options = array())
    {
        $options['method'] = (empty($method) ? 'post' : $method);
        $options['action'] = $action;
        return '<form ' . self::createOption($options) . '>';
    }

    /**
     * 表单结束
     * 输出表单的结束符号
     *
     * @return string
     */
    public static function endForm()
    {
        return '</form>';
    }
} 