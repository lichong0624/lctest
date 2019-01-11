function setContentWidthAndHeight()
{
    var windowWidth  = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    $(".qf-layout-content").height(windowHeight - 121).width(windowWidth - 220).show();
}

setContentWidthAndHeight();

$(window).resize(function () {
    setContentWidthAndHeight();
});

layui.use(['form', 'element', 'layer'], function () {
    var layer = layui.layer;
    layer.config({
        skin : 'layui-layer-molv',
        anim : 0
    });

    var page = new Q.plugin.page();
    page.interceptListen();
});

if (history.pushState) {
    window.addEventListener("popstate", function () {
        var page = new Q.plugin.page();
        page.asyncLoadPage();
    });

}