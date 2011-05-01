<?PHP
//dirLIST v0.3.0 XSPF generator file
define('RAPIDLEECH', 'yes');
error_reporting(0);
set_time_limit(0);
session_start();
define('CONFIG_DIR', '../../configs/');
require_once(CONFIG_DIR.'setup.php');
require_once('../../classes/other.php');

login_check();


require('../config.php');
require('../functions.php');
require('getid3.php');

$folder = '../../'.$dir_to_browse.base64_decode($_GET['folder']);
$dir_content = get_dir_content($folder);
$mp3s = array();

//filter out to keep only mp3 files
foreach($dir_content['files']['name'] as $val)
	if(strtolower(strrchr($val, '.')) == '.mp3')
		$mp3s[] = $val;

//print out file headers
echo '<?xml version="1.0" encoding="UTF-8"?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
<title>dirLIST - Media Player</title>
<creator>Hamdiya</creator>
<trackList>';

//initialize getID3 engine
$getID3 = new getID3;

foreach($mp3s as $val)
{
	$file_path = $folder.'/'.$val;
	$mp3_info = $getID3->analyze($file_path);

	echo "\n".'	<track>'."\n";
	echo '		<location>'.'../'.$dir_to_browse.base64_decode($_GET['folder']).'/'.$val.'</location>'."\n";
	
	if($mp3_info['id3v1']['artist'] != "")
		echo '		<creator>'.$mp3_info['id3v1']['artist'].'</creator>'."\n";
	else
		echo '		<creator>unknown</creator>'."\n";
		
	if($mp3_info['id3v1']['album'] != '')
		echo '		<album>'.$mp3_info['id3v1']['album'].'</album>'."\n";
	else
		echo '		<album>unknown</album>'."\n";
		
	if($mp3_info['id3v1']['title'] != '')
		echo '		<title>'.$mp3_info['id3v1']['title'].'</title>'."\n";
	else
		echo '		<title>unknown</title>'."\n";
	
	if($mp3_info['playtime_seconds'] != '')
		echo '		<duration>'.($mp3_info['playtime_seconds']*1000).'</duration>'."\n";
		

	echo '		<image>media_player_files/cover_art.php?path='.base64_encode($file_path).'</image>'."\n";
	echo '	</track>'."\n";
}

//print out file footers
echo '</trackList>
</playlist>';
?>