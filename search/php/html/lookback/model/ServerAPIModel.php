<?php
include_once("model/logger.php");
class ServerAPIModel{
    private $api_urls = array("http://<?qtalk elasticsearch>:9200/");
    private $api_url = "http://<?qtalk elasticsearch>:9200/";
    private $http_url = "http://<?qtalk qtalk_cowboy_server>:10056/";
    private $size = 15;
    function __construct(){}
    public function searchAPI($raw_data)
    {
        $size = $this->size;
        if(isset($raw_data['pagesize']))$size=$raw_data['pagesize'];
$raw=<<<eof
{
  "query": {
    "bool": {
      "must":[{
        "query_string": {
          "default_field": "body",
          "query": "{$raw_data["keyword"]}"
        }
      }],
      "should": [
        {
          "term": {
            "from": "{$raw_data["user"]}"
          }
        },
        {
          "match": {
            "to": "{$raw_data["user"]}"
          }
        }
      ],
      "minimum_should_match": 1
    }
  },
  "from": {$raw_data["from"]},
  "size": {$size},
  "highlight":{
     "fields":{
	"body":{}
     }
  }
}
eof;
/*  "sort": {
    "time": {
      "order": "desc"
    }
  }
*/
        return $this->handleSingleResult($raw);
    }
    
    public function actualSearch($raw_data)
    {
$raw=<<<eof
{
  "query": {
    "filtered": {
      "query": {
        "match": {
          "body": "{$raw_data['keyword']}"
        }
      },
      "filter": {
        "term": {
          "conversation": "{$raw_data['conv']}"
        }
      }
    }
  },
 "highlight": {
    "fields": {
      "body": {}
    }
  },
  "from": {$raw_data["from"]},
  "size": {$this->size}
}
eof;
        return $this->handleSingleResult($raw);
    }    

    public function searchDetailsAPI($raw_data)
    {
/*$time_pre="message.time";
$term_filter="message.conversation";
if($raw_data['flag']!="0")
{
    $time_pre="muc_msg.time";
    $term_filter="muc_msg.from";
}
$index=$raw_data['user']."_".$raw_data['from'];
$start=$raw_data['time'] - 120000;
$end=$raw_data['time'] + 120000;
$raw=<<<eof
{
  "query": {
    "filtered": {
      "query": {
        "range": {
          "{$time_pre}": {
            "gte": {$start},
            "lte": {$end}
          }
        }
      },
      "filter": {
        "term": {
          "{$term_filter}": "{$index}"
        }
      }
    }
  },
  "sort": {
    "{$time_pre}": {
      "order": "asc"
    }
  }
}
eof;*/
        require_once("model/Libcurl.class.php");
        $http = new Libcurl($this->http_url."getadjacentmsg");
        $http->doPOST($raw_data,true);
        $return = $http->getBody();
        $arr_result = json_decode($return,true);
        return $arr_result;
    }
    
    public function get_nicks($raw_data)
    {
        require_once("model/Libcurl.class.php");
        $http = new Libcurl($this->http_url."getusernick");
        $http->doPOST($raw_data,true);
        $return = $http->getBody();
        $arr_result = json_decode($return,true);
        return $arr_result;
    }
    
    public function get_single_history($fu,$tu,$ts,$limit,$d,$u,$k)
    {
/*$index =$fu<$tu?$fu."_".$tu:$tu."_".$fu;
$direction = "gt";
if($d=="0") $direction="lt";
$raw=<<<eof
{
  "query": {
    "filtered": {
      "query": {
        "range": {
          "message.time": {
            "{$direction}": {$ts}
          }
        }
      },
      "filter": {
        "term": {
          "message.conversation": "{$index}"
        }
      }
    }
  },
  "from": 0,
  "size": {$limit},
  "sort": {
    "message.time": {
      "order": "desc"
    }
  }
}
eof;
	return $this->handleSingleResult($raw);*/
 	require_once("model/Libcurl.class.php");
	$url = $this->http_url."domain/get_msgs?u=".$u.
                                "&k=".$k."&p=search&v=1";
        $http = new Libcurl($url);

$body=<<<eof
{"from":"{$fu}",
"to":"{$tu}",
"from_host":"ejabhost1",
"to_host":"ejabhost1",
"timestamp":"{$ts}",
"limitnum":"{$limit}",
"direction":"{$d}",
"u":"{$u}",
"k":"{$k}"}
eof;

        $http->doPOST($body,true);
        $return = $http->getBody();
        $arr_result=json_decode($return,true);
        $result=array();
        $result['ret']= $arr_result['ret'];
        if($result['ret'])$result['data']=$arr_result['data'];
        return $result;

    }
    
    public function get_muc_history($term,$muc_arr,$start,$pagesize)
    {
	$c = count($muc_arr);
	if($c==1)
	{
	   return  $this->actualSearchMuc($muc_arr,$term,$start);
	}
        $match_raw = "";
        $end = count($muc_arr)-1;
        $x = false;
	foreach($muc_arr as $k=>$v)
        {
            if($x)
            {
                $match_raw=$match_raw.",";
            }
            else{
                $x=true;
            }

            $match_raw=$match_raw.'{"term": {"from": "'.$k.'"}}';
        }
$muc_query=<<<eof
{
  "query": {
    "bool": {
      "must": {
        "query_string": {
          "default_field": "body",
          "query": "{$term}"
        }
      },
      "should": [
	{$match_raw}
      ],
      "minimum_should_match": 1
    }
  },
  "from": {$start},
  "size": {$pagesize},
  "highlight":{
       "fields":{
          "body":{}
       }
   }
}
eof;
        return $this->handleMucResult($muc_query,$muc_arr);
    }

    private function actualSearchMuc($muc_arr,$term,$offset)
    {
$k=key($muc_arr);
$muc_query=<<<eof
{
  "query": {
    "filtered": {
      "query": {
        "match": {
          "body": "{$term}"
        }
      },
      "filter": {
        "term": {
          "from": "{$k}"
        }
      }
    }
  },
 "highlight": {
    "fields": {
      "body": {}
    }
  },
  "from": {$offset},
  "size": {$this->size}
}
eof;
	return $this->handleMucResult($muc_query,$muc_arr);
    }   

    public function get_muc_more_history($muc_id,$timestamp,$limit,$d,$u,$k,$name='群组')
    {
/*$direction = "gt";
if($d=="0") $direction="lt";
$muc_query=<<<eof
{
  "query": {
    "filtered": {
      "query": {
        "range": {
          "muc_msg.time": {
            "$direction": {$timestamp}
          }
        }
      },
      "filter": {
        "term": {
          "muc_msg.from": "{$muc_id}"
        }
      }
    }
  },
  "from": 0,
  "size": {$limit},
  "sort": {
    "muc_msg.time": {
      "order": "desc"
    }
  }
}
eof;
        $muc_arr=array($muc_id=>$name);
	return $this->handleMucResult($muc_query,$muc_arr);*/
require_once("model/Libcurl.class.php");
	$url = $this->http_url."domain/get_muc_msg?u=".$u."&k=".$k."&p=search&v=1";
        $http = new Libcurl($url);
$json_body=<<<eof
{"muc_name":"{$muc_id}",
"timestamp":"{$timestamp}",
"limitnum":"{$limit}",
"direction":"{$d}",
"type":"0",
"domain":"ejabhost1",
"u":"{$u}",
"k":"{$k}"}
eof;
        $http->doPOST($json_body,true);
        $return = $http->getBody();
        $arr_result=json_decode($return,true);
        $result= array("ret"=>$arr_result['ret']);
        if($result['ret'])$result['data']=$arr_result['data']['Msg'];
        return $result;
    }
    public function agg_search_single($raw_data)
    {
        $size = 5;
        if(isset($raw_data['pagesize']))$size=$raw_data['pagesize'];
        $raw=<<<eof
{
  "query": {
    "filtered": {
      "query": {
        "match": {
          "body": "{$raw_data['keyword']}"
        }
      },
      "filter": {
        "bool": {
          "should": [
            {
              "term": {
                "to": "{$raw_data['user']}"
              }
            },
            {
              "term": {
                "from": "{$raw_data['user']}"
              }
            }
          ]
        }
      }
    }
  },
  "aggs": {
    "top_to": {
      "terms": {
        "field": "conversation",
        "size":{$size}
      },
      "aggs": {
        "top_to_hits": {
          "top_hits": {
            "from": 0,
            "size": 1
          }
        }
      }
    }
  },
  "size": 0
}
eof;
        $url=$this->generateUrl("message");
        return $this->handleAggResult($raw,$url,array("user"=>$raw_data['user']));
    }
    public function agg_search_muc($raw_data,$muc_arr)
    {
        $match_raw = "";
        $end = count($muc_arr)-1;
        $x = false;
        foreach($muc_arr as $k=>$v)
        {
            if($x)
            {
                $match_raw=$match_raw.",";
            }
            else{
                $x=true;
            }

            $match_raw=$match_raw.'{"term": {"from": "'.$k.'"}}';
        }
$muc_query=<<<eof
{
  "query": {
    "filtered": {
      "query": {
        "match": {
          "body": "{$raw_data['keyword']}"
        }
      },
      "filter": {
        "bool": {
          "should": [
{$match_raw}
          ]
        }
      }
    }
  },
	"aggs": {
        "top_to": {
            "terms": {
                "field": "from",
                "size": 5
            },
            "aggs": {
                "top_to_hits": {
                    "top_hits": {
                        "from" : 0,
                        "size" : 1
                    }
                }
            }
        }
    },"size": 0
}
eof;
        $url =$this->generateUrl("muc_msg");
        return $this->handleAggResult($muc_query,$url,array("muc"=>$muc_arr));
    }
    
    public function generateUrl($type)
    {
        $date_info = getdate();
        $now_mon = $date_info["mon"];
        $now_year=$date_info["year"];
        $h = rand(0,2);
        $url = $this->api_urls[$h];
        //$url=$this->api_url;
        for($x=0;$x<3;$x++)
        {     
             if($x>0) $url=$url.",";
             $mon = $now_mon-$x;
             $year = $now_year;
             if($mon==0)
             {
                 $mon = 12;
                 $year = $year -1;
             }
             else if($mon<0){
                $mon = 12+$mon;
                $year = $year -1;
             }
             $url = $url."message_".$year."_".$mon;
        }
        $url = $url."/".$type."/_search";
        return $url;      
    }
    private function handleMucResult($muc_query,$muc_arr)
    {
        require_once("model/Libcurl.class.php");
        $url =$this->generateUrl("muc_msg");
        $http =  new Libcurl($url);
	$http->doPOST($muc_query,true);
        $return = $http->getBody();
        $arr_result = json_decode($return,true);
        $handled_result=array();
        $handled_result["ret"]=false;
        $handled_result["data"]=array();
        $handled_result["errmsg"] = "error";
        $handled_result["errcode"] = "-1";
	$handled_result["total"] = $arr_result["hits"]["total"];
        $len =isset($arr_result["hits"]["hits"])? 
			count($arr_result["hits"]["hits"]):
			0;
        if(isset($arr_result["timed_out"])&&!$arr_result["timed_out"]
                &&isset($arr_result["hits"])&&$len>0)
        {
                for($x=0;$x<$len;$x++)
                {
                    $muc = $arr_result["hits"]["hits"][$x]["_source"]["from"];
                    $handled_result["data"][$x]=array();
                    $handled_result["data"][$x]["N"]=
                        $arr_result["hits"]["hits"][$x]["_source"]["to"];
                    $handled_result["data"][$x]["M"]=$muc_arr[$muc];
                    $handled_result["data"][$x]["D"]=
                        $arr_result["hits"]["hits"][$x]["_source"]["time"];
                    $handled_result["data"][$x]["B"]=
                        $arr_result["hits"]["hits"][$x]["_source"]["msg"];
		    $handled_result["data"][$x]["body"]=
	            	$arr_result["hits"]["hits"][$x]["highlight"]["body"][0];
                    $handled_result["data"][$x]["R"]=$muc;
                }
                $handled_result["ret"]=true;
                $handled_result["errmsg"]="";
                $handled_result["errcode"]="0";
 		unset($arr_result);
        }
        return $handled_result;
    }
    private function handleSingleResult($raw)
    {
        require_once("model/Libcurl.class.php");
        $url =$this->generateUrl("message");
        $http =  new Libcurl($url);
        $http->doPOST($raw,true);
        $return = $http->getBody();
        $arr_result = json_decode($return,true);
        $handled_result=array();
        $handled_result["ret"]=false;
        $handled_result["data"]=array();
        $handled_result["errmsg"] = "error";
        $handled_result["errcode"] = "-1";
        $handled_result["total"] = $arr_result["hits"]["total"];
        $len = isset($arr_result["hits"]["hits"])?
			count($arr_result["hits"]["hits"]):
			0;
        if(isset($arr_result["timed_out"])&&!$arr_result["timed_out"]
                &&isset($arr_result["hits"])&&$len>0)
        {
                for($x=0;$x<$len;$x++)
                {
                    $handled_result["data"][$x]=array();
                    $handled_result["data"][$x]["F"]=
                        $arr_result["hits"]["hits"][$x]["_source"]["from"];
                    $handled_result["data"][$x]["T"]=
                        $arr_result["hits"]["hits"][$x]["_source"]["to"];
                    $handled_result["data"][$x]["D"]=
                        $arr_result["hits"]["hits"][$x]["_source"]["time"];
                    $handled_result["data"][$x]["B"]=
                        $arr_result["hits"]["hits"][$x]["_source"]["msg"];
		    $handled_result["data"][$x]["body"]=
			$arr_result["hits"]["hits"][$x]["highlight"]["body"][0];
                }
                $handled_result["ret"]=true;
                $handled_result["errmsg"]="";
                $handled_result["errcode"]="0";
                unset($arr_result);
	}
        return $handled_result;
    }
    
    private function handleAggResult($query,$url,$ext_arr)
    {
	file_log("url is :".$url);
	file_log("query is :".$query);

	require_once("model/Libcurl.class.php");        
        $http =  new Libcurl($url);
        $http->doPOST($query,true);
        $return = $http->getBody();
       $arr_result = json_decode($return,true);
        $handled_result=array();
        $handled_result["ret"]=false;
        $handled_result["data"]=array();
        $handled_result["errmsg"] = "error";
        $handled_result["errcode"] = "-1";
        $len=count($arr_result['aggregations']['top_to']['buckets']);
        if($len>0)
        {
           for($x=0;$x<$len;$x++)
           {
	       $name = $arr_result['aggregations']['top_to']['buckets'][$x]['key'];
               if(isset($ext_arr['muc']))
	       {
                    $handled_result["data"][$x]["muc_name"]= $ext_arr['muc'][$name];
	       }
	       else
	       {
		    $name=str_replace(array("_",$ext_arr['user']),'',$name);
	       }
	       $handled_result["data"][$x]["key"]=$name;
               $handled_result['data'][$x]['count']=$arr_result['aggregations']['top_to']['buckets'][$x]['doc_count'];
               if(isset($arr_result['aggregations']['top_to']['buckets'][$x]['key']))
	       {
                    $handled_result["data"][$x]["conv"]=
                        $arr_result['aggregations']['top_to']['buckets'][$x]['key'];
	       }
               if($handled_result['data'][$x]['count']==1)
               {
 		   $handled_result['data'][$x]['msg']=
			$arr_result['aggregations']['top_to']['buckets'][$x]['top_to_hits']['hits']['hits'][0]['_source'];                   
               }

  	       $handled_result["ret"]=true;
               $handled_result["errmsg"]="";
               $handled_result["errcode"]="0";
           }
	   unset($arr_result);
        }
        return $handled_result;
    }
    public function getVCardCross($userId,$domain,$u,$k)
    {
        $raw = array("domain"=>$domain,"users"=>array(array("user"=>$userId,"version"=>"0")));
  	require_once("model/Libcurl.class.php");
        $url=$this->http_url."domain/get_vcard_info?u=".$u."&k=".$k."&p=qsearch&v=1";
        $http =  new Libcurl($url);
        $http->doPOST($raw,true);
        $return = $http->getBody();
        $arr_result = json_decode($return,true);
        if($arr_result['ret'])
        {
           return $arr_result['data'][0]['users'][0]; 
        }
        return false;
    }
}
?>
