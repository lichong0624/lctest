<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-4-25
 * @version     : $id$
 */
class Admin_Plugin_ECharts_Scatter extends Admin_Plugin_ECharts_ECharts
{

    protected $_chartType = Admin_Plugin_ECharts_ECharts::CHART_TYPE_SCATTER;

    /**
     * 默认一套样式
     *
     * @var array
     */
    public $_seriesExt = [
        'type'             => 'scatter',
        'coordinateSystem' => 'geo',
        'symbolSize'       => 12,
        'label'            => [
            'normal' => ['show' => true, 'emphasis' => true],
        ],
        'itemStyle'        => [
            'emphasis' => [
                ['borderColor' => '#000', 'borderWidth' => 1]
            ]
        ]
    ];


    /**
     * 地理坐标系组件
     *
     * @var array
     */
    protected $_geo = [
        'map'       => 'china',
        'label'     => [
            'emphasis' => ['show' => false]
        ],
        'roam'      => true,
        'itemStyle' => [
            'normal'   => [
                'areaColor'   => '#323c48',
                'borderColor' => '#404a59'
            ],
            'emphasis' => [
                'areaColor' => '#2a333d'
            ]
        ]
    ];


    /**
     * 设置地理坐标系组件
     *
     * @param $geo
     * @return $this
     */
    public function setGeo(array $geo = [])
    {
        $this->_geo = array_merge($this->_geo, $geo);
        return $this;
    }


    /**
     * 获取地理坐标系
     *
     * @return array
     */
    public function getGeo()
    {
        return $this->_geo;
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
            'grid'            => $this->getGrid(),
            'visualMap'       => $this->getVisualMap(),
            'tooltip'         => $this->getTooltip(),
            'toolbox'         => $this->getToolbox(),
            'geo'             => $this->getGeo(),
            'series'          => $this->getSeries(),
            'textStyle'       => $this->getTextStyle()
        ], $option);
        parent::setOption($_option);
        return $this;
    }
}