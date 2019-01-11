<?php
$form = $vali->form;
$form->setJsValidate(true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新闻后台</title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
</head>
<style>
    .layui-table{
        table-layout:fixed;
    }
    .layui-table td{
        width:200px;
        word-break:keep-all;/* 不换行 */
        white-space:nowrap;/* 不换行 */
        overflow:hidden;/* 内容超出宽度时隐藏超出部分的内容 */
        text-overflow:ellipsis;
    }
    .add-form select {
         position: absolute;
         left: 0;
        height:40px;
          top:4px;
         padding: 5px 0;
         z-index: 899;
         min-width: 100%;
          border: 1px solid #d2d2d2;
          max-height: 300px;
          overflow-y: auto;
          background-color: #fff;
          border-radius: 2px;
          box-shadow: 0 2px 4px rgba(0,0,0,.12);
         box-sizing: border-box;
     }
</style>
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
                    <dd><a href="/?c=Default&a=BasicData">基本信息</a></dd>
                    <dd><a href="/?c=Default&a=ChangePwd">修改密码</a></dd>
<!--                    <dd><a href="">安全设置</a></dd>-->
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
                        <dd class="layui-this"><a href="/?c=List&a=Admin">文章管理</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">用户管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="/?c=User&a=Index">用户列表</a></dd>

                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">管理员管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="/?c=Admin&a=Index">管理员列表</a></dd>

                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <div class="layui-body">
        <form class="add-form" action="" method="post">
            <?=$form->input('id', null, 'hidden')?>
            <input name="e" value="exec" type="hidden"/>
            <div class="layui-form-item">
                <label class="layui-form-label"><?=$form->name('news_name')?></label>
                <div class="layui-input-block">
                    <?=$form->input('news_name', ['class' => 'layui-input'])?>
                    <?=$form->error('news_name')?>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><?=$form->name('news_author')?></label>
                <div class="layui-input-block">
                    <?=$form->input('news_author', ['class' => 'layui-input'])?>
                    <?=$form->error('news_author')?>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><?=$form->name('news_time')?></label>
                <div class="layui-input-block">
                    <?=$form->input('news_time', ['class' => 'layui-input', 'type' => 'date', 'style' => 'width:200px;'])?>
                    <?=$form->error('news_time')?>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><?=$form->name('class_id')?></label>
                <div class="layui-input-block">
                    <?=$form->select('class_id', $class)?>
                    <?=$form->error('class_id')?>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label"><?=$form->name('news_content')?></label>
                <div class="layui-input-block">
                    <?=$form->textarea('news_content', ['class' => 'layui-input','style' => 'height:200px;width:1000px;'])?>
                    <?=$form->error('news_content')?>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" type="submit">立即提交</button>
                </div>
            </div>
        </form>
    <div class="layui-footer">
    </div>
</div>
<script>
    var validator = new Q.plugin.validator('.add-form');
</script>
</body>
</html>
