<?php

  $bir = $_POST['bir'];
if($bir == "ok"){
    $post = array();
    $post["cap"] = $_POST["cap"];
    $post["uid"] = $_POST["uid"];
    $Referer =$_POST["link"];
    $Url = parse_url($Referer);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);	
		
	preg_match('/[^\'"]+download\d\/[^\'"]+/', $page, $dwn);
	$Url=parse_url($dwn[0]);	
    $FileName = basename($dwn[0]);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
    
    if(preg_match('/Location: *(.+)/', $page, $redir)){
    $redirect=rtrim($redir["1"]);
          $Url = parse_url($redirect); 
         
         
		  
insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] ."&port=".$Url["port"]."&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&cookie=" . urlencode ( $cookie ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "") );
    }
}else{

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);




preg_match('/(action)[ ="]+.+?"/', $page, $act);
$action = preg_replace('/(action)[ ="]+/i', '', $act[0]);
$action = str_replace("\"","",$action);
if (!$action) html_error("file not found. check manually the link    "."http://".$Url["host"].$Url["port"].$Url["path"].$Url["query"], 0);

preg_match('/\w{32}/', $page, $parm1);
$uid = $parm1[0];


if(preg_match_all('/Set-Cookie: *(.+);/', $page, $cook)){
		$cookie = implode(';', $cook[1]);
	}else{
		html_error("Cookie not found.", 0);
	}
	
	
    $post = array();
    $post["uid"] = $uid;
    $post["download"] = "Download";
    $post["fix"] = "1";
    $Referer =$action;
    $Url = parse_url($action);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);	
		
	preg_match('/http:\/\/.+cap\/[\w.]+/', $page, $cap);
	$Url=parse_url($cap[0]);
	
		
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $free_link, $cookie, 0, 0, $_GET["proxy"],$pauth);
		$headerend = strpos($page,"\r\n\r\n");
		$pass_img = substr($page,$headerend+4);
        $imgfile=$options['download_dir']."bitroad_captcha.jpg";
		write_file($imgfile, $pass_img);

		
	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$imgfile\">$nn";
	print	"<input name=\"bir\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"link\" value=\"http://bitroad.net/download3.php\" type=\"hidden\">$nn";
	print	"<input name=\"cap\" type=\"text\" >$nn";
    print   "<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn"; 
	print	"<input name=\"uid\" value=\"$uid\" type=\"hidden\">$nn";	
    print	"<input name=\"approve\" type=\"submit\" class=\"tool-upload\"></form>";
	
}

/*
bitroad download plug-in writted by kaox
*/

?>

