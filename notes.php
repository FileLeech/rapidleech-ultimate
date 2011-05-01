<?php
define('RAPIDLEECH', 'yes');
error_reporting(0);
set_time_limit(0);
session_start();
define('CONFIG_DIR', 'configs/');
require_once(CONFIG_DIR.'setup.php');
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
// Include other useful functions
require_once('classes/other.php');

login_check();

include(TEMPLATE_DIR.'header.php'); ?>
<br />
<?php

	if (!file_exists($options['download_dir'].lang(327).".txt")) {
		$temp = fopen($options['download_dir'].lang(327).".txt","w");
		fclose($temp);
	}
	if (isset($_POST['notes']) && $_POST['notes']) {
		file_put_contents($options['download_dir'].lang(327).".txt",$_POST['notes']);
?>
	<p><?php echo lang(325); ?></p>
<?php
	}
	$content = file_get_contents($options['download_dir'].lang(327).".txt");
?>
<div align="center">
<form method="post" action="<?php echo (!$PHP_SELF ? $_SERVER["PHP_SELF"] : $PHP_SELF); ?>">
<textarea class="notes" name="notes" rows="1" cols="1"><?php echo $content; ?></textarea>
<br /><input type="submit" name="submit" value="<?php echo lang(326); ?>" />
</form>
</div>
<?php include(TEMPLATE_DIR.'footer.php'); ?>
