<?PHP
//dirLIST v0.3.0 file/folder deletion file
session_start();

if(!$_SESSION['logged_in'] || empty($_GET['item_name']))
	die('Access Denied');

require('../config.php');
require('../functions.php');

if($listing_mode == 0) //http deletion
{
	$item_path = '../../'.$dir_to_browse.base64_decode($_GET['folder']);
	$item_path .= (empty($_GET['folder'])) ? base64_decode($_GET['item_name']) : '/'.base64_decode($_GET['item_name']);
	
	if(is_dir($item_path.'/'))
		delete_directory($item_path.'/', 0);
	elseif(is_file($item_path))
		unlink($item_path);
		
	header("Location: ../../index..php?folder=".$_GET['folder']);
	exit;

}
elseif($listing_mode == 1) //ftp deletion
{
	$item_path = $dir_to_browse.base64_decode($_GET['folder']);
	
	$item_path .= (empty($_GET['folder'])) ? base64_decode($_GET['item_name']) : '/'.base64_decode($_GET['item_name']);
	
	$ftp_stream = ftp_connect($ftp_host);
	ftp_login($ftp_stream, $ftp_username, $ftp_password);
	
	if(ftp_size($ftp_stream, $item_path) != '-1')
		@ftp_delete($ftp_stream, $item_path);
	else
		delete_directory($item_path.'/', 1);
	
	header("Location: ../../index..php?folder=".$_GET['folder']);
	exit;

}
?>