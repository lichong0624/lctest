$(function () {

    //登录切换
    $(".item_tab li").click(function () {
        $(this).addClass("on").siblings().removeClass("on");
    });
    //返回顶部
    $(".contact").hover(
        function () {
            $(".tel_num").fadeIn(100);
        },
        function () {
            $(".tel_num").fadeOut(100);
        }
    );
    $(".weixin").hover(
        function () {
            $(".weixin_ewm").fadeIn(100);
        },
        function () {
            $(".weixin_ewm").fadeOut(100);
        }
    );
    $(".returntop").click(function () {
        $("html,body").animate({scrollTop : "0"}, 300);
    });
});