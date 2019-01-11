<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
    <!--        <link rel="stylesheet" href="http://demo1.mycodes.net/daima/hongse_calendar/css/calendar.css">-->
    <style>
        *{
            padding:0;
            margin:0;
            text-decoration:none;
        }
        a{
            color:black;
        }
        .return-top{
            font-size:14px;
            display:block;
            width:30px;
            height:36px;
            position:fixed;
            right:170px;
            bottom:120px;
            background-color:#F9F9f9;
        }
        .new{
            width:100%;
        }
        .new-header{
            width:1180px;
            height:80px;
            margin:0 auto;
            border:1px solid black;
            /*background-color:#00274e;*/
        }
        .new-header-left{
            width:18%;
            height:100%;
            display:inline-block;
            float:left;
            font-size:38px;
            text-align:center;
            line-height:80px;
        }
        .new-header-right{
            width:20%;
            height:100%;
            /*border:1px solid black;*/
            float:right;
        }
        .new-header-right-p{
            font-size:18px;
            margin-left:60px;
        }
        .new-header-right-p > a{
            color:black;
        }
        /*.new-header-right-p > a:hover{*/
        /*color:#034081;*/
        /*}*/
        .new-neck{
            width:1180px;
            height:36px;
            margin:10px auto;
            /*border:1px solid red;*/
        }
        .new-neck-ul{
            margin:0;
            padding:0;
            height:100%;
            border-top:2px solid dimgray;
            list-style-type:none;
            background-color:#00274e;
        }
        .new-neck-ul > li:last-child{
            border-right:0px;
        }
        .new-neck-ul > li{
            border-right:1px solid #F2F2F2;
            text-align:center;
            width:60px;
            display:inline-block;
        }
        .new-neck-ul a{
            color:#FFF;
            line-height:40px;
        }
        .new-neck-ul a:hover{
            color:red;
        }
        .new-body{
            border-top:1px;
            display:flex;
            justify-content:space-between;
            border:1px solid red;
            width:1180px;
            margin:auto;
            border-top:1px solid dimgray;
        }
        .new-body-left{
            width:800px;
            border-right:1px solid #F9F9F9;
            /*border:1px solid red;*/
        }
        .new-body-l{
            float:left;
            margin:20px 10px;
            width:380px;
            height:360px;
            border-bottom:1px dashed #000;
        }
        .new-body-l > p{
            text-align:center;
            background-color:#f2f2f2;
            margin:0;
            padding-left:10px;
            font-size:24px;
            color:black;
        }
        .tt{
            display:inline-block;
            width:4px;
            height:16px;
            border-radius:15px;
            background-color:#0a53a3;
        }
        .new-body-cont{
            margin-left:20px;
            width:350px;

            /*!*margin-top:10px;*!padding-top:;*/
        }
        .new-body-cont > li{
            height:28px;
            line-height:35px;
        }
        .new-body-cont a{
            color:#333;
            font-size:18px;
        }
        .new-body-cont a:hover{
            color:#034081;
            text-decoration:underline;
        }
        .new-body-right{
            width:375px;
        }
        .new-body-r{
            background-color:#f9f9f9;
            margin:20px 10px 10px 15px;
        }
        .new-body-r > p{
            padding-left:10px;
            height:38px;
            line-height:38px;
            font-size:16px;
            color:black;
            /*background-color:#F9F9F9;*/
        }
        .new-body-r-u{
            width:350px;
            margin-top:1px;

        }
        .new-body-r-u > li{
            margin-left:10px;
            text-overflow:ellipsis;
            overflow:hidden;
            white-space:nowrap;
            height:30px;
            line-height:36px;
            border-bottom:2px solid #FFF;
        }
        .new-body-r-u a{
            font-size:16px;
            color:#333;
        }
        .new-body-r-u a:hover{
            color:darkgreen;
        }
    </style>
</head>
<body>
<div class="new">
    <div class="new-header">
        <div class="new-header-left">
            <em>新闻</em>
        </div>
        <div class="new-header-right">
            <input type="hidden" id="user" value="<?php echo $user['id']?>">
            <?php if (isset($user) && !empty($user)) { ?>
                <p class="new-header-right-p">
                    <a href="/?c=Login&a=Up">退出</a>
                    <?php echo $user['username']; ?>
                </p>
            <?php } else { ?>
                <!--                    <span class="denl"><a href="/?c=Login&a=Default"></a></span>-->
                <p class="new-header-right-p">
                    <a href="/?c=Login&a=Default">登录注册</a>
                </p>
            <?php } ?>
        </div>
    </div>
    <div class="new-neck">
        <?php if (isset($class) && !empty($class)) { ?>
            <ul class="new-neck-ul">
                <?php foreach ($class as $k => $v) { ?>
                    <li><a href=""><?php echo $v; ?></a></li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
    <div class="new-body">
        <div class="new-body-left">
            <?php if (isset($data) && !empty($data)) { ?>
                <?php foreach ($data as $k => $v) { ?>
                    <div class="new-body-l">
                        <p><span class="tt"></span><a href=""><?php echo $v['class_name']; ?></a></p>
                        <div style="height:10px;"></div>
                        <?php foreach ($v['content'] as $a => $c) { ?>
                            <ul class="new-body-cont">
                                <li>
                                    <a href="/?c=New&a=Content&id=<?php echo $c['id']; ?>"><?php echo $c['news_name']; ?></a>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="new-body-l">
                <p><span class="tt"></span><a href=""></a></p>
                <ul class="new-body-cont">
                    <li><a href="">金句来了！习近平这话为发展指明方向</a></li>
                    <li><a href="">国际媒体头条速览：特朗普一炸向退役陆军上将</a></li>
                    <li><a href="">国际媒普新年第一炮，炸向退役陆军上将</a></li>
                    <li><a href="">国际媒体头条速览：特朗役陆军上将</a></li>
                    <li><a href="">国际媒体第一炮，炸向退役陆军</a></li>
                    <li><a href="">国际媒新年第88888888一炮，退役陆军上将</a></li>
                    <li><a href="">国际媒体头年第一炮，炸向退役陆军上将</a></li>
                    <li><a href="">国际媒体头条速览：特朗普新一炮上将</a></li>
                </ul>

            </div>
            <div class="new-body-l">
                <p><span class="tt"></span><a href=""></a></p>
                <ul class="new-body-cont">
                    <li><a href="">金句来了！习近平这话为发展指明方向</a></li>
                    <li><a href="">国际媒体头条速览：特朗普一炸向退役陆军上将</a></li>
                    <li><a href="">国际媒普新年第一炮，炸向退役陆军上将</a></li>
                    <li><a href="">国际媒体头条速览：特朗役陆军上将</a></li>
                    <li><a href="">国际媒体第一炮，炸向退役陆军</a></li>
                    <li><a href="">国际媒新年第88888888一炮，退役陆军上将</a></li>
                    <li><a href="">国际媒体头年第一炮，炸向退役陆军上将</a></li>
                    <li><a href="">国际媒体头条速览：特朗普新一炮上将</a></li>
                </ul>

            </div>


        </div>
        <div class="new-body-right">
            <div class="new-body-r">
                <p style="border-bottom:2px solid #fff">
                    <img src="https://img.icons8.com/material/24/000000/asterisk.png"><a href="">我的收藏</a></p>

                <ul class="new-body-r-u">
                    <?php if (isset($collect) && !empty($collect)) { ?>
                        <?php foreach ($collect as $k => $v) { ?>
                            <li><?php echo $v['news_name']; ?></li>
                        <?php } ?>
                    <?php } else { ?>
                        <?php echo '快去收藏吧'; ?>
                    <?php }?>
                </ul>
            </div>
            <div class="new-body-release">
                <p>我发布的</p>
                <ul>
                    <li></li>
                    <li class="release">前去发布</li>
                </ul>
            </div>
            <div class="return-top"><em>返回<br>顶部</em></div>
        </div>

    </div>
</div>
<script>
    $(function () {
        $('.return-top').click(function () {
            $('html , body').animate({scrollTop : 0}, 'slow');
        });

        $('.release').click(function(){
            var userId = $('#user').val();
            // console.log(userId);
            if (userId) {
               $.ajax({
                   url:'/?c=Release&a=Index'
               })
            }else {
                alert('请登录！');
            }

        });
    });



</script>
</body>
</html>
