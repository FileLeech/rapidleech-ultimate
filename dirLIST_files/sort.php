<?PHP
//dirLIST v0.3.0 content sorting file

session_start();
$by = $_GET['by'];
$folder = $_GET['folder'];

$current_order = $_SESSION['sort']['order'];
if($current_order == 0)
	$_SESSION['sort']['order'] = 1;
else
	$_SESSION['sort']['order'] = 0;


if($by == "name")
{
	$_SESSION['sort']['by'] = 0;
}
elseif($by == "size")
{
	$_SESSION['sort']['by'] = 1;
}
else //sort by date
{
	$_SESSION['sort']['by'] = 2;
}

header("Location: ../index..php?folder=".$folder);
?>