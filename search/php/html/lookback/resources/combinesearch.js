
(function($){
        $(function(){
                var pullUpAction =function(val) {
                        if (val.length==0) {
                                return false;
                        }
                        $.ajax({
                                        type: "GET",
                                        url: "main_controller.php?action=search_single_msg&method=search&s=1&from=0&term="+val,
                                        dataType: "json",
                                        success: function (msg) {   //json数据类型
                                          if(!msg.ret || msg.content == null){
                                                return false;
                                          }
                                          $(msg.content).appendTo($("#content"));
                                          $("#content").highlight(val);
                                          return false;
                                        }
                                });
                };
var getSearch = function(e)
{
   if(e.keyCode==13)
   {
    var val = $("#search_edit").val();
    if (val.length==0)
    {
        return false;
    }
    $("#content").empty();
    pullUpAction(val);
  }
}
$("#search_edit").on("keyup",getSearch);
});
})(jQuery);
