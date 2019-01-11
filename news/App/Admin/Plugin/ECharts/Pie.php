<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-4-25
 * @version     : $id$
 */
class Admin_Plugin_ECharts_Pie extends Admin_Plugin_ECharts_ECharts
{

    protected $_chartType = Admin_Plugin_ECharts_ECharts::CHART_TYPE_PIE;

    protected $_space = '';

    /**
     * 默认一套样式
     *
     * @var array
     */
    public $_seriesExt = [
        'type'         => 'pie',
        'radius'       => [0, '65%'],
        'center'       => ['50%', '60%'],
        'selectedMode' => 'single',
        'label'        => [
            'normal' => ['show' => false, 'emphasis' => true],
        ],
        'itemStyle'    => [
            'emphasis' => [
                ['borderColor' => 'rgba(0, 0, 0, 0.5)', 'shadowOffsetX' => 0, 'shadowBlur' => 10]
            ]
        ]
    ];

    /**
     * 获取
     *
     * @return array
     */
    public function getSeriesExt(): array
    {
        return $this->_seriesExt;
    }

    /**
     *
     * @param array $seriesExt
     */
    public function setSeriesExt(array $seriesExt)
    {
        $seriesExt        = array_merge($this->_seriesExt, $seriesExt);
        $this->_seriesExt = $seriesExt;

        return $this;
    }


    /**
     * 设置嵌套环形图嵌套间距(暂时还没用上,需要想好怎么加)
     *
     * @param  $space
     * @return $this
     */
    public function setSeriesDataRadiusSpace($space = '10%')
    {
        $this->_space = $space;
        return $this;
    }


    /**
     * 获取嵌套环形图嵌套间距
     *
     * @return string
     */
    public function getSeriesDataRadiusSpace()
    {
        return $this->_space;
    }

    /**
     * 设置地图参数
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
            'tooltip'         => $this->getTooltip(),
            'toolbox'         => $this->getToolbox(),
            'series'          => $this->getSeries(),
            'textStyle'       => $this->getTextStyle()
        ], $option);

        parent::setOption($_option);
        return $this;
    }
}