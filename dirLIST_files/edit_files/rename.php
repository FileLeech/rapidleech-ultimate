<?PHP
//dirLIST v0.3.0 file/folder rename file
session_start();
error_reporting(0);
if(!$_SESSION['logged_in'])
	die('Access Denied');

if(empty($_GET['item_name']) && empty($_POST['new_name']))
	die('Access Denied');

require('../config.php');
require('../functions.php');

$local_text = (empty($_SESSION['lang_id'])) ? set_local_text(0) : set_local_text($_SESSION['lang_id']);

$rename_action = FALSE;

if($_POST['Submit'] == 'Rename')
{
	if($listing_mode == 0) //http rename
	{
		$old_name = '../../'.$dir_to_browse.'/'.base64_decode($_GET['folder']).'/'.base64_decode($_GET['item_name']);
		$new_name = '../../'.$dir_to_browse.'/'.base64_decode($_GET['folder']).'/'.$_POST['new_name'];
		
		if(rename($old_name, $new_name))
			$rename_action = TRUE;
		else
			die('Could not rename file/folder. Permission denied');
	}
	elseif($listing_mode == 1) //ftp rename
	{
		$ftp_stream = ftp_connect($ftp_host) or die(display_error_message("<b>Could not connect to FTP host</b>"));
		ftp_login($ftp_stream, $ftp_username, $ftp_password) or die(display_error_message("<b>Could not login to FTP host</b>"));

		$old_name = $dir_to_browse.base64_decode($_GET['folder']).'/'.base64_decode($_GET['item_name']);
		$new_name = $dir_to_browse.base64_decode($_GET['folder']).'/'.$_POST['new_name'];
		
		if(@ftp_rename($ftp_stream, $old_name, $new_name))
			$rename_action = TRUE;
		else
			die('Could not rename file/folder. Permission denied');
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>dirLIST - Rename File/Folder</title>
<style type="text/css">
<!--
#main_table {
	border: 1px dashed #666666;
	background-color: #B9E9FF;
}
.large_text {
	font-family: Verdana;
	font-size: 16px;
	font-weight: bold;
}
.lables {
	font-family: Verdana;
	font-size: 10px;
	font-weight: bold;
	text-transform: uppercase;
}
.current_name {
	font-family: Verdana;
	font-size: 11px;
	font-weight: bold;
	color: #1BD700;
}
.input_field {
	font-family: Calibri,Tahoma;
	font-size: 18px;
	font-weight: bold;
	height: 25px;
	border-top-width: 2px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-top-color: #006A9D;
	border-right-color: #006A9D;
	border-bottom-color: #006A9D;
	border-left-color: #006A9D;
	width: 100%;
}
.error {
	font-family: Calibri, Tahoma;
	font-size: 12px;
	font-weight: bold;
	text-transform: uppercase;
	color: #F00;
}
#button {
	height: 35px;
	width: 100%;
}
-->
</style>
<script type="text/javascript">
function close_window()
{
	window.opener.location.reload();
	window.close();
}

function onload_events()
{
	<?PHP if($rename_action == TRUE) echo 'close_window();'; ?>
	document.getElementById('new_name').focus();	
	
}
</script>
</head>

<body onload="onload_events();">

<?PHP if(!$rename_action) { ?>
<form id="form1" name="form1" method="post" action="">
  <table width="450" border="0" align="center" cellpadding="5" cellspacing="0" id="main_table">
    <tr>
      <td width="108" align="right" class="lables"><?PHP echo $local_text['current_name']; ?></td>
      <td width="334" class="current_name"><?PHP echo base64_decode($_GET['item_name']); ?></td>
    </tr>
    <tr>
      <td align="right" class="lables"><?PHP echo $local_text['new_name']; ?></td>
      <td><input name="new_name" type="text" class="input_field" id="new_name" /></td>
    </tr>
    <tr>
      <td><input name="Submit" type="hidden" id="Submit" value="Rename" /></td>
      <td><input type="submit" name="button" id="button" value="<?PHP echo $local_text['rename']; ?>" /></td>
    </tr>
  </table>
</form>
<?PHP } ?>
</body>
</html>