<?php
require_once("templates/agg_homepage_template.php");
//@include_once("model/logger.php");

class new_homepage{
    private $template;
    function __construct(){
	$this->template = new agg_homepage_template();
    }

    public function show()
    {
        if(isset($_GET['source']))
        {
	    $is_muc=false;
            if($_GET['source']=='muc') $is_muc = true;
            $this->template->printHtml($is_muc);
            exit();
        }
	$this->template->show_combien_home("","");
    }
    
   public function show_pre()
   {
        //require_once("utils/cache_util.php");
        $term = $_SESSION["term"];
        $rkey = "agg_c_";
        $arr; //cache_util::get_temp_arr($rkey.$_SESSION["user"].$term);

	if(!empty($arr))
        {
             $this->template->show_combien_home($arr,$term);
        }
        else
        {
           $this->show();
        }
   }
}
?>
