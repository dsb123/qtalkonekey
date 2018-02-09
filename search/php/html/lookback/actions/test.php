<?php
/**
 * Created by PhpStorm.
 * User: may
 * Date: 2016/10/19
 * Time: 下午4:12
 */

require_once("model/qredis.sentinel.php");
class authorization
{
    /**
     * 认证用户是否已登录
     * @param string $user
     * @param string $key
     * @return boolean
     */
    public static function auth_user($user,$key){
        global $cur_redis;
        if(!$cur_redis)
        {
            $cur_redis = qredis::getInstance();
        }
        $cur_redis->selectDB('2');
        $result = $cur_redis->hget($user,$key)?true:false;
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
        $user ="";
        $key = "";
        if(isset($_SESSION["user"])&&isset($_SESSION["key"]))
        {
            $user = $_SESSION["user"];
            $key = $_SESSION["key"];
        }
        else if(isset($_COOKIE['_u'])&&isset($_COOKIE['_k']))
        {
            $_SESSION["user"] = $_COOKIE['_u'];
            $_SESSION["key"] = $_COOKIE['_k'];
            $user = $_COOKIE['_u'];
            $key = $_COOKIE['_k'];
        }
        else if(isset($_GET['user'])&&isset($_GET['key']))
        {
            $_SESSION["user"] = $_GET['user'];
            $_SESSION["key"] = $_GET['key'];
            $user = $_GET['user'];
            $key = $_GET['key'];
        }
        else {
            return false;
        }
        if(authorization::auth_user($user,$key))
        {
            return true;
        }
        unset($_SESSION['user']);
        unset($_SESSION['key']);
        return false;
    }
}

if (!authorization::auth_user('dan.liu', 'asdf'))
    echo 'failed!';
