<?php
/**
 * @author  李锁柱 <suozhu.li@qunar.com> 
 * @version $Id$
 * 2015-8-28 11:09:29
 * UTF-8

$redis = new redis(); 
$redis->connect('10.88.132.177', 6379);
$redis->auth('redis_master@ejab');
$redis->select(2);
$count = $redis->dbSize();

$result = $redis->hget($argv[1],$argv[2]); 
var_dump($result);
//redis_master@ejab
*/

require 'vendor/predis/predis/autoload.php';
class qredis{
    public static $__instance = '';//单例
    public static $__redis = '';//redis链接
    public static $__error ='';//错误信息

    /*
     * 构造函数
     */
    public function __construct() {
        return self::connect();
    }
    /**
     * 连接redis服务器
     * @param string $host 主机
     * @param string $port 端口
     * @param string $timeout 超时时间
     */
    private function connect(){
        include_once(dirname(__FILE__)."/db_conf.sentinel.php");
        
        self::$__redis = new Redis();
        self::$__redis->connect($redis_conf['host'], $redis_conf['port'], $redis_conf['timeout']);
        self::$__redis->auth($redis_conf['password']); 
    }

  /**
     * 选择数据库
     * @param string $db
     */
    public function selectDB($db){
       return self::$__redis->select($db);
    }

    /**
     * 初始化单例
     * @return type
     */
    public static function getInstance(){
        if(!self::$__instance || !is_resource(self::$__redis))
        {
            self::$__instance = new qredis();
        }
        return self::$__instance;
    }
    /**
     * hget 获取值
     * @param string $key
     * @param string $hash
     * @return string   
     */
    public function hget($key,$hash){
        return self::$__redis->hGet($key,$hash);
    }
    /**
     *  设置值
     * @param string $key
     * @param string $hash
     * @param string $value
     * @return boolean
     */
    public function  hset($key,$hash,$value){
        return self::$__redis->hSet($key,$hash,$value);
    }
    /**
     *  删除值
     * @param string $key
     * @param string $hash
     */
    public function hdel($key,$hash){
        return self::$__redis->hDel($key,$hash);
    }
    
    /***
    *  get value
    ****/
    public function get($key)
    {
        return self::$__redis->get($key);
    }

    public function set($key,$value,$t)
    {
	return self::$__redis->setex($key,$t,$value);
    }
}
?>
