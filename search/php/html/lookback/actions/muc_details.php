<?php
require_once("model/ServerAPIModel.php");
require_once("templates/muc_details_template.php");
class muc_details{
    function __construct(){
         $this->api = new ServerAPIModel();
         $this->template= new muc_details_template();
    }
   
    function show()
    {
       echo "error";
    }

    function showDetails()
    {
        $muc= $_GET["muc"];
        $time = $_GET["time"];
        if(!isset($muc))
        {
                echo "error";
                exit();
        }
        $raw_data='{"user":"'.$_SESSION["user"].'","muc":"'.$muc.'","key":"'.$_SESSION["key"].'","flag":"1","time":'.$time.'}';
        $arr_data = $this->api->searchDetailsAPI($raw_data);
        $this->template->printSearchDetails($arr_data,$muc);
    }

    function showUpMore()
    {
        $mucid = $_GET["muc"];
        $t    =$_GET["time"];
        $limit= "5";
        $d = "1";
        $arr_data = $this->api->get_muc_more_history($mucid,$t+1,$limit,$d,$_SESSION["user"],$_SESSION["key"]);
	if(isset($arr_data["ret"])&&$arr_data["ret"])
	        $arr_data=array_reverse($arr_data["data"]);
	else
		$arr_data=array();
	//$arr_data=array_reverse($arr_data);
        $this->template->printMoreDetails($arr_data,true);
    }

    function showDownMore()
    {
        $mucid =$_GET["muc"];
        $t    =$_GET["time"];
        $limit= "5";
        $d = "0";
        $arr_data = $this->api->get_muc_more_history($mucid,$t-1,$limit,$d,$_SESSION["user"],$_SESSION["key"]);
        if(isset($arr_data["ret"])&&$arr_data["ret"])
	        $arr_data = $arr_data["data"];
	else
		$arr_data = array();
        //$arr_data=array_reverse($arr_data);
        $this->template->printMoreDetails($arr_data,false);
    }
}
?>
