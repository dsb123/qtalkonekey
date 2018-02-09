<?php
 /* 李锁柱 <suozhu.li@qunar.com> 
 * @version $Id$
 * 2015-8-27 16:12:03
 * UTF-8
 *
 */
include_once("model/pg_conf.php");
include_once("model/logger.php");

function post_check($post)     
{     
    if (!get_magic_quotes_gpc()) // 判断magic_quotes_gpc是否为打开     
    {     
    $post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤     
    }     
    $post = str_replace("_", "\_", $post); // 把 '_'过滤掉     
    $post = str_replace("%", "\%", $post); // 把' % '过滤掉     
    $post = nl2br($post); // 回车转换     
    $post= htmlspecialchars($post); // html标记转换        
    return $post;     
}   
/**
 * 连接数据库
 */
function db_connect(){
    global $db_conf;
    $db = pg_connect("host={$db_conf['host']} port={$db_conf['port']} dbname={$db_conf['name']} user={$db_conf['user']} password={$db_conf['pass']}");
    if(!$db){
        file_log(pg_last_error());
        send_mail("数据库连接失败",$db_conf['host']."连接失败 error_info:".pg_last_error());
        return false;
    }
    return $db;
}
/**
 * 执行一条sql语句，
 * @param string $sql
 */
function db_query($sql){
    $db = db_connect();
    if(!$db){
        return false;
    }
    $result = pg_query($db,$sql);

    if(!is_resource($result)){
        $error = "sql:".$sql.";pg_result_error:".pg_result_error($result)  .";lasterror:". pg_last_error($db);
        file_log($error);
        send_mail("查询语句失败",$error);
    }
    return $result;
}
/**
 * 查询pg语句
 * @param string $sql  sql语句
 * @param boolean $one 是否获取一条
 * @return array
 */
function db_result($sql,$one=true){

    $query_result = db_query($sql);

    if(!is_resource($query_result)){
        return $query_result;
    }

    if($one){//此处有可能没有值返回的false
        $result = pg_fetch_assoc($query_result);
    }else{
        $result = array();
        while ($row = pg_fetch_assoc($query_result)) {
            $result[] = $row;
        }
    }
    return $result?$result:array();
}
/**
 * 插入数组到某到中去
 * @param string $table
 * @param string $array
 * @return boolean
 */
function db_insert($table,$array){
    $db = db_connect();
    if(!$db){
        return false;
    }
    if(!pg_insert ( $db ,$table,$array)){
        $error_info = str_replace("\n",' ', var_export($array,true));
        $error = "array:".$error_info.";pg_result_error:".pg_result_error($result)  .";lasterror:". pg_last_error($db);
        file_log($error);
        send_mail("插入数据库失败",$error);
    }
    return true;
}


function db_update($tablename,$data,$condition)
{
    $db = db_connect();

    $state = pg_prepare($db,$tablename,$data,$condition) or die("could not update") ;
    return $state;
}

function db_insert_with_trans($table,$sql,$data)
{
    $db = db_connect();
    pg_query($db,"BEGIN");
    $len = count($data);
    for($x=0;$x<$len;$x++)
    {
	pg_query_params($db,$sql,$data[$x]);
    }
    $pg_query($db,"COMMIT");
    return true;
}

?>

