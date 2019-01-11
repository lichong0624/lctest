<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-4-25
 * @version     : $id$
 */
class Admin_Plugin_ECharts_Line extends Admin_Plugin_ECharts_ECharts
{
    protected $_chartType = Admin_Plugin_ECharts_ECharts::CHART_TYPE_LINE;

    /**
     * 默认一套样式
     *
     * @var array
     */
    public $_seriesExt = [
        'type'      => 'line',
        'smooth'    => true,
        'markPoint' => [
            'data' => [
                ['type' => 'max', 'name' => '最大值'],
                ['type' => 'min', 'name' => '最小值']
            ]
        ],
        'label'     => [
            'normal' => ['show' => true, 'emphasis' => true],
        ],
        'markLine'  => [
            'data' => [
                ['type' => 'average', 'name' => '平均值']
            ]
        ]
    ];

    /**
     * 直角坐标系横轴
     *
     * @var array
     */
    protected $_xAxis = [
        'type'        => 'category',
        'boundaryGap' => false,
        //坐标横轴数据
        'data'        => []
    ];

    /**
     * 设置直角坐标系横轴
     *
     * @param $xAxis
     * @return $this
     */
    public function setXAxis(array $xAxis = [])
    {
        $this->_xAxis = $xAxis;
        return $this;
    }

    /**
     * 获取直角坐标系横轴
     *
     * @return array
     */
    public function getXAxis()
    {
        return $this->_xAxis;
    }

    /**
     * 直角坐标系横轴数据
     *
     * @var array
     */
    protected $_xAxisData = [];


    /**
     * 设置直角坐标系横轴
     *
     * @param $xAxisData
     * @return $this
     */
    public function setXAxisData(array $xAxisData = [])
    {
        $this->_xAxisData = $xAxisData;
        $this->setXAxis(array_merge($this->getXAxis(), ['data' => $xAxisData]));
        return $this;
    }


    /**
     * 直角坐标系纵轴
     *
     * @var array
     */
    protected $_yAxis = [
        'type'      => 'value',
        'axisLabel' => [
            'formatter' => '{value}'
        ],
    ];


    /**
     * 设置直角坐标系纵轴
     *
     * @param $yAxis
     * @return $this
     */
    public function setYAxis(array $yAxis = [])
    {
        $this->_yAxis = array_merge($this->_yAxis, $yAxis);
        return $this;
    }


    /**
     * 获取直角坐标系纵轴
     *
     * @return array
     */
    public function getYAxis()
    {
        return $this->_yAxis;
    }


    /**
     * 设置折线图是否是折线的面积图
     *
     * @param bool $areaStyle
     * @return $this
     */
    public function setAreaStyle($areaStyle = false)
    {
        if ($areaStyle) {
            self::$_seriesExt = array_merge(self::$_seriesExt, ['areaStyle' => ['normal' => '{}']]);
        }
        return $this;
    }


    /**
     * 设置线形图参数
     *
     * @param array $option
     * @return $this
     */
    public function setOption($option = [])
    {
        $_option = array_merge([
            'backgroundColor' => $this->getBackgroundColor(),
            'title'           => $this->getTitle(),
            'legend'          => $this->getLegend(),
            'grid'            => $this->getGrid(),
            'xAxis'           => $this->getXAxis(),
            'yAxis'           => $this->getYAxis(),
            'tooltip'         => $this->getTooltip(),
            'toolbox'         => $this->getToolbox(),
            'series'          => $this->getSeries(),
            'textStyle'       => $this->getTextStyle()
        ], $option);
        parent::setOption($_option);
        return $this;
    }
}