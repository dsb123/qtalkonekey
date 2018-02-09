<?php
/**
 * Created by PhpStorm.
 * User: may
 * Date: 2016/10/19
 * Time: 下午2:06
 */

require_once(__DIR__ . '/qredis.sentinel.php');

class CKeyAuthorization
{
    private static function getKey($user)
    {
        global $cur_redis;
        if (!$cur_redis) {
            $cur_redis = qredis::getInstance();
        }

        $cur_redis->selectDB('2');
        return $cur_redis->HKEYS($user);
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

    public static function checkToken($user, $counter, $userKey)
    {
        $keys = self::getKey($user);
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
    private static function urlsafe_b64decode($string) {
   	$data = str_replace(array('-','_'),array('+','/'),$string);
   	$mod4 = strlen($data) % 4;
   	if ($mod4) {
       		$data .= substr('====', $mod4);
   	}
   	return base64_decode($data);
    }

    public static function checkCkeyString($ckeyString)
    {
        if (strlen($ckeyString) <= 0) {
            return false;
        }

        $theKey =base64_decode($ckeyString); //self::urlsafe_b64decode($ckeyString);

        parse_str($theKey, $output);

        //error_log("str is " . $theKey . " key is " . implode($output, "|"), 3, '/tmp/checktoken.txt');

        //error_log("total str is" . $ckeyString, 3, '/tmp/checktoken.txt');
        return self::checkToken($output['u'], $output['t'], $output['k']);
    }
}
