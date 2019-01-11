<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 *
 * @var $conf
 * @var $_content
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?=$conf['APP_NAME']?></title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
</head>
<body>
<div class="qf-layout qf-layout-iframe">
    <!-- 侧边栏结束 -->
    <div class="qf-layout-content">
        <?=$_content?>
    </div>

</div>
<?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_FOOTER, true)?>
<?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_FOOTER, true)?>
<script>
    var windowWidth  = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;

     $(".qf-layout-content").height(windowHeight - 40).width(windowWidth - 40).show();
    layui.use(['layer'], function () {
        layer.config({
            skin : 'layui-layer-molv',
            anim : 0
        });
    });
</script>
</body>
</html>
