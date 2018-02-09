<?php
 /* 李锁柱 <suozhu.li@qunar.com> 
 * @version $Id$
 * 2015-8-27 16:12:03
 * UTF-8
 *
 */


function file_log($msg){
        $filename = "/home/q/php/log/qtalkphp.log";
        if (!$handle = fopen($filename, 'a'))
        {
                print "open logfile $filename error\n<br>";
                return -1;
        }
        $time = strftime("%F %T ", time());
        if (!fwrite($handle, $time.$msg."\n"))
        {
                print "write logfile $filename error\n<br>";
                return -2;
        }
        fclose($handle);
        return 0;
}

function send_mail($title,$content){
    @mail("xuejie.bi@qunar.com","Qtalk_user_comment".$title,$content);
}
?>

