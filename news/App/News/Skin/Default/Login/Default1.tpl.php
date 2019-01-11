<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>登录界面</title>
    <!---->
    <!--    <link rel="stylesheet" type="text/css" href="http://demo1.mycodes.net/houtai/login-reg/css/bootstrap.min.css">-->
    <!--    <link rel="stylesheet" type="text/css" href="http://demo1.mycodes.net/houtai/login-reg/css/style.css">-->
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <!--    <script type="text/javascript" async="" src="ht tps://dup.baidustatic.com/dup/ui/painter/bottomSearchBar.js"></script>-->
    <!--    <script src="https://hm.baidu.com/hm.js?2f8eec1c22efc7bd5da0c3b8301ff3ef"></script>-->
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
    <!--    <script src="http://demo1.mycodes.net/houtai/login-reg/js/jquery-1.7.2.min.js" type="text/javascript"></script>-->
    <script type="text/javascript">
        function userName()
        {
            var userName = $('#name_r').val();
            var url = '/?c=Login&a=Enroll';
            $.ajax({
                url  : url,
                data : {
                    userName : userName,
                },
                type     : 'post',
                dataType : 'json',
                success  : function (data) {
                    if (data.status == 1) {
                        alert('用户名已存在')
                    }
                },
                error    : function () {
                    alert('userName');
                }
            });

        }




        function barter_btn(bb)
        {
            $(bb).parent().parent().fadeOut(1000);
            $(bb).parent().parent().siblings().fadeIn(2000);
        }

    </script>
</head>

<body class="login_body">
<div class="login_div">
    <div class="col-xs-12 login_title">登录</div>
    <form action="/?c=Login&a=Login" class="login" method="post">

        <div class="nav">
            <div class="nav login_nav">
                <div class="col-xs-4 login_username">
                    用户名:
                </div>
                <div class="col-xs-6 login_usernameInput">
                    <input type="text" name="username" id="name" required value="" placeholder="&nbsp;&nbsp;用户名" ">
                </div>
                <div class="col-xs-1 ok_gou">
                    √
                </div>
                <div class="col-xs-1 error_cuo">
                    ×
                </div>
            </div>
            <div class="nav login_psdNav">
                <div class="col-xs-4">
                    密&nbsp;&nbsp;&nbsp;码:
                </div>
                <div class="col-xs-6">
                    <input type="password" name="password" id="psd" value="" required placeholder="&nbsp;&nbsp;密码" ">
                </div>
                <div class="col-xs-1 ok_gou">
                    √
                </div>
                <div class="col-xs-1 error_cuo">
                    ×
                </div>
            </div>
            <div class="col-xs-12 login_btn_div">
                <input type="submit" class="sub_btn" value="登录" id="login">
            </div>
        </div>
    </form>

    <div class="col-xs-12 barter_btnDiv">
        <button class="barter_btn" onclick="javascript:barter_btn(this)">没有账号?前往注册</button>
    </div>
</div>

<div class="register_body">
    <div class="col-xs-12 register_title">注册</div>
    <form action="/?c=Login&a=Enroll" class="register" method="post">

        <div class="nav">
            <div class="nav register_nav">
                <div class="col-xs-4">
                    用户名:
                </div>
                <div class="col-xs-6">
                    <input type="text" name="username" id="name_r" required value="" placeholder="&nbsp;&nbsp;用户名" onblur="userName()">
                </div>
                <div class="col-xs-1 ok_gou">
                    √
                </div>
                <div class="col-xs-1 error_cuo">
                    ×
                </div>
            </div>
            <div class="nav register_psdnav">
                <div class="col-xs-4">
                    密&nbsp;&nbsp;&nbsp;码:
                </div>
                <div class="col-xs-6">
                    <input type="password" name="password" id="psd_r" required value="" placeholder="&nbsp;&nbsp;密码">
                </div>
                <div class="col-xs-1 ok_gou">
                    √
                </div>
                <div class="col-xs-1 error_cuo">
                    ×
                </div>
            </div>
            <div class="nav register_affirm">
                <div class="col-xs-4">
                    确认密码:
                </div>
                <div class="col-xs-6">
                    <input type="password" name="passwordto" id="affirm_psd" required value="" placeholder="&nbsp;&nbsp;确认密码" onblur="userName()">
                </div>
                <div class="col-xs-1 ok_gou">
                    √
                </div>
                <div class="col-xs-1 error_cuo">
                    ×
                </div>
            </div>
            <div class="col-xs-12 register_btn_div">
                <input type="submit" class="sub_btn" value="注册" id="register">
            </div>
        </div>
    </form>
    <div class="col-xs-12 barter_register">
        <button class="barter_registerBtn" onclick="javascript:barter_btn(this)" style="">已有秘籍?返回登录</button>
    </div>
</div>

</body>
</html>
