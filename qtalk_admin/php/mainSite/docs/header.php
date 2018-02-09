<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/10
 * Time: 11:21
 */
?>

<?php
require dirname(__FILE__) . '/../../common/config.php';
require_once dirname(__FILE__) . '/../../common/DBQtalk.class.php';
require_once dirname(__FILE__) . '/../../common/Utility.class.php';
require_once dirname(__FILE__) . '/../../common/importUser.class.php';
require_once dirname(__FILE__) . '/../../common/UserCookie.Class.php';
require_once dirname(__FILE__) . '/../ErrorType.Enum.php';
?>

<link rel="stylesheet" href="../css/header.css"/>
<div class="header_container">
    <div class="page_header">
        <div class="logo" onclick="location.href='../index.php';">
            <img class="logo" src="../images/app_icon.png" alt=""/><span class="logo">Qunar IM</span>
        </div>
        <div class="nav">
            <!--导航条-->
            <ul class="nav-main">
                <li id="li-1">文档首页</li>
                <li id="li-2">SDK下载</li>
                <li id="li-3">知识库</li>
                <li id="li-4">开源项目</li>
            </ul>
        </div>
    </div>
    <?php
    $userCookie = new UserCookie();
    $userInfo = $userCookie -> loginUserInfo();
    $isLogin = !empty($userInfo);
    ?>
    <?php if ($isLogin) { ?>
        <div class="logout">
            <a class="pc-g-logout" href="../setting.php"><span><?php echo $userInfo['nick_name']; ?></span></a>
        </div>
        <!--隐藏盒子-->
        <div id="userSetting" class="hidden-box" style="display: none;">
            <ul>
                <li id="changePwd">修改密码</li>
                <li id="userManager">用户管理</li>
                <li id="quitLogin">退出</li>
            </ul>
        </div>
    <?php } else {?>
        <div class="login">
            <a class="pc-g-signin " href="../login.php">登录</a> <a class="pc-g-signup" href="../register.php">注册</a>
        </div>
    <?php } ?>
</div>
<script type="text/javascript" src="../scripts/jquery-3.2.1.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
//    nav-li hover e
        var num;
        $('.logout').hover(function(){
            /*图标向上旋转*/
            //$(this).children().removeClass().addClass('hover-up');
            /*下拉框出现*/
            $menuBox = $('#userSetting');
            var left = $(this).position().left;
            $menuBox.css("left",left+"px");
            $menuBox.slideDown(300);
        },function(){
            /*图标向下旋转*/
            //$(this).children().removeClass().addClass('hover-down');
            /*下拉框消失*/
            $menuBox = $('#userSetting');
            $menuBox.hide();
        });
//    hidden-box hover e
        $('.hidden-box').hover(function(){
            /*保持图标向上*/
            // $('#li-'+num).children().removeClass().addClass('hover-up');
            $(this).show();
        },function(){
            $(this).slideUp(200);
            // $('#li-'+num).children().removeClass().addClass('hover-down');
        });
    });

    $("#changePwd").click(function () { location.href="changePassword.php"; });
    $("#userManager").click(function () { location.href="setting.php"; });
    // 退出登录
    $("#quitLogin").click(function () {
        $(this).removeClass("btn-primary");
        $(this).prop("disabled", true);
        $.ajax({
            type: "POST",
            data: $("#loginForm").serialize(),
            dataType: "json",
            url: "./public_method_ajax.php?action=logout",
            success: function (msg) {
                if (msg.ok) {
                    window.location.reload();
                } else {
                    if (msg.msg) {
                        alert(msg.msg);
                    } else {
                        alert("退出失败");
                    }
                    $(this).removeProp("disabled");
                    $(this).removeClass("btn-primary").addClass("btn-primary");
                }
            }
        });
    });
</script>