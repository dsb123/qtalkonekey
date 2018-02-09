<?php
require_once("model/ServerAPIModel.php");
require_once("templates/agg_search_template.php");


include_once("model/common.php");
@include_once("model/logger.php");

class agg_search_msg
{
    private $template;
    private $api;

    function __construct(){
	$this->template = new agg_search_template();
	$this->api = new ServerAPIModel();
    }
    function search()
    {
	if(!isset($_GET["term"])||empty($_GET["term"]))
	{
		file_log("trem is null ");
		echo "empty";
		exit();
	}
        $_SESSION['term']=$_GET["term"];
	$raw_data=array();
        $raw_data["user"] = $_SESSION["user"];
	$raw_data["keyword"] = $_GET["term"];
	$raw_data["from"]=0;
        if(isset($_GET['from']))$raw_data['from']=$_GET['from'];
        $raw_data["pagesize"]=5;
        $arr_data=$this->api->agg_search_single($raw_data);

        $userid = $_SESSION["user"];
        $start=0;
        $muc_arr = array();
        $sql = "select i.show_name,t.om from muc_vcard_info as i,(select muc_name || '@conference.ejabhost1' as mn,muc_name as om from muc_room_users where username='"
                      .$userid."') as t  where i.muc_name = t.mn;";
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
        $search_result= $this->api->agg_search_muc($raw_data,$muc_arr);
        $content= $this->template->generateHtml($arr_data['data'],$search_result['data'],$_SESSION['term']);
        $this->template->returnJson($content);
        require_once("utils/cache_util.php");
        //cache_util::set_temp_arr("agg_c_".$_SESSION["user"].$_GET["term"],$content,30);
    }
     
    function show()
    {
	echo "error";
    }
}
?>
