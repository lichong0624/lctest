
var titles = new Array("PC端网页支付", "移动端网页支付", "用户扫码支付", "商户扫码支付", "手机APP支付", "公众号支付", "服务窗支付", "聚合码支付", "即将上线更多产品");
var contents = new Array("电脑上打开电商网站进行付款时，出现支付宝扫码，微信扫码，银联扫码以及银联网页支付等。",
    "不依赖APP等媒介，手机网页也可以拥有多种支付方式，如微信H5支付、支付宝H5支付、银联快捷等支付方式。",
    "用户选择自己喜欢的支付方式扫码完成支付，支付方式的定向选择，用户的方便就是你的方便。",
    "使用扫码枪或者POS设备扫描用户的二维码，完成收款。现阶段各大商超，医院商店都采用这种方式。",
    "手机APP内，接入多种支付方式，方便用户选择自己喜欢的支付方式。",
    "在新媒体盛行的时代，公众号内就选公众号支付。",
    "支付宝推出服务窗，各种外卖小吃都会入驻，外卖点单完成付款，选择支付宝服务窗支付就对了",
    "一“码”多用，微信，支付宝，银联，QQ ，京东各种主流支付方式集于一“码”",
    "你需要的支付方式都在此，不要错过！");
$(function () {
    $(".aggregate-itemLink").hover(function (e) {
        var id = e.currentTarget.id;
        $('.label-des-title').text(titles[id]);
        $('.label-des-content').text(contents[id]);
    }, function (e) {

    });

});
