<?php
$vali->form->setJsValidate(true);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>发布</title>
    <?=Q_ClientScript::getFile(Q_ClientScript::FILE_TYPE_JS, Q_ClientScript::POS_HEADER, true)?>
    <style>
        *{
            padding:0px;
            margin:0px;
        }
        .release-header{
            border-bottom:0;
            width:100%;
            height:60px;
            margin:0 auto;
            box-shadow:0 0 5px #00274e;
        }
        .release-eye{
            margin:0 auto;
            width:894px;
            height:60px;
        }
        .myrelease{
            display:inline-block;
            width:150px;
            height:100%;
            font-size:26px;
            text-align:center;
            line-height:80px;
        }
        .login{
            color:#333;
            font-size:13px;
            margin-left:550px;
        }
        .form{
            margin:0 auto;
            width:870px;
            height:870px;

        }
        .release-form{
            margin-left: 20px;
            width: 650px;
            border-right: 2px solid #f2f2f2;
        }
        .release-form-item{
            /* border: 1px solid;*/
            width:600px;
            height:90px;
            margin-top:15px;
        }
        .release-form-label{
            font-size:18px;
        }
        .release-input > input{
            width:400px;
            height:40px;
            margin-top:10px;
        }
        .release-input > select{
            width:400px;
            height:40px;
        }
        .error{
            font-size:14px;
            color:red;
        }
        .submit{
            width:60px;
            height:30px;
            margin:10px 240px 0;
            background-color:;
        }
    </style>
</head>
<body>
<div class="release-header">
    <div class="release-eye">
        <div class="myrelease">发布新闻</div>
        <?php if (isset($user) && !empty($user)) { ?>
                <a href="javaScript:;" class="login"><?php echo $user['username']; ?></a>
            </p>
        <?php } else { ?>
            <a href="/?c=Login&a=Default" class="login">登录</a>
        <?php } ?>
    </div>
</div>
<div class="release-body">
    <div class="form">
        <input type="hidden" id="user-id" value="<?php echo $userId?>">
        <form class="release-form" action="" method="post">
            <input name="e" value="exec" type="hidden"/>
            <div class="release-form-item">
                <label class="release-form-label"><?=$vali->form->name('news_name')?></label>
                <div class="release-input">
                    <?=$vali->form->input('news_name')?>
                    <?=$vali->form->error('news_name')?>
                </div>
            </div>
            <div class="release-form-item">
                <label class="release-form-label"><?=$vali->form->name('news_author')?></label>
                <div class="release-input">
                    <?=$vali->form->input('news_author')?>
                    <?=$vali->form->error('news_author')?>
                </div>
            </div>
            <div class="release-form-item">
                <label class="release-form-label"><?=$vali->form->name('class_id')?></label>
                <div class="release-input">
                    <?=$vali->form->select('class_id', $class)?>
                    <?=$vali->form->error('class_id')?>
                </div>
            </div>
            <div class="release-form-text">
                <label class="release-form-label"><?=$vali->form->name('news_content')?></label>
                <div class="release-input-text">
                    <?=$vali->form->textarea('news_content', ['style' => 'height:200px;width:550px;'])?>
                    <?=$vali->form->error('news_content')?>
                </div>
            </div>
            <div class="">
                <div class="">
                    <button class="submit" type="submit">立即提交</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var validator = new Q.plugin.validator('.release-form');

    $(function(){
        $('.submit').click(function(){
            var userId = $('#user-id').val();
            if (!userId) {
                alert('请先登录');
                $('.submit').attr('type','button');
            }
        })
    })
</script>
</body>
<!--<script>-->
<!-- window.location.href="http://www.baidu.com";-->
<!--</script>-->
</html>



