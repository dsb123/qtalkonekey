<?php
require_once("templates/homepage_template.php");

class homepage{
    private $template;
    function __construct(){
	$this->template = new homepage_template();
    }

    public function show()
    {
        if(isset($_GET['source']))
        {
	    $is_muc=false;
            $actual="";
            if($_GET['source']=='muc'){
		 $is_muc = true;
                 if(isset($_GET['tm'])){
		      $actual="&muc=".$_GET['tm'];
		 }
            }
	    else
            {
		if(isset($_GET["ts"]))$actual="&t=".$_GET['ts'].'&conv='.$_GET['conv'];
	    }
            echo $this->template->printHtml($_GET['term'],$is_muc,$actual);
            exit();
        }
        require_once("templates/combine_homepage_template.php");
        $tp = new combine_homepage_template();
	$tp->show_combien_home("","");
    }
   
    
   public function show_pre()
   {
        //require_once("utils/cache_util.php");
        $term = $_SESSION["term"];
        $rkey = "c";
        if(isset($_GET['source']))
        {
	    if($_GET['source']=='muc') $rkey="m";
            else $rkey="s";
        }
        $arr; //cache_util::get_temp_arr($rkey.$_SESSION["user"].$term);
	if(!empty($arr))
        {
            if(isset($_GET['source']))
            {
                 $is_muc=false;
                 if($_GET['source']=='muc') $is_muc=true;
                 $c = count($arr);
		 $template;
                 if($is_muc)
                 {
                     require_once("templates/search_muc_msg_template.php");
                     $template = new search_muc_msg_template();
                 }
                 else{
                     require_once("templates/search_single_msg_template.php");
                     $template = new search_single_msg_template();
                 }
                 $content = $template->generateResultView($arr,$c);
                 $this->template->printPreHtml($content,$_SESSION["term"],$is_muc,$c);
                 exit();
            }
             require_once("templates/combine_homepage_template.php");
             $tp = new combine_homepage_template();
             $tp->show_combien_home($arr,$term);
        }
        else
        {
           $this->show();
        }
   }
}
?>
