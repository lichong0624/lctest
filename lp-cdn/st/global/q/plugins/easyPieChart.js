var Q    = Q || {};
Q.plugin = Q.plugin || {};

/**
 * 饼状图:easyPieChart
 * demo:
 * <div class="easy-pie-chart percentage" data-percent="90" data-size="90">
 <span class="percent"></span>
 </div>
 *
 */
Q.plugin.easyPieChart = function () {
    var self = this;

    self.init = function (option) {
        var options = {
            elem      : '.easy-pie-chart.percentage'
            , showBox : '.percent'
        };
        $.extend(options, option);

        $(options.elem).each(function () {
            var _obj     = $(this);
            var _size    = parseInt(_obj.data('size')) || 50;
            var _percent = parseInt(_obj.data('percent'));
            _obj.css('height', _size + 'px');
            _obj.css('width', _size + 'px');
            _obj.css('line-height', _size + 'px');
            _obj.find(options.showBox).html(_percent + '%');
            _obj.easyPieChart({
                barColor   : '#38B03F',
                trackColor : '#E2E2E2',
                scaleColor : false,
                lineCap    : 'butt',
                lineWidth  : parseInt(_size / 10),
                animate    : 1000,
                size       : _size
            });
        });
    };
};