<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/22
 * Time: 11:48
 */
?>
<?php
require_once dirname(__FILE__) . '/public_method_ajax.php';
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--[if lte IE 8]><script src="http://cdn.bootcss.com/selectivizr/1.0.2/selectivizr.js"></script><![endif]-->
    <!--[if lt IE 9]><script src="http://cdn.bootcss.com/html5shiv/3.7/html5shiv.js"></script><![endif]-->
    <title>用户管理</title>
    <link rel="stylesheet" href="css/domain_user_manager.css"/>
</head>
<body>
<div id="container">
    <header>
        <!-- Header -->
	<?php include "header.php"; ?>
    </header>
    <section class="main">
        <!-- Content -->
        <?php
        $domainId = "1";
        $pageCount = 10;
        $pageNumCount = 10;
	$iu = new importUser();
        $searchWord = trim(htmlspecialchars(filter_input(INPUT_GET, 'search_word', FILTER_SANITIZE_STRING), ENT_QUOTES));
        $pageIndex = filter_input(INPUT_GET, "page"); // user name
        $hostId = "1"; // user name
        if (empty($searchWord)) {
            $count = $iu->getHostUserCount($domainId);
        } else {
            $count = $iu->searchHostUserCount($domainId,$searchWord);
        }
        $pages = (int)(($count - 1) / $pageCount) + 1;
        if (empty($pageIndex) || $pageIndex < 1) {
            $pageIndex = 1;
        } else if ($pageIndex > $pages) {
            $pageIndex = $pages;
        }
        $offset = $i = ($pageIndex - 1) * $pageCount;
        $limit = $pageCount;
        if (empty($searchWord)) {
            $host_users = $iu->getHostUserPage($domainId,$offset,$limit);
        } else {
            $host_users = $iu->searchHostUserPage($domainId,$offset,$limit,$searchWord);
        }
        $hostInfo = getHostInfo($domainId);
        //var_dump($hostInfo);
        ?>
        <div class="user_manager_container">
            <div class="add-user">
                <div class="add-user-button">+ 添加用户</div>
                <div class="import_container" style="display: none;">
                    <div class="l-wrapper">
                        <div class="title">添加新用户</div>
                        <input type="hidden" name="action" value="import_users"/>
                        <input type="hidden" id="host_id" name="host_id" value="<?php echo $domainId; ?>"/>
                        <table id="import_user_list">
                            <thead>
                            <tr>
                                <td colspan="5">
                                    <div class="form-group-label"><a class="import_csv" href="demo.csv" target="_blank">csv模板</a></div>
                                    <div class="form-group-upload">
                                        <a href="javascript:void(0);" class="file" id="select_file">上传CSV文件
                                            <input accept="text/csv" type="file" name="upload_users" id="upload_users"/>
                                        </a>
                                    </div>
                                    <div class="form-group-tips"></div>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_id" id='user_id' value="" placeholder="用户登录id"/></div></td>
                                <td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_name" id='user_name' value="" placeholder="姓名"/></div></td>
                                <td><div style="margin-right: 16px;"><input class="input_text" type="text" name="department" id='department' value="" placeholder="部门：/部门/组"/></div></td>
                                <td><div style="margin-right: 16px;"><input class="input_text" type="text" name="tel" id='tel' value="" placeholder="手机号"/></div></td>
                                <td><div class="domain_user_delete"></div></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="buttons">
                            <input class="import_user_add" type="button" value="+ 新增"/>
                            <input class="import_user_save" type="button" value="保存"/>
                        </div>
                    </div>
                    <div class="retract">
                        收起<embed src="images/arrow_down.svg" width="16px" height="16px"
                                 type="image/svg+xml"
                                 pluginspage="http://www.adobe.com/svg/viewer/install/" />
                    </div>
                </div>
            </div>
            <?php if (count($host_users)>0){ ?>
                <div class="user_list">
                    <div class="user_list_header">
                        <div class="title">域[<?php echo $hostInfo['host']?>]用户列表</div>
                        <div class="search">
                            <form action = 'domain_user_manager.php' method="GET">
                                <input type="hidden" value="<?php echo $domainId; ?>" name="id" id="id"/>
                                <input class="input_search" type="text" value="<?php echo $searchWord; ?>" placeholder="登录ID、用户名" name="search_word" id="search_word"/>
                                <input class="btn_search" type="submit" value="搜索"/>
                            </form>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>登录id</th>
                            <th>姓名</th>
                            <th>部门</th>
                            <th>手机号</th>
                            <th>初始密码</th>
                            <th style="text-align: center;" colspan="2">操作</th>
                        <tr>
                        </thead>
                        <tbody>
                        <?php foreach ($host_users as $user) { ?>
                            <tr>
                                <td><a class="update" data-id='<?php echo $user['id']; ?>' href="host_user.php?uid=<?php echo $user['id']; ?>"><?php echo $user['user_id']; ?></a></td>
                                <td><?php echo $user['user_name']; ?></td>
                                <td><?php echo $user['department']; ?></td>
                                <td><?php echo $user['tel']; ?></td>
                                <td><?php echo $user['initialpwd'] == 1 ? $user['password'] : "已修改"; ?></td>
                                <td style="text-align: center;"><a class="reset" data-id='<?php echo $user['id']; ?>' data-user="<?php echo $user['user_name']; ?>">重置</a></td>
                                <td style="text-align: center;"><a class="delete" data-id='<?php echo $user['id']; ?>' data-user="<?php echo $user['user_name']; ?>">删除</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="page_number">
                        <?php
                        $searchQuery = (empty($searchWord)?"":"&search_word={$searchWord}");
                        if ($pageIndex > 1) {
                            $index = $pageIndex - 1;
                            $url = "domain_user_manager.php?page={$index}&id={$hostId}{$searchQuery}";
                            echo "<a class='page_prep' href=\"{$url}\">< 上一页</a>";
                        }
                        $pageNumStart = $pageIndex - (int)($pageNumCount/2);
                        if ($pageNumStart < 1) {
                            $pageNumStart = 1;
                        }
                        if ($pageNumStart + (int)($pageNumCount/2)  - 1 > $pageCount) {
                            $pageNumStart = $pages - $pageNumCount + 1;
                        }
                        $i = 1;
                        while ($pageNumStart <= $pages && $i <= $pageNumCount ){
                            if ($pageNumStart == $pageIndex) {
                                echo "<span class='page_num'>{$pageNumStart}</span>";
                            } else {
                                $url = "domain_user_manager.php?page={$pageNumStart}&id={$hostId}{$searchQuery}";
                                echo "<a class='page_num' href=\"{$url}\">{$pageNumStart}</a>";
                            }
                            $i++;
                            $pageNumStart++;
                        }
                        if ($pageIndex < $pages) {
                            $index = $pageIndex + 1;
                            $url = "domain_user_manager.php?page={$index}&id={$hostId}{$searchQuery}";
                            echo "<a class='page_next' href=\"{$url}\">下一页 ></a>";
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
    <footer>
        <!-- footer -->
    </footer>
</div>
<script type="text/javascript" src="scripts/jquery.csv-0.83.min.js"></script>
<script type="text/javascript">

    $(".import_container").hide();
    $(".add-user-button").click(function () {
        $(this).hide();
        $(".import_container").show();
    });
    $(".retract").click(function () {
       $(".add-user-button").show();
        $(".import_container").hide();
    });

    var importUserDelete = function () {
        $tr = $(this).parent().parent('tr');
        $tr.remove();
        updateImportUserCount();
    };

    var updateImportUserCount = function () {
        var count = 0;
        $("#import_user_list tr #user_id").each(function () {
            var userId = $(this).val();
            if (userId) {
                count++;
            }
        });
        $(".form-group-tips").html("您一共添加了"+count+"位用户，继续请点'保存'");
    };

    function getImportUser() {
        var importUser = new Array();
        $("#import_user_list tr").each(function () {
            var userId = $(this).find("#user_id").val();
            var userName = $(this).find("#user_name").val();
            var tel = $(this).find("#tel").val();
            var department = $(this).find("#department").val();
            if (userId) {
                importUser[userId] = new Array();
                importUser[userId]["user_id"] = userId;
                importUser[userId]["user_name"] = userName;
                importUser[userId]["tel"] = tel;
                importUser[userId]["department"] = department;
            }
        });
        return importUser;
    }

    function vailPhone(phone){
        var flag = false;
        var message = "";
        var myreg = /^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if(phone == ''){
            message = "手机号码不能为空！";
        }else if(phone.length !=11){
            message = "请输入有效的手机号码！";
        }else if(!myreg.test(phone)){
            message = "请输入有效的手机号码！";
        }
//        else if(checkPhoneIsExist()){
//            message = "该手机号码已经被绑定！";
//        }
        else{
            flag = true;
        }
        var err = {
          "ok":flag,
          "msg":message
        };
        return err;
    }

    //选择文件
    $("#select_file").on("change","input[type='file']",function(){
        var filePath=$(this).val();
        if(filePath.indexOf("csv")!=-1){
//            var arr=filePath.split('\\');
//            var fileName=arr[arr.length-1];
//            $("#file_name").html(fileName);
//            $(".form-group-tips").html(filePath);
            var importUser = getImportUser();
            $input = $(this)[0];
            if(window.FileReader) {
                var fr = new FileReader();
                var file = $(this)[0].files[0];
                fr.onload=function(e){
                    var text = e.target.result;
                    var data = $.csv.toArrays(text);
                    if (data[0][0] != '用户ID') {
                        $(".form-group-tips").html("请确认上传的表头第一列为'用户ID'");
                    } else {
                        $tr = null;
                        $("#import_user_list tbody tr").each(function () {
                            var userId = $(this).find("#user_id").val();
                            if (userId){
                                $(this).remove();
                            } else {
                                $tr = $(this).eq(0);
                            }
                        });
                        if (!$tr) {
                            $trHtml = $('<tr>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_id" id="user_id" value="" placeholder="用户登录id"/></div></td>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_name" id="user_name" value="" placeholder="姓名"/></div></td>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="department" id="department" value="" placeholder="部门"/></div></td>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="tel" id="tel" value="" placeholder="手机号"/></div></td>');
                            $trHtml = $trHtml.append('<td><div class="domain_user_delete"></div></td>');
                            $("#import_user_list tbody").append($trHtml);
                            $tr = $trHtml.eq(0);
                        }
                        for(var i=1;i<data.length;i++){
                            var userId = data[i][0];
                            var name = data[i][1];
                            var tel = data[i][2];
                            var department = data[i][3];
                            if (importUser[userId]){

                            } else {
                                importUser[userId] = new Array();
                                importUser[userId]["user_id"] = userId;
                                importUser[userId]["user_name"] = name;
                                importUser[userId]["tel"] = tel;
                                importUser[userId]["department"] = department;
                            }
                        }
                        var count = 0;
                        for (var i in importUser) {
                            var userId = importUser[i]["user_id"];
                            var name =  importUser[i]["user_name"];
                            var tel = importUser[i]["tel"];
                            var department = importUser[i]["department"];

                            $trHtml = $('<tr>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_id" id="user_id" value="'+userId+'" placeholder="用户登录id"/></div></td>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_name" id="user_name" value="'+name+'" placeholder="姓名"/></div></td>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="department" id="department" value="'+department+'" placeholder="部门"/></div></td>');
                            $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="tel" id="tel" value="'+tel+'" placeholder="手机号"/></div></td>');
                            $trHtml = $trHtml.append('<td><div class="domain_user_delete"></div></td>');
                            $tr.before($trHtml);
                            $tr = $trHtml;
                            count++;
                        }
                        $(".form-group-tips").html("您一共添加了"+count+"位用户，继续请点'保存'");
                        $(".domain_user_delete").click(importUserDelete);
                        $(".domain_user_delete").click(importUserDelete);
                        $("#import_user_list tr #user_id").blur(updateImportUserCount);
                        $input.outerHTML=$input.outerHTML;
                    }
                };
                fr.readAsText(file);
            }
            else {
                alert("Not supported by your browser!");
            }
        }else{
            $(".form-group-tips").html("您未上传文件，或者您上传文件类型有误！").show();
        }
    });
    $(".domain_user_delete").click(importUserDelete);
    $("#import_user_list tr #user_id").blur(updateImportUserCount);

    $(".import_user_add").click(function () {
        $trHtml = $('<tr>');
        $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_id" id="user_id" value="" placeholder="用户登录id"/></div></td>');
        $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="user_name" id="user_name" value="" placeholder="姓名"/></div></td>');
        $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="department" id="department" value="" placeholder="部门"/></div></td>');
        $trHtml = $trHtml.append('<td><div style="margin-right: 16px;"><input class="input_text" type="text" name="tel" id="tel" value="" placeholder="手机号"/></div></td>');
        $trHtml = $trHtml.append('<td><div class="domain_user_delete"></div></td>');
        $("#import_user_list tbody").append($trHtml);
        $(".domain_user_delete").click(importUserDelete);
        $("#import_user_list tr #user_id").blur(updateImportUserCount);
    });

    function checkDepartment(department) {
        var fdStart = department.indexOf("/");
        var depts=department.split("/"); //字符分割
        if (department.length <= 0) {
            result = {};
            result["ok"] = 0;
            result["msg"] = "部门必须填写";
            return result;
        } else  {
            if (fdStart == 0){
                if (depts.length > 6){
                    result = {};
                    result["ok"] = 0;
                    result["msg"] = "最多支持5级部门";
                    return result;
                } else {
                    result = {};
                    result["ok"] = 1;
                    result["msg"] = "正确";
                    result["data"] = depts;
                    return result;
                }
            }else{
                result = {};
                result["ok"] = 0;
                result["msg"] = "部门信息必须以'/'开头";
                return result;
            }
        }
    }

    $(".import_user_save").click(function () {
        var importUser = new Array();
        var checkResult = true;
        $("#import_user_list tr").each(function () {
            $input_user_id = $(this).find("#user_id");
            var userId = $input_user_id.val();
            if (userId){
                if (importUser[userId]){
                    $input_user_id.addClass("error_info");
                    $input_user_id.val("用户ID与["+userId+"]重复");
                    $input_user_id.focus(function (e) {
                        $(this).val(userId);
                        $(this).removeClass("error_info");
                        $(this).unbind(e);
                    });
                    checkResult = false;
                }
                importUser[userId] = new Array();
                var input_user_name = $(this).find("#user_name");
                var userName = input_user_name.val();
                if (!userName){
                    input_user_name.addClass("error_info");
                    input_user_name.val("用户名必须填写");
                    input_user_name.focus(function (e) {
                        $(this).val("");
                        $(this).removeClass("error_info");
                        $(this).unbind(e);
                    });
                    checkResult = false;
                }
                var input_department = $(this).find("#department");
                var department = input_department.val();
                var deptCheck = checkDepartment(department);
                if (!deptCheck.ok){
                    input_department.addClass("error_info");
                    input_department.val(deptCheck.msg);
                    input_department.focus(function (e) {
                        $(this).val(department);
                        $(this).removeClass("error_info");
                        $(this).unbind(e);
                    });
                    checkResult = false;
                }

                var input_tel = $(this).find("#tel");
                var tel = input_tel.val();
                var telCheck = vailPhone(tel);
                if (!telCheck.ok){
                    input_tel.addClass("error_info");
                    input_tel.val(telCheck.msg);
                    input_tel.focus(function (e) {
                        $(this).val(tel);
                        $(this).removeClass("error_info");
                        $(this).unbind(e);
                    });
                    checkResult = false;
                }
                importUser[userId]["user_id"] = userId.toLowerCase();
                importUser[userId]["user_name"] = userName;
                importUser[userId]["tel"] = tel;
                importUser[userId]["department"] = department;
                importUser[userId]["dep1"] = deptCheck.data[1];
                importUser[userId]["dep2"] = deptCheck.data[2]?deptCheck.data[2]:"";
                importUser[userId]["dep3"] = deptCheck.data[3]?deptCheck.data[3]:"";
                importUser[userId]["dep4"] = deptCheck.data[4]?deptCheck.data[4]:"";
                importUser[userId]["dep5"] = deptCheck.data[5]?deptCheck.data[5]:"";
            }
        });
        if ( !checkResult ) {
            return;
        }
        var host_id = $(".import_container #host_id").val();
        var requestList = [];
        var index = 0;
        for (var i in importUser) {
            var userId = importUser[i]["user_id"];
            var name = importUser[i]["user_name"];
            var tel = importUser[i]["tel"];
            var department = importUser[i]["department"];
            var dep1 = importUser[i]["dep1"];
            var dep2 = importUser[i]["dep2"];
            var dep3 = importUser[i]["dep3"];
            var dep4 = importUser[i]["dep4"];
            var dep5 = importUser[i]["dep5"];
            requestList[index]= {};
            requestList[index]['user_id'] = userId;
            requestList[index]['user_name'] = name;
            requestList[index]['tel'] = tel;
            requestList[index]['department'] = department;
            requestList[index]['dep1'] = dep1;
            requestList[index]['dep2'] = dep2;
            requestList[index]['dep3'] = dep3;
            requestList[index]['dep4'] = dep4;
            requestList[index]['dep5'] = dep5;
            requestList[index]['host_id'] = host_id;
            index++;
        }
        if (requestList.length <= 0){
            alert("请录入所要添加的用户");
            return;
        }
        var requestJsonStr = JSON.stringify(requestList);
        $.ajax({
            type: "POST",
            data: requestJsonStr,
            dataType: "json",
            url: "./public_method_ajax.php?action=add_user_list&host=<?php echo $hostInfo["host"]?>",
            success: function (msg) {
                if (msg.ok) {
                    window.location.reload();
                } else {
                    alert(msg.msg);
                }
            },
            complete : function(XMLHttpRequest,status){ //请求完成后最终执行参数
                if (status != 'success') {
                    if(status=='timeout'){//超时,status还有success,error等值的情况
                        $ajaxRequest.abort();
                        alert("超时");
                    } else  {
                        alert(status);
                    }
                }
                return false;
            }
        });
    });

    function kickUser(userId) {
        $.ajax({
            type: "POST",
            data: "user_id=" + userId + "&" + "host=<?php echo $hostInfo["host"]?>",
            url: "./public_method_ajax.php?action=kick_user",
            success:function (msg) {
                alert(msg);
            },
            complete : function(XMLHttpRequest,status){ //请求完成后最终执行参数
                if (status != 'success') {
                    if(status=='timeout'){//超时,status还有success,error等值的情况
                        $ajaxRequest.abort();
                        alert("超时");
                    } else  {
                        alert(status);
                    }
                }
                return false;
            }
        });
    }

    //重置密码
    $("a[class*='reset']").click(function () {
        $userid = $(this).attr('data-id');
        $userName = $(this).attr('data-user');
        $.ajax({
            type: "POST",
            data: "user_id=" + $userid + "&" + "host=<?php echo $hostInfo["host"]?>" + "&" + "user=" + $userName,
            dataType: "json",
            url: "./public_method_ajax.php?action=reinit_password",
            success: function (msg) {
                if (msg.ok) {
                    alert("密码重置成功！");
                    window.location.reload();
                } else {
                    alert(msg.msg);
                }
            }
        });
    });
    //删除
    $("a[class*='delete']").click(function () {
        $user_name = $(this).attr("data-user");
        if (!confirm('你确认要删除['+$user_name+']用户数据吗，删除后无法登录?')) {
            return;
        }
        $.ajax({
            type: "POST",
            data: "user_id=" + $(this).attr('data-id') + "&" + "host=<?php echo $hostInfo["host"];?>" + "&" + "user=" + $user_name + "&" + "host_id=" + <?php echo  $hostInfo["id"];?>,
            dataType: "json",
            url: "./public_method_ajax.php?action=delete_user",
            success: function (msg) {
                if (msg.ok) {
                    window.location.reload();
                } else {
                    alert(msg.msg);
                }
            }
        });
    });
</script>
</body>
</html>

