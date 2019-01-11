<?php
/**
 *
 * @author      : Jack<zhaoligang@dalingpao.com>
 * @copyright(c): 17-4-25
 * @version     : $id$
 */
class Admin_Plugin_ECharts_ECharts
{
    const CHART_TYPE_LINE    = 'Line'; //折线/面积图
    const CHART_TYPE_BAR     = 'Bar'; //柱状/条形图
    const CHART_TYPE_MAP     = 'Map';//地图
    const CHART_TYPE_SCATTER = 'Scatter';//散点（气泡）图。

    const CHART_TYPE_EFFECT_SCATTER = 'EffectScatter';//带有涟漪特效动画的散点（气泡）图。利用动画特效可以将某些想要突出的数据进行视觉突出。
    const CHART_TYPE_PIE            = 'Pie'; //饼图
    const CHART_TYPE_RADAR          = 'Radar';//雷达图
    const CHART_TYPE_TREE_MAP       = 'TreeMap';//常见的表达『层级数据』『树状数据』的可视化形式。它主要用面积的方式，便于突出展现出『树』的各层级中重要的节点。
    const CHART_TYPE_BOX_PLOT       = 'BoxPlot';//『箱形图』、『盒须图』、『盒式图』、『盒状图』、『箱线图』
    const CHART_TYPE_CANDLESTICK    = 'Candlestick';//K线图
    const CHART_TYPE_HEAT_MAP       = 'HeatMap';//热力图
    const CHART_TYPE_PARALLEL       = 'Parallel';//平行坐标系的系列。
    const CHART_TYPE_GRAPH          = 'Graph';//关系图 用于展现节点以及节点之间的关系数据。
    const CHART_TYPE_SAN_KEY        = 'SanKey';//桑基图 是一种特殊的流图, 它主要用来表示原材料、能量等如何从初始形式经过中间过程的加工、转化到达最终形式
    const CHART_TYPE_FUNNEL         = 'Funnel';//漏斗图
    const CHART_TYPE_GAUGE          = 'Gauge';//仪表盘
    const CHART_TYPE_PICTORIAL_BAR  = 'PictorialBar';//象形柱图 象形柱图是可以设置各种具象图形元素（如图片、SVG PathData 等）的柱状图
    const CHART_TYPE_THEME_RIVER    = 'ThemeRiver';//主题河流 是一种特殊的流图, 它主要用来表示事件或主题等在一段时间内的变化


    /**
     * 图表标题
     *
     * @var array
     */
    protected $_title = [];

    /**
     * 设置标题
     *
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        if (is_string($title)) {
            $title = ['text' => $title];
        }

        $this->_title = array_merge($this->_title, $title);
        return $this;
    }


    /**
     * 获取标题
     *
     * @return array
     */
    public function getTitle()
    {
        return $this->_title;
    }


    /**
     * 图表图例 图例组件展现了不同系列的标记(symbol)，颜色和名字。可以通过点击图例控制哪些系列不显示
     *
     * @var array
     */
    protected $_legend = [];

    /**
     * 设置图例
     *
     * @param $legend
     * @return $this
     */
    public function setLegend(array $legend = [])
    {
        $this->_legend = array_merge($this->_legend, $legend);
        return $this;
    }

    /**
     * 获取图例
     *
     * @return array
     */
    public function getLegend()
    {
        return $this->_legend;
    }


    /**
     * 图表图例数据，即条目标题
     *
     * @var array
     */
    protected $_legendData = [];


    /**
     * 设置图例
     *
     * @param $legendData
     * @return $this
     */
    public function setLegendData(array $legendData = [])
    {
        $this->_legendData = $legendData;
        $this->setLegend(array_merge($this->getLegend(), ['data' => $legendData]));
        return $this;
    }

    /**
     * 是否选中图表图例显示
     *
     * @var array
     */
    protected $_legendSelected = [];

    /**
     * 设置图例
     *
     * @param $legendSelected
     * @return $this
     */
    public function setLegendSelected(array $legendSelected = [])
    {
        $this->_legendSelected = $legendSelected;
        $this->setLegend(array_merge($this->getLegend(), ['selected' => $legendSelected]));
        return $this;
    }


    /**
     * 直角坐标系内绘图网格，单个 grid 内最多可以放置上下两个 X 轴，左右两个 Y 轴。可以在网格上绘制折线图，柱状图，散点图（气泡图）。
     *
     * @var array
     */
    protected $_grid = [
        'left'         => '3%',
        'right'        => '4%',
        'bottom'       => '3%',
        'containLabel' => true,
    ];


    /**
     * 设置网格
     *
     * @param $grid
     * @return $this
     */
    public function setGrid(array $grid = [])
    {
        $this->_grid = array_merge($this->_grid, $grid);
        return $this;
    }

    /**
     * 获取网格
     *
     * @return array
     */
    public function getGrid()
    {
        return $this->_grid;
    }


    /**
     * 视觉映射组件，用于进行『视觉编码』，也就是将数据映射到视觉元素（视觉通道）
     *
     * @var array
     */
    protected $_visualMap = [
        'calculable' => true,
        'left'       => 'left',
        'top'        => 'bottom',
        'text'       => ['高', '低'],
        'textStyle'  => ['color' => '#000']
    ];

    /**
     * 设置视觉映射组件
     *
     * @param array $visualMap
     * @return $this
     */
    public function setVisualMap(array $visualMap = [])
    {
        $this->_visualMap = array_merge($this->_visualMap, $visualMap);
        return $this;
    }


    /**
     * 获取视觉映射组件
     *
     * @return array
     */
    public function getVisualMap()
    {
        return $this->_visualMap;
    }

    /**
     * 提示框组件
     *
     * @var array
     */
    protected $_tooltip = ['trigger' => 'axis'];


    /**
     * 设置提示框组件
     *
     * @param $tooltip
     * @return $this
     */
    public function setTooltip(array $tooltip = [])
    {
        $this->_tooltip = array_merge($this->_tooltip, $tooltip);
        return $this;
    }


    /**
     * 获取提示框组件
     *
     * @return array
     */
    public function getTooltip()
    {
        return $this->_tooltip;
    }


    /**
     * 工具栏
     *
     * @var array
     */
    protected $_toolbox = [
        'show'    => true,
        'feature' => [
            'mark'        => ['show' => true],
            'dataView'    => ['show' => true, 'readOnly' => false],
            'magicType'   => ['show' => true, 'type' => ['line', 'bar']],
            'restore'     => ['show' => true],
            'saveAsImage' => ['show' => true]
        ]
    ];


    /**
     * 设置工具栏
     *
     * @param $toolBox
     * @return $this
     */
    public function setToolbox(array $toolBox = [])
    {
        $this->_toolbox = array_merge($this->_toolbox, $toolBox);
        return $this;
    }


    /**
     * 获取提示框组件
     *
     * @return array
     */
    public function getToolbox()
    {
        return $this->_toolbox;
    }

    /**
     * 背景色
     *
     * @var string
     */
    protected $_backgroundColor = 'rgba(255,255,255)';


    /**
     * 设置背景色
     *
     * @param $backgroundColor
     * @return $this
     */
    public function setBackgroundColor($backgroundColor = '')
    {
        $this->_backgroundColor = $backgroundColor;
        return $this;
    }


    /**
     * 获取背景色
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->_backgroundColor;
    }

    /**setOption
     * 全局的字体样式
     *
     * @var string
     */
    protected $_textStyle = [];


    /**
     * 设置全局的字体样式
     *
     * @param $textStyle
     * @return $this
     */
    public function setTextStyle(array $textStyle = [])
    {
        $this->_textStyle = $textStyle;
        return $this;
    }

    /**
     * 获取字体样式
     *
     * @return string
     */
    public function getTextStyle()
    {
        return $this->_textStyle;
    }


    /**
     * 系列列表.每个系列通过 type 决定自己的图表类型
     *
     * @var array
     */
    protected $_series = [];

    /**
     * 设置系列列表
     *
     * @param $series
     * @return $this
     */
    public function setSeries(array $series = [])
    {
        $this->_series = $series;
        return $this;
    }

    /**
     * 获取系列列表
     *
     * @return array
     */
    public function getSeries()
    {
        return $this->_series;
    }


    /**
     * 系列列表数据.系列列表的Data数据
     *
     * @var array
     */
    protected $_seriesData = [];

    /**
     * 设置系列列表数据，内置一套配置作为系列的样式
     *
     * @param $seriesData
     * @return $this
     */
    public function setSeriesData(array $seriesData = [])
    {
        if (empty($seriesData)) {
            return $this;
        }

        $_classInstanceKey = self::$_classInstanceKey;
        $calledClass       = self::$_instance[$_classInstanceKey];
        //获取系列默认的图表系列样式
        $_seriesExt = empty($calledClass->_seriesExt) ? [] : $calledClass->_seriesExt;

        //获取图表的系列标题
        $_legend = $this->getLegend();
        $_series = [];
        foreach ($seriesData as $k => $_row) {
            $_series[$k] = [
                'name' => empty($_legend['data'][$k]) ? '' : $_legend['data'][$k],
                'data' => $_row
            ];

            if (is_callable($_row)) {
                $_series[$k]['data'] = $_series[$k]['data']($_row);
            }
            $_series[$k] = array_merge($_seriesExt, $_series[$k]);
        }

        //设置图表系列
        $this->setSeries($_series);
        //设置图表系列数据
        $this->_seriesData = $seriesData;
        return $this;
    }


    /**
     * 获取系列列表数据
     *
     * @return array
     */
    public function getSeriesData()
    {
        return $this->_seriesData;
    }


    /**
     * 执行 preprocessor 函数 function($this)[]
     * 用于预处理对象的回调
     *
     * @param callable $preprocessor
     * @return $this
     * @throws Q_Exception
     */
    public function seriesDataPreprocessor($preprocessor)
    {
        try {
            if (is_callable($preprocessor)) {
                $_seriesData = $this->getSeriesData();

                if (empty($_seriesData)) {
                    return $this;
                }

                $_data = [];
                foreach ($_seriesData as $k => $_row) {
                    $_data[$k] = $preprocessor($_row);
                }
                $this->setSeriesData($_data);
            }
        } catch (Q_Exception $ex) {
            throw new Q_Exception($ex->getMessage(), $ex->getCode());
        }

        return $this;
    }

    /**
     * 图表参数
     *
     * @var array
     */
    protected $_option = [];

    /**
     * 设置图表实例的配置项以及数据，万能接口
     *
     * @param $option
     * @return $this
     */
    public function setOption($option = [])
    {
        $this->_option = array_merge($this->_option, $option);
        return $this;
    }

    /**
     * 获取图表参数
     *
     * @return array
     */
    public function getOption()
    {
        self::instance($this->_chartType)->setOption();
        return $this->_option;
    }

    /**
     * 图表类型
     *
     * @var string
     */
    protected $_chartType = '';

    /**
     * @var string
     */
    protected static $_classInstanceKey = '';

    /**
     * @var null
     */
    private static $_instance = null;


    /**
     * @param string $chartType
     * @return $this
     * @throws Q_Exception
     */
    public static function instance($chartType = '')
    {
        if (empty($chartType)) {
            $chartType = substr(get_called_class(), 25);
        }

        $_classInstanceKey = "Admin_Plugin_ECharts_{$chartType}";

        if (!class_exists($_classInstanceKey)) {
            throw new Q_Exception("The controller of '{$_classInstanceKey}' is not exists!");
        }

        if (empty(self::$_instance[$_classInstanceKey])) {
            $obj                                 = new $_classInstanceKey();
            self::$_instance[$_classInstanceKey] = $obj;
        }

        $obj                     = self::$_instance[$_classInstanceKey];
        $obj->_chartType         = $chartType;
        self::$_classInstanceKey = $_classInstanceKey;
        return $obj;
    }


    /**
     * 图表模板
     *
     * @var string
     */
    protected $_chartTpl = 'Plugin/ECharts/Default';

    /**
     * 设置图表模板
     *
     * @param $chartTpl
     * @return $this
     */
    public function setChartTpl($chartTpl = '')
    {
        $this->_chartTpl = $chartTpl;
        return $this;
    }

    /**
     * 获取图表ID
     *
     * @return string
     */
    public function getChartTpl()
    {
        return $this->_chartTpl;
    }


    /**
     * 图表ID
     *
     * @var string
     */
    protected $_chartId = 'chartId';

    /**
     * 设置图表ID
     *
     * @param $chartId
     * @return $this
     */
    public function setChartId($chartId = 'chartId')
    {
        $this->_chartId = $chartId;
        return $this;
    }

    /**
     * 获取图表ID
     *
     * @return string
     */
    public function getChartId()
    {

        return $this->_chartId;
    }

    /**
     * 图表页面显示样式
     *
     * @var string
     */
    protected $_chartStyle = 'width:100%;min-height:300px;';

    /**
     * 设置图表样式
     *
     * @param  string $chartStyle
     * @return $this
     */
    public function setChartStyle($chartStyle = '')
    {
        $this->_chartStyle = $chartStyle;
        return $this;
    }

    /**
     * 获取图表ID
     *
     * @return string
     */
    public function getChartStyle()
    {
        return $this->_chartStyle;
    }

    /**
     * 返回画好的图表
     *
     * @return string
     */
    public function display()
    {
        $output               = Q_Response::instance();
        $output->chartId      = $this->getChartId();
        $output->chartStyle   = $this->getChartStyle();
        $output->option       = json_encode($this->getOption());
        $output->globalServer = Q_Config::get('Global', 'ST_SERVER') . 'global/';
        return $output->fetchCol($this->getChartTpl());
    }

    /**
     * 返回图表设置信息
     *
     * @return array
     */
    public function getData()
    {
        return $this->getOption();
    }
}