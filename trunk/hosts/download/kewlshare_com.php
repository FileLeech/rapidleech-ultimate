<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer,0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,"deleted or not found","Sorry, the file you requested is either deleted or not found in our database.",0);
	$cookie=GetCookies($page);
	$nL=cut_str($page, 'nL" value="', '"');
	$cL=cut_str($page, 'cL" value="', '"');
	$post=arraY();
	$post["nL"]=$nL;
	$post["cL"]=$cL;
	$post["selection"]="Free";
	$post['x'] = rand(1,140);
    $post['y'] = rand(1,20);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_present($page,"increase your limit","Your Current Download Limit Per 1 hour is over. Please Upgrade your account to increase your limit. Thank You",0);
	unset ($post);
	insert_timer(15, "<b>Timer :</b>");
	$nL=cut_str($page, 'nL" value="', '"');
	if(!$nL){
	
	preg_match('/Location:.+?\\r/i', $page, $loca);
    $redir = rtrim($loca[0]);
    preg_match('/http:.+/i', $redir, $loca);
	if (!$loca[0]){
	html_error("download data is not found");}else{
	$Url=parse_url($loca[0]);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);
	    is_page($page);
	
	preg_match('/We had a request[^<]+/i', $page, $errmsg);
    if ($errmsg[0]){
	html_error($errmsg[0]);}else{
        html_error("Wait for your turn");}
    }
    }
	$cL=cut_str($page, 'cL" value="', '"');
	$post["nL"]=$nL;
	$post["cL"]=$cL;
	$post["act"]="captcha";
	$post['x'] = rand(1,140);
    $post['y'] = rand(1,20);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	preg_match_all('/http:\/\/.+kewlshare\.com\/dl\/[^\'"]+/i', $page, $down);   
    $Url=parse_url($down[0][1]);
    $FileName = !$FileName ? basename($Url["path"]) : $FileName;
    insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&cookie=".urlencode($cookie)."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
	
/*************************\  
written by kaox 13/06/2009
fixed by kaox 04/09/2009
\*************************/

?>