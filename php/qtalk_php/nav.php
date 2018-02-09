<?php

$result = <<<eof
{
    "Login": {
        "loginType": "password"
    }, 
    "baseaddess": {
        "capability": "http://im.qunar.com/pubim/static/sec/capability_list",
        "checkconfig": "https://qt.qunar.com/checkconfig.php",
        "simpleapiurl": "https://qt.qunar.com",
        "sms_token": "https://smsauth.qunar.com/api/2.0/token",
        "fileurl": "http://<?qtalk HOSTURL>",
        "domain": "test.com",
        "javaurl": "http://<?qtalk HOSTURL>/package",
        "protobufPcPort": 5201,
        "sms_verify": "https://smsauth.qunar.com/api/2.0/verify_code",
        "xmpp": "<?qtalk HOSTURL>",
        "xmppport": 5222,
        "protobufPort": 5202,
        "pubkey": "rsa_public_key",
        "xmppmport": 5223,
        "apiurl": "http://<?qtalk HOSTURL>/api"
    },
    "hosts": "http://<?qtalk HOSTURL>/php/subnav.php",
    "version": 10005, 
    "qrcode": {
        "auth": "https://qt.qunar.com/package/qtapi/common/qrcode/auth.qunar"
    }, 
    "ops": {
        "host": "https://opsapp.qunar.com", 
        "checkversion": "https://opsapp.qunar.com/qtalk/rnbundle/version/check", 
        "conf": "https://opsapp.qunar.com/ops/opsapp/api/conf"
    }, 
    "versions": {
        "checkconfig": 10000
    }, 
    "imConfig": {
        "VideoHost": "", 
        "OpsAPI": "", 
        "showOA": false, 
        "RsaEncodeType": 1
    }, 
    "ability": {
        "resetpwd": "", 
        "getPushState": "", 
        "searchurl": "http://<?qtalk HOSTURL>/search/html/s/qtalk/search.php", 
        "setPushState": "", 
        "qCloudHost": "", 
        "mconfig": "https://im.qunar.com/pubim/pub/mainSite/touchs/mconfig.php"
    }, 
    "client": {
        "logreport": "http://sk.qunar.com/c"
    }, 
    "video": {
        "group_room_host": "https://qt.qunar.com/rtc/index.php", 
        "wsshost": "wss://l-wxapp2.vc.beta.cn0.qunar.com:9090", 
        "apihost": "https://l-wxapp2.vc.beta.cn0.qunar.com:9090", 
        "signal_host": "https://qt.qunar.com/rtc/pc/index.html"
    }, 
    "ad": {
        "adsec": 5, 
        "adurl": "http://touch.dujia.qunar.com/", 
        "shown": false
    }
}
eof;

echo $result;