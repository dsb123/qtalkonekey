<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/14
 * Time: 16:32
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>公共IM接入服务</title>
    <link rel="stylesheet" href="../css/reset.css"/>
    <link rel="stylesheet" href="../css/docs_main.css"/>
</head>
<body>
<div id="container">
    <header>
        <!-- Header -->
        <?php include "header.php"; ?>
    </header>
    <section class="main">
        <!-- 基本功能 -->
        <div class="docs_content">
            <div class="l-wrapper">
                <h2>开发文档</h2>
                <div class="docs_group">
                    <h3>接入必读</h3>
                    <hr/>
                    <table>
                        <tr><th></th></tr>
                        <tr><td><a href="#">新手指南</a></td></tr>
                    </table>
                </div>
                <div class="docs_group">
                    <h3>IM 基础服务开发指南</h3>
                    <hr/>
                    <table>
                        <tr><th>Android</th><th>IOS</th><th>Server</th></tr>
                        <tr><td><a href="#">IM 界面组件 - IMKit</a></td><td><a href="#">IM 界面组件 - IMKit</a></td><td><a href="#">Server 开发指南</a></td></tr>
                        <tr><td><a href="#">IM 通讯能力库 - IMLib</a></td><td><a href="#">IM 通讯能力库 - IMLib</a></td><td><a href="#">广播推送服务</a></td></tr>
                    </table>
                </div>
                <div class="docs_group">
                    <h3>扩展服务开发指南</h3>
                    <hr/>
                    <table>
                        <tr><th>Android</th><th>IOS</th><th>Server</th></tr>
                        <tr><td><a href="#">客服服务</a></td><td><a href="#">公众服务</a></td><td><a href="#">短信服务</a></td></tr>
                    </table>
                </div>
                <div class="docs_group">
                    <h3>其他</h3>
                    <hr/>
                    <table>
                        <tr><th>功能服务</th><th>技术主题</th><th>更新日志</th><th>附录</th></tr>
                        <tr><td><a href="#">功能服务</a></td><td><a href="#">技术主题</a></td><td><a href="#">更新日志</a></td><td><a href="#">附录</a></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <!-- footer -->
        <?php include "footer.php"; ?>
    </footer>
</div>
</body>
</html>
