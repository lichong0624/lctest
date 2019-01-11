<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 *
 * @var $_content
 * @var $__PAGE_SCRIPT__
 */
?>
    <!--JS的外部引用有问题，待处理-->
<?=$_content?>

<?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_FOOTER, true)?>
<?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_FOOTER, true)?>
    <script>
        layui.use(['form'], function () {
            layui.form.render();
        });
    </script
<?=$__PAGE_SCRIPT__?>
