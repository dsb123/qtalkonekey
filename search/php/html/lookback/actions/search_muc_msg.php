<?php
require_once("model/ServerAPIModel.php");
require_once("templates/search_muc_msg_template.php");
@include_once("model/common.php");
class search_muc_msg{
     private $template;
     private $api;

     function __construct(){
	$this->template = new search_muc_msg_template();
        $this->api = new ServerAPIModel();
     }
     
     function search_muc()
     {
        if(!isset($_GET["term"])||empty($_GET["term"]))
        {
                echo "empty";
                exit();
        }
        $_SESSION['term']=$_GET["term"];
        $term=$_GET["term"];
        $userid = $_SESSION["user"];
        $start=0;
        if(isset($_GET['from'])) $start=$_GET['from'];
        $muc_arr = array();
        if(isset($_GET['muc'])){
            $sql = "select show_name from muc_vcard_info where muc_name = '".$_GET['muc']."@conference.ejabhost1'";
            $result = db_query($sql);
            $arr = pg_fetch_all($result);
            if(is_array($arr))
               $muc_arr[$_GET['muc']] = $arr[0]["show_name"];
        }
        else{
             $sql = "select i.show_name,t.om from muc_vcard_info as i,(select muc_name || '@conference.ejabhost1' as mn,muc_name as om from muc_room_users where username='".$userid."') as t  where i.muc_name = t.mn;";
             $result = db_query($sql);
             $arr = pg_fetch_all($result);
             if(is_array($arr))
             {
                 $len = count($arr);
                 for($x=0;$x<$len;$x++)
                 {
                     $muc_arr[$arr[$x]["om"]] = $arr[$x]["show_name"];
                 }
             }
        }
        $search_result= $this->api->get_muc_history($term,$muc_arr,$start,15);
        //if($start == 0&&$search_result["ret"])
        //{
            //require_once("utils/cache_util.php");
            //cache_util::set_temp_arr("m".$_SESSION["user"].$_GET["term"],$search_result["data"],120);
        //}
        $this->template->printSearchResults($search_result);
     }
}
?>
