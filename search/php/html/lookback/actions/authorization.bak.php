<?php
require_once("model/qredis.sentinel.php");
//require_once("../../common/ckeyAuth.php"); //for ckey test, something might be wrong with quoation.
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

    public static function checkCkeyString($ckeyString)
    {
        if (strlen($ckeyString) <= 0) {
            return false;
        }

        $theKey = base64_decode($ckeyString); //self::urlsafe_b64decode($ckeyString);

        parse_str($theKey, $output);

        //error_log("str is " . $theKey . " key is " . implode($output, "|"), 3, '/tmp/checktoken.txt');

        //error_log("total str is" . $ckeyString, 3, '/tmp/checktoken.txt');

        return self::checkToken($output['u'], $output['t'], $output['k']);
    }


    private static function getKey($user)
    {
        global $cur_redis;
        if (!$cur_redis) {
            $cur_redis = qredis::getInstance();
        }

        $cur_redis->selectDB('2');

        return $cur_redis->HKEYS($user);

    }

    public static function checkToken($user, $counter, $userKey)
    {
        file_log($user);
        $keys = self::getKey($user);
        file_log($keys);
        //$deviceKeys = self::getDeviceKey($user);
        //$keys = array_merge($keys,$deviceKeys);
        $newKeys = array();

        if (count($keys) > 0) {
            $isMatch = false;

            foreach ($keys as &$key) {
                $newKey = $key . $counter;
                $theKey = strtoupper(md5($newKey));
                $userKey = strtoupper($userKey);
                array_push($newKeys, $theKey);


                if (strcmp($theKey, $userKey) == 0) {
                    $isMatch = true;

                    $_SESSION["user"] = $user;
                    $_SESSION["key"] = $key;
                    $_COOKIE['_u'] = $user;
                    $_COOKIE['_k'] = $key;

                    break;
                }
//                echo "newKey is " . $newKey . "\r\n";
//                echo "md5 is " . "\r\n";
//                echo "base64 is " . base64_encode($newKey) . "\r\n";
                unset($key);
            }

            if (!$isMatch) {
                error_log("user is " . $user . " counter is " . $counter . " key is " . $userKey . " keys is " . implode($newKeys, "|") . "\n", 3, "/tmp/checktoken.txt");
            }

            return $isMatch;
        } else {
            error_log("user is " . $user . " counter is " . $counter . " key is " . $userKey . " keys is NULL \n", 3, "/tmp/checktoken.txt");
        }
    }

    private static function getDeviceKey($user)
    {
        global $cur_redis;
        if (!$cur_redis) {
            $cur_redis = qredis::getInstance();
        }

        $cur_redis->selectDB('5');
        return $cur_redis->get($user);
    }

    public static function checkUK()
    {
        $headers = array();
        if (function_exists("apache_request_headers"))
            $headers = apache_request_headers();

        if (isset($_COOKIE['_q']) && isset($_COOKIE['_v'])) {
            file_log("qvdetected!" . $_COOKIE['_q'] . "and" . $_COOKIE['_v']);
        }

        if ($headers && isset($headers['-u']) && isset($headers['-k'])) {
            $user = $headers['-u'];
            $key = $headers['-k'];
        } else if (isset($_SERVER['-u']) && isset($_SERVER['-k'])) {
            $user = $_SERVER['-u'];
            $key = $_SERVER['-k'];
        } else if (isset($_COOKIE['q_ckey'])) {
            $ckeyString = $_COOKIE['q_ckey'];
            if (self::checkCkeyString($ckeyString)) {
                file_log("ckey:" . $ckeyString . "check ok!");
                $s_cookie = serialize($_COOKIE);
                $s_session = serialize($_SESSION);
                file_log("cookie:" . $s_cookie);
                file_log("session:" . $s_session);
                return true;
            } else {
                file_log("ckey:" . $ckeyString . "check failed!");
                return false;
            }
        } else if (isset($_COOKIE['_u']) && isset($_COOKIE['_k'])) {

            $user = $_COOKIE['_u'];
            $key = $_COOKIE['_k'];
            file_log("cookie!user: " . $user . "key" . $key . "success!");
        } else if (isset($_GET['user']) && isset($_GET['key'])) {
            $user = $_GET['user'];
            $key = $_GET['key'];
        } else if (isset($_SESSION["user"]) && isset($_SESSION["user"])) //uk
        {
            $user = $_SESSION["user"];
            $key = $_SESSION["key"];
        } else {
            file_log("user: " . $user . "key" . $key . "getfailed!");
            return false;
        }
        file_log("user: " . $user . "key" . $key);
        if (authorization::auth_user($user, $key)) {
            if (!isset($_SESSION["user"]))
                $_SESSION["user"] = $user;
            if (!isset($_SESSION["key"]))
                $_SESSION["key"] = $key;
            file_log("user: " . $user . "key" . $key . "success!");
            file_log("shouldbe here");
            return true;
        } else if (!authorization::auth_user($user, $key)) {
            file_log("user: " . $user . "key" . $key . "authorfailed");
        }
        unset($_SESSION['user']);
        unset($_SESSION['key']);
        return false;
    }
}