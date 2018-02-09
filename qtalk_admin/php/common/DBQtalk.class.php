<?php

require dirname(__FILE__).'/config.php';
class DBQTalk {

	public static $mInstance = null;   //子类需要声明
	public static $mConnection = null; //子类需要声明
	public static $mError = null;   //子类需要声明
	function __construct() {

	}

	/**
	 * 禁止clone
	 */
	private function __clone() {

	}
    

	/**
	 * 单例
	 * @param  string $db  QDB 类调用时管用
	 * 返回类
	 */
	public static function &Instance() {
        $class = get_called_class();
		if (!(static::$mInstance instanceof $class)) {
			//每个类只初始化一次类只初始化一次 
			static::$mInstance = new $class ();
		}
		//pg 连接根据不同的类调用，初始化不同的数据库，并加入判断连接断开->重连，连接错误->重连
		self::connect(); //连接数据库
		return static::$mInstance;
	}

	/**
	 * 连接数据库
	 * pg 连接根据不同的类调用，初始化不同的数据库，并加入判断连接断开->重连，连接错误->重连
	 */
	public static function connect() {
		global $db_config_qtalk;
		if (!is_resource(static::$mConnection)) {
			
            $host = (string) $db_config_qtalk['host'];
            $user = (string) $db_config_qtalk['user'];
            $pass = (string) $db_config_qtalk['pass'];
            $name = (string) $db_config_qtalk['name'];
                            //数据库链接默认5432可手工指定
            $port = (int) (isset($db_config_qtalk['port'])?$db_config_qtalk['port'] :5432);

            //只要数据库连接有一点不一样的地方就会新建一个pg链接,否则是同一个链接
            static::$mConnection = pg_connect("host={$host} port={$port} dbname={$name} user={$user} password={$pass}");

            if (false === static::$mConnection) {
                static::$mError = "pg:{$db_config} Connect failed";
                
            } else if (pg_last_error(static::$mConnection)) {
                static::$mError = "Connect failed: " . pg_last_error(static::$mConnection);
            }
		} 
	}

	/**
	 * 获取数据库链接
	 * @return pg resource 
	 */
	static public function GetLinkId() {
		static::Instance();
		return static::$mConnection;
	}

	function __destruct() {
		self::Close();
	}
	/**
	 * 关闭pg连接，当前不关
	 * @return type
	 */
	static public function Close() {
		if (is_resource(static::$mConnection)) {
			pg_close(static::$mConnection);
		}
	}

	/**
	 * 开始事务
	 * level 级别 http://wiki.corp.qunar.com/pages/viewpage.action?pageId=43255305
	 * 
	 * @param integer $level    隔离级别   默认1
	 *                          1=SERIALIZABLE 可串行化 
	 *                          2=REPEATABLE READ 可重复读
	 *                          3=READ COMMITTED 读已提交
	 *                          4=READ UNCOMMITTED 读未提交
	 * @return  boolean|resource 
	 * 
	 */
	public static function TransBegin($level = 1) {
		$level_value = 'BEGIN;SET transaction ISOLATION LEVEL  ';
		switch ($level) {
			case 1:
				$level_value .= " SERIALIZABLE "; //可串行化
				break;
			case 2:
				$level_value .= " REPEATABLE READ "; //可重复读
				break;
			case 3:
				$level_value .= " READ COMMITTED "; //读已提交
				break;
			case 4:
				$level_value .= " READ UNCOMMITTED "; //读未提交
				break;
			default :
				$level_value .= " SERIALIZABLE "; //可串行化
		}
		$level_value .= ";";
		return self::Query($level_value);
	}

	/**
	 * 事务回滚
	 * @return boolean|resource 
	 */
	public static function TransRollback() {
		return self::Query("ROLLBACK;");
	}

	/**
	 * 事务提交
	 * @return boolean|resource 
	 */
	public static function TransCommit() {
		return self::Query("COMMIT;");
	}

	/**
	 * 转义字符
	 * @param string $string    要转义的字符串
	 * @return pg_escape_string($string)
	 */
	static public function EscapeString($string) {
		static::Instance();
		return pg_escape_string($string);
	}

	/**
	 * 查询sql并返回查询结果resource 
	 * @global string $QUNARSTATS
	 * @param string $sql
	 * @param boolean $nestloop  true 为关闭，默认是打开状态 关闭规划器对嵌套循环连接规划类型的使用
	 * @return boolean|resource 
	 */
	static public function Query($sql) {
		static::Instance();

		$result = pg_query(static::$mConnection, $sql);
		
		if ($result) {
			return $result;
		} else {
			static::$mError = pg_last_error(static::$mConnection);
		}
		return false;
	}

    /**
     * 查询sql并返回查询结果resource
     * @global string $QUNARSTATS
     * @param string $sql
     * @param $params 查询参数
     * @param boolean $nestloop  true 为关闭，默认是打开状态 关闭规划器对嵌套循环连接规划类型的使用
     * @return boolean|resource
     */
    static public function safeQuery($sql,array $params) {
        static::Instance();

        $result = pg_query_params(static::$mConnection, $sql,$params);

        if ($result) {
            return $result;
        } else {
            static::$mError = pg_last_error(static::$mConnection);
        }
        return false;
    }

	/**
	 * 获取sql语句查询结果
	 * @global string $QUNARSTATS
	 * @global string $QUNARSTATS
	 * @param string $sql
	 * @param boolean $one 默认true
	 * @param boolean $cache
	 * @param boolean $nestloop
	 * @param  $dberror 引用 $dberror变量
	 * @return array
	 */
	static public function GetQueryResult($sql, $one = true) {
		
		$ret = array();
		if ($result = self::Query($sql)) {
			while ($row = pg_fetch_assoc($result)) {
				$row = array_change_key_case($row, CASE_LOWER);
				if ($one) {
					$ret = $row;
					break;
				} else {
					array_push($ret, $row);
				}
			}
			pg_free_result($result);
		}
		return $ret;
	}


    /**
     * 获取sql语句查询结果
     * @global string $QUNARSTATS
     * @global string $QUNARSTATS
     * @param string $sql
     * @param boolean $one 默认true
     * @param boolean $cache
     * @param boolean $nestloop
     * @param  $dberror 引用 $dberror变量
     * @return array
     */
    static public function GetSafeQueryResult($sql, array $params, $one = true) {

        $ret = array();
        if ($result = self::safeQuery($sql, $params)) {
            while ($row = pg_fetch_assoc($result)) {
                $row = array_change_key_case($row, CASE_LOWER);
                if ($one) {
                    $ret = $row;
                    break;
                } else {
                    array_push($ret, $row);
                }
            }
            pg_free_result($result);
        }
        return $ret;
    }

    /**
     * 插入
     * @param type $table
     * @param type $condition
     * @param type $return
     * @return boolean
     */
    static public function Insert($table, $condition, $return = 'id') {
		static::Instance();
		$sql = "INSERT INTO {$table} ";

		$content = null;
		$columnlist = null;
		$valuelist = null;

		$columnlist .= '(';
		$valuelist .= '(';
		foreach ($condition as $k => $v) {
			$v_str = null;
			if (is_numeric($v)) {
				$v_str = "'{$v}'";
			} else if (preg_match("|^ARRAY\[.*\]$|", $v) ) {
				$v_str = "{$v}";
			}else if (preg_match("|ltree\[\]$|", $v) ) {
				$v_str = "{$v}";
			}else if (preg_match("|jsonb$|", $v) ) {
				$v_str = "{$v}";
			}else if (preg_match("|hanzi_to_pinyin\(.*?\)$|", $v) ) {
                $v_str = "{$v}";
            }else if (preg_match("|lquery\[\]$|", $v) ) {
				$v_str = "{$v}";
			}else if (is_null($v)) {
				$v_str = 'NULL';
			}else if ($v=='') {
                $v_str = "''";
            }else {
				$v_str = "'" . self::EscapeString($v) . "'";
			}
			$columnlist .= "$k,";
			$valuelist .= "$v_str,";
		}
		$columnlist = trim($columnlist, ',');
		$valuelist = trim($valuelist, ',');
		$columnlist .= ')';
		$valuelist .= ')';

		$content = $columnlist . ' VALUES ' . $valuelist;
		if (!empty($return)) {
			$content .= " RETURNING " . $return;
		}
		$sql .= $content;
		$result = self::Query($sql);

		if (false == $result) {
			return false;
		}
        if($return){
            $row = pg_fetch_assoc($result);
            $return = trim($return);
            if ($return == '*') {
                $return = 'id';
            }
            return $row[$return];
        }else{
            $insert_id = true;
        }    
		return $insert_id;
	}
}
