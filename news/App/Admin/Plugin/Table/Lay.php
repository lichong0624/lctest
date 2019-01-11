<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 */
class Admin_Plugin_Table_Lay extends Admin_Plugin_Abstract
{
    const DATA_LIMIT = 10000;

    /**
     * 单例
     */
    protected static $_instance     = null;
    protected        $_id           = 'table';
    protected        $_filter       = 'table';
    protected        $_cols         = [];
    protected        $_data         = [];
    protected        $_containerId  = 'containerId';
    protected        $_cellMinWidth = 60;
    protected        $_width        = false;
    protected        $_height       = false;
    protected        $_skin         = false;
    protected        $_even         = false;
    protected        $_size         = false;
    protected        $_done         = null;
    protected        $_foot         = [];
    protected        $_page         = false;
    protected        $_limit        = self::DATA_LIMIT;//发现不设置的话超过**不再渲染
    protected        $_limits       = [];
    protected        $_loading      = false;
    protected        $_initSort     = false;

    protected $_tpl       = 'Plugin/Table/Table';
    protected $_scriptTpl = '';//列表插件模板

    /**
     * Admin_Plugin_Table_Lay
     *
     * @param null $config
     * @return Admin_Plugin_Table_Lay|null
     * @throws Q_Exception
     */
    public static function instance($config = null)
    {
        if (isset(self::$_instance)) {
            $obj = self::$_instance;
        } else {
            $obj             = new self();
            self::$_instance = $obj;
        }
        if (!empty($config) && is_array($config)) {
            $obj->init($config);
        } elseif (is_string($config)) {
            $obj->setId($config);
        }

        return $obj;
    }


    /**
     * 初始化配置参数
     *
     * @param $config
     * @return $this
     * @throws Q_Exception
     */
    public function init($config)
    {
        try {
            foreach ($config as $_key => $_val) {
                $_funName = 'set' . ucfirst($_key);
                if (method_exists($this, $_funName)) {
                    $this->$_funName($_val);
                }
            }
        } catch (Exception $ex) {
            throw new Q_Exception($ex->getMessage(), -1);
        }

        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id = 'table')
    {
        $input     = Q_Request::instance();
        $ctlName   = $input->ctlName;
        $actName   = $input->actName;
        $id        = md5($ctlName . $actName . $id);
        $this->_id = $id;
        $this->setFilter($id);
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }


    /**
     * @param string $filter
     * @return $this
     */
    public function setFilter(string $filter = 'table')
    {
        $this->_filter = $filter;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        if (empty($this->_filter)) {
            $this->setFilter();
        }
        return $this->_filter;
    }

    /**
     * @param array $cols
     * @return $this
     */
    public function setCols(array $cols = [])
    {
        $this->_cols = $cols;
        return $this;
    }

    /**
     * @return array
     */
    public function getCols()
    {
        return $this->_cols;
    }


    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data = [])
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param array $foot
     * @return $this
     */
    public function setFoot(array $foot)
    {
        $this->_foot = $foot;
        return $this;
    }

    /**
     * @return array
     */
    public function getFoot(): array
    {
        return $this->_foot;
    }


    /**
     * @param string $containerId
     * @return $this
     */
    public function setContainerId(string $containerId = 'containerId')
    {
        $this->_containerId = $containerId;
        return $this;
    }

    /**
     * @return string
     */
    public function getContainerId()
    {
        return $this->_containerId;
    }


    /**
     * @param bool|int $width
     * @return $this
     */
    public function setWidth($width = false)
    {
        $this->_width = $width;
        return $this;
    }

    /**
     * @return int|false
     */
    public function getWidth()
    {
        return $this->_width;
    }


    /**
     * @param bool|int $height
     * @return $this
     */
    public function setHeight($height = false)
    {
        $this->_height = $height;
        return $this;
    }

    /**
     * @return int|false
     */
    public function getHeight()
    {
        return $this->_height;
    }


    /**
     * @param int $cellMinWidth
     * @return $this
     */
    public function setCellMinWidth($cellMinWidth = 60)
    {
        $this->_cellMinWidth = $cellMinWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getCellMinWidth()
    {
        return $this->_cellMinWidth;
    }


    /**
     * @param bool|string $skin
     * @return $this
     */
    public function setSkin($skin = false)
    {
        $this->_skin = $skin;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getSkin()
    {
        return $this->_skin;
    }


    /**
     * @param string $tpl
     * @return $this
     */
    public function setTpl(string $tpl = '')
    {
        $this->_tpl = $tpl;
        return $this;
    }


    /**
     * @return string
     */
    public function getTpl(): string
    {
        return $this->_tpl;
    }

    /**
     * @param bool|string $even
     * @return $this
     */
    public function setEven($even = false)
    {
        $this->_even = $even;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getEven()
    {
        return $this->_even;
    }


    /**
     * @param bool|int $size
     * @return $this
     */
    public function setSize($size = false)
    {
        $this->_size = $size;
        return $this;
    }

    /**
     * @return int|false
     */
    public function getSize()
    {
        return $this->_size;
    }


    /**
     * @param $done
     * @return $this
     */
    public function setDone($done)
    {
        $this->_done = $done;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDone()
    {
        return $this->_done;
    }


    /**
     * @param string $scriptTpl
     * @return $this
     */
    public function setScriptTpl(string $scriptTpl)
    {
        $this->_scriptTpl = $scriptTpl;
        return $this;
    }

    /**
     * @return string
     */
    public function getScriptTpl(): string
    {
        return $this->_scriptTpl;
    }


    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->_limit;
    }

    /**
     * 获取表格
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public function getHtml(Q_Request $input, Q_Response $output)
    {
        $order   = $input->getArray('order');
        $request = $input->requestArray('', null, true);

        $_tpl = $this->getTpl();

        $data = [
            'id'           => $this->getId(),
            'filter'       => $this->getFilter(),
            'cols'         => Admin_Plugin_Table_LayCols::instance($this->getCols())->getCols(),
            'data'         => $this->getData(),
            'containerid'  => $this->getContainerId(),
            'width'        => $this->getWidth(),
            'height'       => $this->getHeight(),
            'cellMinWidth' => $this->getCellMinWidth(),
            'skin'         => $this->getSkin(),
            'even'         => $this->getEven(),
            'size'         => $this->getSize(),
            'done'         => $this->getDone(),
            'scriptTpl'    => $this->getScriptTpl(),
            'limit'        => $this->getLimit(),
            'order'        => $order,
            'request'      => $request,
            'foot'         => Admin_Plugin_Table_LayFoot::instance($this->getFoot())->getFoot()
        ];

        return $output->fetchCol($_tpl, $data);
    }
}