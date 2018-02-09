<?php
require_once("utils/utility.php");
require_once("utils/cache_util.php");
class combine_search_template{
    function __construct(){}
    function generateHtml($sdata,$mdata){
        $arr_len = count($sdata);
        $item = "";
        $user = $_SESSION['user'];
        if($arr_len>0)
        {  
           $item='<div class="groupheader">二人聊天</div>';
           $fname="";$tname="";$issend=true;$nicks_arr=array();
           for($x=0;$x<$arr_len;$x++)
           {
                $msg = $sdata[$x];
                if($msg["F"] == $user)
                {
                     $fname="我";
                     $issend = true;
                }else if(isset($nicks_arr[$msg["F"]])){
                     $fname = $nicks_arr[$msg["F"]];
                }else {
                     $fname = cache_util::get_nick($msg["F"]);
                     $nicks_arr[$msg["F"]] = $fname;
                }
                if($msg["T"] == $user)
                {
                     $issend = false;
                }else if(isset($nicks_arr[$msg["T"]])){
                     $tname = $nicks_arr[$msg["T"]];
                }else {
                     $tname = cache_util::get_nick($msg["T"]);
                     $nicks_arr[$msg["T"]] = $tname;
                }
                if(empty($fname)) $fname=$msg["F"];
                if(empty($tname)) $tname=$msg["T"];
                $msg_content = QTalkUtility::parse_im_obj($msg["body"]);
		$item =$item.'<div class="conv">';
	        if($issend)
        	{
	               $item=$item.'<div><span class="title">发给'.$tname.'('.date('Y-m-d H:i',$msg["D"]).')'.
                	'</span><span class="detail"><a class="linkbtn" href="main_controller.php?action=single_details&method=showDetails&from='
        	        .$msg["F"].'&to='.$msg["T"].'&time='.$msg["D"].'">查看上下文</a></span></div>'
	                .'<div class="rightd"><div class="main"><div class="speech right">'.$msg_content
                	.'</div></div><div class="rightimg">'.$fname.'</div></div>';
          	}
          	else
          	{
              	 $item=$item.'<div><span class="title">来自'.$fname.'('.date('Y-m-d H:i',$msg["D"]).')'.
              		 '</span><span class="detail"><a class="linkbtn" href="main_controller.php?action=single_details&method=showDetails&from='
        	       .$msg["F"].'&to='.$msg["T"].'&time='.$msg["D"].'">查看上下文</a></span></div>'
	               .'<div class="leftd"><div class="leftimg">'.$fname.'</div>'
			 .'<div class="main"><div class="speech left">'.$msg_content.'</div></div></div>';
        	 }
	         $item =$item.'</div>';
           }
           $item=$item.'<div class="groupfooter"><a href="main_controller.php?action=homepage&source=single">查看更多单聊</a></div>';
        }
        $arr_len = count($mdata);
        if($arr_len>0)
        {
           $item=$item.'<div class="groupheader">群组聊天</div>';
           $fname="";$issend=true;
           $myNick = cache_util::get_nick($user);
           for($x=0;$x<$arr_len;$x++)
           {
                $msg = $mdata[$x];
                if($msg["N"] == $myNick)
                {
                     $fname="我";
                     $issend = true;
                }else {
                     $fname = $msg["N"];
                     $issend=false;
                }
                $msg_content = QTalkUtility::parse_im_obj($msg["B"]);
                $item=$item.'<div class="conv">';
                if($issend){
                     $item=$item.'<div><span class="title">'.$msg['M'].'('.date('Y-m-d H:i',$msg["D"]).')'.
                	'</span><span class="detail"><a class="linkbtn" href="main_controller.php?action=muc_details&method=showDetails&muc='
                	.$msg["R"].'&time='.$msg["D"].'">查看上下文</a></span></div>'
                	.'<div class="rightd"><div class="main"><div class="speech right">'.$msg_content
                	.'</div></div><div class="rightimg">'.$fname.'</div></div>';
                }
                else
                {
               	     $item=$item.'<div><span class="title">'.$msg['M'].'('.date('Y-m-d H:i',$msg["D"]).')'.
               		'</span><span class="detail"><a class="linkbtn" href="main_controller.php?action=muc_details&method=showDetails&muc='
               		.$msg["R"].'&time='.$msg["D"].'">查看上下文</a></span></div>'
               		.'<div class="leftd"><div class="leftimg">'.$fname.'</div>'
 			.'<div class="main"><div class="speech left">'.$msg_content.'</div></div></div>';
		}
                $item=$item."</div>";
            }
            $item=$item.'<div id="groupfooter"><a href="main_controller.php?action=homepage&source=muc">查看更多群聊</a></div>'; 
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
