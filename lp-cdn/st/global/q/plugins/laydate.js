var Q    = Q || {};
Q.plugin = Q.plugin || {};
/*
时间类型：type
year	年选择器　　只提供年列表选择
month	年月选择器	只提供年、月选择
date	日期选择器	可选择：年、月、日。type默认值，一般可不填
time	时间选择器	只提供时、分、秒选择
datetime	日期时间选择器	可选择：年、月、日、时、分、秒
*/

Q.plugin.laydate = function () {
    var self = this;

    self.render = function (option) {
        var options = {
            elem         : '.input-date'
            , type       : 'date' //日期类型:year,month,date,time,datetime,
            , showBottom : true
            , range      : false
            , done       : null
        };
        $.extend(options, option);

        layui.use('laydate', function () {
            var laydate = layui.laydate;

            $(options.elem).each(function (i, elem) {
                laydate.render({
                    elem         : elem
                    , type       : options.type
                    , showBottom : options.showBottom
                    , range      : options.range
                    , done       : options.done
                });
            });
        });
    }
};