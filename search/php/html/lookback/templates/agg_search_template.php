<?php
require_once("utils/utility.php");
require_once("utils/cache_util.php");
class agg_search_template{
    function __construct(){}
    function generateHtml($sdata,$mdata,$term){
        $arr_len = count($sdata);
        $item = "";
        $user = $_SESSION['user'];
        if($arr_len>0)
        {  
           $item='<div class="groupheader">二人聊天</div>';
           $name = "";
           $nicks_arr=array();
           for($x=0;$x<$arr_len;$x++)
           {
                $msg=$sdata[$x];
                $userId = $msg["key"];
                if(isset($nicks_arr[$userId])){
                     $name = $nicks_arr[$userId];
                }else {
                     $name = cache_util::get_nick($userId);
                     $nicks_arr[$userId] = $name;
                }
                if(empty($name)) $name = $userId;
                $co_count = $msg["count"];
                $msg_content="";
                $link = "";
                if($co_count==1)
                {
                   $msg_content = QTalkUtility::convert_obj_2_html($msg["msg"]["msg"]);
		   $link='<a class="linkbtn" href="main_controller.php?action=single_details&method=showDetails&from='
                        .$msg["msg"]["from"].'&to='.$msg["msg"]["to"].'&time='.$msg["msg"]["time"].'">查看上下文</a>';
                }
                else
                {
                   $msg_content = "有".$co_count."条相关消息";
		   $link = '<a class="linkbtn" href="main_controller.php?action=homepage&method=show&term='.$term.'&source=s&ts='.$userId.'&conv='.$msg["conv"].'">详细搜索</a>';
                }
		$item =$item.'<div class="conv">';
	        $item=$item.'<div><span class="title">'.$name.
                	'</span><span class="detail">'.$link.'</span></div>'
	                .'<div class="leftd"><b>'.$msg_content
                	.'</b></div>';
	        $item =$item.'</div>';
           }
           //$item=$item.'<div class="groupfooter"><a href="main_controller.php?action=new_homepage&source=single">搜索更多单聊</a></div>';
        }
        $arr_len = count($mdata);
        if($arr_len>0)
        {
           $item=$item.'<div class="groupheader">群组聊天</div>';
           for($x=0;$x<$arr_len;$x++)
           {
                $msg = $mdata[$x];
                $co_count = $msg["count"];
		$link = "";
                if($co_count==1)
                {
		     $msg_content =$msg["msg"]["nick"].":".QTalkUtility::convert_obj_2_html($msg["msg"]["msg"]);
		     $link='<a class="linkbtn" href="main_controller.php?action=muc_details&method=showDetails&muc='
                        .$msg["msg"]["muc"].'&time='.$msg["msg"]["time"].'">查看上下文</a>';
		}
		else
		{
		     $msg_content = "有".$co_count."条相关消息";
		     $link = '<a class="linkbtn" href="main_controller.php?action=homepage&method=show&term='.$term.'&source=muc&tm='.$msg["key"].'">搜索详细</a>';
		}
                $item=$item.'<div class="conv">';
                $item=$item.'<div><span class="title">'.$msg['muc_name'].
                	'</span><span class="detail">'.$link.'</span></div>'
                	.'<div class="leftd"><b>'.$msg_content
                	.'</b></div>';
                $item=$item."</div>";
            }
            //$item=$item.'<div id="groupfooter"><a href="main_controller.php?action=homepage&source=muc">查看更多群聊</a></div>'; 
       }
       return $item;
   }
   
   function returnJson($html)
   {
       require_once("entity/search_result.php");
       $result = new search_result();
       $result->content=$html;
       $result->ret = true;
       $result->offset=0;
       $result->hasnext=true;
       echo json_encode($result);
   }
}
?>
