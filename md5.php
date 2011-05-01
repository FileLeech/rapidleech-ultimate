<?php
define('RAPIDLEECH', 'yes');
error_reporting(0);
set_time_limit(0);
session_start();
define('CONFIG_DIR', 'configs/');
require_once(CONFIG_DIR.'setup.php');
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
define ('CREDITS', '<small class="small-credits">By jmsmarcelo</small><br />');
// Include other useful functions
require_once('classes/other.php');
login_check();
include(TEMPLATE_DIR.'header.php'); ?>
<title><?php echo lang(383); ?></title>
<center>
<h2><?php echo lang(383); ?></h2><br />
<table class="md5table" align="center" border="0" cellspacing="1" cellpadding="3">
  <tr>
    <th align="center"><?php echo lang(104); ?></th>
    <th align="center"><?php echo lang(56); ?></th>
    <th align="center">MD5</th>
  </tr>
<?php
// Here the downloads
$path = $options['download_dir'];
if (isset($_GET['file'])) {
    $handle = fopen("$path/".$_GET['file']."", 'a+');
    if ($handle) {
        fwrite($handle, '0');
    } else {
        echo "<br />Error!<br />";   
    }
    fclose($handle);
}
$handle = opendir ($path);
while (false !== ($file = readdir ($handle))){
    if ($file != "."){
        if ($file != ".."){
            if ($file != "index.html") {
                if (! is_dir($path."/".$file)) {
?>    
  <tr>
    <td align="right"><b> &nbsp; <a href='md5.php?file=<?php echo $file; ?>'><?php echo $file; ?></a> &nbsp; </b></td>
    <td align="center"> &nbsp; <?php echo filesize($path."/".$file); ?> <small>bytes</small> &nbsp; </td>
    <td><b> &nbsp; <?php echo md5_file($path."/".$file); ?> &nbsp; </b></td>
  </tr>
<?php
                }
            }
        }
    }
}
closedir($handle);
?>
</table>
<br /><br />
<?php
print CREDITS;
?><br />
</center>
<?php include(TEMPLATE_DIR.'footer.php'); ?>