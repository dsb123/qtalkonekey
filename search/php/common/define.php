<?php

/**
 * Created by PhpStorm.
 * User: may
 * Date: 2016/10/26
 * Time: 下午3:15
 */
class dbKey
{
    public static $qtalkDB_readonly = 0;
}

class Environment
{

    private static $instance;
    private $pg_connection_string;

    //private static $host1 = '10.88.132.176';
    private static $host1 = '<?qtalk postgres>';

    private static $port = 5432;
    private static $qtalk_dbname = 'ejabberd';
    private static $user = 'ejabberd';
    private static $password = '<?qtalk postgres_password>';


    public function get_db_string($item)
    {
        return $this->pg_connection_string[$item];
    }


    public static function sharedInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct()
    {
        $username = self::$user;
        $pwd = self::$password;
        $port = self::$port;

        $dbname1 = self::$qtalk_dbname;

        $ejabdb1 = self::$host1;

        $this->pg_connection_string = array(
            "host={$ejabdb1} port={$port} dbname={$dbname1} user={$username} password={$pwd}",
        );
    }

    private function __clone()
    {
    }


    private function __wakeup()
    {
    }
}
