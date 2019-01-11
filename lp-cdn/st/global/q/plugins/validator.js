/**
 * Created by wiki on 15-7-30.
 */

var Q    = Q || {};
Q.plugin = Q.plugin || {};

/**
 * 验证插件
 * @param form
 * @param option
 */
Q.plugin.validator = function (form, option)
{
    var self    = this;
    var options = {
        'alertErrorMsg' : null,//是否弹出错误信息
        'customValid'   : null,
        'submitValid'   : null//提交时自定义验证
    };
    $.extend(options, option);

    self.MSG_TYPE_ERROR   = 'error';
    self.MSG_TYPE_SUCCESS = 'success';

    self.form    = $(form);
    self.defMsgs = {
        'error'   : '{name} 验证失败',
        'success' : '{name} 验证成功'
    };
    self.msg     = {
        'error'   : {},
        'success' : {}
    };

    self.validElems            = {};
    var _curEventType          = 'blur';
    self.disableAjaxValidate   = false;
    var _ajaxValidElementStats = {};
    var _hasAjaxError          = false;
    var _ajaxValidSuccessCount = 0;


    var _getValidElement = function ()
    {
        return self.form.find('[data-valid]');
    };

    /**
     * 把要验证的表单缓存到属性中
     */
    $(_getValidElement()).each(function ()
    {
        var _field              = $(this).data('field');
        self.validElems[_field] = $(this);
    });

    /**
     * 验证单个表单
     * @param elem
     */
    self.validate = function (elem)
    {

        var _field = $(elem).data('field');
        var _val   = _getValidValue(elem);

        if ($(elem).data('oldVal') === _val) {
            self.showMsg(elem);
            return;
        }

        $(elem).data('oldVal', _val);

        if (_curEventType != 'submit') {
            self.disableAjaxValidate = false;
        }
        delete self.msg[self.MSG_TYPE_ERROR][_field];
        delete self.msg[self.MSG_TYPE_SUCCESS][_field];

        if (options.customValid && options.customValid[_field]) {
            options.customValid[_field].call(elem, self);
        }

        var rules = $(elem).data('valid');
        for (var _ruleType in rules) {
            if (_ruleType == 'ajax') {
                if (self.disableAjaxValidate) {
                    continue;
                }
                _hasAjaxError                  = true;
                _ajaxValidElementStats[_field] = true;
            }
            self.validFunc[_ruleType] && self.validFunc[_ruleType](elem, rules[_ruleType]);
        }
        self.showMsg(elem);
    };


    self.showMsg = function (elem)
    {
        var _field = $(elem).data('field');

        var _msgClass = 'valid-msg-' + _field;
        var _msgCon   = $('.' + _msgClass);
        if (!_msgCon[0]) {
            _msgCon = $('<span class="valid-msg ' + _msgClass + '"></span>');
            $(elem).after(_msgCon);
        }

        var _errorCss   = self.MSG_TYPE_ERROR + ' valid-' + self.MSG_TYPE_ERROR;
        var _successCss = self.MSG_TYPE_SUCCESS + ' valid-' + self.MSG_TYPE_SUCCESS;

        if ($.isEmptyObject(self.msg[self.MSG_TYPE_ERROR][_field])
            && $.isEmptyObject(self.msg[self.MSG_TYPE_SUCCESS][_field])) {
            $(elem).removeClass(_errorCss).removeClass(_successCss);
            _msgCon.hide();
            return;
        }

        if (!$.isEmptyObject(self.msg[self.MSG_TYPE_ERROR][_field])) {
            $(elem).removeClass(_successCss).addClass(_errorCss);
            _msgCon.show().removeClass(_successCss).addClass(_errorCss).html(self.msg[self.MSG_TYPE_ERROR][_field].join('<br>'));
        } else if (!$.isEmptyObject(self.msg[self.MSG_TYPE_SUCCESS][_field])) {
            $(elem).removeClass(_errorCss).addClass(_successCss);
            _msgCon.show().removeClass(_errorCss).addClass(_successCss).html(self.msg[self.MSG_TYPE_SUCCESS][_field].join('<br>'));
        }
    };

    self.alertErrorMsg = function (elem)
    {
        if (!options['alertErrorMsg']) {
            return;
        }

        var _field = $(elem).data('field');

        var _errors = (self.msg[self.MSG_TYPE_ERROR][_field]).join('<br>');
        if ($.isEmptyObject(_errors)) {
            return;
        }

        var _alert = options['alertErrorMsg'];
        if (_alert === true) {
            Q.ui.alert({
                'content'  : _errors,
                'callback' : {
                    'submit' : function (dialog, data)
                    {
                        $(elem).focus();
                    }
                }
            });
        } else if ($.isFunction(_alert)) {
            _alert(_errors, elem);
        }
    };


    $(_getValidElement()).on('blur', function ()
    {
        _curEventType = 'blur';
        self.validate(this);
    });

    self.form.submit(function ()
    {
        if (self.form.data('disabled')) {
            return false;
        }

        _curEventType = 'submit';

        if (false == self.validateForm()) {
            return false;
        }

        self.form.data('disabled', true);

    });

    self.validateForm = function ()
    {
        var _validElems = $(_getValidElement());
        _validElems.each(function ()
        {
            self.validate(this);
            self.alertErrorMsg(this);
        });

        options.submitValid && options.submitValid.call(self);

        if (!$.isEmptyObject(self.msg[self.MSG_TYPE_ERROR])) {
            return false;
        }


        if (_hasAjaxError) {
            return false;
        }

        return true;
    };

    /**
     *
     * @param elem
     * @param msg
     * @param msgType
     */
    self.addMsg = function (elem, msg, msgType)
    {
        var _field = $(elem).data('field');

        self.msg[msgType][_field] = [];
        self.msg[msgType][_field].push(msg);
        return self;
    };

    var _getValidValue = function (elem)
    {
        var val = $(elem).val();

        //如果是用label包裹的单个radio或checkbox，获取内部input值
        if ($(elem)[0].tagName.toLowerCase() == 'label') {
            val = $(elem).find('input:checked').val();
        }

        val = $.trim(val);

        //一组数据
        if ($(elem).hasClass('valid-group')) {
            var _elem = $(elem).find('input:checked');

            if (_elem.attr('type') == 'radio') {
                val = $.trim(_elem.val());
            } else {
                val = [];
                _elem.each(function ()
                {
                    var _val = $.trim($(this).val());
                    val.push(_val);
                });
                val = val.length > 0 ? val : null;
            }
        }
        return val;
    };

    self.formatMsg = function (elem, rule, defMsg, msgType)
    {
        var _name = $(elem).data('name');
        var _val  = _getValidValue(elem);
        var _msg  = rule['message'] || defMsg || self.defMsgs[msgType];

        _msg = _msg.replace('{name}', _name);

        if (_val) {
            _msg = _msg.replace('{value}', _val);
        }

        if (typeof(rule['length']) == 'number') {
            _msg = _msg.replace('{length}', rule['length']);
        }

        if (typeof(rule['min']) == 'number') {
            _msg = _msg.replace('{min}', rule['min']);
        }

        if (typeof(rule['max']) == 'number') {
            _msg = _msg.replace('{max}', rule['max']);
        }

        return _msg;
    };


    self.validFunc = {
        'required' : function (elem, rule)
        {
            var _val = _getValidValue(elem);
            if (!_val) {
                self.addMsg(elem, self.formatMsg(elem, rule, '请输入{name}', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'number'   : function (elem, rule)
        {
            var _val = _getValidValue(elem);
            if (!_val) {
                return;
            }

            if (isNaN(_val)) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name}必须为数字', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }

            if (typeof(rule['min']) == 'number' && _val < rule['min']) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name}必须大于等于{min}', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }

            if (typeof(rule['max']) == 'number' && _val > rule['max']) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name}必须小于等于{max}', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'email'    : function (elem, rule)
        {
            var _val = _getValidValue(elem);

            if (_val && !/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/.test(_val)) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{value}不是正确的邮箱格式', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'url'      : function (elem, rule)
        {
            var _val = _getValidValue(elem);

            if (_val && !/^(http[s]?:\/\/)?[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/.test(_val)) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{value}不是正确的网址', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'compare'  : function (elem, rule)
        {
            var _val  = _getValidValue(elem);
            var _val2 = _getValidValue($('[data-field=' + rule['field'] + ']'));

            if (_val !== _val2) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name}与' + $(elem).data('name') + '不相同', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'length'   : function (elem, rule)
        {
            var _val = _getValidValue(elem);
            if (!_val) {
                return;
            }
            var _len = Q.tools.string.length(_val);
            if (typeof(rule['length']) == 'number' && _len != rule['length']) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name}长度不等于' + rule['length'], self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }

            if (typeof(rule['min']) == 'number' && _len < rule['min']) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name}长度不能小于' + rule['min'], self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }

            if (typeof(rule['max']) == 'number' && _len > rule['max']) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name}长度不能大于' + rule['max'], self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'mobile'   : function (elem, rule)
        {
            var _val = _getValidValue(elem);

            if (_val && !/^(1\d{10})$/.test(_val)) {
                self.addMsg(elem, self.formatMsg(elem, rule, '手机号 {value} 格式不正确', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'chinese'  : function (elem, rule)
        {
            var _val = _getValidValue(elem);

            if (_val && !/^[\u4E00-\u9FA5]+$/.test(_val)) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name} 不是中文', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },
        'idcard'   : function (elem, rule)
        {
            var _val = _getValidValue(elem);

            if (_val && !/^(\d{15}|\d{17}[\dXx])$/.test(_val)) {
                self.addMsg(elem, self.formatMsg(elem, rule, '{name} 不正确', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
            }
        },

        'reg'  : function (elem, rule)
        {
            var _val = _getValidValue(elem);

            if (_val && rule['reg']) {
                eval('_reg = ' + rule['reg']);
                if (!_reg.test(_val)) {
                    self.addMsg(elem, self.formatMsg(elem, rule, _val + '{name}没有通过验证', self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
                }
            }
        },
        'ajax' : function (elem, rule)
        {
            var _val = _getValidValue(elem);

            var _url = rule['url'];
            if (_val && _url) {
                $.ajax({
	                async    :false,
                    url      : _url,
                    data     : {'val' : _val},
                    context  : elem,
                    success  : function (data)
                    {
                        var _field = $(this).data('field');
                        var _msg   = '服务器验证失败';
                        if (data) {
                            _msg = data['msg'] || data['data'];
                        }

                        rule['message'] = _msg;
                        if (!data || data['status'] != 1) {
                            self.disableAjaxValidate = false;
                            _hasAjaxError            = true;
                            self.addMsg($(this), self.formatMsg($(this), rule, _msg, self.MSG_TYPE_ERROR), self.MSG_TYPE_ERROR);
                        } else {
                            _ajaxValidElementStats[_field] = false;

                            _hasAjaxError = false;
                            for (var _f in _ajaxValidElementStats) {
                                if (_ajaxValidElementStats[_f]) {
                                    _hasAjaxError = true;
                                }
                            }

                            if (!_hasAjaxError) {
                                self.disableAjaxValidate = true;
                                //提交表单
                                if (_curEventType == 'submit') {
                                    self.form.submit();
                                }
                            }
                            self.addMsg($(this), self.formatMsg($(this), rule, _msg, self.MSG_TYPE_SUCCESS), self.MSG_TYPE_SUCCESS);
                        }
                        self.showMsg($(this));
                    },
                    dataType : 'json'
                });
            }
        }
    };

    return this;
};