<?php
/**
 *
 * @author      : bobo<zengrongkun@dalingpao.com>
 * @copyright(c): 17-11-2
 * @version     : $id$
 */

class Admin_Plugin_Search extends Admin_Plugin_Abstract
{
    const SEARCH_TYPE_ADVANCED = 'ADVANCED';
    const SEARCH_METHOD_GET    = Admin_Plugin_Form::FORM_METHOD_GET;
    const SEARCH_METHOD_POST   = Admin_Plugin_Form::FORM_METHOD_POST;

    /**
     * @var Admin_Plugin_Form
     */
    private $_formPlugin = null;

    protected $_name       = '';
    protected $_method     = self::SEARCH_METHOD_GET;
    protected $_action     = '';
    protected $_type       = self::SEARCH_TYPE_ADVANCED;
    protected $_items      = [];
    protected $_typeParams = [];

    private static $_instance = [];

    private static $_typeMapping = [
        self::SEARCH_TYPE_ADVANCED => [
            'main' => 'Plugin/Search/Advanced/Main',
            'item' => 'Plugin/Search/Advanced/Item'
        ]
    ];

    public function __construct($name = 'default')
    {
        $formPlugin        = Admin_Plugin_Form::instance();
        $this->_formPlugin = $formPlugin;
    }

    /**
     * @param string $name
     * @return Admin_Plugin_Search
     */
    public static function instance($name = 'default')
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
     * @return $this
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


    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * @param string $type
     * @return Admin_Plugin_Search
     */
    public function setType(string $type): Admin_Plugin_Search
    {
        $this->_type = $type;

        return $this;
    }

    /**
     * @return null
     */
    public function getFormPlugin()
    {
        return $this->_formPlugin;
    }

    /**
     * @param $formPlugin
     * @return Admin_Plugin_Search
     */
    public function setFormPlugin($formPlugin)
    {
        $this->_formPlugin = $formPlugin;
        return $this;
    }


    /**
     * @param string $name
     * @return Admin_Plugin_Search
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
     * @return Admin_Plugin_Search
     */
    public function setMethod(string $method = 'get')
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
     * @return Admin_Plugin_Search
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
     * @return Admin_Plugin_Search
     */
    public function setItems(array $items = [])
    {
        $this->_items = $items;
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
     * @param array $typeParams
     * @return Admin_Plugin_Search
     */
    public function setTypeParams(array $typeParams = [])
    {
        $this->_typeParams = $typeParams;
        return $this;
    }


    /**
     * @return array
     */
    public function getTypeParams()
    {
        return $this->_typeParams;
    }


    /**
     * 获取搜索表单
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return mixed|string
     */
    public function getHtml(Q_Request $input, Q_Response $output)
    {
        $items = $this->getItems();
        $type  = $this->getType();

        if (empty($items)) {
            return '';
        }

        $dealCallback = "_deal" . ucfirst(strtolower($type));

        if (is_callable(array($this, $dealCallback))) {
            return call_user_func(array($this, $dealCallback), $input, $output);
        }

        return '';
    }


    /**
     * 获取默认搜索表单
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     *
     */
    private function _dealAdvanced(Q_Request $input, Q_Response $output)
    {
        $data       = (array)$input->request('data');
        $formName   = $this->getName();
        $formMethod = $this->getMethod();
        $action     = $this->getAction();
        $items      = $this->getItems();
        $typeParams = $this->getTypeParams();

        $advancedItems = [];
        $simpleItems   = [];
        $hiddenItems   = [];
        foreach ($items as $field => $value) {
            $fieldType = empty($value['type']) ? 'input' : $value['type'];
            if ($fieldType == 'hidden') {
                $hiddenItems[$field] = empty($value['value']) ? '' : $value['value'];
                continue;
            }

            if (empty($typeParams[$field])) {
                $simpleItems[$field]          = $value;
                $simpleItems[$field]['field'] = $field;
            } else {
                $advancedItems[$field]          = $value;
                $advancedItems[$field]['field'] = $field;
            }
        }

        $output->data = $data;
        $output->date = empty($data['search_date']) ? [] : $data['search_date'];

        $simpleHtml = $this->_formPlugin
            ->setItems($simpleItems)
            ->setData($data)
            ->getFormBody($input, $output);

        $advancedHtml = $this->_formPlugin
            ->setItems($advancedItems)
            ->setData($data)
            ->getFormBody($input, $output);

        $hiddenHtml   = $this->_formPlugin->setItems($items)->setData($hiddenItems)->getFormHidden($input, $output);
        $output->form = $this->_formPlugin->getForm();

        return $output->fetchCol(self::$_typeMapping[$this->_type]['main'], [
            'simpleHtml'   => $simpleHtml,
            'advancedHtml' => $advancedHtml,
            'action'       => $action,
            'formName'     => $formName,
            'formMethod'   => $formMethod,
            'hiddenHtml'   => $hiddenHtml,
        ]);
    }
}