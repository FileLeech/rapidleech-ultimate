<?php
/**
 * @author Somik Khan - Updated by jmsmarcelo
 * @copyright 2010-2011
 */


define('RAPIDLEECH', 'yes');
error_reporting(0);
//ini_set('display_errors', 1);
set_time_limit(0);
ini_alter("memory_limit", "1024M");
ob_end_clean();
ob_implicit_flush(TRUE);
ignore_user_abort(1);
clearstatcache();
$PHP_SELF = !$PHP_SELF ? $_SERVER["PHP_SELF"] : $PHP_SELF;
define('HOST_DIR', 'hosts/');
define('IMAGE_DIR', 'images/');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
define('RAPIDLEECH', 'yes');
define('ROOT_DIR', realpath("./"));
define('PATH_SPLITTER', (strstr(ROOT_DIR, "\\") ? "\\" : "/"));
require_once(CONFIG_DIR.'setup.php');
if (substr($options['download_dir'],-1) != '/') $options['download_dir'] .= '/';
define('DOWNLOAD_DIR', (substr($options['download_dir'], 0, 6) == "ftp://" ? '' : $options['download_dir']));
$nn = "\r\n";
require_once("classes/other.php");
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );

login_check();

require(TEMPLATE_DIR.'/header.php');
?>
<title>Multi <?php echo lang(334); ?></title>

<?php
echo '<br /><div style="text-align:center;">';



if(!empty($_POST['links'])){
    
    if( !empty($_POST['premium_user']) && !empty($_POST['premium_pass']) ){
        $premium = '&amp;premium_user='.$_POST['premium_user'].'&amp;premium_pass='.$_POST['premium_pass'];
    }
    
    
    $all_links = trim($_POST['links']);
    $link_arr = explode("\n",$all_links);
    
    foreach($link_arr as $link){
        $link = trim($link);
        $link = str_replace("\n","",$link);
        $link = str_replace("\r","",$link);
        
        $gotoURL = '
                    <iframe src="index.php?link='.$link.'&amp;premium_acc=on'.$premium.'" name="I1" width="90%" height="300" class="input_box" id="I1">
                        <div> Your browser does not support inline frames or is currently config2ured not to display inline frames. </div>
                    </iframe>
                    
                    ';
                    
        echo $gotoURL;
    } 
}

?>




<div style="text-align:center;">
    <br /><br />
    <form action="" method="POST">
        <textarea name="links" id="links" rows="15" style="width: 500px;" ></textarea><br /><br />
        <strong>Premium Account Username:</strong> <input type="text" name="premium_user" value="" />
        <strong>Password:</strong> <input type="text" name="premium_pass" echo "***".substr($numero, 0); value="" /><br /><br />
        <input type="submit" value="Auto Transload All Links" name="B1" style="width:500px;" />
    </form>
</div>



</div>







<?php include(TEMPLATE_DIR.'footer.php'); ?>
