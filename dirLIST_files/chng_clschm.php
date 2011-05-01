<?PHP
//dirLIST v0.3.0 color scheme changer
session_start();
if(isset($_GET['id']))
{
	$_SESSION['color_scheme_session'] = $_GET['id'];
	header('Location: ../index..php?folder='.$_GET['folder']);
}
?>