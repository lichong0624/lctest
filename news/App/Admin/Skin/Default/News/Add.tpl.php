<?php
/**
 * @var $vali Q_Validate
 */

$form = $vali->form;
$form->setJsValidate(true);
?>
<head>
<meta charset="utf-8">
<?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
<?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>

<head/>
<form class="add-form" action="" method="post">
    <input name="e" value="exec" type="hidden">
    <div class="layui-form-item">
        <label class="layui-form-label"><?=$form->name('news_name')?></label>
        <div class="layui-input-inline">
                <?=$form->input('news_name', ['class' => 'layui-input'])?>
                <?=$form->error('news_name')?>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?=$form->name('news_content')?></label>
        <div class="layui-input-inline">
               <?=$form->textArea('news_content', ['class' => 'layui-input' ,'style' => 'height:300px;width:600px;'])?>
               <?=$form->error('news_content')?>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?=$form->name('news_author')?></label>
        <div class="layui-input-inline">
               <?=$form->input('news_author', ['class' => 'layui-input'])?>
               <?=$form->error('news_author')?>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?=$form->name('news_time')?></label>
        <div class="layui-input-inline">
               <?=$form->input('news_time', ['class' => 'layui-input', 'type' => 'date'])?>
               <?=$form->error('news_time')?>
        </div>
    </div>
    <div class="layui-form-item">
        <button type="submit">提交</button>
    </div>
</form>
<script type="text/javascript">
    var validator = new Q.plugin.validator('.add-form');
</script>
