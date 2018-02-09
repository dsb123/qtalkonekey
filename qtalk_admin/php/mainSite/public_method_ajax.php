<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/22
 * Time: 19:13
 */
?>

<?php

require_once dirname(__FILE__) . '/../common/config.php';
require_once dirname(__FILE__) . '/../common/DBQtalk.class.php';
require_once dirname(__FILE__) . '/../common/Utility.class.php';
$action = filter_input(INPUT_GET, 'action'); // user name

function GetUrl($url, $cookie, $timeout = 1)
{
    $ch = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_URL, $url);//设置链接
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置是否返回信息
    curl_setopt($ch, CURLOPT_POST, false);//设置为POST方式
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $response = curl_exec($ch);//接收返回信息
    curl_close($ch); //关闭curl链接
    return $response;//显示返回信息
}

// post json
function PostJson($url, $data_json, $timeout = 1)
{
    $ch = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_URL, $url);//设置链接
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置是否返回信息
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8', 'Content-Length: ' . strlen($data_json)));//设置HTTP头
    curl_setopt($ch, CURLOPT_POST, true);//设置为POST方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);//POST数据
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $response = curl_exec($ch);//接收返回信息
    curl_close($ch); //关闭curl链接
    return $response;//显示返回信息
}


// 检查用户名是否存在
function userNameCheck($userName)
{
    $sql = "Select id From public.user_info Where id = $1 or tel = $2";
    $param = array($userName, $userName);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['id'])) {
        return true;
    }
    return false;
}

// 检查电话号是否存在
function mobileCheck($mobile)
{
    $sql = "Select id From public.user_info Where id = $1 or tel = $2";
    $param = array($mobile, $mobile);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['id'])) {
        return true;
    }
    return false;
}

// 检查邮箱是否存在
function emailCheck($email)
{
    $sql = "Select id From public.user_info Where email = $1 limit 1";
    $param = array($email);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['id'])) {
        return true;
    }
    return false;
}

// domain检查是否存在
function domainCheck($domain)
{
    $sql = "Select id From public.host_info Where host = $1 limit 1";
    $param = array($domain);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['id'])) {
        return true;
    }
    return false;
}

// 是否可以访问domain
function checkLookDomain($userId, $domainId)
{
    $sql = "Select id From public.host_info Where id = $1 and host_admin = $2";
    $param = array($domainId, $userId);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['id'])) {
        return true;
    }
    return false;
}

function checkDomainAccess($userId, $domain)
{
    $sql = "Select id From public.host_info Where host = $1 and host_admin = $2";
    $param = array($domain, $userId);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['id'])) {
        return true;
    }
    return false;
}

// 获取登录的用户信息
function getUserInfo()
{
    $userCookie = new UserCookie();
    $userInfo = $userCookie->loginUserInfo();
    if (empty($userInfo)) {
        return null;
    }
    $sql = "Select * From public.user_info Where id = $1";
    $param = array($userInfo['uid']);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    return $result;
}

// 获取域用户的信息
function getHostUserInfo($userId)
{
    $userCookie = new UserCookie();
    $userInfo = $userCookie->loginUserInfo();
    if (empty($userInfo)) {
        return array("ok" => 0, "msg" => "未登录");
    }
    $sql = "select h.host_admin, h.host, u.* from public.host_users as u left join public.host_info as h on u.host_id = h.id where u.id = $1";
    $param = array($userId);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if ($userInfo["user_name"] != $result["host_admin"]) {
        return array("ok" => 0, "msg" => "无权访问");
    }
    return array("ok" => 1, "msg" => $result);
}

// 获取Host列表
function getHosstList($userName)
{
    $sql = "select * from public.host_info Where host_admin = $1";
    $param = array($userName);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, false);
    $get_host_status_url = "http://l-imapp1.vc.zh.qunar.com:10056/get_host_alive";
    $hosts = array_column($result, 'host');
    $response = json_decode(PostJson($get_host_status_url, json_encode($hosts)), true);
    if ($response["ret"]) {
        $hosts_status = $response["errmsg"];
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["state"] = $hosts_status[$result[$i]["host"]];
        }
        return $result;
    } else {
        return $result;
    }
}

// 获取域信息
function getHostInfo($host_id)
{
    $sql = "select * from public.host_info Where id = $1";
    $param = array($host_id);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    return $result;
}

function getHostInfoByHost($host)
{
    $sql = "select * from public.host_info Where host = $1";
    $param = array($host);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    return $result;
}

function updateDept($hostId)
{
    $requestUrl = "http://<?qtalk qtalk_http_service>:9888/qtalk_http_service/i/qtapi/nck/updateDept.qunar?dept={$hostId}";
    $result = json_decode(GetUrl($requestUrl, "", 5), true);
    //var_dump($result);
    if ($result["ret"]) {
        $error_rs = array(
            "ok" => 1,
            "msg" => "成功。"
        );
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "更新组织架构失败，原因：{$result['errmsg']}。",
        );
    }
    return $error_rs;
}

//  踢用户
function kickUser($userId, $domain)
{
    $requestUrl = "http://<?qtalk qtalk_http_service>:9888/qtalk_http_service/i/qtapi/nck/kickUser.qunar";
    $requestData = array(
        "user" => $userId,
        "server" => $domain
    );
    $result = json_decode(PostJson($requestUrl, json_encode($requestData)), true);
    //var_dump($result);
    if ($result["ret"]) {
        $error_rs = array(
            "ok" => 1,
            "msg" => "成功。"
        );
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "踢下线失败，原因：{$result['errmsg']}。",
        );
    }
    return $error_rs;
}

// 获取域的最大版本号
function getMaxHostUserVersion($host_id){
    $sql = "select max(version) as version from public.host_users Where host_id = $1";
    $param = array($host_id);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    return $result["version"]+1;
}



// 修改密码
if ($action == "cpwd") {
    $userCookie = new UserCookie();
    $userInfo = $userCookie->loginUserInfo();
    if (empty($userInfo)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "未登录",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    $userId = $userInfo["uid"];
    $opwd = htmlspecialchars(filter_input(INPUT_POST, 'opwd', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $npwd = htmlspecialchars(filter_input(INPUT_POST, 'npwd', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $cpwd = htmlspecialchars(filter_input(INPUT_POST, 'cpwd', FILTER_SANITIZE_STRING), ENT_QUOTES);

    if (empty($opwd)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "旧密码为空",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    if (empty($npwd)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "新密码为空",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    if ($npwd != $cpwd) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "两次密码不一样",
        );
        print_r(json_encode($error_rs));
        exit;
    }

    $sql = "Select id, user_id, nick_name, type From public.user_info Where id = $1 and password = $2";
    $param = array($userId, $opwd);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['user_id'])) {
        $sql = "update public.user_info set password = $1 where id = $2";
        $param = array($npwd, $userId);
        if (DBQTalk::safeQuery($sql, $param)) {
            $value = array(
                "uid" => $result['id'],
                "user_name" => $result['user_id'],
                "nick_name" => $result['nick_name'],
            );
            $userCookie = new UserCookie();
            $userCookie->saveLoginCookie($value['uid'], $value, $npwd);
            $error_rs = array(
                "ok" => 1,
                "msg" => "修改密码成功！",
            );
            print_r(json_encode($error_rs));
            exit;
        } else {
            $error_rs = array(
                "ok" => 0,
                "msg" => "修改密码失败！",
            );
            print_r(json_encode($error_rs));
            exit;
        }
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "旧密码不正确！",
        );
        print_r(json_encode($error_rs));
        exit;
    }
}

// 更新用户信息
if ($action == 'update_user_info') {
    $userCookie = new UserCookie();
    $userInfo = $userCookie->loginUserInfo();
    if (empty($userInfo)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "未登录",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    $userId = $userInfo["uid"];
    $nickname = htmlspecialchars(filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING), ENT_QUOTES);
    if (empty($nickname)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "新昵称为空",
        );
        print_r(json_encode($error_rs));
        exit;
    }

    $sql = "Select id, user_id, password From public.user_info Where id = $1";
    $param = array($userId);
    $result = DBQTalk::GetSafeQueryResult($sql, $param, true);
    if (isset($result['user_id'])) {
        $pwd = $result['password'];
        $sql = "update public.user_info set nick_name = $1 where id = $2";
        $param = array($nickname, $userId);
        if (DBQTalk::safeQuery($sql, $param)) {
            $value = array(
                "uid" => $result['id'],
                "user_name" => $result['user_id'],
                "nick_name" => $nickname,
            );
            $userCookie = new UserCookie();
            $userCookie->saveLoginCookie($value['uid'], $value, $pwd);
            $error_rs = array(
                "ok" => 1,
                "msg" => "修改昵称成功！",
            );
            print_r(json_encode($error_rs));
            exit;
        } else {
            $error_rs = array(
                "ok" => 0,
                "msg" => "修改昵称失败！",
            );
            print_r(json_encode($error_rs));
            exit;
        }
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "用户不存在！",
        );
        print_r(json_encode($error_rs));
        exit;
    }
}

// 导入用户
if ($action == 'import_users') {
    require_once dirname(__FILE__) . '/../common/importUser.class.php';
    $userCookie = new UserCookie();
    $userInfo = $userCookie->loginUserInfo();
    if (empty($userInfo)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "未登录",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    $userId = $userInfo["user_name"];
    $domainId = htmlspecialchars(filter_input(INPUT_POST, 'host_id', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $domain = htmlspecialchars(filter_input(INPUT_POST, 'host', FILTER_SANITIZE_STRING), ENT_QUOTES);
    if (!checkLookDomain($userId, $domainId)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "无权管理该域！",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    $iu = new importUser();
    $result = $iu->importArray($domainId);
    print_r(json_encode($result));
    exit;

}

// 增加用户
if ($action == "add_user_list") {
    require_once dirname(__FILE__) . '/../common/importUser.class.php';
    $requestBody = @file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    DBQTALK::TransBegin();
    foreach ($data as $userInfo) {
        $uid = $userInfo['user_id'];
        $name = $userInfo['user_name'];
        $department = $userInfo['department'];
        $dep1 = $userInfo['dep1'];
        $dep2 = $userInfo['dep2'];
        $dep3 = $userInfo['dep3'];
        $dep4 = $userInfo['dep4'];
        $dep5 = $userInfo['dep5'];
        $tel = $userInfo['tel'];
        $pwd = mt_rand(111111, 999999);
        $hostId = $userInfo['host_id'];
        if (empty($name)) {
            $error_rs = array(
                "ok" => 0,
                "msg" => "姓名不能为空",
            );
            DBQTALK::TransRollback();
            print_r(json_encode($error_rs));
            exit;
        }

        if (!Utility::checkMobile($tel)) {
            $error_rs = array(
                "ok" => 0,
                "msg" => "手机号格式不正确",
            );
            DBQTALK::TransRollback();
            print_r(json_encode($error_rs));
            exit;
        }

        if (empty($dep1)) {
            $error_rs = array(
                "ok" => 0,
                "msg" => "部门不能为空",
            );
            print_r(json_encode($error_rs));
            exit;
        }
        $iu = new importUser();
        if ($iu->checkUserExists($uid, $hostId)) {
            $error_rs = array(
                "ok" => 0,
                "msg" => "[{$uid}]用户已存在",
            );
            DBQTALK::TransRollback();
            print_r(json_encode($error_rs));
            exit;
        }
        // 清楚已离职的人
        $iu->clearLeaveOffice($uid, $hostId);
        
        $maxVersion = getMaxHostUserVersion($hostId);
        $inserData = array(
            "user_name" => $name,
            "pinyin" => $name,
            "dep1" => $dep1,
            "dep2" => $dep2,
            "dep3" => $dep3,
            "dep4" => $dep4,
            "dep5" => $dep5,
            "department" => $department,
            "tel" => $tel,
            "password" => $pwd,
            "user_id" => $uid,
            "host_id" => $hostId,
            "version" => $maxVersion,
        );
        $new_id = DBQTalk::Insert("public.host_users", $inserData, 'id');
        if (empty($new_id)) {
            $error_rs = array(
                "ok" => 0,
                "msg" => "操作失败",
            );
            DBQTALK::TransRollback();
            print_r(json_encode($error_rs));
            exit;
        }
    }

    DBQTALK::TransCommit();
//    $domain = filter_input(INPUT_GET, 'host'); // user name
    $result = updateDept($hostId);
    $error_rs = array(
        "ok" => 1,
        "msg" => "操作成功",
    );
    print_r(json_encode($error_rs));
    exit;
}

// 增加用户
if ($action == "add_user") {
    require_once dirname(__FILE__) . '/../common/importUser.class.php';
    $data = array();
    $data['user_name'] = trim(htmlspecialchars(filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING), ENT_QUOTES));
    $data['dep1'] = $data['department'] = trim(htmlspecialchars(filter_input(INPUT_POST, 'department', FILTER_SANITIZE_STRING), ENT_QUOTES));
    $data['tel'] = trim(htmlspecialchars(filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING), ENT_QUOTES));
    $data['password'] = mt_rand(111111, 999999);
    $data['user_id'] = trim(htmlspecialchars(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING), ENT_QUOTES));
    $data['host_id'] = trim(htmlspecialchars(filter_input(INPUT_POST, 'host_id', FILTER_SANITIZE_STRING), ENT_QUOTES));

    if (empty($data['user_name'])) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "姓名不能为空",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    if (!Utility::checkMobile($data['tel'])) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "手机号格式不正确",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    if (empty($data['department'])) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "部门不能为空",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    $iu = new importUser();

    if ($iu->checkUserExists($data['user_id'], $data['host_id'])) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "该用户已存在",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    $new_id = DBQTalk::Insert("public.host_users", $data, 'id');

    if ($new_id) {
        $error_rs = array(
            "ok" => 1,
            "msg" => "操作成功",
        );
        print_r(json_encode($error_rs));
        exit;
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "操作失败",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    exit;
}

// 重置用户密码
if ($action == "reinit_password") {
    $userId = htmlspecialchars(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $domain = htmlspecialchars(filter_input(INPUT_POST, 'host', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $userName = htmlspecialchars(filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING), ENT_QUOTES);
    if (!empty($userId)) {
        $password = mt_rand(111111, 999999);
        $sql = "update public.host_users set password='{$password}', initialpwd = 1 where id = '{$userId}'";
        if (DBQTalk::Query($sql)) {
            $resut = kickUser($userName, $domain);
            print_r(json_encode($resut));
            exit;
            $error_rs = array(
                "ok" => 1,
                "value" => $password,
                "msg" => "操作成功",
            );
            print_r(json_encode($error_rs));
            exit;
        } else {
            $error_rs = array(
                "ok" => 0,
                "msg" => "操作失败",
            );
            print_r(json_encode($error_rs));
            exit;
        }
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "操作失败",
        );
        print_r(json_encode($error_rs));
        exit;
    }
}


// 删除用户
if ($action == "delete_user") {
    $userId = htmlspecialchars(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $domain = htmlspecialchars(filter_input(INPUT_POST, 'host', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $userName = htmlspecialchars(filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $hostId = htmlspecialchars(filter_input(INPUT_POST, 'host_id', FILTER_SANITIZE_STRING), ENT_QUOTES);
    if (!empty($userId)) {
        $maxVersion = getMaxHostUserVersion($hostId);
        $sql = "update public.host_users set hire_flag = 0,version={$maxVersion} where id = '{$userId}'";
        if (DBQTalk::Query($sql)) {
            $result = kickUser($userName, $domain);
            updateDept($hostId);
            $error_rs = array(
                "ok" => 1,
                "value" => $password,
                "msg" => "操作成功",
            );
            print_r(json_encode($error_rs));
            exit;
        } else {
            $error_rs = array(
                "ok" => 0,
                "msg" => "操作失败",
            );
            print_r(json_encode($error_rs));
            exit;
        }
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "操作失败",
        );
        print_r(json_encode($error_rs));
        exit;
    }
}


// 更新域用户信息
if ($action == "update_domain_user") {
    $hostId = htmlspecialchars(filter_input(INPUT_POST, 'host_id', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $uid = htmlspecialchars(filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $userId = htmlspecialchars(filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $name = htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $department = htmlspecialchars(filter_input(INPUT_POST, 'department', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $dep1 = htmlspecialchars(filter_input(INPUT_POST, 'dep1', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $dep2 = htmlspecialchars(filter_input(INPUT_POST, 'dep2', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $dep3 = htmlspecialchars(filter_input(INPUT_POST, 'dep3', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $dep4 = htmlspecialchars(filter_input(INPUT_POST, 'dep4', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $dep5 = htmlspecialchars(filter_input(INPUT_POST, 'dep5', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $mobile = htmlspecialchars(filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $email = htmlspecialchars(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $gender = htmlspecialchars(filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $updateDept = htmlspecialchars(filter_input(INPUT_POST, 'update_dept', FILTER_SANITIZE_STRING), ENT_QUOTES);
    if (empty($name)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "用户名为空",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    if (empty($mobile)) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "手机号为空",
        );
        print_r(json_encode($error_rs));
        exit;
    }
    $result = getHostUserInfo($uid);
    if (!$result["ok"]) {
        print_r(json_encode($result));
        exit;
    }
    if ($result["msg"]["user_id"] != $userId) {
        $error_rs = array(
            "ok" => 0,
            "msg" => "用户ID不正确",
        );
        print_r(json_encode($error_rs));
        exit;
    }

//    $sql = "select user_id from public.host_users WHERE id != $1 and user_id = $2;";
//    $param = array($uid,$userId);
//    $host_user = DBQTalk::GetSafeQueryResult($sql, $param,true);
//    if (isset($host_user['user_id'])) {
//        $error_rs = array(
//            "ok" => 0,
//            "msg" => "更新的用户ID[{$userId}]已存在",
//        );
//        print_r(json_encode($error_rs));
//        exit;
//    }

    $sql = "update public.host_users set user_name = $1,department = $2,tel = $3,email = $4,gender = $5,dep1 = $6,dep2 = $7,dep3 = $8,dep4 = $9,dep5 = $10 where user_id = $11 and id = $12;";
    $param = array($name, $department, $mobile, $email, $gender, $dep1, $dep2, $dep3, $dep4, $dep5, $userId, $uid);
    if (DBQTalk::safeQuery($sql, $param)) {
        if ($updateDept == 'true') {
            $maxVersion = getMaxHostUserVersion($hostId);
            $sql = "update public.host_users set version = {$maxVersion} where id = {$uid}";
            DBQTalk::safeQuery($sql, array());
            $result = updateDept($hostId);
        }
        $error_rs = array(
            "ok" => 1,
            "msg" => "修改域用户信息成功！",
        );
        print_r(json_encode($error_rs));
        exit;
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "修改域用户信息失败！",
        );
        print_r(json_encode($error_rs));
        exit;
    }
}

if ($action == 'kick_user') {
    $userId = htmlspecialchars(filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $domain = htmlspecialchars(filter_input(INPUT_POST, 'host', FILTER_SANITIZE_STRING), ENT_QUOTES);
    $requestUrl = "http://<?qtalk qtalk_http_service>:9888/qtalk_http_service/i/qtapi/nck/kickUser.qunar";
    $requestData = array(
        "user" => "lffan.liu",
        "server" => "ejabhost1"
    );

    $result = json_decode(PostJson($requestUrl, json_encode($requestData)), true);
    var_dump($result);
    if ($result["ret"]) {
        $error_rs = array(
            "ok" => 1,
            "msg" => "成功。"
        );
        print_r(json_encode($error_rs));
        exit;
    } else {
        $error_rs = array(
            "ok" => 0,
            "msg" => "踢下线失败，原因：{$result['errmsg']}。",
        );
        print_r(json_encode($error_rs));
        exit;
    }
}

// 未知处理
if (!empty($action)) {
    $error_rs = array(
        "ok" => 0,
        "msg" => "不认识[$action]事件。",
    );
    print_r(json_encode($error_rs));
    exit;
}

?>

