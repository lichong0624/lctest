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
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <!--        <ul class="layui-nav layui-layout-left">-->
        <!--            <li class="layui-nav-item"><a href="">控制台</a></li>-->
        <!--            <li class="layui-nav-item"><a href="">商品管理</a></li>-->
        <!--            <li class="layui-nav-item"><a href="">用户</a></li>-->
        <!--            <li class="layui-nav-item">-->
        <!--                <a href="javascript:;">其它系统</a>-->
        <!--                <dl class="layui-nav-child">-->
        <!--                    <dd><a href="">邮件管理</a></dd>-->
        <!--                    <dd><a href="">消息管理</a></dd>-->
        <!--                    <dd><a href="">授权管理</a></dd>-->
        <!--                </dl>-->
        <!--            </li>-->
        <!--        </ul>-->
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
                        <!--                        <dd><a href="javascript:;">列表三</a></dd>-->
                        <!--                        <dd><a href="">超链接</a></dd>-->
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
        <div class="layui-btn-group">
            <a href="/?c=Class&a=Add"><button class="layui-btn">增加</button></a>
        </div>
        <div style="padding: 15px;"><table class="layui-table">
                <colgroup>
                    <col width="100">
                    <col width="150">

                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>分类</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php if (isset($data)&&!empty($data)) {?>
                <tbody>
                <?php foreach ($data as $k => $v){?>
                <tr>
                    <td><?php echo $v['id']?></td>
                    <td><?php echo $v['class_name']?></td>
                    <td>
                        <a href="/?c=Class&a=EditClass&id=<?php echo $v['id']?>"><button class="layui-btn">编辑</button></a>
                        <a href="/?c=Class&a=DeleClass&id=<?php echo $v['id']?>"><button class="layui-btn">删除</button></a>
                    </td>
                </tr>
                <?php }?>
                </tbody>
                <?php }?>
            </table>
        </div>
    </div>
</div>
<script>
    //JavaScript代码区域
    layui.use('element', function(){
        var element = layui.element;
    });
</script>
</body>
</html>
