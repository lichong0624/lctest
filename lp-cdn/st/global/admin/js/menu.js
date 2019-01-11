
$('.side-menu').on('click', function () {
    var _self = $(this);
    var _li   = _self.parents('li');
    if (_li.hasClass('layui-nav-itemed')) {
        _li.removeClass('layui-nav-itemed');
    } else {
        _li.addClass('layui-nav-itemed');
    }
});

$('.nav-menu .layui-nav-item a').on('click', function () {
    var id = $(this).data('id');
    $('.sidebar-menu').hide();
    $('.sidebar-menu-' + id).show();
    $(this).parents('.nav-menu').find('li').removeClass('layui-this');
    $(this).parent('li').addClass('layui-this');
});

$('.side-menu').hover(function () {
    $(this).addClass('side-menu-hover');
}, function () {
    $(this).removeClass('side-menu-hover');
});

$('.side-menu').on('click', function () {
    $(this).parent().siblings().find('.side-menu').removeClass('side-menu-choose');
    $(this).addClass('side-menu-choose');
    $(this).parent().find('.layui-nav-child dd').removeClass('layui-this');
});

$('.layui-nav-child dd').on('click', function () {
    $(this).parents('.sidebar-menu').find('.layui-nav-child dd').removeClass('layui-this');
    $(this).addClass('layui-this');
});