<?PHP
//dirLIST v0.3.0 media player file
require('config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media Player</title>

<style type="text/css">
body {
	background-color:#010e17;
}
#player_container {
	border: 1px solid #C0C0C0;
}
.top_text {
	color: #E3E3E3;
	font-family: Verdana;
	font-size: 10px;
	font-weight: bold;
}
</style>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="183" align="center" valign="middle">
      <table width="407" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="142" height="34">&nbsp;</td>
          <td width="230"><span class="top_text">Media Player</span></td>
          <td width="28" align="right" class="top_text" onclick="window.close();" style="cursor:pointer">[X]</td>
        </tr>
      </table>
      <table border="0" cellpadding="2" cellspacing="0" id="player_container">
        <tr>
          <td><object id="player" type="application/x-shockwave-flash" width="400" height="170" data="media_player_files/xspf_player.swf?&playlist_url=media_player_files/generate_playlist.php?folder=<?PHP echo $_GET['folder']; ?>&autoload=true&autoplay=true">
            <param name="movie" value="media_player_files/xspf_player.swf?playlist_url=media_player_files/generate_playlist.php?folder="<?php echo $_get['folder']; ?>&autoload=ture&autoplay=true/>
          </object></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>