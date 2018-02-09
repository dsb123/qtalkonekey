<?php
require_once("utils/utility.php");
require_once("utils/cache_util.php");
class search_single_msg_template{
    function __construct(){}
    public function printSearchResults($arr_data)
    {
	$pre_offset = $_GET["from"];
	require_once("entity/search_result.php");
	$result = new search_result();
	$result->hasnext=false;
	if($arr_data["ret"] == true)
	{
            $arr_len = count($arr_data["data"]);
            $result->content = $this->generateResultView($arr_data["data"],$arr_len);
            $result->offset=$arr_len+$pre_offset;
            $result->ret=true;
	    if($arr_len >=15) $result->hasnext=true;
	}
	else
	{
            $result->content="error";
            $result->ret = false;
            $result->offset=$pre_offset;
	}
	
	echo json_encode($result);
    }
public function generateResultView($arr_data,$arr_len)
{
    $user = $_SESSION["user"];
    $nicks_arr = array();
    $fname = "";
    $tname = "";
    $issend = true;
    $content = "";
    for($x=0;$x<$arr_len;$x++)
    {
        $msg = $arr_data[$x];
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
         $item = '<div class="conv">';
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

         $content=$content.$item;
     }
     return $content;
}
}
?>
