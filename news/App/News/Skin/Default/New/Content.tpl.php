<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
    <style>
        *{
            padding:0px;
            margin:0px;
            list-style:none;
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
            right:460px;
            bottom:120px;
            background-color:#F9F9f9;
        }
        .content-head{
            top:0px;
            left:0px;
            width:100%;
            height:40px;
            border:1px solid black;
            background-color:#212121;
        }
        .content-head-neck{
            width:1180px;
            margin:0 auto;
            height:40px;
        }
        .content-head-ul > li{
            display:inline-block;
            height:40px;
            width:80px;
            border-right:1px solid #FFF;
            line-height:45px;
            text-align:center;
        }
        .content-head-ul a{
            color:#F2F2F2;
            font-size:16px;
        }
        /*.content-head-ul a{*/

        /*color:#F2F2F2;*/
        /*}*/
        .content-body{
            width:1180px;
            margin:0 auto;
            display:flex;
            justify-content:flex-end;
        }
        .body-left{
            width:750px;
        }
        .body-right{
            width:350px;
        }
        .text-title h1{
            margin-top:30px;
            font-size:28px;
            font-weight:700;
            line-height:38px;
            color:#191919;
        }
        .article-time{
            padding-top:15px;
            font-size:16px;
            line-height:20px;
            color:#999;
        }
        .article{
            padding:20px;
            font-size:18px;
            margin-top:30px;
            border-top:2px solid #f2f2f2;
        }
        .collect{
            margin-top:20px;
            font-size:18px;
            padding-right:80px;
            text-align:right;
        }
        .collect > span:hover{
            cursor:pointer;
            color:#b21c12;
        }
        .page-ul{
            display:flex;
            justify-content:space-around;
            margin:20px 10px;
        }
        .page-ul > li{
            display:inline-block;
            font-family:"AR PL UKai TW";
        }
        .page-ul > li:hover{
            cursor:pointer;
            color:#152890;
        }
        #error{
            font-size:48px;
            text-align:center;
        }

    </style>
</head>
<body>

<?php if ($data['id']) { ?>
<div class="content">
    <div class="content-head">
        <div class="content-head-neck">
            <ul class="content-head-ul">
                <li><a href="">首页</a></li>
                <?php if (isset($class) && !empty($class)) { ?>
                    <?php foreach ($class as $k => $v) { ?>
                        <li><a href="<?php echo $v['id']; ?>"><?php echo $v['class_name']; ?></a></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="content-body">
        <div class="body-left">
            <div class="text-title">
                <h1><?php echo isset($data) && !empty($data) ? $data['news_name'] : '暂无数据'; ?></h1>
                <div class="article-time">
                    <span><?php echo isset($data) && !empty($data) ? $data['news_time'] : '暂无数据'; ?></span>
                    <span style="margin-left:30px;">来源:<?php echo isset($data) && !empty($data) ? $data['news_author'] : '暂无数据'; ?></span>
                </div>
            </div>
            <div class="article">
                <p><?php echo isset($data) && !empty($data) ? $data['news_content'] : '暂无数据'; ?></p>
            </div>

            <div id="sc" data-id="<?php echo $data['id'] ?>" class="collect">
                <input type="hidden" id="user" value="<?php echo $userId; ?>">
                <?php if (isset($statc) && !empty($statc)) { ?>
                    <?php echo '<span id="sc_wor1ds" style="color:#b21c12;">已收藏</span>'; ?>
                <?php } else { ?>
                    <?php echo '<span id="sc_wor1ds">收藏</span>'; ?>
                <?php } ?>
            </div>
            <div class="page">
                <ul class="page-ul">
                    <?php if ($up) { ?>
                        <li>
                            <a href="/?c=New&a=Content&id=<?php echo $up['id'] ?>">上一页</a>
                        </li>
                    <?php } else { ?>
                        <li>到头了</li>
                    <?php } ?>
                    <?php if ($down) { ?>
                        <li>
                            <a href="/?c=New&a=Content&id=<?php echo $down['id'] ?>">下一页</>
                        </li>
                    <?php } else { ?>
                        <li>到头了</li>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <div class="body-right"></div>
        <div class="return-top"><em>返回<br>顶部</em></div>
    </div>
    <?php } else { ?>
        <p id="error">404</p>
    <?php } ?>
</div>


<script>
    $(function () {
        $('.return-top').click(function () {
            $('html , body').animate({scrollTop : 0}, 'slow');
        });
    });

    $(function () {
        $('#sc').click(function () {
            var userId = $('#user').val();
            var collect = $('#sc_wor1ds').text();
            var data    = $(this).attr('data-id');
            console.log(data);
            if (!userId) {
                alert('请登录');
            } else {
                if (collect == '已收藏') {
                   // alert(113);
                    var url = '/?c=New&a=DeleCollect';
                    $.ajax({
                        url      : url,
                        data     : {
                            newId   : data,
                            userId : userId
                        },
                        type     : 'post',
                        dataType : 'json',
                        success  : function (data) {
                            $('#sc_wor1ds').html('收藏');
                        },
                        error    : function () {
                            alert('取消失败,请稍后再试');
                        }
                    });
                } else {
                    var url = '/?c=New&a=Collect';
                    $.ajax({
                        url      : url,
                        data     : {
                            newId   : data,
                            userId : userId
                        },
                        type     : 'post',
                        dataType : 'json',
                        success  : function (data) {
                            status = data.status;
                            if (status == 1) {
                                $('#sc_wor1ds').html('已收藏');
                            }
                        },
                        error    : function () {
                            alert('收藏失败,请稍后再试');
                        }
                    });
                }
            }
        });
    })
</script>
</body>
</html>
