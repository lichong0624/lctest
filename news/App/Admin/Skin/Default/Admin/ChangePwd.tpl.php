<?php
$vali->form->setJsValidate(true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>新闻后台</title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">新闻后台</div>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                    贤心
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="/?c=Admin&a=BasicData">基本信息</a></dd>
                    <dd><a href="/?c=Admin&a=ChangePwd">修改密码</a></dd>

                </dl>
            </li>
            <li class="layui-nav-item"><a href="/?c=Default&a=Exit">退了</a></li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                <li class="layui-nav-item layui-nav-itemed">
                    <a class="" href="javascript:;">新闻管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="/?c=Class&a=Index">分类管理</a></dd>
                        <dd><a href="/?c=List&a=Admin">文章管理</a></dd>
                        <!--                        <dd><a href="javascript:;">列表三</a></dd>-->
                        <!--                        <dd><a href="">超链接</a></dd>-->
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">用户管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="/?c=User&a=Index">用户列表</a></dd>
                        <dd><a href="javascript:;">列表二</a></dd>
                    </dl>
                </li>
                <!--                <li class="layui-nav-item"><a href="">用户</a></li>-->
                <!--                <li class="layui-nav-item"><a href="">发布商品</a></li>-->
            </ul>
        </div>
    </div>
    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div class="layui-btn-group">

        </div>
        <div style="padding: 15px;">
            <form class="add-form" action="" method="post">
                <input name="e" value="exec" type="hidden"/>
                <div class="layui-form-item">
                    <label class="layui-form-label"><?=$vali->form->name('admin')?></label>
                    <div class="layui-input-block">
                        <?=$vali->form->input('admin', ['class' => 'layui-input'])?>
                        <?=$vali->form->error('admin')?>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><?=$vali->form->name('old_password')?></label>
                    <div class="layui-input-block">
                        <?=$vali->form->input('old_password', ['class' => 'layui-input'])?>
                        <?=$vali->form->error('old_password')?>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><?=$vali->form->name('password')?></label>
                    <div class="layui-input-block">
                        <?=$vali->form->input('password', ['class' => 'layui-input'])?>
                        <?=$vali->form->error('password')?>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><?=$vali->form->name('confirm_password')?></label>
                    <div class="layui-input-block">
                        <?=$vali->form->input('confirm_password', ['class' => 'layui-input'])?>
                        <?=$vali->form->error('confirm_password')?>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" type="submit">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    layui.use('element', function(){
        var element = layui.element;
    });


    var validator = new Q.plugin.validator('.add-form');
</script>
</body>
</html>
