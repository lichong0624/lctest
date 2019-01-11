<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新闻后台</title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
</head>
<style>
    .layu-co{
        overflow:hidden;
    }
    .layu-car{
        width:260px;
        height:200px;
        margin:30px 20px 20px 20px;
        box-shadow: 0 0 10px 10px #f2f2f2;
        border:1px solid #eee;
        float: left;

    }
    .layu-car-header{
        position: relative;
        height: 60px;
        line-height: 60px;
        padding: 0 15px;
        border-bottom: 2px solid #f2f2f2;
        color: #333;
        border-radius: 2px 2px 0 0;
        font-size: 24px;
    }
    .layu-car-header>span{
        top: 50%;
        margin-top: -9px;
        position: absolute;
        right: 26px;
        height: 20px;
        line-height: 20px;
        display: inline-block;
        padding: 0 6px;
        font-size: 14px;
        text-align: center;
        cursor:move;
        border-radius: 2px;
        background-color: #1E9FFF!important;
    }
    .layu-car-footer{
        /*border:1px solid black;*/
        position: relative;
        margin: 0;
    }
    .layu-car-footer>span{
        font-size: 24px;
        position: absolute;
        top: 86px;
        right: 20px;
    }
    .layu-text{
        font-size: 52px;
        color: #666;
        line-height: 100%;
        padding:32px 20px 0;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-all;
        white-space: nowrap;
    }
    .a{
        border:1px solid red;
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
            <div class="layu-co">
                <div class="layu-car">
                    <div class="layu-car-header">
                        <p>文章数</p>
                        <span><a href="/?c=List&a=Admin">详情</a></span>
                    </div>
                    <div class="layu-car-footer">
                        <p class="layu-text">
                            <?php echo empty($artnum) ?  '暂无数据' : $artnum;?>
                        </p>
                        <span>篇</span>
                    </div>
                </div>
                <div class="layu-car">
                    <div class="layu-car-header">
                        <p>用户数</p>
                        <span><a href="">详情</a></span>
                    </div>
                    <div class="layu-car-footer">
                        <p class="layu-text">
                            <?php echo empty($usrnum) ?  '暂无数据' : $usrnum;?>
                        </p>
                        <span>人</span>
                    </div>
                </div>
                <div class="layu-car">
                    <div class="layu-car-header">
                        <p>收藏数</p>
                        <span><a href="">详情</a></span>
                    </div>
                    <div class="layu-car-footer">
                        <p class="layu-text">
                            <?php echo empty($colnum) ?  '暂无数据' : $colnum;?>
                        </p>
                        <span>篇</span>
                    </div>
                </div>
            </div>
        <div class="a"></div>
    </div>
    <div class="layui-footer">

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
