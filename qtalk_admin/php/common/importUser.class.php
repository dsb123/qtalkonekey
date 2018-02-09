<?php

/**
 * @author  李锁柱 <suozhu.li@qunar.com>
 * @version $Id$
 * 2017-8-9 16:42:10
 * UTF-8
 *
 */
//导入用户模块
class importUser
{

    var $file = '';
    var $file_name = '';

    function __construct()
    {
        if (isset($_FILES['upload_users'])) {
            $this->file = $_FILES['upload_users']['tmp_name'];
            $this->file_name = $_FILES['upload_users']['name'];
        }
    }

    //检测csv
    function checkCsv()
    {
        return strtolower(strrchr($this->file_name, ".")) !== '.csv' ? false : true;
    }

    //导入数组
    function importArray($host_id)
    {
        $error_msg = "";
        if (empty($this->file)) {
            $error_msg = "请确认上传文件，仅支持(以逗号分隔的csv格式)";
            return array("ok" => 0,'data' => array(), 'msg' => $error_msg);
        }
        if (!$this->checkCsv()) {
            $error_msg = "请确认上传文件，仅支持(以逗号分隔的csv格式)";
            return array("ok" => 0,'data' => array(), 'msg' => $error_msg);
        }

        $fp = fopen($this->file, "r");
        if (!$fp) {
            $error_msg = "无法打开上传的文件";
            return array("ok" => 0,'data' => array(), 'msg' => $error_msg);
        }
        $i = 0;
        $from_charset = '';
        $data['user'] = array();
        DBQTalk::TransBegin();
        $msg = "";
        while (($line = fgets($fp)) !== false) {

            if ($i == 0) {
                $i++;
                //第一行用户判断字符集
                $arr = str_getcsv($line);

                if ($this->coveCharset('gbk', 'utf-8', $arr[0]) == '用户ID') {
                    $from_charset = 'gbk';
                } else if ($arr[0] == '用户ID' || $arr[0] == "\xEF\xBB\xBF用户ID") {
                    $from_charset = 'utf-8';
                } else {
                    $msg = "请确认上传的表头第一列为'用户ID'";
                    return array("ok" => 0,'data' => array(), 'msg' => $msg);
                }
                continue;
            }
            $i++;
            $is_ok = 1;
            if ($from_charset == 'gbk') {
                $line = $this->coveCharset('gbk', 'utf-8', $line);
            }
            $arr = str_getcsv($line);
            if (empty($arr[0])) {
                $msg .= "第{$i}行用户Id为空;　";
                $is_ok = 0;
            }
            if (empty($arr[1])) {
                $msg .= "第{$i}行姓名为空;　";
                $is_ok = 0;
            }
            if (!Utility::checkMobile($arr[2])) {
                $msg .= "第{$i}行手机号错误;　";
                $is_ok = 0;
            }
            if (empty($arr[0]) && empty($arr[1]) && empty($arr[2])) {
                //全部为空
                continue;
            }
            $data_arr = array(
                'user_name' => $arr[1],
                'department' => $arr[3],
                'dep1' => $arr[3],
                'tel' => $arr[2],
                'password' => mt_rand(111111, 999999),
                'user_id' => $arr[0],
                'host_id' => $host_id,
            );
//            var_dump($data_arr);
            if ($is_ok && $this->checkUserExists($data_arr['user_id'],$data_arr['host_id'])) {
                $msg .= "第{$i}行用户Id已存在;　";
                $is_ok = 0;
            }
            if ($is_ok) {
                $new_id = DBQTalk::Insert("public.host_users", $data_arr, 'id');
//                var_dump($new_id);
                if (!$new_id) {
                    //操作数据库失败时报错
                    DBQTalk::TransRollback();
                    $msg .= "第{$i}行导入失败~;　";
                    return array("ok" => 0,'data' => array(), 'msg' => $msg);
                } else {
                    $data_arr['id'] = $new_id;
                }
            }
            if ($is_ok && isset($data_arr['id'])) {
                $data['user'][] = $data_arr;
            } else {
                $data['error'][] = $data_arr;
            }
        }
        DBQTalk::TransCommit();
        fclose($fp);
        return array("ok" => 1,'data' => array(), 'msg' => $msg);
    }

    /**
     * 获取某域名下所有用户
     * @param type $host_id
     * @return type
     */

    function getHostUser($host_id)
    {
        $sql = "select * from public.host_users where host_id='{$host_id}' and hire_flag = 1";
        return DBQTalk::GetQueryResult($sql, false);
    }

    function getHostUserCount($host_id){
        $sql = "select count(1) as count from public.host_users where host_id=$1 and hire_flag = 1";
        $param = array($host_id);
        $result = DBQTalk::GetSafeQueryResult($sql,$param,false);
        return $result[0]["count"];
    }

    function searchHostUserCount($host_id,$searchStr){
        $sql = "select count(1) as count from public.host_users where host_id=$1 and hire_flag = 1 and (user_id like $2 or user_name like $3)";
        $param = array($host_id,"%{$searchStr}%","%{$searchStr}%");
        $result = DBQTalk::GetSafeQueryResult($sql,$param,false);
        return $result[0]["count"];
    }

    function getHostUserPage($host_id,$offset,$limit){

        $sql = "select * from public.host_users where host_id= $1 and hire_flag = 1 ORDER BY user_id ASC offset $2 limit $3";
        $param = array($host_id,$offset,$limit);
        return DBQTalk::GetSafeQueryResult($sql,$param,false);
    }

    function searchHostUserPage($host_id,$offset,$limit,$searchStr){

        $sql = "select * from public.host_users where host_id= $1 and hire_flag = 1 and (user_id like $2 or user_name like $3) ORDER BY user_id ASC offset $4 limit $5";
        $param = array($host_id,"%{$searchStr}%","%{$searchStr}%",$offset,$limit);
        return DBQTalk::GetSafeQueryResult($sql,$param,false);
    }

    /**
     * 检测用户存不存在
     * @param type $user_id
     * @return type
     */
    function checkUserExists($user_id, $host_id)
    {
        $sql = "select id from public.host_users where user_id='{$user_id}' and host_id = '{$host_id}' and hire_flag = 1";
//        var_dump($sql);
        //判断是否存在该域名
        $is_exists = DBQTalk::GetQueryResult($sql, true);
//        var_dump($is_exists);
        return is_array($is_exists) && isset($is_exists['id']) ? true : false;
    }

    /**
     * 检测用户存不存在
     * @param string $user_id
     * @return boolean
     */
    function clearLeaveOffice($user_id, $host_id)
    {
        $sql = "delete from public.host_users where user_id='{$user_id}' and host_id = '{$host_id}' and hire_flag = 0";
        $result = DBQTalk::safeQuery($sql, array());
        return $result;
    }

    //转换字符集
    function coveCharset($from, $to, $str)
    {
        if (function_exists("mb_convert_encoding")) {
            $str = mb_convert_encoding($str, $to, $from);
        } else {
            $str = iconv($from, $to, $str);
        }
        return $str;
    }


}
