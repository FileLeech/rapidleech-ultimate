<?PHP
//dirLIST v0.3.0 file download script
error_reporting(0);
set_time_limit(0);
require("config.php");

$_GET['file'] = base64_encode(rawurldecode(base64_decode($_GET['file']))); //this is messy i know, will fix it up

$file_path = ($listing_mode == 0) ? "../".base64_decode($_GET['file']) : base64_decode(rtrim($_GET['file']));

//Security feature to prevent downloading of files above $dir_to_browse
if(count(explode('../', base64_decode($_GET['file']))) > count(explode('../',$dir_to_browse)))
	die('Access Denied');

//Deny access to files and folders that have been excluded
foreach($exclude as $val)
{
	if($val != '.' && $val != '..')
	{
		if(count(explode($val, base64_decode($_GET['file']))) > 1)
			die('Access Denied');			  
	}
}

//Check if valid file
$file_valid = FALSE;
if($listing_mode == 0 && is_file($file_path))
{
	$file_valid = TRUE;
	$file_size = filesize($file_path);
}
elseif($listing_mode == 1)
{
	$ftp_stream = ftp_connect($ftp_host);
	$ftp_login = ftp_login($ftp_stream, $ftp_username, $ftp_password);
	$file_size = ftp_size($ftp_stream, $file_path);
	
	if($file_size != -1)
		$file_valid = TRUE;
}

if(!$file_valid)
	die('Error: The file <b>'.basename($file_path).'</b> does not exist. Please go back and select a file');


//Check if valid file -done

//At this stage, it is assumed that the file is valid and to proceed with prompting the user to download it

$file_name = str_replace(array('+', " "), array('_','_'), basename($file_path));

header('Cache-control: private');
header('Content-Type: application/octet-stream'); 
header('Content-Length: '.filesize($file_path));
header('Content-Disposition: attachment; filename='.$file_name);
ob_flush();

$fh = ($listing_mode == 0) ? fopen($file_path, "r") : fopen('ftp://'.$ftp_username.':'.$ftp_password.'@'.$ftp_host.'/'.$file_path, "r");

while(!feof($fh))
{
	echo ($listing_mode == 0) ?  fread($fh, round($speed*1024, 0)) : fread($fh, 1048576);
	ob_flush();
	if($listing_mode == 0) sleep(1);
}
fclose($fh);
exit;
?>