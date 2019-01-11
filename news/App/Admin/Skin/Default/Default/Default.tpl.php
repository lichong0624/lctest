<?php

$form = $vali->form;
$form->setJsValidate(true);
?>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>登录</title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
</head>
<style>
</style>
<body>
<div id="particles-js">
    <div class="login" style="display: block;">
        <div class="login-top">
            登录
        </div>
        <form class="admin-form" action="" method="post">
            <input name="e" value="exec" type="hidden">
        <div class="login-center clearfix">
            <div class="login-center-img"><img src="http://st.lp-cdn.com/global/admin/images/name.png"></div>
            <div class="login-center-input">
                <?= $form->input('admin', [ 'name' => 'admin', 'placeholder' => '请输入您的用户名', 'onfocus' => "this.placeholder=''", 'onblur' => "this.placeholder='请输入您的用户名'", 'type' => 'text'])?>
<!--  <input type="text" name="admin" value="" placeholder="请输入您的用户名" onfocus="this.placeholder=''" onblur="this.placeholder='请输入您的用户名'">-->
                <div class="login-center-input-text">用户名</div>
                <span><?= $form->error('admin')?></span>
            </div>
        </div>
        <div class="login-center clearfix">
            <div class="login-center-img"><img src="http://st.lp-cdn.com/global/admin/images/password.png"></div>
            <div class="login-center-input">
                <?= $form->input('password', [ 'name' => 'password', 'placeholder' => '请输入您的密码', 'onfocus' => "this.placeholder=''", 'onblur' => "this.placeholder='请输入您的密码'", 'type' => 'password'])?>
<!--                <input type="password" name="password" value="" placeholder="请输入您的密码" onfocus="this.placeholder=''" onblur="this.placeholder='请输入您的密码'">-->
                <div class="login-center-input-text">密码</div>
                <span><?= $form->error('password')?></span>
            </div>
        </div>
        <div class="login-button">
            <input type="submit" id="submit" value="Login">
        </div>
        </form>
    </div>
    <div class="sk-rotating-plane"></div>
<!-- scripts -->
<script src="http://demo1.mycodes.net/denglu/HTML5_yonghudenglu/js/particles.min.js"></script>
<script src="http://demo1.mycodes.net/denglu/HTML5_yonghudenglu/js/app.js"></script>
<script type="text/javascript">
    var validator = new Q.plugin.validator('.admin-form');
</script>
</div>
</body>
</html>

