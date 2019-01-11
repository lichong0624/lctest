// edit---->think angle chagne
//
//
// it is event 给每个元素td 或者tr 进行事件注册 可以写自己的回调函数   也可以用插件自带的某一写毁掉函数
//
//
// 比如说，要实现一个td 有两个内容，但是需要分开编辑，这个不是table事件，而是页面事件，。。。应为所有的
var Q    = Q || {};
Q.plugin = Q.plugin || {};

var layer, form;

Q.plugin.page = function () {
    var self             = this;
    const DATA_TYPE_JSON = 'json';
    const DATA_TYPE_HTML = 'html';

    //异步加载页面
    self.asyncLoadPage   = function (loadParam) {
        var loadParams = {
            insertCon   : '#qf-layout-content'
            , url       : location.href
            , data      : {}
            , dataType  : DATA_TYPE_JSON
            , pushState : true
        };
        $.extend(loadParams, loadParam);

        var loadIndex   = 0;
        var url         = loadParams['url']
            , insertCon = loadParams['insertCon']
            , data      = loadParams['data'];

        if (url === "" || url === undefined || url === "#" || new RegExp('^javascript:').test(url)) {
            return true;
        }

        loadParams.pushState && window.history.pushState({}, '', url);

        $.ajax({
            url          : url
            , data       : data
            , type       : 'GET'
            , success    : function (res) {
                var _dataType = typeof res;
                var _data     = '';
                if (_dataType === 'string') {
                    _data = res;
                } else if (_dataType === 'object') {
                    if (res.status < 1) {
                        layer.msg(res.msg, {icon : 5});

                        return false;
                    }

                    _data = res.data;

                }

                $(insertCon).html(_data);
            }
            , error      : function (res) {
                if (res.status !== 200) {
                    layer.msg(res['responseText'], {icon : 5});
                }

                return false;
            }
            , beforeSend : function () {
                // loadIndex = self.loading(loadParams['insertCon'])
            }
            , complete   : function () {
                layer.close(loadIndex);
            }
        });

    };
    //拦截器
    self.interceptListen = function (interceptParam) {
        var that            = this;
        var interceptParams = {
            'beforeFunc' : null
            , 'con'      : "body"
            , "callback" : null
        };
        $.extend(interceptParams, interceptParam);

        var con = interceptParams['con'] || "body";

        //拦截a事件
        $(con).on('click', "a:not('.external')", function (e) {
            e.preventDefault();
            that.intercept(this, interceptParams);
        });
    };

    self.intercept = function (clickObj, param) {
        var that = this;
        if (param['beforeFunc'] !== null && typeof param['beforeFunc'] === "function") {
            var p = new Promise(function (resolve, reject) {
                param["beforeFunc"](resolve, reject);
            });
            p.then(function () {
                that.asyncLoadPage({
                    url : $(clickObj).attr('href'),
                    con : $(clickObj).data('insertCon')
                });
            });
        } else {
            that.asyncLoadPage({
                url : $(clickObj).attr('href'),
                con : $(clickObj).data('insertCon')
            });
        }
    };

    self.loading = function (elem) {
        var obj = $(elem);

        if (obj.length === 0) {
            return false;
        }

        var offset  = obj.offset();
        var _width  = obj.width() * 0.5;
        var _height = obj.height() * 0.5;

        return layer.load(0, {
            offset : [offset.top + _height, offset.left + _width],
            time   : 3000
        });
    };
};

