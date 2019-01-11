/**
 * Created by wiki on 14/12/8.
 */
var Q = Q || {};

//快捷键
Q.keyCode = {
    ALT: 18,
    BACKSPACE: 8,
    CAPS_LOCK: 20,
    COMMA: 188,
    COMMAND: 91,
    COMMAND_LEFT: 91, // COMMAND
    COMMAND_RIGHT: 93,
    CONTROL: 17,
    DELETE: 46,
    DOWN: 40,
    END: 35,
    ENTER: 13,
    ESCAPE: 27,
    HOME: 36,
    INSERT: 45,
    LEFT: 37,
    MENU: 93, // COMMAND_RIGHT
    NUMPAD_ADD: 107,
    NUMPAD_DECIMAL: 110,
    NUMPAD_DIVIDE: 111,
    NUMPAD_ENTER: 108,
    NUMPAD_MULTIPLY: 106,
    NUMPAD_SUBTRACT: 109,
    PAGE_DOWN: 34,
    PAGE_UP: 33,
    PERIOD: 190,
    RIGHT: 39,
    SHIFT: 16,
    SPACE: 32,
    TAB: 9,
    UP: 38,
    F7: 118,
    F12: 123,
    S: 83,
    WINDOWS: 91 // COMMAND
};

Q.ui = Q.ui || {};
Q.widget = Q.widget || {};

Q.tools = {
    string: {
        length: function (str) {
            var _cArr = str.match(/[\u4E00-\u9FA5]/ig);
            return str.length + (_cArr == null ? 0 : _cArr.length);
        }
    }
};

Q.ui.dialog = function (option) {
    var options = {
        id: 'modal-dialog',
        title: '提示',
        url: '',//frame url，有URL，content就为iframe url中的内容
        content: '提示',
        frameName: '',
        width: 0,
        height: 0,
        autoWidth: false,
        showBtn: [
            'cancel',
            'submit'
        ],
        buttons: {
            'cancel': '<button type="button" class="btn btn-sm btn-default btn-cancel" data-dismiss="modal">取消</button>',
            'submit': '<button type="button" class="btn btn-sm btn-primary btn-submit" data-dismiss="modal">确认</button>'
        },
        init: null,//初始化函数
        callback: {
            'cancel': null,
            'submit': null
        }
    };

    $.extend(options, option);
    options.url && (options.content = '<iframe scrolling="auto" width="100%" height="100%" name="' + options.frameName + '" frameborder="0" src="' + options.url + '"></iframe>');
    if ($('#' + options.id)[0]) {
        var dialog = $('#' + options.id);
        var footer = dialog.find('.modal-footer');
    } else {
        var width = '';
        var height = '';
        if (!options.autoWidth) {
            width = options.width > 0 ? ' style="width:' + options.width + 'px"' : ' style="width:' + $(window).width() * 0.7 + 'px"';
            height = options.height > 0 ? ' style="height:' + options.height + 'px"' : ' style="height:' + $(window).height() * 0.7 + 'px"';
        }
        var tpl =
            [
                '<div class="modal" id="' + options.id + '">',
                '<div class="modal-masklayer"></div>',
                '  <div class="modal-dialog" ' + width + '>',
                '    <div class="modal-content">',
                '      <div class="modal-header">',
                '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>',
                '        <h4 class="modal-title"></h4>',
                '      </div>',
                '      <div class="modal-body" ' + height + '></div>',
                '      <div class="modal-footer"></div>',
                '    </div>',
                '  </div>',
                '</div>'
            ].join("\n");

        $('body').append(tpl);
        //添加resize事件
        var dialog = $('#' + options.id);

        $(window).resize(function () {
            if (options.width == 0) {
                var _width = $(window).width() * 0.7 + 'px';
                dialog.find('.modal-dialog').css("width", _width);
            }

            if (options.height == 0) {
                var _height = $(window).height() * 0.7 + 'px';
                dialog.find('.modal-body').css("height", _height);
            }
        });

        var footer = dialog.find('.modal-footer');
        if (options.showBtn == 'ALL') {
            $.each(buttons, function (k, v) {
                $(v).addClass('btn-' + k);
                footer.append($(v));
            });
        } else if ($.isArray(options.showBtn)) {
            $.each(options.showBtn, function (k, v) {
                var _btn = $(options.buttons[v]).addClass('btn-' + v);
                footer.append(_btn);
            });
        }
    }

    dialog.on('click', '[data-dismiss=modal]', function () {
        dialog.remove();
    });

    //dialog.draggable && dialog.draggable();
    var titleCon = dialog.find('.modal-title');
    var contentCon = dialog.find('.modal-body');
    titleCon.html('').append(options.title);
    contentCon.html('').append(options.content);

    var frame = dialog.find('iframe');

    if ($.isFunction(options.callback)) {
        window.returnValue = window.returnValue || {};
        options.callback.call(dialog, window.returnValue);
    } else if (!$.isEmptyObject(options.callback)) {
        $.each(options.callback, function (k, v) {
            if (!$.isFunction(v)) {
                return;
            }
            footer.find('.btn-' + k).unbind().bind('click', function () {
                window.returnValue = window.returnValue || {};
                v.call(dialog, window.returnValue);
            });
        });
    }

    dialog.show();

    autoCenter(dialog);

    footer.find('.btn-submit').focus();
    $.isFunction(options.init) && options.init(dialog);
    return dialog;
};

var autoCenter = function (dialog) {
    var _dialogWindow = dialog.find('.modal-dialog');
    _dialogWindow.css('top', ($(window).height() - _dialogWindow.height()) / 2);
};

Q.ui.confirm = function (options, subimtFunc, cancelFunc) {
    options.id = options.id || 'modal-confirm';
    options['title'] = options.title || '提示';
    options['content'] = options.content || '';
    options['showBtn'] = options.showBtn || ['cancel', 'submit'];
    options['callback'] = options['callback'] || {};
    options['autoWidth'] = true;

    if ($.isFunction(subimtFunc)) {
        options['callback']['submit'] = subimtFunc;
    }

    if ($.isFunction(cancelFunc)) {
        options['callback']['cancel'] = cancelFunc;
    }

    var pop = Q.ui.dialog(options);
};

Q.ui.alert = function (option) {
    var options = {
        id: 'modal-alert',
        title: '提示',
        width: 0,
        height: 0,
        content: '',
        autoWidth: true
    };

    if (!$.isPlainObject(option)) {
        options.content = option;
    } else {
        $.extend(options, option);
    }

    options['showBtn'] = [
        'submit'
    ];
    var pop = Q.ui.dialog(options);
};

/**
 * toast提示
 * @param option
 */
Q.ui.toast = function (option, duration) {
    /**
     *
     * @type {{id: string, title: string, content: string, duration: number, showBtn: Array, autoWidth: boolean, callback: null}}
     */
    var options = {
        id: 'modal-toast',
        title: '提示',
        content: '',
        duration: 2000,
        showBtn: [],
        autoWidth: true,
        callback: null
    };

    if ($.isPlainObject(option)) {
        $.extend(options, option);
    } else {
        options.content = option;
        options.duration = duration ? duration : 2000;
    }

    if ($.isFunction(options['callback'])) {
        options['callback'] = options['callback'];
    }

    var pop = Q.ui.dialog(options);
    setTimeout(function () {
        pop.hide();
    }, options['duration']);
};


/**
 * ajax小圆圈菊花遮罩
 * @param option
 */
Q.ui.spinner = function (option) {
    var options = {
        id: 'modal-spinner',
        icon: 'fa-spinner',
        show: true
    };

    $.extend(options, option);

    if (!$('#' + options.id)[0]) {
        var tpl = [
            '<div class="modal" id="' + options.id + '">',
            '   <div class="modal-masklayer"></div>',
            '   <div class="modal-dialog spinner-box">',
            '       <i class="' + options.icon + ' loading-icon fa fa-spin fa-2x white" aria-hidden="true"></i>',
            '   </div>',
            '</div>'
        ].join("\n");

        $('body').append(tpl);
    }

    var spinner = $('#' + options.id);

    autoCenter(spinner);

    if (options['show']) {
        spinner.show();
    } else {
        spinner.hide();
    }
};

Q.ui.window = function (option) {
    var options = {
        id: 'modal-window',
        width: 1000,
        height: 500
    };

    if (!$.isPlainObject(option)) {
        options.content = option;
    } else {
        $.extend(options, option);
    }

    var pop = Q.ui.dialog(options);

    return pop;
};

Q.tools.addFavorite = function (obj) {
    var url = document.location.href;
    var title = document.title;
    if (document.all) {
        window.external.AddFavorite(url, title);
    }
//    else if (window.sidebar) {
//        window.sidebar.addPanel(title, url, "");
//    }
    else if (window.opera && window.print) {
        obj.setAttribute('rel', 'sidebar');
        obj.setAttribute('href', url);
        obj.setAttribute('title', title);
    } else {
        alert('请使用Ctrl+D收藏本站');
    }
    return false;
};

/**
 * 幻灯片切换器
 * 依赖 jquery.easing-1.3.js文件
 * @param swiper
 * @param config
 */
Q.widget.swiper = function (swiper, config) {
    this.configs = {
        'mode': 'h',// or v
        'autoPlay': 1000,// 自动播放,间隔时间,如果为0,就不自动播放
        'duration': 500,//切换效果时长
        'showNum': 1,//一次显示几个
        'canvas': '',//画布,不填为this
        'minWidth': 0,//最小宽度
        'minHeight': 0,//最大宽度
        'con': '.con',
        'item': '.con a',
        'selector': '.hd',
        'selectorEvent': 'click',
        'selectorItemTag': 'li',
        'selectorCurClass': 'cur',
        'nextButton': '',
        'prevButton': '',
        'onChange': ''
    };

    $.extend(this.configs, config);
    var _this = this;

    var isH = _this.configs['mode'] === 'h';

    $(swiper).each(function () {

        var canvas = _this.configs['canvas'] ? $(this).find(_this.configs['canvas']) : $(this);
        var width;
        var height;

        var items = $(this).find(_this.configs['item']);
        var itemLen = items.length;
        var conObj = $(this).find(_this.configs['con']);

        var nextButton = $(this).find(_this.configs['nextButton']);
        var prevButton = $(this).find(_this.configs['prevButton']);
        var curPos = 0;

        //设置宽高
        var _leftNum = itemLen % _this.configs['showNum'];
        itemLen = _leftNum ? (itemLen + _this.configs['showNum'] - _leftNum) : itemLen;//重置itemLen,分页取整
        var _drawSize = function () {
            width = Math.max(canvas.width(), _this.configs['minWidth']);
            height = Math.max(canvas.height(), _this.configs['minHeight']);
            if (isH) {//水平
                conObj.width(width * itemLen / _this.configs['showNum']);
            } else {
                conObj.height(height * itemLen / _this.configs['showNum']);
            }
        };

        _drawSize();

        $(window).resize(_drawSize);

        var selectorItem = null;

        if (_this.configs['selector']) {
            var selectorObj = $(this).find(_this.configs['selector']);
            selectorItem = selectorObj.find(_this.configs['selectorItemTag']);

            if (!selectorItem.length) {
                selectorItem = '';
                for (var i = 0; i < itemLen; ++i) {
                    selectorItem += '<' + _this.configs['selectorItemTag'] + '/>';
                }
                //初始化选择器
                selectorObj.html('').append(selectorItem);
                selectorItem = selectorObj.find(_this.configs['selectorItemTag']);
            }

            selectorItem.eq(0).addClass(_this.configs['selectorCurClass']);

        }

        //移动
        var move = function (pos) {
            var toLeft = 0;
            var toTop = 0;
            curPos = 0;

            if (pos < 0) {
                pos = itemLen - _this.configs['showNum'];
            }

            if (isH) {//水平移动
                if (pos + 1 <= itemLen) {
                    toLeft = -width * pos / _this.configs['showNum'];
                    curPos = pos;
                }
            } else {
                if (pos + 1 <= itemLen) {
                    toTop = -height * pos / _this.configs['showNum'];
                    curPos = pos;
                }
            }
            //给选择器添加样式
            selectorItem && selectorItem.removeClass(_this.configs['selectorCurClass'])
                .eq(curPos).addClass(_this.configs['selectorCurClass']);
            conObj.stop().animate({
                left: toLeft,
                top: toTop
            }, {
                easing: 'easeInOutQuad',
                duration: _this.configs['duration'],
                complete: _this.configs['onChange']
            });

        };

        //移动到下一个
        var next = function () {
            move(curPos + _this.configs['showNum']);
        };

        //移动到上一个
        var prev = function () {
            move(curPos - _this.configs['showNum']);
        };

        var _autoPlayHd = null;

        //自动播放
        var play = function () {
            if (!_this.configs['autoPlay']) {
                return false;
            }

            //已经在播放了
            if (_autoPlayHd) {
                return false;
            }

            _autoPlayHd = window.setInterval(function () {
                next();
            }, _this.configs['autoPlay']);
        };

        var stop = function () {
            window.clearInterval(_autoPlayHd);
            _autoPlayHd = null;
        };

        //鼠标移入,停止自动播放
        $(swiper).mouseover(function () {
            stop();
        }).mouseout(function () {
            play();
        });

        _this.configs['autoPlay'] && play();

        selectorItem && selectorItem[_this.configs['selectorEvent']](function () {

            move($(this).index());
        });

        nextButton.click(function () {
            next();
        });
        prevButton.click(function () {
            prev();
        });
    });

};

Q.AjaxUpFile = function (sel, setting) {
    var settings = {
        'req': '',
        'target': 'ajax_upfile_frame_',
        'callback': null
    };
    $.extend(settings, setting);

    $(sel).each(
        function (i) {
            var frameName = settings.target + i;
            var frame = document.createElement('iframe');
            $(frame).attr({id: frameName, name: frameName}).appendTo('body').hide();
            $(frame).data('fileObj', this);
            $(frame).load(
                function () {
                    var data = $(this).contents().find('body').text();
                    if (!data) {
                        return false;
                    }

                    settings.callback(data, $(this).data('fileObj'));
                    return true;
                });

            var form = document.createElement('form');

            var req = $(this).data('req') ? $(this).data('req') : settings.req;

            $(form).attr(
                {
                    method: 'post',
                    action: req,
                    target: frameName,
                    enctype: 'multipart/form-data'
                }).appendTo('body').hide();

            $(this).change(form, function (event) {
                var p = $(this).parent();
                $(this).appendTo(event.data);
                $(event.data).submit();
                $(this).appendTo(p);
                $(this).val('');
            });
        });
};
Q.recursiveSelectAce = function (config) {
    var configs = {
        'selCon': '',
        'selName': '',
        'data': null,
        'defaultVal': '',
        'firstOpt': '请选择分类',
        'ajaxUrl': '',
        'deep': 0,
        'multiple': false,
        'class': '',
        'change': null
    };

    $.extend(configs, config);

    configs.defaultVal = $.isArray(configs.defaultVal) ? configs.defaultVal : configs.defaultVal.split(',');
    var createSel = function (data, level, defVal) {
        level = level || 1;
        var offset = level - 1;
        var selObj = $('select:eq(' + offset + ')', configs.selCon);

        if (!selObj[0]) {
            var _multiple = configs.multiple ? ' multiple="multiple" ' : '';
            var _class = configs['class'] ? ' class="' + configs['class'] + '" ' : '';
            var _fsop = configs.firstOpt ? ('<option value="">' + configs.firstOpt + '</option>') : '';
            var _selName = configs.selName.replace('{LEVEL}', level);
            selObj = $('<select ' + _multiple + _class + ' name="' + _selName + '" data-level="' + level + '">' + _fsop + '</select>');
            $(configs.selCon).append(selObj);
        }

        selObj[0].length = configs.firstOpt ? 1 : 0;

        $.each(data, function (i, v) {
            var _opt = $('<option value="' + v['id'] + '"' + (defVal == v['id'] ? ' selected="selected"' : '') + '>' + v['name'] + '</option>');
            v['son'] && _opt.data('son', v['son']);
            selObj.append(_opt);
        });

        selObj.change();

        return selObj;
    };

    var rmChildSel = function (offset) {
        $(configs.selCon).find('select:gt(' + offset + ')').remove();
    };

    var ajaxSel = function (pid, level) {
        $.get(configs.ajaxUrl, {pid: pid}, function (data) {
            if (!data || !data.status) {//没有数据
                rmChildSel(level - 1);
                return false;
            }
            var defVal = configs.defaultVal[level - 1];
            createSel(data.data, level, defVal);
            configs.defaultVal[level - 1] = 0;
        }, 'json');
    };

    $(configs.selCon).on('change', 'select', function (event) {

        var _id = this.value;
        var _level = $(this).data('level');
        var _curOpt = $('option[value="' + _id + '"]', this);
        var son = _curOpt.data('son');

        if (!configs.deep || configs.deep > _level) {

            if (!son && configs.ajaxUrl && _id > 0) {
                ajaxSel(_id, _level + 1);
            } else if (son) {
                createSel(son, _level + 1);
            } else {
                rmChildSel(_level - 1);
            }
        }
        configs.change && configs.change.call(this, event, this);
    });

    if (!configs.data && configs.ajaxUrl) {
        ajaxSel(0, 1);
    } else {
        createSel(configs.data, 1);

        configs.defaultVal && $(configs.defaultVal).each(
            function (i, v) {
                $(configs.selCon).find('select:eq(' + i + ')').val(v).change();
            }
        );
    }

};

Q.recursiveSelect = function (config) {
    var form;
    var $form;
    var configs = {
        'selName': '',
        'data': null,
        'defaultVal': '',
        'firstOpt': '请选择',
        'ajaxUrl': '',
        'deep': 0,
        'multiple': false,
        'change': null,
        'filter': 'region_id'
    };
    $.extend(configs, config);

    if (configs.defaultVal) {
        configs.defaultVal = JSON.parse(configs.defaultVal);
    }

    layui.use(['form'], function () {
        form = layui.form;
        $form = $('form');
        loader(configs.data, 1);
    });

    var loader = function (data, level) {
        level = level || 1;
        var offset = level - 1;
        var loadHtml = '';
        var selectIndex = -1;
        var _selName = configs.selName.replace('{LEVEL}', level);

        for (var i = 0; i < data.length; i++) {
            var select = '';
            var listCount = 0;

            if (data[i].son) {
                listCount = data[i].son.length
            }

            if (configs.defaultVal[offset] === data[i].id) {
                select = ' selected = "selected"'
                selectIndex = i
            }

            loadHtml += '<option value="' + data[i].id + '_' + listCount + '_' + i + '"' + select + '>' + data[i].name + '</option>';
        }
        //初始化省数据
        if (level === 1) {
            $form.find('select[name="' + _selName + '"]').append(loadHtml);
        } else {
            $form.find('select[name="' + _selName + '"]').html('<option value="">' + configs.firstOpt + '</option>').append(loadHtml).parent().show();
        }

        form.render();

        if (configs.defaultVal[offset + 1]) {
            loader(data[selectIndex].son, level + 1);
        }

        form.on('select(' + configs.filter[offset] + ')', function (res) {
            var value = res.value;
            var _data = value.split('_');
            var code = _data[0];
            var count = _data[1];
            var index = _data[2];
            if (configs.deep > level) {

                if (level === 1) {
                    $form.find('select[name="' + configs.selName.replace('{LEVEL}', level + 2) + '"]').html('<option value="">' + configs.firstOpt + '</option>').parent().hide();
                }

                if (count > 0) {
                    loader(data[index].son, level + 1);
                } else {
                    $form.find('select[name="' + configs.selName.replace('{LEVEL}', level + 1) + '"]').html('').parent().hide();
                }
            }
        });

        $form.find('select[name=' + configs.selName[offset] + ']').on('change', 'select', function (event) {
            configs.change && configs.change.call(this, event, this);
        });
    }
}

Q.relAjaxSelect = function (sel, config) {
    var configs = {
        rel: '',
        ajaxUrl: '',
        firstOpt: '请选择',
        defVal: {}
    };
    $.extend(configs, config);

    var self = this;

    var rel = $(sel).data('rel') || configs['rel'];
    var ajaxUrl = $(sel).data('ajax-url') || configs['ajaxUrl'];

    if (!rel || !ajaxUrl) {
        return false;
    }

    $(sel).data('rel', rel);
    $(sel).data('ajax-url', ajaxUrl);

    var _clearOption = function (sel) {
        $(sel)[0].length = configs.firstOpt ? 1 : 0;
    };

    var _createOption = function (data, sel, defVal) {
        _clearOption(sel);

        $.each(data, function (i, v) {
            var _val = v['id'] || i;
            var _text = v['name'] || v;
            var _opt = $('<option value="' + _val + '"' + (defVal == _val ? ' selected="selected"' : '') + '>' + _text + '</option>');
            _opt.data('data', v);
            $(sel).append(_opt);
        });
        $(sel).change();
        return $(sel);
    };

    var _changeFunc = function () {
        var _sel = $(this);
        var _childSel = _sel.data('rel');
        var _ajaxUrl = _sel.data('ajax-url');
        var _val = _sel.val();

        if (!$(_childSel)) {
            return;
        }
        _clearOption(_childSel);
        $.ajax({
            url: _ajaxUrl,
            data: {'val': _val},
            context: _sel,
            dataType: 'json',
            success: function (data) {
                var _relSel = $(this).data('rel');
                var _childSel = $(_relSel);
                var _defVal = configs.defVal[_relSel] || '';

                if (data && data['status'] == 1) {
                    _createOption(data['data'], _childSel, _defVal);
                }
                _childSel.trigger("chosen:updated");
            }
        });

    };

    $(sel).on('change', _changeFunc);
    $(sel).change();
};