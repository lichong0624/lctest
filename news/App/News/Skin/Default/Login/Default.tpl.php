<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>登录界面</title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_CSS, Q_ClientScript::POS_HEADER, true)?>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
    <style>
        .error{
            color:red;
            float:left;
            margin-top:13px;
        }
    </style>
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
            </div>
            <div class="nav login_psdNav">
                <div class="col-xs-4">
                    密&nbsp;&nbsp;&nbsp;码:
                </div>
                <div class="col-xs-6">
                    <input type="password" name="password" id="psd" value="" required placeholder="&nbsp;&nbsp;密码" ">
                </div>

            </div>
            <div class="col-xs-12 login_btn_div">
                <input type="submit" class="sub_btn" value="登录" id="login">
            </div>
        </div>
    </form>

    <div class="col-xs-12 barter_btnDiv">
        <button class="barter_btn barter">没有账号?前往注册</button>
    </div>
</div>

<div class="register_body">
    <div class="col-xs-12 register_title">注册</div>
    <form action="/?c=Login&a=Enroll" class="register" id="registe" method="post">

        <div class="nav">
            <div class="nav register_nav">
                <div class="col-xs-4">
                    用户名:
                </div>
                <div class="col-xs-6">
                    <input type="text" name="username" id="name_r" required value="" placeholder="&nbsp;&nbsp;用户名" onblur="userName()">
                </div>
                <span id="name_error" style="display:none;">用户名已存在!</span>
            </div>
            <div class="nav register_psdnav">
                <div class="col-xs-4">
                    密&nbsp;&nbsp;&nbsp;码:
                </div>
                <div class="col-xs-6">
                    <input type="password" name="password" id="psd_r" required value="" placeholder="&nbsp;&nbsp;密码" onblur="psdN()">
                </div>
            </div>
            <div class="nav register_affirm">
                <div class="col-xs-4">
                    确认密码:
                </div>
                <div class="col-xs-6">
                    <input type="password" name="passwordt" id="affirm_psd" required value="" placeholder="&nbsp;&nbsp;确认密码" onblur="passwordT()">
                </div>
                <span id="psd_error" style="display:none;">两次密码不一致</span>
            </div>
            <div class="col-xs-12 register_btn_div">
                <input type="button" class="sub_btn" value="注册" id="register">
            </div>
        </div>
    </form>
    <div class="col-xs-12 barter_register">
        <button class="barter_registerBtn" style="">已有秘籍?返回登录</button>
    </div>
</div>
</body>
<script type="text/javascript">
    //判断用户名是否已存在
    function userName()
    {
        var username = $('#name_r').val();
        console.log(username);
        var url = '/?c=Login&a=UserEnroll';
        $.ajax({
            url      : url,
            data     : {
                username : username,
            },
            type     : 'post',
            dataType : 'json',
            success  : function (data) {
                if (data.status == 1) {
                    $("#name_error").addClass('error').show();
                }
                if (data.status == 0) {
                    $("#name_error").removeClass('error').hide();
                }
            }
        });
    }

    //密码
    function psdN()
    {
        var password  = $('#psd_r').val();
        var passwordT = $('#affirm_psd').val();
        if (!password) {
            alert(1122);
            return false;
        }
        if (password != passwordT) {
            $("#psd_error").addClass('error').show();
        }
        if (password == passwordT) {
            $("#psd_error").removeClass('error').hide();
        }
    }

    function passwordT()
    {
        var password  = $('#psd_r').val();
        var passwordT = $('#affirm_psd').val();
        if (password != passwordT) {
            $("#psd_error").addClass('error').show();

        }
        if (password == passwordT) {
            $("#psd_error").removeClass('error').hide();
        }
    }
    //提交
    $("#register").click(function () {

        $("#registe").submit();
    });

    //登录注册切换
    $(".barter").click(function(){
        $('.login_div').fadeOut(2000).hide();
        $('.register_body').fadeIn(2000).show();
    });

    $(".barter_registerBtn").click(function(){
        $('.login_div').fadeOut(2000).show();
        $('.register_body').fadeIn(2000).hide();
    });
</script>
</html>
