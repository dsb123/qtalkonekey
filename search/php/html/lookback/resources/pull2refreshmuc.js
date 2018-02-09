var     needUp=true;
var     needDown = true;
var     myScroll;
var upTime,downTime,muc;

(function($){
        $(function(){
var pullUpAction = function(){
        if(!needUp||upTime ==0)
        {
            myScroll.refresh();
            $("#pullUpLabel").html('没有更多数据了'); 
            return false;
        }
         $.ajax({
            type: "GET",
            url: "main_controller.php?action=muc_details&method=showUpMore&muc="+muc+"&time="+upTime,
            dataType: "json",
            success: function (msg) {   //json数据类型
                if(!msg.ret || msg.c == null){
                    myScroll.refresh();
                    needUp = false;
                    $("#pullUpLabel").html('没有聊天记录了');
                    return false;
                }
                $("#m_con").append(msg.c);
                upTime = msg.t;
                myScroll.refresh();
                if(upTime ==0){
			needUp = false;
			$("#pullUpLabel").html('没有聊天记录了');
		}
                return false;
            }
        });

};

var pullDownAction = function(){
        if(!needDown||downTime==0)
        {
            myScroll.refresh();
            $("#pullDownLabel").html('没有更多数据了'); 
            return false;
         }
         $.ajax({
            type: "GET",
            url: "main_controller.php?action=muc_details&method=showDownMore&muc="+muc+"&time="+downTime,
            dataType: "json",
            success: function (msg) {   //json数据类型
                if(!msg.ret || msg.c == null){
                    needDown=false;
                    myScroll.refresh();
                    $("#pullDownLabel").html('没有聊天记录了');
                    return false;
                }
                $("#m_con").prepend(msg.c);
                downTime = msg.t;
                myScroll.refresh(); 
                if(downTime ==0){ 
			needDown = false;
			$("#pullDownLabel").html('没有聊天记录了');
		}
                return false;
            }
        });
};
                myScroll = iscrollAssist.newVerScrollForPull($('#wrapper'),pullDownAction,pullUpAction,$('#search_edit'));
                myScroll.refresh();
                if(myScroll.maxScrollY === 0) {
                    $("#pullUpLabel").html('没有更多数据了');
                }
        });
})(jQuery);

