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
                        <dd class="layui-this"><a href="/?c=Class&a=Index">分类管理</a></dd>
                        <dd><a href="/?c=List&a=Admin">文章管理</a></dd>
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
        <!-- 内容主体区域 -->
        <form class="layui-form" action="./?c=Class&a=AddData" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">分类名称</label>
                <div class="layui-input-block">
                    <input type="text" name="class_name" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn" lay-filter="formDemo">立即提交</button>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-footer">
        <!-- 底部固定区域 -->
    </div>
</div>
<script>
    layui.use('form', function(){s

        var form = layui.form;
        //监听提交
        form.on('submit(formDemo)', function(data){

            return false;
        });
    });


    $(function).on
</script>
</body>
</html>
