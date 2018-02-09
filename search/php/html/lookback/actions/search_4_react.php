<?php
require_once("model/ServerAPIModel.php");
require_once("templates/agg_search_template.php");
@include_once("model/common.php");

class search_4_react
{
    private $template;
    private $api;

    function __construct()
    {
        $this->template = new agg_search_template();
        $this->api = new ServerAPIModel();
    }

    function search_single()
    {
        if (!isset($_GET["term"]) || empty($_GET["term"])) {
            echo "empty";
            exit();
        }
        $raw_data = array();
        $raw_data["user"] = $_SESSION["user"];
        $raw_data["keyword"] = $_GET["term"];
        $raw_data["from"] = 0;
        $raw_data["pagesize"] = isset($_GET['pagesize'])?$_GET['pagesize']:5;
        if (isset($_GET['start'])) $raw_data['from'] = $_GET['start'];
        if (isset($_GET['tu'])) {
            $raw_data["touser"] = $_GET['tu'];
            $raw_data["conv"] = strnatcmp($raw_data['user'],$raw_data['touser'])<=0? $raw_data['user']."_".$raw_data['touser']:
                $raw_data["touser"]."_".$raw_data["user"];
            $arr_data = $this->api->actualSearch($raw_data);
        } else {
            $arr_data = $this->api->searchAPI($raw_data);
        }
        $arr_data['hasMore'] = false;
        if($arr_data['total']>$raw_data["pagesize"]+$raw_data['from']) $arr_data['hasMore'] = true;
        echo json_encode($arr_data,JSON_UNESCAPED_UNICODE);
    }

    function search_muc()
    {
        if (!isset($_GET["term"]) || empty($_GET["term"])) {
            echo "empty";
            exit();
        }
        $term = $_GET["term"];
        $userid = $_SESSION["user"];
        $start = 0;
	$pagesize=isset($_GET['pagesize'])?$_GET['pagesize']:5;
        if (isset($_GET['start'])) $start = $_GET['start'];
        $muc_arr = array();
        if (isset($_GET['muc'])) {
            $sql = "select show_name from muc_vcard_info where muc_name = '" . $_GET['muc'] . "@conference.ejabhost1'";
            $result = db_query($sql);
            $arr = pg_fetch_all($result);
            if (is_array($arr))
                $muc_arr[$_GET['muc']] = $arr[0]["show_name"];
        } else {
            $sql = "select i.show_name,t.om from muc_vcard_info as i,(select muc_name || '@conference.ejabhost1' as mn,muc_name as om from muc_room_users where username='" . $userid . "') as t  where i.muc_name = t.mn;";
            $result = db_query($sql);
            $arr = pg_fetch_all($result);
            if (is_array($arr)) {
                $len = count($arr);
                for ($x = 0; $x < $len; $x++) {
                    $muc_arr[$arr[$x]["om"]] = $arr[$x]["show_name"];
                }
            }
            else{
		echo "error";
exit();
	    }
        }
        $search_result = $this->api->get_muc_history($term, $muc_arr, $start,$pagesize);
	$search_result['hasMore'] = false;
        if($search_result['total']>$pagesize+$start) $search_result['hasMore'] = true;
        echo json_encode($search_result,JSON_UNESCAPED_UNICODE);
    }

    function show()
    {
        echo "error";
    }
}
