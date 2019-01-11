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
        word-break:keep-all; /* 不换行 */
        white-space:nowrap; /* 不换行 */
        overflow:hidden; /* 内容超出宽度时隐藏超出部分的内容 */
        text-overflow:ellipsis;
    }
    .add-form select{
        position:absolute;
        left:0;
        height:40px;
        top:4px;
        padding:5px 0;
        z-index:899;
        min-width:100%;
        border:1px solid #d2d2d2;
        max-height:300px;
        overflow-y:auto;
        background-color:#fff;
        border-radius:2px;
        box-shadow:0 2px 4px rgba(0, 0, 0, .12);
        box-sizing:border-box;
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
            <ul class="layui-nav layui-nav-tree" lay-filter="test">
                <li class="layui-nav-item">
                    <a class="" href="javascript:;">新闻管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="/?c=Class&a=Index">分类管理</a></dd>
                        <dd><a href="/?c=List&a=Admin">文章管理</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item layui-nav-itemed">
                    <a href="javascript:;">用户管理</a>
                    <dl class="layui-nav-child">
                        <dd class="layui-this"><a href="/?c=User&a=Index">用户列表</a></dd>

                    </dl>
                </li>
                <li class="layui-nav-item ">
                    <a href="javascript:;">管理员管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="/?c=Admin&a=Index">管理员列表</a></dd>

                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <div class="layui-body">
        <div style="padding: 15px;">
            <table class="layui-table">
                <thead>
                <tr>
                    <th >序号</th>
                    <th>账号</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php if (isset($data)&&!empty($data)) {?>
                    <tbody>
                    <?php foreach ($data as $k => $v){?>
                        <tr>
                            <td><?php echo $v['id']?></td>
                            <td><?php echo $v['username']?></td>
                            <td>
                                <a href="javascript:;"><button class="layui-btn">详情</button></a>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                <?php }?>
            </table>
        </div>
        <div class="layui-footer">
        </div>
    </div>
    <script>
        layui.use('element', function(){
            var element = layui.element;

        });
    </script>
</body>
</html>
