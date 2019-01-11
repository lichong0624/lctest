var Q    = Q || {};
Q.plugin = Q.plugin || {};

//逐次递增字体大小，使字体达到最大的字号
Q.plugin.resizeFont = function () {
    var self = this;

    self.init = function (option) {
        var options = {
            area        : '.schedule-body'
            , elem      : '.schedule-body p cite'
            , defSize   : '20px'
            , maxHeight : 0
            , maxWidth  : 0
        };

        $.extend(options, option);

        var bodyObj   = $(options.area);
        var citeObj   = $(options.elem);
        var maxHeight = options.maxHeight > 0 ? options.maxHeight : bodyObj.height(); //固定高度
        var maxWidth  = options.maxWidth > 0 ? options.maxWidth : bodyObj.width(); //固定宽度
        var width     = [];
        var height    = [];

        citeObj.each(function (i, obj) {
            height[i] = obj.offsetHeight;
            width[i]  = obj.offsetWidth;
            if (width[i] === 0 || height[i] === 0) {
                return false;
            }

            obj.style.fontSize = options.defSize;
            while (parseInt(height[i]) < maxHeight && parseInt(width[i]) < maxWidth) {
                obj.style.fontSize = parseInt(obj.style.fontSize) + 1 + "px";
                height[i]          = obj.offsetHeight;
                width[i]           = obj.offsetWidth;
            }

            return this;
        });
    };
};