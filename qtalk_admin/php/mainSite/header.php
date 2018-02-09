<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/23
 * Time: 20:57
 */
?>

<?php
require dirname(__FILE__) . '/../common/config.php';
require_once dirname(__FILE__) . '/../common/DBQtalk.class.php';
require_once dirname(__FILE__) . '/../common/Utility.class.php';
require_once dirname(__FILE__) . '/../common/importUser.class.php';
require_once dirname(__FILE__) . '/ErrorType.Enum.php';
?>

<link rel="stylesheet" href="css/header_new.css?v=1.0"/>
<link rel="stylesheet" href="css/simpleAlert.css"/>
<header class="header">
</header>

<script src="scripts/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="scripts/common-alert.js"></script>
<script type="text/javascript">
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
