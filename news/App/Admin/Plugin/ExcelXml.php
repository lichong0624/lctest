<?php
/**
 *
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 15-8-17
 * @version     : $id$
 */
set_time_limit(0);
class Admin_Plugin_ExcelXml extends Admin_Plugin_Abstract
{
    protected static $_defStyle = [
        'alignment' => [
            'horizontal' => Lib_Excel_XMLWriter::STYLE_ALIGNMENT_HORIZONTAL_CENTER,#水平居中
            'vertical'   => Lib_Excel_XMLWriter::STYLE_ALIGNMENT_VERTICAL_CENTER,#垂直居中
        ],
    ];
    /**
     * @var $this
     */
    protected static $_instance = null;
    /**
     * @var Lib_Excel_XMLWriter
     */
    protected static $_excel;#定义单元格默认样式名
    protected        $_styleArr     = ['defcell' => true, 'notice' => true, 'num' => true];
    protected        $_styleId      = '';
    protected        $_style        = [];
    protected        $_defFieldConf = [];
    protected        $_validation   = [];
    protected        $_excelSet     = [];
    protected        $_excelData    = [];
    protected        $_path         = null;
    protected        $_hasStyle     = false;
    protected        $_hasValidate  = false;


    /**
     * Admin_Plugin_ExcelXml constructor.
     *
     * @param array|null $excelSet
     * @param array|null $excelData
     * @param null       $filesName
     * @throws Q_Exception
     */
    public function __construct(array $excelSet = null, array $excelData = null, $filesName = null)
    {
        if (isset($excelSet)) {
            $this->setExcelSet($excelSet, $filesName);
        }

        if (isset($excelData)) {
            $this->setExcelData($excelData, $filesName);
        }
    }


    /**
     * @param array|null $excelSet
     * @param array|null $excelData
     * @param null       $filesName
     * @param null       $path
     * @param bool       $isCompression
     * @throws Q_Exception
     */
    public static function instance(array $excelSet = null, array $excelData = null, $filesName = null, $path = null, $isCompression = true)
    {
        $obj   = self::$_instance;
        $excel = self::$_excel;
        if (!self::$_instance) {
            $obj             = new self();
            $excel           = new Lib_Excel_XMLWriter();
            self::$_instance = $obj;
            self::$_excel    = $excel;
        }
        return $obj->setFilePath($path)->setExcelSet($excelSet, $filesName)->setExcelData($excelData, $filesName, $isCompression);
    }

    /**
     * @param mixed $path
     * @return $this
     */
    public function setFilePath($path)
    {
        $this->_path = VAR_PATH . $path;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHasStyle()
    {
        return $this->_hasStyle;
    }

    /**
     * 设置是否需要配置，如果为false，不进行设置
     *
     * @param bool $hasStyleSel
     * @return $this
     */
    public function setHasStyle($hasStyleSel = false)
    {
        $this->_hasStyle = $hasStyleSel;
        return $this;
    }

    /**
     * @param bool $hasValidate
     * @return $this
     */
    public function setHasValidate($hasValidate = false)
    {
        $this->_hasValidate = $hasValidate;
        return $this;
    }

    /**
     * #字段相关设置
     * #默认字段配置
     *
     * @param string $styleId
     * @return $this
     */
    public function setStyleId($styleId = 'defcell')
    {
        $styleId  = $this->_styleId;
        $styleArr = $this->_styleArr;
        if ($styleArr[$styleId]) {
            $this->_styleId = $styleId;
        }
        return $this;
    }


    /**
     * @param array|null $style
     * @return $this
     */
    public function setStyle(array $style = null)
    {
        $style            = $this->_style;
        $defStyle         = self::$_defStyle;
        $style            += $defStyle;#定义单元格默认样式
        $style['borders'] = [
            'all' => [
                'color' => '#000000',
            ]
        ];
        if ($this->_styleId == 'notice') {
            $style['background'] = '#ffff00';
        }
        if ($this->_styleId == 'num') {
            $style['alignment']['horizontal'] = Lib_Excel_XMLWriter::STYLE_ALIGNMENT_HORIZONTAL_RIGHT;#右对齐
        }
        $this->_style = $style;
        return $this;
    }


    /**
     * @param array|null $defFieldConf
     * @return $this
     */
    public function setDefFieldConf(array $defFieldConf = null)
    {
        $style                 = $this->_styleId;
        $styleArr              = $this->_styleArr;
        $defFieldConf['style'] = $styleArr[$style];
        $defFieldConf          = $defFieldConf + $this->_defFieldConf;
        $this->_defFieldConf   = $defFieldConf;
        return $this;
    }

    /**
     * @param array|null $validation
     * @return $this
     */
    public function setValidation(array $validation = null)
    {
        $validation        = $validation + $this->_validation;
        $this->_validation = $validation;
        return $this;
    }

    /**
     * @return array
     */
    public function getExcelSet()
    {
        return $this->_excelSet;
    }

    /**
     * @param $excelSet
     * @return $this
     */
    public function setExcelSet($excelSet, $filesName)
    {
        $this->_excelSet = $excelSet;
        $excel           = self::$_excel;
        $path            = $this->_path;
        $hasStyle        = $this->_hasStyle;
        foreach ($excelSet as &$_val) {
            if (isset($_val['style']) && !$hasStyle) {
                unset($_val['style']);
            }
        }
        $excel->setPath($path . $filesName)
            ->setIsAppend(true)
            ->setHeader($excelSet, true);
        if ($hasStyle) {
            $defStyle     = self::$_defStyle;
            $style        = $this->_style;
            $styleId      = $this->_styleId;
            $defFieldConf = $this->_defFieldConf;
            $excel->setDefaultStyle($defStyle)
                ->setStyle($style, $styleId)
                ->setDefaultFieldConf($defFieldConf)
                ->setHeaderOrderType(Lib_Excel_XMLWriter::HEADER_ORDER_TYPE_CUSTOM)
                ->setHasAutoFilter(true);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getExcelData()
    {
        return $this->_excelData;
    }

    /**
     * @param $excelData
     * @param $filesName
     * @param $isCompression
     * @throws Q_Exception
     */
    public function setExcelData($excelData, $filesName, $isCompression = true)
    {
        $this->_excelData = $excelData;
        $validation       = $this->_validation;
        $excel            = self::$_excel;
        $page             = 1;

        while (true) {
            $param = [
                'page'     => $page,
                'pageSize' => 1000,
            ];
            !empty($excelData['param']) && $param = array_merge($param, $excelData['param']);
            #获取数据
            $data = Q_DAL_Client::instance()->call($excelData['DALName'], $excelData['a'], $param);
            if (isset($data['data'])) {
                $totalPage = $data['totalPage'];
                $excel->setTotalRow($data['total'])#设置总数量
                ->setData($data['data'])#设置当前数据结果集
                ->append();#以追加模式加入到文档中
                if ($totalPage <= $page) {
                    break;
                }
                ++$page;
            } else {
                throw new Q_Exception('没有要导出的数据', -1);
            }
        }

        if ($this->_hasValidate) {
            $excel->setDataValidation($validation);#设置数据验证
            #保存文档
        }
        $excel->save()->output($filesName, $isCompression);#直接输出到浏览器
        Q::end();
    }

}