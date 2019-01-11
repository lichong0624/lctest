<?php
/**
 * @var $vali Q_Validate
 */

$form = $vali->form;
$form->setJsValidate(true);
?>
<form action="?/c=do" method="post">
    <input name="e" value="exec" type="hidden"/>
    <div>
        <span>新闻标题</span>
        <input type="test" value="" name="title">
    </div>
    <div>
        <span>新闻内容</span>
        <textarea rows="10" cols="30" name="content"></textarea>
    </div>
    <div>
        <span>发布时间</span>
        <input type="date" value="" name="time">
    </div>
    <div>
        <span>作者</span>
        <input type="test" value="" name="author">
    </div>
    <button type="submit">提交</button>
</form>
