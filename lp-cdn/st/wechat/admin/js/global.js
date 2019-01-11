var $
    , active = null;

layui.use(['layer', 'jquery'], function () {
    var layer = layui.layer
        , $   = layui.jquery;

    $      = layui.$;
    active = {
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
                        } else {
                            layer.msg(res.msg, {icon : 5});
                        }

                        new Q.plugin.page().asyncLoadPage({
                            url : window.location.href
                        });
                    },
                    error      : function (res) {
                        layer.msg(res.msg, {icon : 5});
                    }
                });
            }, function (index, layero) {
                layer.close(index);
            });
        },
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

                var _action = _form.attr('action');
                var _data   = _form.serialize();

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

                            new Q.plugin.page().asyncLoadPage({
                                url : window.location.href
                            });

                        } else {
                            layer.msg(res.msg, {icon : 5});
                        }
                    },
                });
            } catch (e) {
                layer.msg('服务器错误！', {icon : 5});
                return false;
            }
        },
    };
});