<?php

/**
 *
 * @author      : bobo<zengrongkun@dalingpao.com>
 * @copyright(c): 17-11-2
 * @version     : $id$
 */
class Admin_Plugin_Form extends Admin_Plugin_Abstract
{

    const FORM_TYPE_DEFAULT = 'DEFAULT';
    const FORM_TYPE_SIMPLE  = 'SIMPLE';

    const  FORM_METHOD_GET  = 'get';
    const  FORM_METHOD_POST = 'post';

    const  INPUT_TYPE_INPUT         = 'input';
    const  INPUT_TYPE_HIDDEN        = 'hidden';
    const  INPUT_TYPE_NUMBER        = 'number';
    const  INPUT_TYPE_PASSWORD      = 'password';
    const  INPUT_TYPE_TEXT_AREA     = 'textArea';
    const  INPUT_TYPE_FILE          = 'file';
    const  INPUT_TYPE_SELECT        = 'select';
    const  INPUT_TYPE_RADIO         = 'radio';
    const  INPUT_TYPE_CHECKBOX      = 'checkbox';
    const  INPUT_TYPE_CHECKBOX_LIST = 'checkboxList';
    const  INPUT_TYPE_RADIO_LIST    = 'radioList';

    private   $_type   = self::FORM_TYPE_DEFAULT;
    protected $_name   = '';
    protected $_method = self::FORM_METHOD_GET;
    protected $_action = '';
    private   $_items  = [];
    private   $_data   = [];

    private $_validate = null;
    private $_valiName = null;
    private $_form     = null;

    private static $_typeMapping = [
        self::FORM_TYPE_DEFAULT => [
            'main' => 'Plugin/Form/Default/Main',
            'item' => 'Plugin/Form/Default/Item',
        ],
    ];

    protected static $_instance = [];

    public function __construct($name = 'data')
    {
        $this->_valiName = $name;
        $this->_validate = Q_Validate::instance($name);
        $this->_form     = $this->_validate->getForm()->setJsValidate(true);
    }

    /**
     * @param string $name
     * @return Admin_Plugin_Form
     */
    public static function instance($name = 'data')
    {
        if (isset(self::$_instance[$name])) {
            $obj = self::$_instance[$name];
        } else {
            $obj                    = new self($name);
            self::$_instance[$name] = $obj;
        }

        return $obj;
    }


    /**
     * 批量初始化
     *
     * @param array $params
     * @return Admin_Plugin_Form
     */
    public function init(array $params = [])
    {
        foreach ($params as $_item => $_row) {
            $_funName = 'set' . ucfirst($_item);
            if (method_exists($this, $_funName)) {
                $this->$_funName($_row);
            }
        }

        return $this;
    }

    public function getHtml(Q_Request $input, Q_Response $output)
    {
        $formName   = $this->getName();
        $formMethod = $this->getMethod();
        $action     = $this->getAction();

        $formBody     = $this->getFormBody($input, $output);
        $formHidden   = $this->getFormHidden($input, $output);
        $output->form = $this->_form;

        return $output->fetchCol(self::$_typeMapping[$this->_type]['main'], [
            'formBody'   => $formBody,
            'formHidden' => $formHidden,
            'formName'   => $formName,
            'method'     => $formMethod,
            'action'     => $action,
        ]);
    }

    public function getFormBody(Q_Request $input, Q_Response $output)
    {
        $items    = $this->getItems();
        $type     = $this->getType();
        $formBody = '';

        if (empty($items)) {
            return '';
        }

        foreach ($items as $field => $item) {
            $fieldType = empty($item['type']) ? self::INPUT_TYPE_INPUT : $item['type'];
            if ($fieldType == self::INPUT_TYPE_HIDDEN) {
                continue;
            }

            $item['field'] = $field;
            $content       = $this->_getFormElement($item);
            $name          = $this->_form->name($field);
            $formBody      .= $output->fetchCol(self::$_typeMapping[$type]['item'], [
                'content' => $content,
                'name'    => $name
            ]);
        }

        return $formBody;
    }

    public function getFormHidden(Q_Request $input, Q_Response $output)
    {
        $data  = $this->getData();
        $items = $this->getItems();

        $formHidden = Q_Form::hidden('c', $input->ctlName);
        $formHidden .= Q_Form::hidden('a', $input->actName);

        foreach ($data as $_key => $_val) {
            $keyType = empty($items[$_key]['type']) ? self::INPUT_TYPE_INPUT : $items[$_key]['type'];

            if ($keyType === self::INPUT_TYPE_HIDDEN) {
                $formHidden .= $this->_form->hidden($_key);
                //$formHidden .= Q_Form::hidden($_key, $_val);
            }
        }

        return $formHidden;
    }

    private function _getFormElement($param)
    {
        $field      = empty($param['field']) ? '' : $param['field'];
        $_defOption = ['class' => 'layui-input', 'data-field' => $field];
        $type       = empty($param['type']) ? self::INPUT_TYPE_INPUT : $param['type'];
        $data       = empty($param['data']) ? [] : $param['data'];
        $option     = empty($param['option']) ? $_defOption : array_merge($_defOption, $param['option']);
        switch ($type) {
            case self::INPUT_TYPE_SELECT:
            case self::INPUT_TYPE_RADIO:
                $content = call_user_func(array($this->_form, $type), $field, $data, $option);
                break;
            case self::INPUT_TYPE_CHECKBOX:
            case self::INPUT_TYPE_CHECKBOX_LIST:
            case self::INPUT_TYPE_RADIO_LIST:
                $content = call_user_func(array($this->_form, $type), $field, $data, [], $option);
                break;
            default:
                $content = call_user_func(array($this->_form, $type), $field, $option);
        }

        return $content;
    }


    /**
     * @param string $name
     * @return Admin_Plugin_Form
     */
    public function setName(string $name = '')
    {
        $this->_name = $name;

        return $this;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * @param string $method
     * @return Admin_Plugin_Form
     */
    public function setMethod(string $method = 'post')
    {
        $this->_method = $method;

        return $this;
    }


    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }


    /**
     * @param string $action
     * @return Admin_Plugin_Form
     */
    public function setAction(string $action = '')
    {
        $this->_action = $action;

        return $this;
    }


    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }


    /**
     * @param array $items
     * @return Admin_Plugin_Form
     */
    public function setItems(array $items = [])
    {
        $this->_items = $items;
        if (!empty($items)) {
            $this->_validate->setRules($items);
        }

        return $this;
    }


    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * @param array $data
     * @return Admin_Plugin_Form
     */
    public function setData(array $data): Admin_Plugin_Form
    {
        $this->_data = $data;
        $this->_validate->setParams($data);

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->_data;
    }


    /**
     * @param null $form
     * @return Admin_Plugin_Form
     */
    public function setForm($form)
    {
        $this->_form = $form;

        return $this;
    }


    /**
     * @return null
     */
    public function getForm()
    {
        return $this->_form;
    }


    /**
     * @param string $type
     * @return Admin_Plugin_Form
     */
    public function setType(string $type = self::FORM_TYPE_DEFAULT)
    {
        $this->_type = $type;

        return $this;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

}