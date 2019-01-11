<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-10-31
 * @version     : $id$
 */
class Admin_Plugin_PageBar extends Admin_Plugin_Abstract
{
    /**
     * 单例
     */
    protected static $_instance = null;

    protected $_skin      = 'Default';
    protected $_tpl       = 'Plugin/PageBar/Default';
    protected $_total     = 0;
    protected $_totalPage = 1;
    protected $_pageBar   = '';


    public function __construct(int $total = 0, int $totalPage = 1, string $pageBar = '')
    {
        if (isset($total)) {
            $this->setTotal($total);
        }

        if (isset($totalPage)) {
            $this->setTotalPage($totalPage);
        }

        if (isset($pageBar)) {
            $this->setPageBar($pageBar);
        }
    }

    /**
     * @param int    $total
     * @param int    $totalPage
     * @param string $pageBar
     * @return Admin_Plugin_PageBar
     */
    public static function instance(int $total = 0, int $totalPage = 1, string $pageBar = '')
    {
        $obj = self::$_instance;
        if (!self::$_instance) {
            $obj             = new self($total, $totalPage, $pageBar);
            self::$_instance = $obj;
        }

        return $obj->setTotal($total)->setTotalPage($totalPage)->setPageBar($pageBar);
    }


    /**
     * @param int $total
     * @return $this
     */
    public function setTotal(int $total = 0)
    {
        $this->_total = $total;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->_total;
    }

    /**
     * @param int $_totalPage
     * @return $this
     */
    public function setTotalPage(int $_totalPage = 1)
    {
        $this->_totalPage = $_totalPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPage()
    {
        return $this->_totalPage;
    }


    /**
     * @param string $_pageBar
     * @return $this
     */
    public function setPageBar(string $_pageBar = '')
    {
        $this->_pageBar = $_pageBar;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageBar()
    {
        return $this->_pageBar;
    }


    /**
     * @param string $_tpl
     * @return $this
     */
    public function setTpl(string $_tpl = '')
    {
        $this->_tpl = $_tpl;
        return $this;
    }

    /**
     * @return string
     */
    public function getTpl()
    {
        return $this->_tpl;
    }


    /**
     * @param string $_skin
     * @return $this
     */
    public function setSkin(string $_skin = 'Default')
    {
        $this->_skin = $_skin;
        $this->setTpl("Plugin/PageBar/{$_skin}");
        return $this;
    }

    /**
     * @return string
     */
    public function getSkin()
    {
        return $this->_skin;
    }


    /**
     * 获取pageBar
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     */
    public function getHtml(Q_Request $input, Q_Response $output)
    {
        $total     = $this->getTotal();
        $totalPage = $this->getTotalPage();
        $pageBar   = $this->getPageBar();
        $tpl       = $this->getTpl();

        return $output->fetchCol($tpl, [
            'total'     => $total,
            'totalPage' => $totalPage,
            'pageBar'   => $pageBar,
        ]);
    }

}