<?php
require_once("utils/utility.php");
require_once("utils/cache_util.php");
class search_muc_msg_template
{
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
    $issend = true;
    $content = "";
    $myNick = cache_util::get_nick($user);
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
             $msg_content = QTalkUtility::parse_im_obj($msg["body"]);
             $item = '<div class="conv">';
 if($issend)
          {
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
         $item =$item.'</div>';

         $content=$content.$item;
     }
     return $content;
}

}
?>
