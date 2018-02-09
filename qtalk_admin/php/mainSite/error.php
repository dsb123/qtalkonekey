<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/22
 * Time: 20:23
 */
?>
<?php
    $errCode = filter_input(INPUT_GET, 'errCode'); // user name
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Qunare IM Error Page</title>
    <link rel="stylesheet" href="css/error.css"/>
</head>
<body>
<div id="container">
    <header>
        <!-- Header -->
        <?php include "header.php"; ?>
    </header>
    <section class="main">
        <!-- Content -->
        <div class="error_content">
            <span>
                <?php
                switch ($errCode){
                    case ErrorType::NotLogin:
                    {
                        echo "当前页面需要登录才能浏览";
                    }
                        break;
                    case ErrorType::NotAccess:
                    {
                        echo "无权访问此页面";
                    }
                        break;
                    default:
                    {
                        echo "错误页面";
                    }
                        break;
                }
                ?>
            </span>
            <br/>
            <br/>
            <a href="index.php">跳转回主页</a>
        </div>
    </section>
    <footer>
        <!-- footer -->
        <?php include "footer.php"; ?>
    </footer>
</div>
</body>
</html>
