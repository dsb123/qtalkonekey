/**
 * Created by Dasate on 2017/9/14.
 * QQ361899429
 */

var onlineConsultingAlert = function (opts) {
    //设置默认参数
    var opt = {
        "closeAll": false,
        "title": "提示",
        "content": "",
        "buttons": {}
    };
    //合并参数
    var option = $.extend(opt, opts);
    //事件
    var dialog = {};
    var $simpleAlert = $('<div class="simpleSelectButtonAlert">');
    var $shelter = $('<div class="simpleAlertShelter">');
    var $simpleAlertBody = $('<div class="simpleSelectButtonAlertBody">');
    var $simpleAlertBodyClose = $('<img class="simpleAlertBodyClose" src="images/domain_close.png" height="14" width="14"/>');
    dialog.init = function () {
        $simpleAlertBody.append($simpleAlertBodyClose);
        var num = 0;
        var only = false;
        var onlyArr = [];
        for (var i = 0; i < 2; i++) {
            for (var key in option.buttons) {
                switch (i) {
                    case 0:
                        onlyArr.push(key);
                        break;
                    case 1:
                        if (onlyArr.length <= 1) {
                            only = true;
                        } else {
                            only = false;
                        }
                        num++;
                        var $btn = $('<button class="simpleSelectButtonAlertBtn simpleSelectButtonAlertBtn' + num + '">' + key + '</button>')
                        $btn.bind("click", option.buttons[key]);
                        if (only) {
                            $btn.addClass("onlyOne")
                        }
                        $simpleAlertBody.append($btn);
                        break;
                }

            }
        }
        $simpleAlert.append($shelter).append($simpleAlertBody);
        $("body").append($simpleAlert);
        $simpleAlertBody.show().animate({"marginTop":"-128px","opacity":"1"},300);
    };
    //右上角关闭按键事件
    $simpleAlertBodyClose.bind("click", function () {
        option.closeAll=false;
        dialog.close();
    });
    dialog.close = function () {
        if(option.closeAll){
            $(".simpleSelectButtonAlertBody").animate({"marginTop": "-188px", "opacity": "0"}, 200, function () {
                $(".simpleSelectButtonAlert").remove()
            });
        }else {
            $simpleAlertBody.animate({"marginTop": "-188px", "opacity": "0"}, 200, function () {
                $(".simpleSelectButtonAlert").last().remove()
            });
        }
    };
    dialog.init();
    return dialog;
};

var domainDelete = function (opts) {
    //设置默认参数
    var opt = {
        "closeAll": false,
        "title": "提示",
        "content": "",
        "buttons": {}
    };
    //合并参数
    var option = $.extend(opt, opts);
    //事件
    var dialog = {};
    var $simpleAlert = $('<div class="simpleAlert">');
    var $shelter = $('<div class="simpleAlertShelter">');
    var $simpleAlertBody = $('<div class="simpleAlertBody">');
    var $simpleAlertBodyClose = $('<img class="simpleAlertBodyClose" src="images/domain_close.png" height="14" width="14"/>');
    var $simpleAlertBodyTitle = $('<p class="simpleAlertBodyTitle">' + option.title + '</p>');
    var $simpleAlertBodyContent = $('<p class="simpleAlertBodyContent">' + option.content + '</p>');
    dialog.init = function () {
        $simpleAlertBody.append($simpleAlertBodyClose).append($simpleAlertBodyContent).append($simpleAlertBodyTitle);
        var num = 0;
        var only = false;
        var onlyArr = [];
        for (var i = 0; i < 2; i++) {
            for (var key in option.buttons) {
                switch (i) {
                    case 0:
                        onlyArr.push(key);
                        break;
                    case 1:
                        if (onlyArr.length <= 1) {
                            only = true;
                        } else {
                            only = false;
                        }
                        num++;
                        var $btn = $('<button class="simpleAlertBtn simpleAlertBtn' + num + '">' + key + '</button>')
                        $btn.bind("click", option.buttons[key]);
                        if (only) {
                            $btn.addClass("onlyOne")
                        }
                        $simpleAlertBody.append($btn);
                        break;
                }

            }
        }
        $simpleAlert.append($shelter).append($simpleAlertBody);
        $("body").append($simpleAlert);
        $simpleAlertBody.show().animate({"marginTop":"-128px","opacity":"1"},300);
    };
    //右上角关闭按键事件
    $simpleAlertBodyClose.bind("click", function () {
        option.closeAll=false;
        dialog.close();
    });
    dialog.close = function () {
        if(option.closeAll){
            $(".simpleAlertBody").animate({"marginTop": "-188px", "opacity": "0"}, 200, function () {
                $(".simpleAlert").remove()
            });
        }else {
            $simpleAlertBody.animate({"marginTop": "-188px", "opacity": "0"}, 200, function () {
                $(".simpleAlert").last().remove()
            });
        }
    };
    dialog.init();
    return dialog;
};


var domainAdd = function (opts) {
    //设置默认参数
    var opt = {
        "closeAll": false,
        "title": "提示",
        "content": "",
        "buttons": {}
    };
    //合并参数
    var option = $.extend(opt, opts);
    //事件
    var dialog = {};
    var $simpleAlert = $('<div class="simpleAlert">');
    var $shelter = $('<div class="simpleAlertShelter">');
    var $simpleAlertBody = $('<div class="simpleAlertBody">');
    var $simpleAlertBodyClose = $('<img class="simpleAlertBodyClose" src="images/domain_close.png" height="14" width="14"/>');
    var $simpleAlertBodyTitle = $('<p class="addAlertBodyTitle">' + option.title + '</p>');
    var $simpleAlertBodyContent = $('<div class="addAlertBodyContent">');
    var domainField = $('<label>域名：<input id="domain_field" class="alert_field" type="text" value="" placeholder="输入域名"/></label><br>');
    var descField = $('<label>描述：<input id="desc_field" class="alert_field" type="text" value="" placeholder="输入描述"/></label><br>');
    $simpleAlertBodyContent.append(domainField).append(descField);
    dialog.init = function () {
        $simpleAlertBody.append($simpleAlertBodyClose).append($simpleAlertBodyContent).append($simpleAlertBodyTitle);
        var num = 0;
        var only = false;
        var onlyArr = [];
        for (var i = 0; i < 2; i++) {
            for (var key in option.buttons) {
                switch (i) {
                    case 0:
                        onlyArr.push(key);
                        break;
                    case 1:
                        if (onlyArr.length <= 1) {
                            only = true;
                        } else {
                            only = false;
                        }
                        num++;
                        var $btn = $('<button class="simpleAlertBtn simpleAlertBtn' + num + '">' + key + '</button>')
                        $btn.bind("click", option.buttons[key]);
                        if (only) {
                            $btn.addClass("onlyOne")
                        }
                        $simpleAlertBody.append($btn);
                        break;
                }

            }
        }
        $simpleAlert.append($shelter).append($simpleAlertBody);
        $("body").append($simpleAlert);
        $simpleAlertBody.show().animate({"marginTop":"-128px","opacity":"1"},300);
    };
    //右上角关闭按键事件
    $simpleAlertBodyClose.bind("click", function () {
        option.closeAll=false;
        dialog.close();
    });
    dialog.close = function () {
        if(option.closeAll){
            $(".simpleAlertBody").animate({"marginTop": "-188px", "opacity": "0"}, 200, function () {
                $(".simpleAlert").remove()
            });
        }else {
            $simpleAlertBody.animate({"marginTop": "-188px", "opacity": "0"}, 200, function () {
                $(".simpleAlert").last().remove()
            });
        }
    };
    dialog.getDomain = function () {
        return $("#domain_field").val();
    };
    dialog.getDescribe = function () {
        return $("#desc_field").val();
    };
    dialog.init();
    return dialog;
};


var messageBoard = function (opts) {
    //设置默认参数
    var opt = {
        "closeAll": false,
        "title": "提示",
        "content": "",
        "buttons": {}
    };
    //合并参数
    var option = $.extend(opt, opts);
    //事件
    var dialog = {};
    var $simpleAlert = $('<div class="simpleAlert">');
    var $shelter = $('<div class="simpleAlertShelter">');
    var $simpleAlertBody = $('<div class="msgBoardBody">');
    var $simpleAlertBodyClose = $('<img class="simpleAlertBodyClose" src="images/domain_close.png" height="14" width="14"/>');
    var $simpleAlertBodyTitle = $('<p class="msgBoardAlertBodyTitle">' + option.title + '</p>');
    var $simpleAlertBodyContent = $('<div class="msgBoardBodyContent">');
    var companyField = $('<label>公司名称：<input id="company" class="alert_field" type="text" value="" placeholder="请输入您的公司名称"/></label><br>');
    var emailField = $('<label>邮箱地址：<input id="email" class="alert_field" type="text" value="" placeholder="请输入您的公司邮箱"/></label><br>');
    var contactField = $('<label>联系方式：<input id="contact_info" class="alert_field" type="text" value="" placeholder="微信号/qq号/电话号"/></label><br>');
    var messageField = $('<label>留言内容：<textarea id="message" class="alert_textarea" placeholder="留言内容" rows="3" cols="30"></textarea></label><br>');
    $simpleAlertBodyContent.append(companyField).append(emailField).append(contactField).append(messageField);
    dialog.init = function () {
        $simpleAlertBody.append($simpleAlertBodyClose).append($simpleAlertBodyContent).append($simpleAlertBodyTitle);
        var num = 0;
        var only = false;
        var onlyArr = [];
        for (var i = 0; i < 2; i++) {
            for (var key in option.buttons) {
                switch (i) {
                    case 0:
                        onlyArr.push(key);
                        break;
                    case 1:
                        if (onlyArr.length <= 1) {
                            only = true;
                        } else {
                            only = false;
                        }
                        num++;
                        var $btn = $('<button class="msgBoardAlertBtn simpleAlertBtn' + num + '">' + key + '</button>')
                        $btn.bind("click", option.buttons[key]);
                        if (only) {
                            $btn.addClass("onlyOne")
                        }
                        $simpleAlertBody.append($btn);
                        break;
                }

            }
        }
        $simpleAlert.append($shelter).append($simpleAlertBody);
        $("body").append($simpleAlert);
        $simpleAlertBody.show().animate({"marginTop":"-190px","opacity":"1"},300);
    };
    //右上角关闭按键事件
    $simpleAlertBodyClose.bind("click", function () {
        option.closeAll=false;
        dialog.close();
    });
    dialog.close = function () {
        if(option.closeAll){
            $(".simpleAlertBody").animate({"marginTop": "-360px", "opacity": "0"}, 200, function () {
                $(".simpleAlert").remove()
            });
        }else {
            $simpleAlertBody.animate({"marginTop": "-360px", "opacity": "0"}, 200, function () {
                $(".simpleAlert").last().remove()
            });
        }
    };
    dialog.getCompany = function () {
        return $("#company").val();
    };
    dialog.getEmail = function () {
        return $("#email").val();
    };
    dialog.getContact = function () {
        return $("#contact_info").val();
    };
    dialog.getMessage = function () {
        return $("#message").val();
    };
    dialog.init();
    return dialog;
};