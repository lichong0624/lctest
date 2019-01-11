<div class="cont_bgbox">
    <div class="header">
        <div class="h-p3 clearfix">
            <div class="h-news">
                <h2>
                    <?php echo $data['news_name']?><span class="btn-audio" data-url=" " style="display: none;"></span>
                </h2>
                <div class="h-info">
                        <span class="sub-time">
                            <span class="h-time"> <?php echo $data['news_time']?></span>
                        </span>
                    <span class="sub-src">
                        来源：
                        <span class="aticle-src">
                       <?php echo $data['news_author']?>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="main">
        <div class="part part1 clearfix" id="">
            <div class="p-right left">
                <div id="p-detail">
                    <iframe src="" class="video-frame" style="display: none;"></iframe>
                    <div class="main-aticle">
                        <p>
                            <?php echo $data['news_content']?>
                        </p>
                    </div>
                    <input type="hidden" id="admin" value="<?php echo $adminId;?>">
                    <?php if (isset($statc)&&!empty($statc)) {?>
                                <div id="sc" data-id="<?php echo $data['id']?>" class="in_right">

                                        <?php echo '<span id="sc_wor1ds">已收藏</span>' ;?>
                                </div>
                                   <?php }else{?>
                        <div id="sc" data-id="<?php echo $data['id']?>" class="in_right">

                            <?php echo '<span id="sc_wor1ds">收藏</span>' ;?>
                        </div>
                    <?php }?>
                    <ul>
                        <?php if($up){?>
                        <li>
                            <a href="/?c=Details&a=Default&id=<?php echo $up['id']?>">上一页</a>
                        </li>
                        <?php }?>
                        <?php if($down){?>
                        <li>
                            <a href="/?c=Details&a=Default&id=<?php echo $down['id']?>">下一页</>
                        </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .cont_bgbox{
        width:1230px;
        height:auto;
        background:#fff;
        overflow:hidden;
        margin:0 auto;
        -moz-box-shadow:0px -6px 20px #e0e4e9;
        -webkit-box-shadow:0px -6px 20px #e0e4e9;
        box-shadow:0px -6px 20px #e0e4e9;
    }
    .header{
        margin-bottom:32px;
        width:1200px;
        display:block;
        background:url(http://www.newsimg.cn/xl2017/images/bg2.jpg) 0 100% no-repeat;
        padding-bottom:30px;
        position:relative;
    }
    .header, .main, {
        width:1200px;
        margin:0 auto;
    }
    .main{
        position:relative;
        overflow:hidden;
    }
    .header, .main, {
        width:1200px;
        margin:0 auto;
    }

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script>
    $(function(){
        $('#sc').click(function(){
            var adminid = $('#admin').val();
            var collect = $('#sc_wor1ds').text();
            var data=$(this).attr('data-id');
            if(!adminid){
                alert('请登录');
            }else{
                if (collect == '已收藏') {
                    var url = '/?c=Details&a=DeleCollect';
                    $.ajax({
                        url      : url,
                        data     : {
                            newid   : data,
                            adminid : adminid
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

                    var url = '/?c=Details&a=Collect';
                    $.ajax({
                        url      : url,
                        data     : {
                            newid   : data,
                            adminid : adminid
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
