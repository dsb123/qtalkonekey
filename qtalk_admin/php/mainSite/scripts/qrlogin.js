function QRLogin(type){
    var state,key,tryNum,qrImgData;
    var delay = 500;
    var maxTryCount = 10;
    var _callback;
    var ctype = type;
    this.startQRLogin = function (callback) {
        _callback = callback;
        state = QRLogin.LoginState.QRCode;
        tryNum = 0;
        key = "";
        qrImgData = "";
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "./qrlogin_method_ajax.php?action=qrimg&type="+ctype,
            timeout : 1000,
            success: function (msg) {
                if (msg.ok){
                    qrImgData = msg.msg;
                    _callback.call(this,state,qrImgData);
                    state = QRLogin.LoginState.Init;
                    key = msg.key;
                    doQRLogin()
                }
            }
        });
    };

    this.restarQRLogin = function () {
        this.startQRLogin(_callback);
    };

    this.stopQRLogin = function () {
        $.ajax({
            type: "GET",
            url: "./qrlogin_method_ajax.php?action=cancel&key="+key
        });
    };

    function initQRCode() {
        $.ajax({
            type: "GET",
            url: "./qrlogin_method_ajax.php?action=init&key="+key,
            dataType: "json",
            timeout : 1000,
            success: function (msg) {
                if (msg.ok){
                    state = QRLogin.LoginState.Waiting_Scan;
                    delay = msg.msg * 1000;
                    if (delay <= 0) {
                        delay = 500;
                    }
                    _callback.call(this,state,msg.msg);
                    setTimeout(doQRLogin, delay);
                } else {
                    setTimeout(doQRLogin, 500);
                    tryNum++;
                }
            }
        });
    }

    function checkReady(phase) {
        $.ajax({
            type: "GET",
            url: "./qrlogin_method_ajax.php?action=check_ready&phase="+phase+"&key="+key,
            dataType: "json",
            timeout : 5000,
            success: function (msg) {
                if (msg.ok){
                    var checkResult = JSON.parse(msg.msg);
                    switch (parseInt(checkResult.t)){
                        case 1:
                        {
                            if (phase == 1){
                                state = QRLogin.LoginState.Waiting_Confirm;
                                _callback.call(this,state,checkResult);
                            } else {
                                state = QRLogin.LoginState.Auth;
                                _callback.call(this,state,checkResult);
                            }
                            doQRLogin();
                        }
                        break;
                        case 2:
                        {
                            state = QRLogin.LoginState.TimeOut;
                            doQRLogin();
                        }
                            break;
                        case 3:
                        {
                            setTimeout(doQRLogin, delay);
                        }
                            break;
                        case 4:
                        {
                            state = QRLogin.LoginState.Cancel;
                            doQRLogin();
                        }
                            break;
                        default:
                        {
                            setTimeout(doQRLogin, delay);
                        }
                            break;
                    }
                } else {
                    setTimeout(doQRLogin, delay);
                    tryNum++;
                }
            },
            complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
                $codeButton = $("#getCheckCode");
                if (status != 'success') {
                    if (status == 'timeout') {//超时,status还有success,error等值的情况
                        $ajaxRequest.abort();
                    }
                }
                return false;
            }
        });
    }

    function doQRLogin() {
        if (tryNum > maxTryCount){
            state = QRLogin.LoginState.Faild;
            _callback.call(this,state,"失败");
            return;
        }
        switch (state){
            case QRLogin.LoginState.Init:{
                initQRCode();
            }
                break;
            case QRLogin.LoginState.Waiting_Scan:{
                checkReady(1);
            }
                break;
            case QRLogin.LoginState.Waiting_Confirm:{
                checkReady(2);
            }
                break;
            case QRLogin.LoginState.Auth:{

            }
                break;
            case QRLogin.LoginState.Cancel:{
                //_callback(this,state,"取消");
                _callback.call(this,state,"取消");
            }
                break;
            case QRLogin.LoginState.TimeOut:{
                _callback.call(this,state,"超时");
            }
                break;
        }
    }
}
QRLogin.LoginState = {"QRCode" : 0,"Init" : 1, "Waiting_Scan" : 2, "Waiting_Confirm" : 3, "Auth" : 4, "Cancel" : 5, "TimeOut" : 6, "Faild" : 7};