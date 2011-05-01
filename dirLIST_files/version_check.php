<?PHP
//dirLIST v0.3.0 version checker file
error_reporting(0);
echo file_get_contents('http://dir-list.sourceforge.net/process/version_check.php?version='.$_GET['version']);
?>