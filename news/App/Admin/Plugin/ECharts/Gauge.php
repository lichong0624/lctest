<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-4-25
 * @version     : $id$
 */
class Admin_Plugin_ECharts_Gauge extends Admin_Plugin_ECharts_ECharts
{

    protected $_chartType = Admin_Plugin_ECharts_ECharts::CHART_TYPE_GAUGE;

    /**
     * 默认一套样式
     *
     * @var array
     */
    public $_seriesExt = [
        'type'   => 'gauge',
        'min'    => 0,
        'max'    => 100,
        'radius' => "90%",
        'detail' => [
            'formatter' => '{value}%'
        ]
    ];

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