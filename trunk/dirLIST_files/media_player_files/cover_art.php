<?PHP
//dirLIST v0.3.0 cover art extractor file
error_reporting(0);

require('getid3.php');
$getID3 = new getID3;

$file = base64_decode($_GET['path']);

$file_details = $getID3->analyze($file);

header('Content-type: image/jpeg');

if($file_details['id3v2']['APIC'][0]['data'] != '')
	echo @$file_details['id3v2']['APIC'][0]['data'];
else
{
	readfile('no_cover.jpg');
	exit;
}
?>