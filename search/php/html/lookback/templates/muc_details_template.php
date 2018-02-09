<?php
require_once("utils/utility.php");
require_once("utils/cache_util.php");
class muc_details_template{
    function __construct(){}
    function printMoreDetails($arr_data,$d)
    {
        require_once("entity/show_more_details.php");
        $user = $_SESSION['user'];
        $result = new show_more_details();
        $result->t=0;
        $result->ret=true;
        $arr_len = count($arr_data);
        if($arr_len==0)
        {
                $result->ret=false;
                echo json_encode($result);
                exit();
        }
        $fname="";$issend=true;
        $myNick = cache_util::get_nick($user);
         //$first = 0;
         //$latest = 0;
         $stamp=0;
         for($x=0;$x<$arr_len;$x++)
         {
                $msg = $arr_data[$x];
                if($msg["N"] == $myNick)
                {
                     $fname="我";
                     $issend = true;
                }else {
                     $fname = $msg["N"];
                     $issend=false;
                }
                $msg_content = QTalkUtility::parse_message($msg["B"]);
                $item="<div>";
                if($msg_content["stamp"]-$stamp>10*60)
                {
                     $item=$item.'<div style="text-align:center;"><span class="time_container">'.date('Y-m-d H:i:s',$msg_content["stamp"]).'</span></div>';
                     $stamp=$msg_content["stamp"];
                }
                if($issend)
                {
                     $item=$item.'<div class="rightd"><div class="main"><div class="speech right">'.$msg_content["body"]
                                 .'</div></div><div class="rightimg">'.$fname.'</div></div>';
                }
                else{
                     $item=$item.'<div class="leftd"><div class="leftimg">'.$fname.'</div>'
                                 .'<div class="main"><div class="speech left">'.$msg_content["body"].'</div></div></div>';
                }
                $item=$item."</div>";
                $result->c =$d?$item.$result->c:$result->c.$item;
                if($x==0)$result->t=$msg_content["stamp"];
		//else $latest = $msg_content["stamp"];
            }
	   /* if($arr_len==5){
		  if(!$d) $result->t=$first;
                    else $result->t=$latest;
	    }*/
            echo json_encode($result);
    }
    function printSearchDetails($arr_data,$mucid)
    {
        if($arr_data["ret"] == true)
        {
            $user = $_SESSION['user'];
            $content = "";
            $arr_len = count($arr_data["data"]);
            $fname = "";
            $issend = true;
            $myNick = cache_util::get_nick($user);
            $stamp=0;
            for($x=0;$x<$arr_len;$x++)
            {
                $msg = $arr_data["data"][$x];
                if($msg["N"] == $myNick)
                {
                     $fname="我";
                     $issend = true;
                }else{
                     $fname = $msg["N"];
                     $issend=false;
                }
                $msg_content = QTalkUtility::convert_obj_2_html($msg["B"]);
                $item="<div>";
                if($msg["D"]-$stamp>10*60)
                {
                     $item=$item.'<div style="text-align:center;"><span class="time_container">'.date('Y-m-d H:i:s',$msg["D"]).'</span></div>';
                     $stamp=$msg["D"];
                }

                if($issend)
                {
                     $item=$item.'<div class="rightd"><div class="main"><div class="speech right">'.$msg_content
                                 .'</div></div><div class="rightimg">'.$fname.'</div></div>';
                }
                else
                {
                    $item=$item.'<div class="leftd"><div class="leftimg">'.$fname.'</div>'
                                 .'<div class="main"><div class="speech left">'.$msg_content.'</div></div></div>';
                }
                $item=$item."</div>";

                $content = $content.$item;
            }
$actionbar="";
$wrapperstyle="";
$headercss="resources/mobileheader.css";
if(!QtalkUtility::is_mobile_request())
{
$actionbar=<<<eof
<header class="bar bar-nav">
<button class="btn btn-link btn-nav pull-left" onclick="window.history.go(-1)"><span class="icon icon-left-nav"></span>后退</button>
<h1 class="maintitle">群聊天详情</h1>
</header>
eof;
$headercss="resources/pcheader.css";
$wrapperstyle='style="margin-top:48px;"';
}

 $result=<<<eof
<html>
<head>
<title>群聊天详情</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui" name="viewport" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta id="twcClient" name="twcClient" content="false" />
<link rel="stylesheet" type="text/css" href="https://qt.qunar.com/lookback/resources/search_style_new.css" />
<link rel="stylesheet" type="text/css" href="resources/pull2refresh.css" />
<link rel="stylesheet" type="text/css" href="{$headercss}" />
</head>
<body>
{$actionbar}
<div id="wrapper" {$wrapperstyle}>
<div id="scroller">
<div id="pullDown"><span id="pullDownIcon" class="pullDownIcon"></span><span id="pullDownLabel" class="pullDownLabel">下拉加载更多</span></div>
<div id="m_con">
{$content}
</div>
<div id="pullUp"><span id="pullUpIcon" class="pullUpIcon"></span><span id="pullUpLabel" class="pullUpLabel">下拉加载更多</span></div>
</div>
</div>
<script src="https://qunarzz.com/jquery/prd/jquery-1.7.2.min.js"></script>
<script src="resources/iscrollAssist.min.js"></script>
<script src="resources/iscroll.min.new.js"></script>
<script src="resources/pull2refreshmuc.js"></script>
<script>
downTime = "{$arr_data["data"][0]["D"]}";
upTime ="{$arr_data["data"][$arr_len-1]["D"]}";
muc="{$mucid}";
</script>
</body>
</html>
eof;
        }
        else
        {
            $result = $arr_data["errmsg"];
        }

        echo $result;
    }

}
?>
