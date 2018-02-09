<?php
require_once("model/ServerAPIModel.php");
require_once("templates/single_details_template.php");
class single_details{
    private $api;
    private $template;
    function __construct(){
      $this->api = new ServerAPIModel();
      $this->template= new single_details_template();
    }
    
    function show()
    {
       echo "error";
    }
    
    function showDetails()
    {
        $from = $_GET["from"];
	$to   = $_GET["to"];
	$time = $_GET["time"];
        if(!isset($from)||
		!isset($to))
	{
		echo "error";
		exit();
	}
//array("user"=>$_SESSION["user"],"from"=>$from,"to"=>$to,"key"=>$_SESSION["key"],"flag"=>"0","time"=>$time);
        $raw_data='{"user":"'.$_SESSION["user"].'","from":"'.$from.'","to":"'.$to.'","key":"'.$_SESSION["key"].'","flag":"0","time":'.$time.'}';
	$arr_data = $this->api->searchDetailsAPI($raw_data);
        $this->template->printSearchDetails($arr_data);
    }

    function showUpMore()
    {
	$from = $_GET["from"];
        $to   = $_GET["to"];
	$t    =$_GET["time"];
        $limit= "5";
	$d = "1";
        $arr_data = $this->api->get_single_history($from,$to,$t+1,$limit,$d,$_SESSION["user"],$_SESSION["key"]);
        if(isset($arr_data["ret"])&&$arr_data["ret"])
                $arr_data = $arr_data["data"];
        else
                $arr_data = array();

        //$arr_data = array_reverse($arr_data);
	$this->template->printMoreDetails($arr_data,true);
    }
    function showDownMore()
    {
	 $from =$_GET["from"];
        $to   = $_GET["to"];
        $t    =$_GET["time"];
        $limit= "5";
        $d = "0";
        $arr_data = $this->api->get_single_history($from,$to,$t-1,$limit,$d,$_SESSION["user"],$_SESSION["key"]);
        if(isset($arr_data["ret"])&&$arr_data["ret"])
                $arr_data = array_reverse($arr_data["data"]);
        else
                $arr_data = array();

        //$arr_data = array_reverse($arr_data);
        $this->template->printMoreDetails($arr_data,false);
    }
}
?>
