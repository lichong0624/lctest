/**
 * Created by hanqiang on 16-11-9.
 */
/**
 *删除数组指定下标或指定对象
 */

var _LP = _LP || {};
_LP.$ || (_LP.$ = function (e)
{
    return document.getElementById(e);
});
_LP.$$ || (_LP.$$ = function (e)
{
    return document.getElementsByTagName(e);
});
_LP.$$$ || (_LP.$$$ = function (e)
{
    return document.getElementsByClassName(e);
});

_LP.common       = _LP.common || {};
_LP.common.cache = {};
_LP.common.doc   = document;
_LP.common.win   = window;
_LP.common.nav   = navigator;
_LP.common.ua    = _LP.common.nav.userAgent;
_LP.common.av    = _LP.common.nav.appVersion;
_LP.common.pf    = _LP.common.nav.platform;

var ec = encodeURIComponent,
    dc = decodeURIComponent;

//md5
_LP.common.md5 = {
    hexcase      : 0,
    hexMd5       : function (a)
    {
        return this.rstr2hex(this.rstrMd5(this.str2rstrUtf8(a)));
    },
    str2rstrUtf8 : function (c)
    {
        var b = "";
        var d = -1;
        var a, e;
        while (++d < c.length) {
            a = c.charCodeAt(d);
            e = d + 1 < c.length ? c.charCodeAt(d + 1) : 0;
            if (55296 <= a && a <= 56319 && 56320 <= e && e <= 57343) {
                a = 65536 + ((a & 1023) << 10) + (e & 1023);
                d++
            }
            if (a <= 127) {
                b += String.fromCharCode(a)
            } else {
                if (a <= 2047) {
                    b += String.fromCharCode(192 | ((a >>> 6) & 31), 128 | (a & 63))
                } else {
                    if (a <= 65535) {
                        b += String.fromCharCode(224 | ((a >>> 12) & 15), 128 | ((a >>> 6) & 63), 128 | (a & 63))
                    } else {
                        if (a <= 2097151) {
                            b += String.fromCharCode(240 | ((a >>> 18) & 7), 128 | ((a >>> 12) & 63), 128 | ((a >>> 6) & 63), 128 | (a & 63))
                        }
                    }
                }
            }
        }
        return b
    },
    rstrMd5      : function (a)
    {
        return this.binl2rstr(this.binlMd5(this.rstr2binl(a), a.length * 8))
    },
    rstr2hex     : function (c)
    {
        try {
            this.hexcase
        } catch (g) {
            this.hexcase = 0
        }
        var f = this.hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
        var b = "";
        var a;
        for (var d = 0; d < c.length; d++) {
            a = c.charCodeAt(d);
            b += f.charAt((a >>> 4) & 15) + f.charAt(a & 15)
        }
        return b
    },
    rstr2binl    : function (b)
    {
        var a = Array(b.length >> 2);
        for (var c = 0; c < a.length; c++) {
            a[c] = 0
        }
        for (var c = 0; c < b.length * 8; c += 8) {
            a[c >> 5] |= (b.charCodeAt(c / 8) & 255) << (c % 32)
        }
        return a
    },
    binlMd5      : function (p, k)
    {
        p[k >> 5] |= 128 << ((k) % 32);
        p[(((k + 64) >>> 9) << 4) + 14] = k;
        var o                           = 1732584193;
        var n                           = -271733879;
        var m                           = -1732584194;
        var l                           = 271733878;
        for (var g = 0; g < p.length; g += 16) {
            var j = o;
            var h = n;
            var f = m;
            var e = l;
            o     = this.md5ff(o, n, m, l, p[g + 0], 7, -680876936);
            l     = this.md5ff(l, o, n, m, p[g + 1], 12, -389564586);
            m     = this.md5ff(m, l, o, n, p[g + 2], 17, 606105819);
            n     = this.md5ff(n, m, l, o, p[g + 3], 22, -1044525330);
            o     = this.md5ff(o, n, m, l, p[g + 4], 7, -176418897);
            l     = this.md5ff(l, o, n, m, p[g + 5], 12, 1200080426);
            m     = this.md5ff(m, l, o, n, p[g + 6], 17, -1473231341);
            n     = this.md5ff(n, m, l, o, p[g + 7], 22, -45705983);
            o     = this.md5ff(o, n, m, l, p[g + 8], 7, 1770035416);
            l     = this.md5ff(l, o, n, m, p[g + 9], 12, -1958414417);
            m     = this.md5ff(m, l, o, n, p[g + 10], 17, -42063);
            n     = this.md5ff(n, m, l, o, p[g + 11], 22, -1990404162);
            o     = this.md5ff(o, n, m, l, p[g + 12], 7, 1804603682);
            l     = this.md5ff(l, o, n, m, p[g + 13], 12, -40341101);
            m     = this.md5ff(m, l, o, n, p[g + 14], 17, -1502002290);
            n     = this.md5ff(n, m, l, o, p[g + 15], 22, 1236535329);
            o     = this.md5gg(o, n, m, l, p[g + 1], 5, -165796510);
            l     = this.md5gg(l, o, n, m, p[g + 6], 9, -1069501632);
            m     = this.md5gg(m, l, o, n, p[g + 11], 14, 643717713);
            n     = this.md5gg(n, m, l, o, p[g + 0], 20, -373897302);
            o     = this.md5gg(o, n, m, l, p[g + 5], 5, -701558691);
            l     = this.md5gg(l, o, n, m, p[g + 10], 9, 38016083);
            m     = this.md5gg(m, l, o, n, p[g + 15], 14, -660478335);
            n     = this.md5gg(n, m, l, o, p[g + 4], 20, -405537848);
            o     = this.md5gg(o, n, m, l, p[g + 9], 5, 568446438);
            l     = this.md5gg(l, o, n, m, p[g + 14], 9, -1019803690);
            m     = this.md5gg(m, l, o, n, p[g + 3], 14, -187363961);
            n     = this.md5gg(n, m, l, o, p[g + 8], 20, 1163531501);
            o     = this.md5gg(o, n, m, l, p[g + 13], 5, -1444681467);
            l     = this.md5gg(l, o, n, m, p[g + 2], 9, -51403784);
            m     = this.md5gg(m, l, o, n, p[g + 7], 14, 1735328473);
            n     = this.md5gg(n, m, l, o, p[g + 12], 20, -1926607734);
            o     = this.md5hh(o, n, m, l, p[g + 5], 4, -378558);
            l     = this.md5hh(l, o, n, m, p[g + 8], 11, -2022574463);
            m     = this.md5hh(m, l, o, n, p[g + 11], 16, 1839030562);
            n     = this.md5hh(n, m, l, o, p[g + 14], 23, -35309556);
            o     = this.md5hh(o, n, m, l, p[g + 1], 4, -1530992060);
            l     = this.md5hh(l, o, n, m, p[g + 4], 11, 1272893353);
            m     = this.md5hh(m, l, o, n, p[g + 7], 16, -155497632);
            n     = this.md5hh(n, m, l, o, p[g + 10], 23, -1094730640);
            o     = this.md5hh(o, n, m, l, p[g + 13], 4, 681279174);
            l     = this.md5hh(l, o, n, m, p[g + 0], 11, -358537222);
            m     = this.md5hh(m, l, o, n, p[g + 3], 16, -722521979);
            n     = this.md5hh(n, m, l, o, p[g + 6], 23, 76029189);
            o     = this.md5hh(o, n, m, l, p[g + 9], 4, -640364487);
            l     = this.md5hh(l, o, n, m, p[g + 12], 11, -421815835);
            m     = this.md5hh(m, l, o, n, p[g + 15], 16, 530742520);
            n     = this.md5hh(n, m, l, o, p[g + 2], 23, -995338651);
            o     = this.md5ii(o, n, m, l, p[g + 0], 6, -198630844);
            l     = this.md5ii(l, o, n, m, p[g + 7], 10, 1126891415);
            m     = this.md5ii(m, l, o, n, p[g + 14], 15, -1416354905);
            n     = this.md5ii(n, m, l, o, p[g + 5], 21, -57434055);
            o     = this.md5ii(o, n, m, l, p[g + 12], 6, 1700485571);
            l     = this.md5ii(l, o, n, m, p[g + 3], 10, -1894986606);
            m     = this.md5ii(m, l, o, n, p[g + 10], 15, -1051523);
            n     = this.md5ii(n, m, l, o, p[g + 1], 21, -2054922799);
            o     = this.md5ii(o, n, m, l, p[g + 8], 6, 1873313359);
            l     = this.md5ii(l, o, n, m, p[g + 15], 10, -30611744);
            m     = this.md5ii(m, l, o, n, p[g + 6], 15, -1560198380);
            n     = this.md5ii(n, m, l, o, p[g + 13], 21, 1309151649);
            o     = this.md5ii(o, n, m, l, p[g + 4], 6, -145523070);
            l     = this.md5ii(l, o, n, m, p[g + 11], 10, -1120210379);
            m     = this.md5ii(m, l, o, n, p[g + 2], 15, 718787259);
            n     = this.md5ii(n, m, l, o, p[g + 9], 21, -343485551);
            o     = this.safeAdd(o, j);
            n     = this.safeAdd(n, h);
            m     = this.safeAdd(m, f);
            l     = this.safeAdd(l, e)
        }
        return Array(o, n, m, l)
    },
    binl2rstr    : function (b)
    {
        var a = "";
        for (var c = 0; c < b.length * 32; c += 8) {
            a += String.fromCharCode((b[c >> 5] >>> (c % 32)) & 255)
        }
        return a
    },
    md5ff        : function (g, f, k, j, e, i, h)
    {
        return this.md5Cmn((f & k) | ((~f) & j), g, f, e, i, h)
    },
    md5gg        : function (g, f, k, j, e, i, h)
    {
        return this.md5Cmn((f & j) | (k & (~j)), g, f, e, i, h)
    },
    md5hh        : function (g, f, k, j, e, i, h)
    {
        return this.md5Cmn(f ^ k ^ j, g, f, e, i, h)
    },
    md5ii        : function (g, f, k, j, e, i, h)
    {
        return this.md5Cmn(k ^ (f | (~j)), g, f, e, i, h)
    },
    md5Cmn       : function (h, e, d, c, g, f)
    {
        return this.safeAdd(this.bitRol(this.safeAdd(this.safeAdd(e, h), this.safeAdd(c, f)), g), d)
    },
    safeAdd      : function (a, d)
    {
        var c = (a & 65535) + (d & 65535);
        var b = (a >> 16) + (d >> 16) + (c >> 16);
        return (b << 16) | (c & 65535)
    },
    bitRol       : function (a, b)
    {
        return (a << b) | (a >>> (32 - b))
    }
};

//加载SCRIPT
_LP.common.loadScript = function (url, callback)
{
    var head    = _LP.$$('head')[0];
    var script  = document.createElement('script');
    script.type = 'text/javascript';
    script.src  = url;

    script.onload = script.onreadystatechange = function ()
    {
        if ((!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) {
            callback && callback();

            script.onload = script.onreadystatechange = null;

            if (head && script.parentNode) {
                head.removeChild(script);
            }
        }
    };

    head.insertBefore(script, head.firstChild);
};

//设置COOKIE
_LP.common.setCookie = function (n, v, t, p)
{
    var minute = t;
    if (isNaN(minute)) {
        minute = 0;
    }
    var exp = new Date();
    if (minute > 0) {
        exp.setTime(exp.getTime() + minute * 60 * 1000);
    }
    if (!p || p == "undefined") {
        p = "/";
    }

    _LP.common.doc.cookie = n + "=" + escape(v)
        + ( ( minute > 0 ) ? ";expires=" + exp.toGMTString() : "" )
        + ( ( p == null ) ? "" : ";path=" + p);
};

//读取COOKIE
_LP.common.getCookie = function (n)
{
    var arr, reg = new RegExp("(^| )" + n + "=([^;]*)(;|$)");

    if (arr = _LP.common.doc.cookie.match(reg)) {
        return unescape(arr[2]);
    } else {
        return null;
    }
};

//创建Ajax
_LP.common.createAjax = function ()
{
    var xhr = null;
    try {
        xhr = new ActiveXObject("microsoft.xmlhttp");
    } catch (e1) {
        try {
            xhr = new XMLHttpRequest();
        } catch (e2) {
            return;
        }
    }
    return xhr;
};

_LP.common.ajax = function (conf)
{
    var type     = conf.type;
    var url      = conf.url;
    var data     = conf.data;
    var dataType = conf.dataType;
    var success  = conf.success;
    if (type == null) {
        type = "get";
    }
    if (dataType == null) {
        dataType = "text";
    }
    var xhr = _LP.common.createAjax();
    xhr.open(type, url, true);
    if (type == "GET" || type == "get") {
        xhr.send(null);
    } else if (type == "POST" || type == "post") {
        xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
        xhr.send(data);
    }
    xhr.onreadystatechange = function ()
    {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (dataType == "text" || dataType == "TEXT") {
                if (success != null) {
                    success(xhr.responseText);
                }
            } else if (dataType == "xml" || dataType == "XML") {
                if (success != null) {
                    success(xhr.responseXML);
                }
            } else if (dataType == "json" || dataType == "JSON") {
                if (success != null) {
                    success(eval("(" + xhr.responseText + ")"));
                }
            }
        }
    };
};

//获取随机数字
_LP.common.getRandomInt = function (min, max)
{
    return Math.floor(Math.random() * (max - min + 1)) + min;
};

_LP.common.inArray = function (needle, array, bool)
{
    if (typeof needle == "string" || typeof needle == "number") {
        var len = array.length;
        for (var i = 0; i < len; i++) {
            if (needle === array[i]) {
                if (bool) {
                    return i;
                }
                return true;
            }
        }
        return false;
    }
};

_LP.common.jsonFlip = function (json)
{
    if (!json) {
        return {};
    }
    var data = {};
    for (var key in json) {

        for (var inKey in json[key]) {
            if (!data[json[key][inKey]]) {
                data[json[key][inKey]] = [];
            }
            data[json[key][inKey]].push(key);
        }
    }

    return data;
};

//解析URL参数
_LP.common.urlEncode = function (param, key, encode)
{
    if (param == null) {
        return '';
    }

    var paramStr = '';
    var t        = typeof (param);
    if (t == 'string' || t == 'number' || t == 'boolean') {
        paramStr += '&' + key + '=' + ((encode == null || encode) ? ec(param) : param);
    } else {
        for (var i in param) {
            var k = key == null ? i : key + (param instanceof Array ? '[' + i + ']' : '.' + i);
            paramStr += _LP.common.urlEncode(param[i], k, encode);
        }
    }
    return paramStr;
};

//base64
_LP.common.base64 = {
    /* private property*/
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    /* public method for encoding */
    encode : function (input)
    {
        var output = "";

        if (window.btoa) {
            output = window.btoa(input);
        } else {
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = this._utf8_encode(input);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                    Base64._keyStr.charAt(enc1) + Base64._keyStr.charAt(enc2) +
                    Base64._keyStr.charAt(enc3) + Base64._keyStr.charAt(enc4);
            }
        }
        return output;
    },

    /* private method for UTF-8 encoding */
    _utf8_encode : function (string)
    {
        string      = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    }
};

//截取
_LP.common.subString = function (str, len, hasDot)
{
    var newL   = 0;
    var newStr = "";
    var cr     = /[^\x00-\xff]/g;
    var sc     = "";
    var strL   = str.replace(cr, "**").length;
    for (var i = 0; i < strL; i++) {
        sc = str.charAt(i).toString();
        if (sc.match(cr) != null) {
            newL += 2;
        }
        else {
            newL++;
        }
        if (newL > len) {
            break;
        }
        newStr += sc;
    }

    if (hasDot && strL > len) {
        newStr += "...";
    }
    return newStr;
};

//客户端类方法
_LP.common.browser = {
    versions    : function ()
    {
        return {
            trident : _LP.common.ua.indexOf('Trident') > -1,
            presto  : _LP.common.ua.indexOf('Presto') > -1,
            webKit  : _LP.common.ua.indexOf('AppleWebKit') > -1,
            gecko   : _LP.common.ua.indexOf('Gecko') > -1 && _LP.common.ua.indexOf('KHTML') == -1,
            mobile  : !!_LP.common.ua.match(/AppleWebKit.*Mobile.*/) || !!_LP.common.ua.match(/AppleWebKit/),
            ios     : !!_LP.common.ua.match(/\(i[^;]+;( _LP.common.ua;)? CPU.+Mac OS X/),
            android : _LP.common.ua.toLowerCase().indexOf('android') > -1,
            iPhone  : _LP.common.ua.indexOf('iPhone') > -1,
            iPad    : _LP.common.ua.indexOf('iPad') > -1,
            webApp  : _LP.common.ua.indexOf('Safari') == -1
        };
    }(),
    height      : function ()
    {
        var winHeight = 0;

        if (_LP.common.win.innerHeight)
            winHeight = _LP.common.win.innerHeight;
        else if ((_LP.common.doc.body) && (_LP.common.doc.body.clientHeight))
            winHeight = _LP.common.doc.body.clientHeight;

        if (_LP.common.doc.documentElement && _LP.common.doc.documentElement.clientHeight && _LP.common.doc.documentElement.clientWidth) {
            winHeight = _LP.common.doc.documentElement.clientHeight;
        }

        return winHeight;
    }(),
    width       : function ()
    {
        var winWidth = 0;

        if (_LP.common.win.innerWidth)
            winWidth = _LP.common.win.innerWidth;
        else if ((_LP.common.doc.body) && (_LP.common.doc.body.clientWidth))
            winWidth = _LP.common.doc.body.clientWidth;

        if (_LP.common.doc.documentElement && _LP.common.doc.documentElement.clientHeight && _LP.common.doc.documentElement.clientWidth) {
            winWidth = _LP.common.doc.documentElement.clientWidth;
        }

        return winWidth;
    }(),
//鼠标位置
    getMousePos : function (event)
    {

    }()
};

//合并数组
_LP.common.extend = function (o, n)
{
    for (var key in n) {
        o[key] = n[key]
    }
    return o;
}

//异步获取省市
var hasArea     = false;
_LP.common.area = function (callback, params)
{
    if (!document || !document.body || !document.body.insertBefore) {
        return setTimeout(arguments.callee, 50);
    }

    if (params == "undefined" || !params) {
        params = {};
    }

    var country  = _LP.common.getCookie("__LP_country");
    var province = _LP.common.getCookie("__LP_province");
    var city     = _LP.common.getCookie("__LP_city");
    var res      = "";

    if (!province) {
        _LP.common.loadScript("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js", function ()
        {
            if (hasArea == false) {
                country  = remote_ip_info.country;
                province = remote_ip_info.province;
                city     = remote_ip_info.city;

                _LP.common.setCookie("__LP_country", country);
                _LP.common.setCookie("__LP_province", province);
                _LP.common.setCookie("__LP_city", city);

                res = callback && callback(_LP.common.extend(params, {cou : country, pro : province, city : city}));

                hasArea = true;

                return res;
            }

        });

        setTimeout(function ()
        {
            if (hasArea == false) {
                res     = callback && callback(params);
                hasArea = true;
            }

        }, 500);

    } else {
        res     = callback && callback(_LP.common.extend(params, {cou : country, pro : province, city : city}));
        hasArea = true;
    }

    return res;
};

//初始化信息类
_LP.common.getInfo = function (sid, aid, ps)
{
    this.sid = sid || "0";
    this.uid = this.readCookie("__LP_UID") || this.getUid();
    this.aid = aid || "0";

    this.outps = ps || "";
    this.ucn   = this.uid;
    this.pcn   = this.uid;
    this.ps    = {
        sid  : this.sid,//计费ID
        uid  : this.uid,//客户端UID
        aid  : this.aid,//广告ID
        vid  : "",//客户端view ID
        rf   : "",//refeter
        lh   : "",//href
        hs   : "",//host
        sw   : -1,//宽
        sh   : -1,//高
        scd  : 1,//色彩度
        sc   : "",//分辨率
        je   : 1,//javaEnabled
        cke  : 1,//cookieEnabled
        hist : -1,//history
        os   : "",//系统
        br   : "",//浏览器
        sv   : "0",//Flash版本
        ure  : 0,
        are  : 0,
        vre  : 0
    };
    this.ured  = null;
    this.ared  = null;
    this.init();
};

_LP.common.getInfo.prototype = {
    init          : function ()
    {
        try {
            this._ips_()
        } catch (ex) {
            console.log(ex);
        }
    },
    _ips_         : function ()
    {
        try {
            var w  = _LP.common.win || window;
            var ws = w.screen;
            var wn = w.navigator;

            this.ps.lh = w.location.href || "";
            this.ps.hs = w.location.host || "";
            this.ps.rf = w.document.referrer || "";

            if (ws) {
                this.ps.sw  = _LP.common.browser.width;
                this.ps.sh  = _LP.common.browser.height;
                this.ps.scd = ws.colorDepth;
                this.ps.sc  = this.getResolution();
            }
            if (wn && typeof wn.javaEnabled == "boolean") {
                this.ps.je = wn.javaEnabled ? 1 : 0
            }
            if (wn && typeof wn.cookieEnabled == "boolean") {
                this.ps.cke = wn.cookieEnabled ? 1 : 0
            }
            if (w.history) {
                this.ps.hist = w.history.length
            }

            this.ps.os = this.getOs();
            this.ps.br = this.getBrowse();
            this.ps.sv = this.swfver();

            this.setCookie("__LP_UID", this.uid, 24 * 60 * 60);

            this.ured = this.readCookie("__LP_UID_RE");
            this.ared = this.readCookie("__LP_AUID_RE");

            if (this.outps.sty == 1) {
                if (this.ured && this.ured != "undefined") {
                    this.ured   = JSON.parse(this.ured);
                    this.ps.ure = this.ured.view;
                }

                if (this.ared && this.ared != "undefined") {
                    var __key = this.sid + "_" + this.aid;
                    this.ared = JSON.parse(this.ared);
                    var __num = this.ared.view[__key];

                    if (__num && __num != "undefined") {
                        this.ps.are = __num;
                        this.ps.vre = __num;
                    }
                }
            }

            if (this.outps.sty == 2) {
                this.ured = JSON.parse(this.ured);
                if (this.ured.click && this.ured.click != "undefined") {
                    this.ps.ure = this.ured.click;
                }

                if (this.ared && this.ared != "undefined") {
                    this.ared = JSON.parse(this.ared);
                }

                if (this.ared.click && this.ared.click != "undefined") {
                    var __key  = this.sid + "_" + this.aid;
                    var __vnum = this.ared.view[__key];
                    var __cnum = this.ared.click[__key];

                    if (__cnum && __cnum != "undefined") {
                        this.ps.are = __cnum;
                        this.ps.vre = __vnum - 1;
                    }
                }
            }

        } catch (ex) {
            console.log(ex);
        }
    },
    getVid        : function ()
    {
        var guid = "";
        for (var i = 1; i <= 32; i++) {
            var n = Math.floor(Math.random() * 16.0).toString(16);
            guid += n;
        }

        var vid = _LP.common.md5.hexMd5(this.sid + new Date().getTime() + guid);
        return vid;
    },
    getUid        : function ()
    {
        var urlParam = _LP.common.urlEncode(this.ps);

        var guid = "";
        for (var i = 1; i <= 32; i++) {
            var n = Math.floor(Math.random() * 16.0).toString(16);
            guid += n;
        }

        var uid = _LP.common.md5.hexMd5(urlParam + new Date().getTime() + guid);
        return uid;
    },
    readCookie    : function (n)
    {
        var a, r = new RegExp("(^| )" + n + "=([^;]*)(;|$)");
        if ((a = _LP.common.doc.cookie.match(r))) {
            return unescape(a[2])
        } else {
            return null
        }
    },
    setCookie     : function (n, v, d)
    {
        var t, e = new Date();
        if (d === 0) {
            t = ""
        }
        if (d === 1) {
            e.setHours(23);
            e.setMinutes(59);
            e.setSeconds(59);
            t = e.toGMTString()
        }
        if (d > 1) {
            e.setTime(e.getTime() + d * 1000);
            t = e.toGMTString()
        }
        _LP.common.doc.cookie = n + "=" + escape(v) + ";path=/;expires=" + t
    },
    getOs         : function ()
    {
        var os = {
            ios      : /( U;|U;)?( )?CPU.+Mac OS X/.test(_LP.common.ua),
            android       : /Android/.test(_LP.common.ua),
            wm       : /Windows CE/.test(_LP.common.ua),
            wp       : /Windows Phone/.test(_LP.common.ua) || /WP7/.test(_LP.common.ua),
            sb       : /Symbian/.test(_LP.common.ua),
            bb       : /BlackBerry/.test(_LP.common.ua) || /RIM Tablet OS/.test(_LP.common.ua),
            bada     : /Bada/.test(_LP.common.ua),
            webos    : (/WebOS/.test(_LP.common.ua)) || (/hpwOS/.test(_LP.common.ua)),
            win      : (_LP.common.pf == "Win32") || (_LP.common.pf == "Win64") || (_LP.common.pf == "Windows"),
            mac      : (_LP.common.pf == "Mac68K") || (_LP.common.pf == "MacPPC") || (_LP.common.pf == "Macintosh") || (_LP.common.pf == "MacIntel"),
            unix     : (_LP.common.pf == "X11"),
            linux    : /Linux/.test(String(_LP.common.pf)),
            winxp    : (/Windows NT 5.1/.test(_LP.common.ua)) || (/Windows XP/.test(_LP.common.ua)),
            win7     : (/Windows NT 6.1/.test(_LP.common.ua)) || (/Windows 7/.test(_LP.common.ua)),
            win8     : (/Windows NT 6.2/.test(_LP.common.ua)) || (/Windows 8/.test(_LP.common.ua)),
            winvista : (/Windows NT 6.0/.test(_LP.common.ua)) || (/Windows Vista/.test(_LP.common.ua)),
            win98    : (/Win98/.test(_LP.common.ua)) || (/Windows 98/.test(_LP.common.ua)),
            win2k    : (/Windows NT 5.0/.test(_LP.common.ua)) || (/Windows 2000/.test(_LP.common.ua)),
            win2003  : (/Windows NT 5.2/.test(_LP.common.ua)) || (/Windows 2003/.test(_LP.common.ua)),
            win95    : (/Win95/.test(_LP.common.ua)) || (/Windows 95/.test(_LP.common.ua)),
            winme    : (/Win 9x 4.90/.test(_LP.common.ua)) || (/Windows ME/.test(_LP.common.ua)),
            winnt4   : (/WinNT/.test(_LP.common.ua)) || (/Windows NT/.test(_LP.common.ua)) || (/WinNT4.0/.test(_LP.common.ua)) || (/Windows NT 4.0/.test(_LP.common.ua))
        };
        try {
            if (os.ios) {
                return "ios";
            }
            if (os.android) {
                return "android";
            }
            if (os.wm) {
                return "windows ce";
            }
            if (os.wp) {
                return "windows phone";
            }
            if (os.sb) {
                return "symbian";
            }
            if (os.bb) {
                return "blackberry";
            }
            if (os.bada) {
                return "bada";
            }
            if (os.webos) {
                return "webos";
            }
            if (os.mac) {
                return "mac";
            }
            if (os.unix && !os.win && !os.mac) {
                return "unix";
            }
            if (os.linux) {
                return "linux";
            }
            if (os.winxp) {
                return "winxp";
            }
            if (os.win7) {
                return "win7";
            }
            if (os.win8) {
                return "win8";
            }
            if (os.winvista) {
                return "winvista";
            }
            if (os.win98) {
                return "win98";
            }
            if (os.win2k) {
                return "win2k";
            }
            if (os.win2003) {
                return "win2003";
            }
            if (os.win95 || os.winme || os.winnt4 && !os.winme && !os.win2k && !os.winxp) {
                return "win";
            }
        } catch (ex) {
        }
        return "";
    },
    getBrowse     : function ()
    {
        var ver = {
            ie5      : /MSIE 5\.0/.test(_LP.common.ua),
            ie5_5    : /MSIE 5\.5/.test(_LP.common.ua),
            ie6      : !/MSIE 7\.0/.test(_LP.common.ua) && /MSIE 6\.0/.test(_LP.common.ua) && !/MSIE 8\.0/.test(_LP.common.ua) && !/MSIE 9\.0/.test(_LP.common.ua),
            ie7      : !/MSIE 6\.0/.test(_LP.common.ua) && /MSIE 7\.0/.test(_LP.common.ua) && !/MSIE 8\.0/.test(_LP.common.ua) && !/MSIE 9\.0/.test(_LP.common.ua),
            ie8      : !/MSIE 6\.0/.test(_LP.common.ua) && !/MSIE 7\.0/.test(_LP.common.ua) && /MSIE 8\.0/.test(_LP.common.ua) && !/MSIE 9\.0/.test(_LP.common.ua),
            ie9      : !/MSIE 6\.0/.test(_LP.common.ua) && !/MSIE 7\.0/.test(_LP.common.ua) && !/MSIE 8\.0/.test(_LP.common.ua) && /MSIE 9\.0/.test(_LP.common.ua),
            ie10     : !/MSIE 6\.0/.test(_LP.common.ua) && !/MSIE 7\.0/.test(_LP.common.ua) && !/MSIE 8\.0/.test(_LP.common.ua) && !/MSIE 9\.0/.test(_LP.common.ua) && /MSIE 10\.0/.test(_LP.common.ua),
            ie11     : /Trident\/7.0;(.*) rv:11.0/.test(_LP.common.ua),
            ee       : e(),
            se       : s("suffixes", "dll", "description", /fancy/),
            sg       : / SE/.test(_LP.common.ua),
            lb       : /LBBROWSER/.test(_LP.common.ua),
            qb       : /QQBrowser/.test(_LP.common.ua),
            cr       : /Chrome/.test(_LP.common.ua),
            sf       : /Safari/.test(_LP.common.ua),
            mt       : /Maxthon/.test(_LP.common.ua),
            uc       : /UCWEB/.test(_LP.common.ua) || /UCBrowser/.test(_LP.common.ua),
            ff       : /Firefox/.test(_LP.common.ua),
            wd       : /TheWorld/.test(_LP.common.ua) || /theworld/.test(_LP.common.ua),
            op       : /Opera/.test(_LP.common.ua) || /OPR/.test(_LP.common.ua),
            tt       : /TencentTraveler/.test(_LP.common.ua),
            bd       : /BIDUBrowser/.test(_LP.common.ua),
            tb       : /TaoBrowser/.test(_LP.common.ua),
            cn       : /CoolNovo/.test(_LP.common.ua),
            av       : /Avant/.test(_LP.common.ua),
            ls       : /LSIE/.test(_LP.common.ua) || /GreenBrowser/.test(_LP.common.ua),
            sy       : /SaaYaa/.test(_LP.common.ua),
            sgm      : /SogouMSE/.test(_LP.common.ua) || /SogouMobileBrowser/.test(_LP.common.ua),
            opm      : /Opera Mini/.test(_LP.common.ua) || /Opera Tablet/.test(_LP.common.ua),
            gg       : window.google || window.chrome,
            isMobile : /AppleWebKit.*Mobile.*/.test(_LP.common.ua)
        };

        function s(x, B, w, A)
        {
            var z = _LP.common.nav.mimeTypes,
                y;
            try {
                for (y in z) {
                    if (z[y][x] == B) {
                        if (A.test(z[y][w])) {
                            return true
                        }
                    }
                }
            } catch (e) {
                return false
            }
            return false
        }

        function e()
        {
            if (/chrome/.test(_LP.common.nav.userAgent.toLowerCase())) {
                var desc = "";
                if (_LP.common.nav.mimeTypes["application/x-shockwave-flash"]) {
                    desc = _LP.common.nav.mimeTypes["application/x-shockwave-flash"].description.toLowerCase();
                }
                if (/adobe/.test(desc)) {
                    return true
                }
            }
        }

        try {
            if (ver.sg && !ver.isMobile) {
                return "se";
            }
            if (ver.lb) {
                return "lbbrowser";
            }
            if (ver.qb) {
                return "qqbrowser";
            }
            if (ver.mt) {
                return "maxthon";
            }
            if (ver.wd) {
                return "theworld";
            }
            if (ver.op && !ver.opm) {
                return "opera";
            }
            if (ver.bd) {
                return "bidubrowser";
            }
            if (ver.tb) {
                return "taobrowser";
            }
            if (ver.cn) {
                return "coolnovo";
            }
            if (ver.ls) {
                return "lsie";
            }
            if (ver.sy) {
                return "saayaa";
            }
            if (ver.av) {
                return "avant";
            }
            if (ver.tt) {
                return "tencent";
            }
            if (ver.ie5) {
                return "ie5";
            }
            if (ver.ie5_5) {
                return "ie5_5";
            }
            if (ver.ie6) {
                return "ie6";
            }
            if (ver.ie7) {
                return "ie7";
            }
            if (ver.ie8) {
                return "ie8";
            }
            if (ver.ie9) {
                return "ie9";
            }
            if (ver.ie10) {
                return "ie10";
            }
            if (ver.se) {
                return "suffixes";
            }
            if (ver.ee) {
                return "shockwave";
            }
            if (ver.uc) {
                return "ucweb";
            }
            if (ver.opm) {
                return "opera mini";
            }
            if (ver.sgm && ver.isMobile) {
                return "sogoumse mobile";
            }
            if (ver.cr && !!ver.gg && ver.isMobile) {
                return "chrome mobile";
            }
            if (ver.ff) {
                return "firefox";
            }
            if (ver.cr && !!ver.gg && !ver.isMobile) {
                return "chrome";
            }
            if (ver.sf && !ver.gg) {
                return "safari";
            }
            if (ver.ie11) {
                return "ie11";
            }
        } catch (ex) {
            console.log(ex);
        }
        return "";
    },
    swfver        : function ()
    {
        if (navigator.plugins && navigator.mimeTypes.length) {
            var b = navigator.plugins["Shockwave Flash"];
            if (b && b.description)
                return b.description.replace(/([a-zA-Z]|\s)+/, "").replace(/(\s)+r/, ".")
        } else {
            var c = null;
            try {
                c = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7")
            } catch (e) {
                var a = 0;
                try {
                    c                   = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
                    a                   = 6;
                    c.AllowScriptAccess = "always"
                } catch (e) {
                    if (a == 6)
                        return a.toString()
                }
                ;
                try {
                    c = new ActiveXObject("ShockwaveFlash.ShockwaveFlash")
                } catch (e) {
                }
            }
            ;
            if (c != null) {
                var a = c.GetVariable("$version").split(" ")[1];
                return a.replace(/,/g, ".")
            }
        }
        ;
        return "0"
    },
    getResolution : function ()
    {
        var sw = _LP.common.win.screen.availWidth;
        var sh = _LP.common.win.screen.availHeight;

        return sw + 'x' + sh;
    },
    getPs         : function (params)
    {
        if (params && params != "undefined") {
            _LP.common.extend(this.ps, params);
        }

        if (this.outps && this.outps != "undefined") {
            _LP.common.extend(this.ps, this.outps);
        }

        var urlParam = _LP.common.urlEncode(this.ps);
        urlParam     = urlParam.substring(1, urlParam.length);

        return urlParam;
    },
    getSign       : function ()
    {

    }
};

//排序数组(目前只做了倒序)
_LP.common.sortObj = function (arrayObj)
{
    var arr = [];
    for (var i in arrayObj) {
        arr.push([arrayObj[i], i]);
    }

    arr.sort(function (a, b)
    {
        return b[0] - a[0];
    });

    var len   = arr.length,
        array = [];
    for (var i = 0; i < len; i++) {
        array.push(arr[i][1]);
    }
    return array;
};

//求数组差集
_LP.common.minus = function (arr1, arr2)
{
    var arr3 = [];
    for (var i = 0; i < arr1.length; i++) {
        var flag = true;
        for (var j = 0; j < arr2.length; j++) {
            if (arr2[j] == arr1[i]) {
                flag = false;
            }
        }
        if (flag) {
            arr3.push(arr1[i]);
        }
    }
    return arr3;
};

//获取参数
_LP.common.getRequest = function ()
{
    var url = location.search; //获取url中"?"符后的字串

    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs    = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    return theRequest;
};

//统计
_LP.common.getStats      = function (params)
{
    var o     = new _LP.common.getInfo(_LP.sid, params.aid, params);
    var param = o.getPs();

    var showTime = (new Date()).getTime();
    var r        = _LP.common.getRandomInt(10000, 99999);

    var _params = param + '&t=' + showTime + r;

    var _img_document           = document.createElement("img");
    _img_document.border        = 0;
    _img_document.style.display = "none";
    _img_document.width         = 1;
    _img_document.height        = 1;
    _img_document.src           = 'http://' + _LP.STATS_DOMAIN + params.url + _params;
    _LP.$$("head")[0].appendChild(_img_document);
    return param;
};
_LP.common.getViewStats  = function (params)
{
    params.sty   = 1;
    var __params = _LP.common.getStats(params);

    var uidRe  = _LP.common.getCookie("__LP_UID_RE");
    var auidRe = _LP.common.getCookie("__LP_AUID_RE");

    if (uidRe && uidRe != "undefined") {
        this.ured      = JSON.parse(uidRe);
        this.ured.view = 1;
    } else {
        this.ured = {view : 1};
    }

    var __key = _LP.sid + "_" + params.aid;
    if (auidRe && auidRe != "undefined") {
        this.ared = JSON.parse(auidRe);

        if (!this.ared.view[__key] || this.ared.view[__key] == "undefined") {
            this.ared.view[__key] = 1;
        } else {
            this.ared["view"][__key] += 1;
        }
    } else {
        this.ared                = {"view" : {}};
        this.ared["view"][__key] = 1;
    }

    this.ured && _LP.common.setCookie("__LP_UID_RE", JSON.stringify(this.ured), 24 * 60 * 60);
    this.ared && _LP.common.setCookie("__LP_AUID_RE", JSON.stringify(this.ared), 24 * 60 * 60);

    return __params;
};
//点击统计
_LP.common.getClickStats = function (params)
{
    params.sty   = 2;
    var __params = _LP.common.getStats(params);

    var uidRe  = _LP.common.getCookie("__LP_UID_RE");
    var auidRe = _LP.common.getCookie("__LP_AUID_RE");

    if (uidRe && uidRe != "undefined") {
        this.ured       = JSON.parse(uidRe);
        this.ured.click = 1;
    } else {
        this.ured = {click : 1};
    }

    var __key = _LP.sid + "_" + params.aid;

    if (auidRe && auidRe != "undefined") {
        this.ared = JSON.parse(auidRe);

        if (!this.ared.click || this.ared.click == "undefined") {
            this.ared.click           = {};
            this.ared["click"][__key] = 1;
        } else {
            if (!this.ared.click[__key] || this.ared.click[__key] == "undefined") {
                this.ared.click[__key] = 1;
            } else {
                this.ared["click"][__key] += 1;
            }
        }
    }

    _LP.common.setCookie("__LP_UID_RE", JSON.stringify(this.ured), 24 * 60 * 60);
    _LP.common.setCookie("__LP_AUID_RE", JSON.stringify(this.ared), 24 * 60 * 60);

    return __params;
};

_LP.common.getRates = function (data)
{
    if (!data) {
        return [];
    }
    var keys      = [];
    var values    = [];
    var totalRate = 0;
    for (var key in data) {
        keys.push(parseInt(key));
        var _rate = Math.round(data[key] * 100);
        values.push(_rate);
        totalRate += _rate;
    }
    return {"keys" : keys, "values" : values, "totalRate" : totalRate};
};

_LP.common.getRand = function (data)
{

    if (!data) {
        return 0;
    }
    var data = _LP.common.getRates(data);

    var data   = data["keys"];
    var rates = data["values"];
    if (data.length == 1) {
        return data.pop();
    }
    var id        = 0;
    var totalRate = data["totalRate"];
    if (totalRate == 0) {
        var _idx = _LP.common.getRandomInt(0, data.length - 1);
        return data[_idx];
    }

    var random = _LP.common.getRandomInt(0, totalRate);
    var _max   = 0, _min = 0;
    for (var i = 0; i < rates.length; i++) {
        _max += rates[i] + 1;
        if (random >= _min && random < _max) {
            id = data[i];
            break;
        }
        _min = _max;
    }

    return id;
};