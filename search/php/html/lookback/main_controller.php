<?php
session_start();
//date_default_timezone_set("PRC");
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);
require_once("actions/authorization.bak.php");
include_once("model/logger.php");

if(!authorization::checkUK())
{
   echo '<p style="text-align:center;">该功能暂时不可用</p>';
   exit();
}
if(isset($_GET['action']))
	$action = $_GET["action"];
if(isset($_GET['method']))
	$method = $_GET["method"];
if(!isset($action))
{
    $action="new_homepage";
}


if(!isset($method))
{
    $method="show";
}
if($action=="new_homepage"&&isset($_SESSION["term"]))
{
    $method="show_pre";
}
$fileName = "actions/".$action.".php";
if(!file_exists($fileName))
{
        echo "action is not defined";
        exit();
}

include($fileName);


$action = new $action;
$action->$method();


?>

