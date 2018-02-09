<?php
require_once("utils/utility.php");
class homepage_template{
    function __construct(){
    }
    public function printHtml($term,$is_muc,$actual)
    {
       $re = $this->generateHtml('',$term,$is_muc,0,$actual);
       return $re;
    }

    private function generateHtml($content,$term,$is_muc,$count=0,$actual="")
    {
        $params;
$actionbar = "";
$headercss = 'resources/mobileheader.css';
$t;
if($is_muc)
{
$params=<<<eof
var url_action="search_muc_msg";
var url_method="search_muc";
var touser="{$actual}";
eof;
$t="群组聊天搜索";
}
else
{
$params=<<<eof
var url_action="search_single_msg";
var url_method="search";
var touser="{$actual}";
eof;
$t="单人聊天搜索";
}
if(!QtalkUtility::is_mobile_request())
{
$actionbar=<<<eof
<header class="bar bar-nav">
<button class="btn btn-link btn-nav pull-left" onclick="window.history.go(-1)"><span class="icon icon-left-nav"></span>后退</button>
<h1 class="maintitle">{$t}</h1>
</header>
eof;
$headercss="resources/pcheader.css";
}


	$html=<<<eof
<html>
<head>
<title>{$t}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui" name="viewport" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta id="twcClient" name="twcClient" content="false" />
<script src="https://qunarzz.com/jquery/prd/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="resources/pull2refresh.css" />
<link rel="stylesheet" type="text/css" href="resources/search_style_new.css" />
<link rel="stylesheet" type="text/css" href="{$headercss}" />
</head>
<body style="overflow-y:hidden;position:absolute;left:0em;right:0em;top:0em;bottom:0em;margin:0em">
{$actionbar}
<div class="faq">
<input id="search_edit"  type="search" value="{$term}" placeholder="搜索关键词" />
</div>
<div id="wrapperdown">
<div id="scroller">
<div id="content">
{$content}
</div>
<div id="pullUp">
	<span id="pullUpdIcon" class="pullUpIcon"></span>
	<span id="pullUpLabel" class="pullUpLabel">上拉刷新</span>
</div>
</div>
</div>
<script>
{$params}
</script>
<script src="resources/iscrollAssist.min.js"></script>
<script src="resources/iscroll.min.new.js"></script>
<script src="resources/pull2refreshagg.js"></script>
<script>
generatedCount = {$count};
</script>
</body>
</html>
eof;
	return $html;
    }
    
function printPreHtml($content,$term,$is_muc,$count=0)
{
    $returnVal = $this->generateHtml($content,$term,$is_muc,$count);
    echo $returnVal;
}
}
?>
