<?PHP
//dirLIST v0.3.0 admin login file
error_reporting(0);
require('config.php');
require('functions.php');
session_start();

if(@$_GET['logout'] == true)
{
	$_SESSION['logged_in'] = FALSE;
	header("Location: ../index..php?folder=".$_GET['folder']);
	exit;
}

if($admin_password == '')
{
	echo '<span style="font-family:Tahoma;font-size:12px"> Please set a password in the configuration file located at <strong>dirLIST_files/config.php</strong>
	<br /><br />
	This is a necessary security feature to prevent the admin feature in fresh dirLIST installations from being used by unauthorised personal</span>
	';
	exit;
}

if(@$_POST['submit'] == 'Login')
{
	//This is basic authentication, if you wish, you may setup a MySQL database and use it to store the admin username and password
	if($_POST['username'] == $admin_username && $_POST['password'] == $admin_password)
	{
		$_SESSION['logged_in'] = TRUE;
		header("Location: ../index..php?folder=".$_POST['folder']);
		exit;
	}
	else
		$login_error = TRUE;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>dirLIST - Admin Login</title>
<style type="text/css">
<!--
#login_table {
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
.input_field {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 16px;
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
#submit {
	height: 35px;
	width: 100%;
}
-->
</style>
</head>

<body>
<form id="form1" name="form1" method="post" action="admin_login.php">
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <table width="291" border="0" align="center" cellpadding="0" cellspacing="0" id="login_table">
    <tr>
      <td align="center" class="large_text">&nbsp;</td>
      <td height="25" align="center"><?PHP if(@$login_error) { ?><span class="error">Wrong Username/Password</span><?PHP } ?></td>
      <td class="large_text">&nbsp;</td>
    </tr>
    <tr>
      <td width="40" align="center" class="large_text">&nbsp;</td>
      <td height="28" align="center" class="large_text">Admin Login</td>
      <td width="40" class="large_text">&nbsp;</td>
    </tr>
    <tr>
      <td class="lables">&nbsp;</td>
      <td height="18" class="lables">username</td>
      <td class="lables">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="username" type="text" class="input_field" id="username" /></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="lables">&nbsp;</td>
      <td height="20" class="lables">password</td>
      <td class="lables">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input class="input_field" type="password" name="password" id="password" /></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="53" align="center"><input type="submit" name="submit" id="submit" value="Login" /></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="25"><input name="folder" type="hidden" id="folder" value="<?PHP echo $_GET['folder']; ?>" />
      <input type="hidden" name="dir" id="dir" /></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
</body>
</html>