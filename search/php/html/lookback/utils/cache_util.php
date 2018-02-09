<?php
include_once("model/qredis.sentinel.php");
class cache_util{
    
    function __construct(){}
    public static function get_nick($userId)
    {
       global $cur_redis;
       return $userId;
       if(!$cur_redis) 
       {
            $cur_redis = qredis::getInstance();
       }
       $nick = $cur_redis->get($userId);
       if(!isset($nick)) return $userId;
       return $nick;
    }
   
    public static function set_temp_arr($key,$temp,$timeout)
    {
       global $cur_redis;
       if(!$cur_redis)
       {
            $cur_redis = qredis::getInstance();
       }
       $temp = json_encode($temp);
       $cur_redis->set($key,$temp,$timeout);
    }
    public static function get_temp_arr($key)
    {
       global $cur_redis;
       if(!$cur_redis)
       {
            $cur_redis = qredis::getInstance();
       }
       $val = $cur_redis->get($key);
       if(!isset($val)) return array();
       return json_decode($val,true);

    }
}
?>
