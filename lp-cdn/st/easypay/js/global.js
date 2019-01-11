var $
    , active = null;

layui.use(['form', 'layer', 'laydate', 'table', 'laytpl'], function () {
    var form    = layui.form
        , layer = layui.layer
        , $     = layui.jquery;

    $      = layui.$;
    active = {
        submit  : function (domObj) {
            try {
                var _form   = domObj.parents('form');
                var _modal  = domObj.parents('.modal');
                var _ignore = _form.attr('is_ignore');

                //表单是否忽略验证
                if (_ignore == 'undefined') {
                    _ignore = false;
                }

                //前端validate验证
                if (!_ignore && (validator == 'undefined' || !validator.validateForm())) {
                    layer.closeAll('loading');
                    return false;
                }
                
                domObj.prop('disabled', true);    //阻止多次提交

                var _action     = _form.attr('action');
                var _data       = _form.serialize();
                var _refreshUrl = _form.data('refresh-url');

                $.ajax({
                    type       : "POST",
                    url        : _action,
                    async      : false,
                    data       : _data,
                    beforeSend : function () {
                        layer.load();
                    },
                    complete   : function () {
                        layer.closeAll('loading'); //关闭loading
                    },
                    success    : function (res) {
                        if (res.status > 0) {
                            layer.msg(res.msg, {icon : 6});
                            _modal.remove();

                            var _modalId = $('.modal').attr('id');
                            if (typeof(_modalId) != 'undefined') {
                                refreshModalDiv(_refreshUrl, _modalId);//刷新父级div
                            } else {
                                refreshTab(_refreshUrl);//刷新tab
                            }
                        } else {
                            layer.msg(res.msg, {icon : 5});
                        }
                    },
                });
            } catch (e) {
                console.log(e);
                layer.msg('服务器错误！', {icon : 5});

                return false;
            }
        },
        show    : function (url, options) {
            $.ajax({
                type       : "GET",
                url        : url,
                async      : false,
                beforeSend : function () {
                    layer.load();
                },
                complete   : function () {
                    layer.closeAll('loading'); //关闭loading
                },
                success    : function (str) {
                    if (typeof str === "object") {
                        layer.msg(str.msg, {icon : 5});
                    } else {
                        var option = {
                            content : str,
                            showBtn : [],
                            buttons : {},
                            init    : null//初始化函数data
                        };

                        $.extend(option, options);
                        Q.ui.dialog(option);
                    }
                },
                error      : function (res) {
                    layer.msg(res.msg, {icon : 5});
                }
            });
        },
        confirm : function (url, refreshUrl) {
            layer.confirm("确定要执行吗？", {}, function (index, layero) {
                $.ajax({
                    type       : "GET",
                    url        : url,
                    async      : false,
                    beforeSend : function () {
                        layer.load();
                    },
                    complete   : function () {
                        layer.closeAll('loading'); //关闭loading
                    },
                    success    : function (res) {
                        if (res.status > 0) {
                            layer.msg(res.msg, {icon : 6});
                            layer.close(index);
                            var _modalId = $('.modal').attr('id');
                            if (typeof(_modalId) != 'undefined') {
                                refreshModalDiv(refreshUrl, _modalId);//刷新父级div
                            } else {
                                refreshTab(refreshUrl);//刷新tab
                            }
                        } else {
                            layer.msg(res.msg, {icon : 5});
                        }
                    },
                    error      : function (res) {
                        layer.msg(res.msg, {icon : 5});
                    }
                });
            }, function (index, layero) {
                layer.close(index);
            });
        }
    };
});
