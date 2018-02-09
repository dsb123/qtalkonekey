<?php
//date_default_timezone_set("PRC");
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

class search_place
{
function __construct(){}
function start_search()
{
$ak = 'mQpOIxMNzDvVSto3iv838If5GYUEDYcU';
 
//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
$sk = 'SB38TXbvzoEOqxqztn53X7lKp9ZPNiSr';
 
//以Geocoding服务为例，地理编码的请求url，参数待填
$url = 'http://api.map.baidu.com/place/v2/search?query=%s&region=%s&output=%s&page_size=%s&page_num=%s&scope=%s&ak=%s&city_limit=true'; 
//get请求uri前缀
$uri = '/place/v2/search';
 
//地理编码的请求中address参数
$q = $_GET['term'];
$region=$_GET["city"];
$page_size=$_GET['psize'];
$page_num=$_GET['pnum'];
$scope='2';
//地理编码的请求output参数
$output = 'json';
 
//构造请求串数组
$querystring_arrays = array (
	'q' => $q,
	'region'=>$region,
	'output' => $output,
	'ak' => $ak,
	'page_size'=>$page_size,
	'page_num'=>$page_num,
	'scope'=>$scope,
        'city_limit'=>true
);

/* 
//以Geocoding服务为例，地理编码的请求url，参数待填
$url = "http://api.map.baidu.com/geocoder/v2/?address=%s&output=%s&ak=%s&sn=%s";
 
//get请求uri前缀
$uri = '/geocoder/v2/';
 
//地理编码的请求中address参数
$address = '百度大厦';
 
//地理编码的请求output参数
$output = 'json';
 
//构造请求串数组
$querystring_arrays = array (
	'address' => $address,
	'output' => $output,
	'ak' => $ak
);
*/
//调用sn计算函数，默认get请求
//$sn = $this->caculateAKSN($ak, $sk, $uri, $querystring_arrays);
// $target = sprintf($url, urlencode($address), $output, $ak, $sn);
//请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
$target = sprintf($url, urlencode($q),urlencode($region), $output,$page_size,$page_num,$scope, $ak);
$this->output_result($target);
//echo $target;
}
function output_result($url)
{
       require_once("model/Libcurl.class.php");
        $http =  new Libcurl($url);
        $http->doGET();
        $return = $http->getBody();
        $arr_result = json_decode($return,true);
$html="名字,地址,电话<br>";
if($arr_result['status']==0&&isset($arr_result['results']))
{
        $len = count($arr_result['results']);
if($len==0) {
echo "没有数据";
exit();
}
	for($x=0;$x<$len;$x++)
	{
	  $item=$arr_result['results'][$x];
          $html=$html.$item['name'].',';
          $html=$html.$item['address'].',';
	  if(isset($item['telephone']))
          {
                $tel = str_replace(',','|',$item['telephone']);
          	$html=$html.$tel.'<br>';
	  }
          else
         {
	       $html=$html.'no telephone<br>';
         }
	}
}
echo $html;
}
function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET')
{  
    if ($method === 'POST'){  
        ksort($querystring_arrays);  
    }  
    $querystring = http_build_query($querystring_arrays);  
    return md5(urlencode($url.'?'.$querystring.$sk));  
}
}
$search_service=new search_place();
$search_service->start_search();
//echo "aaa";
?>
