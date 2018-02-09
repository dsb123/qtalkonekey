<?php
require_once("model/ServerAPIModel.php");
require_once("templates/search_single_msg_template.php");
@include_once("model/common.php");
class search_single_msg
{
    private $template;
    private $api;

    function __construct(){
	$this->template = new search_single_msg_template();
	$this->api = new ServerAPIModel();
    }
    function search()
    {
	if(!isset($_GET["term"])||empty($_GET["term"]))
	{
		echo "empty";
		exit();
	}
        $_SESSION['term']=$_GET["term"];
	$raw_data=array();
        $raw_data["user"] = $_SESSION["user"];
	$raw_data["keyword"] = $_GET["term"];
	$raw_data["from"]=0;
        if(isset($_GET['from']))$raw_data['from']=$_GET['from'];
        $rkey = "s";
	if(isset($_GET['t']))
	{
	    $raw_data["touser"]=$_GET['t'];
            $raw_data['conv']=$_GET['conv'];
	    $arr_data=$this->api->actualSearch($raw_data);
	}
        else
        {
            $b = isset($_GET['s']);
            if($b)$raw_data["pagesize"]=5;
            $arr_data=$this->api->searchAPI($raw_data);
            if($b){
                $term=$_GET["term"];
                $userid = $_SESSION["user"];
                $start=0;
                $muc_arr = array();
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
                $search_result= $this->api->get_muc_history($term,$muc_arr,$start,5);
                require_once("templates/combine_search_template.php");
                $tp = new combine_search_template();
                $content= $tp->generateHtml($arr_data['data'],$search_result['data']);
                $tp->returnJson($content);
                //require_once("utils/cache_util.php");
                //cache_util::set_temp_arr("c".$_SESSION["user"].$_GET["term"],$content,120);
                //cache_util::set_temp_arr("m".$_SESSION["user"].$_GET["term"],$search_result['data'],120);
            }
        }
        //if($_GET["from"] == 0&&$arr_data["ret"])
	//{
	    //require_once("utils/cache_util.php");
 //           cache_util::set_temp_arr("s".$_SESSION["user"].$_GET["term"],$arr_data["data"],120);
	//}
        if(isset($_GET['s']))exit();
        $this->template->printSearchResults($arr_data);
    }
     
    function show()
    {
	echo "error";
    }
}
?>
