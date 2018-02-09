<?php
require_once("model/qredis.sentinel.php");
include_once("model/logger.php");

class authorization
{
    /**
     * 认证用户是否已登录
     * @param string $user
     * @param string $key
     * @return boolean
     */
    public static function auth_user($user, $key)
    {
        global $cur_redis;
        if (!$cur_redis) {
            $cur_redis = qredis::getInstance();
        }
        $cur_redis->selectDB('2');
        $result = $cur_redis->hget($user, $key) ? true : false;
        $cur_redis->selectDB('3');
        return $result;
    }

    public static function checkUK()
    {
        /* if(isset($_GET['user'])&&isset($_GET['key']))
         {
              $_SESSION["user"] = $_GET['user'];
              $_SESSION["key"] = $_GET['key'];
         }*/
        $headers = apache_request_headers();

	if (isset($_COOKIE['_q']) && isset($_COOKIE['_v'])) 
        {
		return false;
        }

        if($headers&&isset($headers['-u'])&&isset($headers['-k']))
        {
            $user = $headers['-u'];
            $key = $headers['-k'];
        }
        else if(isset($_SERVER['-u'])&&isset($_SERVER['-k']))
        {
            $user = $_SERVER['-u'];
            $key = $_SERVER['-k'];
        }
        else if (isset($_COOKIE['_u']) && isset($_COOKIE['_k'])) {
            $user = $_COOKIE['_u'];
            $key = $_COOKIE['_k'];
	   file_log("cookie user: ".$user."  key".$key);
        } else if (isset($_GET['user']) && isset($_GET['key'])) {
            $user = $_GET['user'];
            $key = $_GET['key'];
	   file_log("user: ".$user."key".$key);
        }else if(isset($_SESSION["user"])&&isset($_SESSION["user"]))
        {
	    $user = $_SESSION["user"];
	    $key = $_SESSION["key"];
        }
        else {
            return false;
        }
        if (authorization::auth_user($user, $key)) {
            if(!isset($_SESSION["user"]))
                $_SESSION["user"] =$user;
            if(!isset($_SESSION["key"]))
                $_SESSION["key"] = $key;
            return true;
        }
        unset($_SESSION['user']);
        unset($_SESSION['key']);
        return false;
    }
}


