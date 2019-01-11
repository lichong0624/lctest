<?php
$vali->form->setJsValidate(true);
?>
<head>
    <meta charset="utf-8">
    <title>新闻后台</title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
</head>
<form class="form-horizontal edit-form" action="./?c=Upload&a=UploadImage" method="post" enctype="multipart/form-data">
    <input name="e" value="exec" type="hidden"/>
    <div class="layui-input-inline">
        <label class="layui-form-label"><?=$vali->form->name('file')?></label>
        <input type='file' name='file' />
    </div>
    <div class="layui-input-inline">
        <button class="layui-btn" type="submit">立即提交</button>
    </div>
</form>
<script>


    var validator = new Q.plugin.validator('.edit-form');
</script>
