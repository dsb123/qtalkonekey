<?php

/**
 * Created by PhpStorm.
 * User: may
 * Date: 2016/10/26
 * Time: 上午11:50
 */
class UserLib
{
    private static $RECENT_TIME = '7 day';

    public static function searchUser($connection, $key, $limit, $offset = 0)
    {
        if ($connection) {

            $all = array();
            $sql = "select a.user_id, a.department, b.url as icon, a.user_name, b.mood from host_users as a left join vcard_version b on a.user_id = b.username where a.hire_flag != 0 and a.user_id ilike $1 or a.user_name ilike $1 or a.pinyin  ilike $1 order by length(replace(a.pinyin, $4, '')) limit $2 offset $3;";
            $result = pg_query_params($connection, $sql, array("%{$key}%", $limit, $offset, "$key"));
            while ($row = pg_fetch_array($result)) {
                $name = $row[3];
                $mood = $row[4];
                $department = $row[1];
                $username = $row[0];
//		if($groupId== Q01 ){
//		foreach ($username as $a) {
//			$time = UserLib::findlasttalk($connection, $userId, $a);
//			array_push($row[5], $time);}}
                array_push($all, array(
                        'icon' => $row[2],
                        'name' => $name,
                        'qtalkname' => $username,
                        'label' => $mood ? "{$name}({$username}) - ${mood}" : "{$name}({$username})",
                        'content' => "{$department}",
                        'uri' => "{$row[0]}@ejabhost1",
                    )
                );
            }
            return $all;
        } else {
            echo 'ERROR';
        }
    }

   public static function findlasttalk($connection, $user, $key)
	{
		if($connection){
			if($user){
				$sql = "SELECT max(create_time) from msg_history where (m_to =$1 and m_from = $2) or (m_to =$2 and m_from = $1);";
				$result = pg_query_params($connection, $sql, array($user, $key));
				return $result;
				 }
			else echo "userid required";
				}
		else echo "ERROR!";
	}
		

   public static function searchGroupbysingleuser($connection, $user, $key, $limit, $offset = 0)
    {
        $users = explode(' ', $key);
        $perSql = "";
        $count = 1;
        if (count($users) == 0) {
            $perSql = "'$key'" . " ";
            $count++;
        } else {
            foreach ($users as $per) {
                $enter = pg_escape_string($per);

                if ($per != end($users))
                    $perSql .= "'$enter', ";
                else {
                    $perSql .= "'$enter') ";
                }
                $count++;
            }
        }


        if ($connection) {
            $all = array();
	    $sql = "select A.muc_room_name,B.show_name, B.muc_title, B.muc_pic from (select muc_room_name,max from (select muc_room_name, max(create_time) from muc_room_history where muc_room_name in (select muc_name from muc_room_users where username in (select user_id from host_users where user_id in ($1, " . $perSql . " or user_name in ($1, " . $perSql . " or split_part(pinyin,'|',1) in ($1, " . $perSql . ") group by muc_name having count(*) = $2) group by muc_room_name )t1 order by max desc limit $3 offset $4) as A join muc_vcard_info as B on a.muc_room_name||'@conference.ejabhost1' = b.muc_name ;";    
		//$sql = "select a.muc_name, b.show_name, b.muc_title, b.muc_pic from ((select muc_name from muc_room_users where username = $1 ) intersect (select muc_name from muc_room_users where username in (select username from users where username in (" . $perSql . " or name in ( " . $perSql . " or fpinyin in (" . $perSql . ") group by muc_name having count(*) = $2 )) as a join muc_vcard_info as b on concat(a.muc_name, '@conference.ejabhost1') = b.muc_name limit $3 offset $4;";
	    // $result = pg_query_params($connection, $sql, array($user, "%{$key}%", $limit, $offset));
            $result = pg_query_params($connection, $sql, array($user, $count, $limit, $offset));
                while ($row = pg_fetch_array($result)) {
                $name = $row[1];
                $title = $row[2];
                $pic = $row[3];
		if (!$title) $title = '';
		//if it's null then it would cause crahs on Mac, so check null or unset or false by using "!isset($a)||empty($a)"
                array_push($all, array(
                        'icon' => $pic,
                        'label' => $name,
                        'content' => $title,
                        'uri' => "$row[0]@conference.ejabhost1",
                    )
                );
            }
            return $all;

        } else {
            echo 'ERROR';
        }
}
   public static function getGroupicon($connection, $muc_array)
    {
	if($connection){
	$all = array();
	$sql = "SELECT muc_name, muc_pic from muc_vcard_info where muc_name = '" . $muc_array . "@conference.ejabhost1';";
        $result = pg_query($connection, $sql);
        $row = pg_fetch_array($result);
        $icon_array = $row[1];
	$name = $row[0];
	return $icon_array;
	
	}else{
	echo 'ERROR';
	}}

/*

   public static function searchGroupbyKeyword($connection, $user, $key, $limit, $offset = 0)
    {
        if ($connection) {
//   /lookback/main_controller.php?action=search_4_react&method=search_muc&term={{$key}}&pagesize={{$limit}}&start={{$offset}}  
            $all = array();
	    $sql = "SELECT b.muc_room_name, a.muc_pic, a.muc_name, a.show_name, a.muc_title, b.create_time, b.packet from muc_vcard_info as a join (SELECT muc_room_name, create_time, packet FROM ( SELECT DISTINCT ON (muc_room_name) *
  FROM muc_room_history                                                                                            
  WHERE muc_room_name in (select muc_name from muc_room_users where username= $1 ) and packet like $2 
  ORDER BY muc_room_name, create_time DESC
) t) as b on a.muc_name = concat(b.muc_room_name, '@conference.ejabhost1') ORDER BY create_time DESC limit $3 offset $4;";
	    $result = pg_query_params($connection, $sql, array($user, "%{$key}%", $limit, $offset));
                while ($row = pg_fetch_array($result)) {
                $name = $row[3];
                $title = $row[4];
                $pic = $row[1];
		$object = new SimpleXMLElement($row[6]);
		$array = json_decode(json_encode($object), true);
		$unsolved = $array["@attributes"]["realfrom"];
		$solved = explode('@',$unsolved);
	        $message = $solved[0].":".$array[body];
		$s_message = $message;
		if (strlen($message)>40)  {
	$t_message = substr($message,0,40);
	$s_message = $t_message . "......";}
		array_push($all, array(
                        'icon' => $pic,
                        'label' => $name,
			'content' => $s_message,                
 //    'content' => $title,
                        'uri' => "$row[0]@conference.ejabhost1",
//			'text' => $array[body],
//			'from' => $solved[0],
			)
                );
            }
            return $all;
        } else { 
            echo 'ERROR';
        }
}
*/

          public static function searchGroupbyKeywordnew($conn, $username, $limit, $offset, $userId, $ckey)
{          
           $ch = curl_init();
        //   $es_get="http://qt.qunar.com/lookback/main_controller.php?action=search_4_react&method=search_muc&term=". urlencode($username) ."&pagesize=". urlencode($limit) ."&start". urlencode($offset) ."&user=". urlencode($userId) ."key=". urlencode($ckey) ."";
           $es_get="http://qt.qunar.com/lookback/main_controller.php?action=search_4_react&method=search_mucagg&term=" . urlencode($username) . "&pagesize=" . urlencode($limit) . "&start=" . urlencode($offset) . "&user=" . urlencode($userId) . "&key=619861510813382832963";
           curl_setopt($ch,CURLOPT_URL,$es_get);
           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
           curl_setopt($ch,CURLOPT_HEADER,0); //将头文件输出
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

           $result = curl_exec($ch);
           curl_close($ch);
           $muc_array = array();
           $DSL = json_decode($result);
           $keywordArray = array();
        foreach($DSL->data as $zhi1){
	if ($zhi1->count == 1){
	$zhi = $zhi1->msg;
//        $a = new SimpleXMLElement($zhi->msg);
//        $array = json_decode(json_encode($a), true);
        $b = $zhi->time;;
        $icon = UserLib::getGroupicon($conn,$zhi1->key);
        $raw_content = $zhi -> body;
        if(strlen($zhi->body) > 40){
        $left = explode("<em>",$zhi->body);
        if(strlen($left[0] > 20)){
        $left[0] = "......" . substr($left[0],-20);
        }
        $right = explode("</em>",$zhi->body);
        if(strlen($right[1] > 20)){
        $right[1] = substr($right[1],20) . "......";
        }
        $raw_content = $left[0]."<em>".$username."</em>".$right[1];
        }
         
         array_push($keywordArray, array(
        'uri' => $zhi1->key."@conference.ejabhost1",
        'label' => $zhi1->muc_name,
        'content' => $zhi->to.":".$raw_content,
        'msec_time' => $b,
        'icon' => $icon,
	'count' => 1,
        ));
        }
	else{
	$icon = UserLib::getGroupicon($conn,$zhi1->key);
	array_push($keywordArray, array(
	'uri' => $zhi1->key."@conference.ejabhost1",
	'label' => $zhi1->muc_name,
	'content' => "有" . $zhi1->count ."条相关消息",
	'msec_time' => null,
	'icon' => $icon,
	'count' => $zhi1->count,
	));
	}}
        usort($keywordArray, function($a, $b) {
        return $b['msec_time'] - $a['msec_time'];
});     
        return $keywordArray;
}


           public static function searchGroupDetail($conn, $groupId, $username, $limit, $offset, $userId, $ckey)
{
	if($groupId){
        $ch = curl_init();
	$es_get="http://qt.qunar.com/lookback/main_controller.php?action=search_muc_msg&method=search_mucagg&term=" . urlencode($username) ."&pagesize=" . urlencode($limit) ."&start=" . urlencode($offset) . "&muc=" . urlencode($groupId) . "&user=" . urlencode($userId) . "&key=619861510207707380545";
//	$es_get="http://qt.qunar.com/lookback/main_controller.php?action=search_muc_msg&method=search_mucagg&term=" . urlencode($username) ."&pagesize=" . urlencode($limit) ."&start=" . urlencode($offset) . "&muc=" . urlencode($groupId) . "&user=" . urlencode($userId) . "&key=" . urlencode($ckey);
	curl_setopt($ch,CURLOPT_URL,$es_get);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch,CURLOPT_HEADER,0); //将头文件输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
        curl_close($ch);
	$DSL = json_decode($result);
        $keywordArray = array();
        foreach($DSL->data as $zhi){
	$a = new SimpleXMLElement($zhi->B);
        $array = json_decode(json_encode($a), true);
        $b = $array["@attributes"]["msec_times"];
        $icon = UserLib::getGroupicon($conn,$zhi->R);
        $raw_content = $zhi -> body;
        if(strlen($zhi->body) > 40){
        $left = explode("<em>",$zhi->body);
        if(strlen($left[0] > 20)){
        $left[0] = "......" . substr($left[0],-20);
        }
        $right = explode("</em>",$zhi->body);
        if(strlen($right[1] > 20)){
        $right[1] = substr($right[1],20) . "......";
        }
        $raw_content = $left[0]."<em>".$username."</em>".$right[1];
        }

         array_push($keywordArray, array(
        'uri' => $zhi->R."@conference.ejabhost1",
        'label' => $zhi->M,
        'content' => $zhi->N.":".$raw_content,
        'msec_time' => $b,
        'icon' => $icon,
        ));
        }
        usort($keywordArray, function($a, $b) {
        return $b['msec_time'] - $a['msec_time'];
});	 
	return $keywordArray;
}else{
echo "error!";}
}
           public static function searchGroupbyKeyword($conn, $username, $limit, $offset, $userId, $ckey)
{
           $ch = curl_init();
           //$es_get="http://qt.qunar.com/lookback/main_controller.php?action=search_4_react&method=search_muc&term=". urlencode($username) ."&pagesize=". urlencode($limit) ."&start". urlencode($offset) ."&user=". urlencode($userId) ."&key=61986150984249756172";
           $es_get="http://qt.qunar.com/lookback/main_controller.php?action=search_4_react&method=search_muc&term=". urlencode($username) ."&pagesize=". urlencode($limit) ."&start". urlencode($offset) ."&user=". urlencode($userId) ."key=". urlencode($ckey) .""; 
           curl_setopt($ch,CURLOPT_URL,$es_get);
           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
           curl_setopt($ch,CURLOPT_HEADER,0); //将头文件输出
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $result = curl_exec($ch);
           curl_close($ch);
           $DSL = json_decode($result);
           $keywordArray = array();
        foreach($DSL->data as $zhi){
        $a = new SimpleXMLElement($zhi->B);
        $array = json_decode(json_encode($a), true);
        $b = $array["@attributes"]["msec_times"];
        $icon = UserLib::getGroupicon($conn,$zhi->R);
        $raw_content = $zhi -> body;
        if(strlen($zhi->body) > 40){
        $left = explode("<em>",$zhi->body);
        if(strlen($left[0] > 20)){
        $left[0] = "......" . substr($left[0],-20);
        }
        $right = explode("</em>",$zhi->body);
        if(strlen($right[1] > 20)){
        $right[1] = substr($right[1],20) . "......";
        }
        $raw_content = $left[0]."<em>".$username."</em>".$right[1];
        }

         array_push($keywordArray, array(
        'uri' => $zhi->R."@conference.ejabhost1",
        'label' => $zhi->M,
        'content' => $zhi->N.":".$raw_content,
        'msec_time' => $b,
        'icon' => $icon,
        ));
        }
        usort($keywordArray, function($a, $b) {
        return $b['msec_time'] - $a['msec_time'];
});
        return $keywordArray;
}
                                                                                                                                                                                           


    public static function recentSingleContacts($connection, $user)
    {
        if ($connection) {

            $all = array();
            $sql = 'select b.msg_id, a.user, b.m_body, b.create_time from (select case when m_from = $1 then m_to else m_from end as user, max(id) as msgid from msg_history where create_time > now() - interval \'7 day\' and  (m_from = $1 or m_to = $1) group by case when m_from = $1 then m_to else m_from end) as a inner join msg_history as b on a.msgid = b.id order by b.create_time asc;';
            $result = pg_query_params($connection, $sql, array("$user"));

            while ($row = pg_fetch_array($result)) {
                $msgId = $row[0];
                $userName = $row[1];
                $body = $row[2];
                $time = $row[3];
                array_push($all, array(
                        'msgid' => $msgId,
                        'name' => $userName,
                        'content' => $body,
                        'time' => $time,
                    )
                );
            }
            return $all;
        } else {
            echo 'ERROR';
        }
    }

    public static function recentGroupContacts($connection, $user)
    {
        if ($connection) {

            $all = array();
            $sql = 'select b.msg_id, a.groupname, b.packet, b.create_time from (select concat(b.muc_room_name, \'@conference.\', b.host) as groupname, max(b.id) as msgid from muc_room_users a inner join muc_room_history b on a.muc_name = b.muc_room_name and a.host = b.host where a.username = $1 and b.create_time > now() - interval \'7 day\' group by concat(b.muc_room_name, \'@conference.\',b.host)) a inner join muc_room_history b on a.msgid = b.id order by b.create_time asc;';
            $result = pg_query_params($connection, $sql, array("$user"));
            while ($row = pg_fetch_array($result)) {
                $msgId = $row[0];
                $userName = $row[1];
                $body = $row[2];
                $time = $row[3];
                array_push($all, array(
                        'msgid' => $msgId,
                        'name' => $userName,
                        'content' => $body,
                        'time' => $time,
                    )
                );
            }
            return $all;
        } else {
            echo 'ERROR';
        }
    }


	public static function searchGroupwithOrder($connection, $user, $key, $limit, $offset = 0)
    {
        if ($connection && $user) {
            $all = array();

            $sql="SELECT b.show_name, a.muc_room_name, b.muc_title, b.muc_pic from (select muc_room_name, max(id) from muc_room_history where muc_room_name in (select muc_name from muc_room_users where username= $1) group by muc_room_name order by max(id) desc limit $3 offset $4) as a left join muc_vcard_info as b on concat(a.muc_room_name, '@conference.ejabhost1') = b.muc_name where b.muc_name like $2 or b.show_name like $2;";
	    $result = pg_query_params($connection, $sql, array($user, "%{$key}%", $limit, $offset));

            while ($row = pg_fetch_array($result)) {
                $name = $row[0];//show_name
                $title = $row[2];//muc_title
                $pic = $row[3];

                array_push($all, array(
                        'icon' => $pic,
                        'label' => $name,
                        'content' => $title,
                        'uri' => "$row[1]@conference.ejabhost1",
                    )
                );
            }
            return $all;
        } else {
            echo 'ERROR';
        }
    }


    public static function searchGroupByName($connection, $user, $key, $limit, $offset = 0)
    {
        if ($connection && $user) {
            $all = array();

            $sql = "select a.muc_name, a.host, a.date, b.show_name, b.muc_title, b.muc_pic from muc_room_users as a left join muc_vcard_info as b on concat(a.muc_name, '@conference.', a.host) = b.muc_name where a.username = $1 and b.show_name like $2 limit $3 offset $4;";
            $result = pg_query_params($connection, $sql, array($user, "%{$key}%", $limit, $offset));

            while ($row = pg_fetch_array($result)) {
                $name = $row[3];
                $title = $row[4];
                $pic = $row[5];

                array_push($all, array(
                        'icon' => $pic,
                        'label' => $name,
                        'content' => $title,
                        'uri' => "$row[0]@conference.{$row[1]}",
                    )
                );
            }
            return $all;
        } else {
            echo 'ERROR';
        }
    }

    public static function searchGroupByUsers($connection, $user, $key, $limit, $offset = 0)
    {
        //    muc_name             |   host    |     date      |            show_name            |                muc_title                | muc_pic
//    ----------------------------------+-----------+---------------+---------------------------------+-----------------------------------------+---------
// qtalk客户端开发群                | ejabhost1 | 1465903033335 | qtalk核心开发组                 | remember: NEVER Garbage IN Garbage OUT. |
// 4fac863708fd481cbbb2b4cd7d282b68 | ejabhost1 | 1465896115858 | QC中向用户推荐核心产品 list部分 | QC中向用户推荐核心产品                  |
//(2 rows)

        $users = explode(' ', $key);
        $perSql = "";
        $count = 1;
        if (count($users) == 0) {
            $perSql = "'$key'" . " ";
            $count++;
        } else {
            foreach ($users as $per) {
                $enter = pg_escape_string($per);

                if ($per != end($users))
                    $perSql .= "'$enter', ";
                else {
                    $perSql .= "'$enter') ";
                }
                $count++;
            }
        }

        if ($connection && $user) {
            $all = array();

            $sql = "select muc_name, 'ejabhost1', now(), show_name, muc_title, muc_pic from muc_vcard_info where muc_name in (select concat(muc_name, '@conference.ejabhost1') from muc_room_users where username in ($1, " . $perSql . "group by muc_name having count(*) = $2) limit $3 offset $4;";
            $result = pg_query_params($connection, $sql, array($user, $count, $limit, $offset));

            while ($row = pg_fetch_array($result)) {
                $name = $row[3];
                $title = $row[4];
                $pic = $row[5];

                array_push($all, array(
                        'icon' => $pic,
                        'label' => $name,
                        'content' => $title,
                        'uri' => "$row[0]",
                    )
                );
            }
            return $all;
        } else {
            echo 'ERROR';
        }
    }

    public static function searchGroup($connection, $user, $key, $limit, $offset = 0)
    {
//    muc_name             |   host    |     date      |            show_name            |                muc_title                | muc_pic
//    ----------------------------------+-----------+---------------+---------------------------------+-----------------------------------------+---------
// qtalk客户端开发群                | ejabhost1 | 1465903033335 | qtalk核心开发组                 | remember: NEVER Garbage IN Garbage OUT. |
// 4fac863708fd481cbbb2b4cd7d282b68 | ejabhost1 | 1465896115858 | QC中向用户推荐核心产品 list部分 | QC中向用户推荐核心产品                  |
//(2 rows)
        if ($connection && $user) {
            $all = array();

            $sql = "select a.muc_name, a.host, a.date, b.show_name, b.muc_title, b.muc_pic from muc_room_users as a left join muc_vcard_info as b on concat(a.muc_name, '@conference.', a.host) = b.muc_name where a.username = $1 and b.show_name ilike $2 limit $3 offset $4;";
            $result = pg_query_params($connection, $sql, array($user, "%{$key}%", $limit, $offset));

            while ($row = pg_fetch_array($result)) {
                $name = $row[3];
                $title = $row[4];
                $pic = $row[5];

                array_push($all, array(
                        'icon' => $pic,
                        'label' => $name,
                        'content' => $title,
                        'uri' => "$row[0]@conference.{$row[1]}",
                    )
                );
            }
            return $all;
        } else {
            echo 'ERROR';
        }
    }

    public static function getIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function load_login_data_for_tts($connection)
    {
        if ($connection) {

            $sql = "select a.web_name, a.name, b.username, b.platform, b.login_time, b.logout_at from (select * from (select * from dashboard.seat a inner join dashboard.busi_seat_mapping b on a.id = b.seat_id where b.busi_id = 1) a inner join dashboard.supplier b on a.supplier_id = b.id) a inner join login_data b on a.qunar_name = b.username where b.login_time > now() - interval '10 min';";

            $result = pg_query($connection, $sql);

            while ($row = pg_fetch_array($result)) {
                $name = $row[3];
                $title = $row[4];
                $pic = $row[5];

                $platform = $row[3];

                $resultString = null;

                if (strpos($platform, "_P[IOS]") !== false) {
                    $resultString = $row[0] . "  |  " . $row[1] . "  |  " . $row[2] . "  |  " . " Mobile " . "  |  " . $row[4] . "  |  " . $row[5] . PHP_EOL;

                } elseif (strpos($platform, "_P[Android]") !== false) {
                    $resultString = $row[0] . "  |  " . $row[1] . "  |  " . $row[2] . "  |  " . " Mobile " . "  |  " . $row[4] . "  |  " . $row[5] . PHP_EOL;

                } elseif (strpos($platform, "_P[PC") !== false) {
                    $resultString = $row[0] . "  |  " . $row[1] . "  |  " . $row[2] . "  |  " . " PC " . "  |  " . $row[4] . "  |  " . $row[5] . PHP_EOL;
                }

                if ($resultString != null) {
                    file_put_contents("/tmp/tts.txt", $resultString, FILE_APPEND);
                }
            }
            echo 'DONE';
        } else {
            echo 'ERROR';
        }
    }

    public static function request($url, $jsonText = '', $output = '')
    {
        $cmd = '';
        if (strlen($jsonText) <= 0) {
            $cmd = "curl '";
            $cmd .= $url;
            $cmd .= "'";
        } else {
            $cmd = "curl -i -H 'content-type: application/json' -X POST -d '";
            $cmd .= $jsonText;
            $cmd .= "' " . $url;
        }
        exec($cmd, $output, $exit);
        return array('result' => $exit == 0,
            'output' => $output);
    }

    public static function getuserDetails($type, $userId)
    {

//    username/mobile/email/uid/nickname
        $url = "http://tu.corp.qunar.com/int_api/userinfo.php?" . $type . "=";
        $parameter = "&v=2.0&p=qt_ios";

        $theUrl = $url . $userId . $parameter;

        $result = self::request($theUrl);
        $result['result'];
        if ($result['result']) {
            $resultStr = $result['output']['0'];
            $array = json_decode($resultStr);

            return $array->data;
        }
        return null;
    }

    public static function getuserBetaDetail($type, $userId)
    {
        //    username/mobile/email/uid/nickname
        $url = "http://tu.corp.qunar.com/int_api/userinfo.php?" . $type . "=";
        $parameter = "&v=2.0&p=qt_ios";

        $theUrl = $url . $userId . $parameter;

        $result = self::request($theUrl);
        $result['result'];
        if ($result['result']) {
            $resultStr = $result['output']['0'];
            $array = json_decode($resultStr);

            return $array->data;
        }
        return null;

    }

    public static function inIPWhiteList($whiteList)
    {
        $ipAddress = self::getIP();
        return isset($whiteList) ? in_array($ipAddress, $whiteList) : false;
    }
}
