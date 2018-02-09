<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/13
 * Time: 11:17
 *
 * 修改密码
 *
 */
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Qunar IM 修改密码</title>
    <link rel="stylesheet" href="css/changePwd.css"/>
</head>
<body>

<div id="container">
    <header>
        <!-- Header -->
        <?php include "header.php"; ?>
    </header>
    <section class="main"><!-- Content -->
        <div class="change_pwd_content">
            <div class="dd-split-line-end"></div>
            <!-- 手机号 -->
            <div class="item_group">
                <div class="item_label">&nbsp;</div>
                <div class="item_input"><h2>网站密码修改</h2></div>
                <div class="item_extend">&nbsp;</div>
            </div>
            <!-- 旧密码 -->
            <div class="item_group">
                <div class="item_label">&nbsp;</div>
                <div class="item_input"><input type="text" class="icon-pwd" id="oldPassword" name="oldPassword" placeholder="旧密码"/></div>
                <div class="item_extend">&nbsp;</div>
            </div>
            <!-- 新密码 -->
            <div class="item_group">
                <div class="item_label">&nbsp;</div>
                <div class="item_input"><input type="text" class="icon-pwd" id="newPassword" name="newPassword" placeholder="新密码"/></div>
                <div class="item_extend">&nbsp;</div>
            </div>
            <!-- 确认密码 -->
            <div class="item_group">
                <div class="item_label">&nbsp;</div>
                <div class="item_input"><input type="text" class="icon-cpwd" id="confirmPassword" name="confirmPassword" placeholder="确认密码"></div>
                <div class="item_extend">&nbsp;</div>
            </div>
            <div class="dd-split-line-end"></div>
            <!-- 提交按钮 -->
            <div class="item_group">
                <div class="item_label">&nbsp;</div>
                <div class="item_input"><button type="button" class="btn btn-primary btn-login" id="btnSubmit">提交</button></div>
                <div class="item_extend">&nbsp;</div>
            </div>
            <!-- 忘记密码 -->
<!--            <div class="item_group">-->
<!--                <div class="item_label">&nbsp;</div>-->
<!--                <div class="item_input forget_pwd"><a href="#">忘记密码？</a></div>-->
<!--                <div class="item_extend">&nbsp;</div>-->
<!--            </div>-->
            <div class="dd-split-line-end"></div>
            <div class="dd-split-line-end"></div>
        </div>
    </section>
    <footer>
        <!-- Footer -->
        <?php include "footer.php"; ?>
    </footer>
</div>
<!-- 处理事件 -->
<script type="text/javascript">
    $("#btnSubmit").click(function () {
        $(this).removeClass("btn-primary");
        $(this).prop("disabled", true);
        $opwd = $("#oldPassword").val();
        $npwd = $("#newPassword").val();
        $cpwd = $("#confirmPassword").val();
        $requestData = "opwd="+$opwd+"&"+"npwd="+$npwd+"&"+"cpwd="+$cpwd;
        $.ajax({
            type: "POST",
            data: $requestData,
            dataType: "json",
            url: "./public_method_ajax.php?action=cpwd",
            timeout : 1000,
            success: function (msg) {
                if (msg.ok) {
//                    if (document.referrer.length > 0){
//                        window.location.href = document.referrer;
//                    } else {
                        window.location.href = "index.php";
//                    }
                    return false;
                } else {
                    if (msg.msg) {
                        alert(msg.msg);
                    } else {
                        alert("修改失败");
                    }
                    $('#btnSubmit').removeAttr("disabled");
                    $("#btnSubmit").removeClass("btn-primary").addClass("btn-primary");
                }
            },
            complete : function(XMLHttpRequest,status){ //请求完成后最终执行参数
                if(status=='timeout'){//超时,status还有success,error等值的情况
                    $ajaxRequest.abort();
                    alert("超时");
                }
                $('#btnSubmit').removeAttr("disabled");
                $('#btnSubmit').removeClass("btn-primary").addClass("btn-primary");
            }
        });
    });
</script>
</body>
</html>
