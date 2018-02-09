<?php
require_once("utils/XML2Array.php");
class QTalkUtility
{
public static function is_mobile_request() {
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = 0;
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))$mobile_browser++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) $mobile_browser++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) $mobile_browser++;
    if (isset($_SERVER['HTTP_PROFILE'])) {
        $mobile_browser++;
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
        );
    }
    if (isset($mobile_agents)&&in_array($mobile_ua, $mobile_agents)) $mobile_browser++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) $mobile_browser++;
    // Pre-final check to reset everything if the user is on Windows  
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) $mobile_browser = 0;
    // But WP7 is also Windows, with a slightly different characteristic  
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) $mobile_browser++;
    if ($mobile_browser > 0){
        return true;
    } else {
        return false;
    }
}

public static function parse_im_obj($body)
{
     $pattern = '/\[obj type="([\w]+)" value="([\S]+)"([\w|=|\s|\.]+)?\]/i';
     if(preg_match_all($pattern,$body,$arr))
     {
        $len = count($arr[0]);
        for($x=0;$x<$len;$x++)
        {
             $all_obj = $arr[0][$x];
             $type=$arr[1][$x];
             $value=$arr[2][$x];
             if($type=="image")
             {
                 $body=str_replace($all_obj,'<img src="https://qt.qunar.com/'.$value.'&user='.$_SESSION['user'].
                '&tkey='.$_SESSION['key'].'" />',$body);
             }else if($type=="url")
             {
                $body=str_replace($all_obj,'<a href="'.$value.'">'.$value.'</a>',$body);
             }
             else if($type=="emoticon"){
                $body=str_replace($all_obj,$value,$body);
             }
                //$html=$html."<br>".$all_obj."<br>".$type."<br>".$value;
        }

     }
     return $body;	
}

public static function convert_obj_2_html($msg)
{
     $array = XML2Array::createArray($msg);
     $html = "";
     if(is_array($array['message']['body'])){
	  $html = $array['message']['body']['@value'];
     }
     else{ 
         $html=$array['message']['body'];
     }
     $pattern = '/\[obj type="([\w]+)" value="([\S]+)"([\w|=|\s|\.]+)?\]/i';
     if(preg_match_all($pattern,$html,$arr))
     {
	$len = count($arr[0]);
	for($x=0;$x<$len;$x++)
        {
	     $all_obj = $arr[0][$x];
             $type=$arr[1][$x];
             $value=$arr[2][$x];
             if($type=="image")
             {
		$head = substr($value,0,4);
                if ($head == "http")
                {
                 	$html=str_replace($all_obj,'<img src="'.$value.'&user='.$_SESSION['user'].
			'&tkey='.$_SESSION['key'].'" />',$html);
		}else
		{
                        $html=str_replace($all_obj,'<img src="https://qt.qunar.com/'.$value.'&user='.$_SESSION['user'].
                        '&tkey='.$_SESSION['key'].'" />',$html);	
		}
             }else if($type=="url")
             {
                $html=str_replace($all_obj,'<a href="'.$value.'">'.$value.'</a>',$html);
             }
             else if($type=="emoticon"){
		$html=str_replace($all_obj,$value,$html);
	     }
		//$html=$html."<br>".$all_obj."<br>".$type."<br>".$value;
	}
	
     }
     return $html;
}

public static function parse_message($xml)
{
     $result = array();
     $array = XML2Array::createArray($xml);
     $html = "";
     if(is_array($array['message']['body'])){
          $html = $array['message']['body']['@value'];
     }
     else{
         $html=$array['message']['body'];
     }
     $pattern = '/\[obj type="([\w]+)" value="([\S]+)"([\w|=|\s]+)?\]/i';
     if(preg_match_all($pattern,$html,$arr))
     {
        $len = count($arr[0]);
        for($x=0;$x<$len;$x++)
        {
             $all_obj = $arr[0][$x];
             $type=$arr[1][$x];
             $value=$arr[2][$x];
             if($type=="image")
             {
                 $html=str_replace($all_obj,'<img src="https://qt.qunar.com/'.$value.'&user='.$_SESSION['user'].
                '&tkey='.$_SESSION['key'].'" />',$html);
             }else if($type=="url")
             {
                $html=str_replace($all_obj,'<a href="'.$value.'">'.$value.'</a>',$html);
             }
             else if($type=="emoticon"){
                $html=str_replace($all_obj,$value,$html);
             }
                //$html=$html."<br>".$all_obj."<br>".$type."<br>".$value;
        }

     }
     $tstamp = 0;
     if(isset($array['message']['stime']))
     {
        $stamp=strtotime($array['message']['stime']['@attributes']['stamp']);
        $stamp=$stamp+60*60*8;
     }
     else if(isset($array['message']['x']))
     {
	$stamp=strtotime($array['message']['x']['@attributes']['stamp']);
        $stamp=$stamp+60*60*8;
     }
     $result["body"]=$html;
     $result["stamp"]=$stamp;
     return $result;
}

}
?>
