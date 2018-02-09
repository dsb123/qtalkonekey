<?php
require_once("utils/utility.php");
class combine_homepage_template
{
    function __construct(){}
    function show_combien_home($content,$term)
    {
$actionbar = "";
$headercss = 'resources/mobileheader.css';
if(!QtalkUtility::is_mobile_request())
{
$actionbar=<<<eof
<header class="bar bar-nav">
<!--<button class="btn btn-link btn-nav pull-left" onclick="window.history.go(-1)"><span class="icon icon-left-nav"></span>后退</button>-->
<h1 class="maintitle">搜索</h1>
</header>
eof;
$headercss="resources/pcheader.css";
}
        $html=<<<eof
<html>
<head>
<title>搜索</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui" name="viewport" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta id="twcClient" name="twcClient" content="false" />
<script src="https://qunarzz.com/jquery/prd/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="resources/search_style_new.css" />
<link rel="stylesheet" type="text/css" href="{$headercss}" />
</head>
<body>
{$actionbar}
<div class="faq">
<input id="search_edit" type="search" value="{$term}" placeholder="搜索关键词" />
</div>
<div id="content">
{$content}
</div>
<script src="resources/combinesearch.js"></script>
</body>
</html>
eof;
       echo $html;
    }
}
?>
