var generatedCount = 0;
var needLoadMore = true;
var iscrollObj;
(function($){
	$(function(){
		var pullUpAction =function() {
                        var val = $("#search_edit").val();
         		if (val.length==0||!needLoadMore) {
            			iscrollObj.refresh();
				$("#pullUpLabel").html('没有更多数据了'); 
            			return false;
        		}
        		$.ajax({
            				type: "GET",
            				url: "main_controller.php?action="+url_action+"&method="+url_method+"&from="+generatedCount+"&term="+val+touser,
            				dataType: "json",
            				success: function (msg) {   //json数据类型
                			if(msg==null||!msg.ret || msg.content == null){
                                                iscrollObj.refresh();
                                                $("#pullUpLabel").html('没有更多数据了');
                    				return false;
                			}
                			generatedCount=msg.offset;
                			$(msg.content).appendTo($("#content"));
                			needLoadMore =  msg.hasnext;
					iscrollObj.refresh();
                			if(!msg.hasnext)
                			{
                     				$("#pullUpLabel").html('没有更多数据了');
                			}
                			return false;
            				}
        			});
		};
var doSearch = function()
{
    var val = $("#search_edit").val();
    if (val.length==0)
    {
        return false;
    }
    needLoadMore = true;
    generatedCount = 0;
    $("#content").empty();
    pullUpAction();
}

var getSearch = function(e) {
    if (e.keyCode === 13) {
        doSearch();
    }
}

		iscrollObj = iscrollAssist.newVerScrollForPull($('#wrapperdown'),null,pullUpAction,$('#search_edit'));
                iscrollObj.refresh();
                if(iscrollObj.maxScrollY === 0) {
		    $("#pullUpLabel").html('没有更多数据了');
                }
		$("#search_edit").on("keyup",getSearch);
                doSearch();
        });
})(jQuery);
