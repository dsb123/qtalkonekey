<?php

$result = <<<eof
{
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
    }
}
eof;

echo $result;