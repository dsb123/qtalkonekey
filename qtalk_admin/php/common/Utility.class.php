<?php

class Utility {
    
    /**
     * userid  唯一规则 public_{hostid}_{tel}
     * @param type $host_id
     * @param type $mobile
     * @return type
     */
    static function buildPublicUserId($host_id,$mobile){
        return "public_{$host_id}_{$mobile}";
    }
    /**
     * 检测 手机号
     * @param type $mobile
     * @return type
     */
    static function checkMobile($mobile){
        return preg_match('/^1[34578][\d]{9}$/', $mobile);
    }
    /**
     * 优化HttpRequest函数，由于需要更多的参数，优化参数为数组类型，也建议以后使用数组类型的参数
     * @author meijie.ge meijie.ge@qunar.com
     * @param type $options
     * @return type
     * 之前注释：
     *  zhangyu：支持timeout设置，默认2秒
     * gemeijie:改成了1秒，2秒如果请求失败，会再次请求一个DoGet/DoPost的2秒超时请求，这样页面肯定会502
     */
    static function GetHttpRequest($options = array()) {
        $url = !empty($options['url']) ? strval($options['url']) : '';
        $data = !empty($options['data']) ? (is_array($options['data']) ? $options['data'] : array()) : array();
        $abort = !empty($options['abort']) ? boolval($options['abort']) : true;
        $timeout = is_numeric($options['timeout']) &&$options['timeout']>0  ? $options['timeout'] : 1;
//        $log = !empty($options['log']) ? boolval($options['log']) : false; 
        $cookie = !empty($options['cookie']) ? strval($options['cookie']) : '';
        $log_prefix = !empty($options['log_prefix']) ? strval($options['log_prefix']) : '';
        $log_action = !empty($options['log_action']) ? strval($options['log_action']) : '';
        $log_type = !empty($options['log_type']) ? strval($options['log_type']) : 'all';

        
        $ch = curl_init();
        if($cookie){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if (isset($options['setcookie']) && $options['setcookie']) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        if (is_array($data) && $data) {
            if ($options['post_json']) {//支持post json格式
                $json_options = !empty($options['json_options']) ? $options['json_options'] : '';
                if ($json_options) {
                    $formdata = json_encode($data, $json_options);
                } else {
                    $formdata = json_encode($data);
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
            } elseif ($options['post_formdata']) { //支持form data
                $formdata = $data;
            } else {
                $formdata = http_build_query($data);
            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
        }else{
            $headers = array(
                "Cache-Control: no-cache",
            );
            if(array_key_exists("header_accept", $options)){
                array_push($headers, $options['header_accept']);
            }
            if($headers){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
        }
        if(strtolower(substr($url, 0,8) =='https://')){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在 
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, ($timeout * 1000));
        $result = curl_exec($ch);
        ###echo "curl -i -X POST -d '{$formdata}' {$options['url']}";
        if ($log_prefix && $log_action) {
            QLog::info($log_prefix, $log_action, "param:" . json_encode($options));
            if (in_array($log_type, array("all", "http_code"))) {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($http_code == 0) {
                    $http_code = curl_errno($ch);
                    if ($http_code == 28) {
                        //curl超时
                        $http_code = 504;
                    }
                }
                QLog::info($log_prefix, $log_action, "http_code:" . $http_code . ($http_code != 200 ? ";" . $url : ""));
            }
            if (in_array($log_type, array("all"))) {
                QLog::info($log_prefix, $log_action, $result);
            }
        }

        curl_close($ch);

        return (false === $result && false == $abort) ? ( empty($data) ? self:: DoGet($url, 1, $cookie) : self::DoPost($url, $data) ) : $result;
    }
    

    static function IsMobile($no) {
        return preg_match('/^1[\d]{10}$/', $no)
                || preg_match('/^0[\d]{10,11}$/', $no);
    }
}