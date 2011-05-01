<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

class megaupload_com extends DownloadClass {
	
	public function Download($link) {
		global $premium_acc,$mu_cookie_user_value;
		$matches = "";
		$Url = parse_url(trim($link));
		if (preg_match ( "/f=(\w+)/", $Url ["query"], $matches )) {
			$page = $this->GetPage("http://www.megaupload.com/xml/folderfiles.php?folderid=" . $matches [1]);
			if (! preg_match_all ( "/url=\"(http[^\"]+)\"/", $page, $matches )) html_error ( 'link not found' );
			
			if (! is_file ( "audl.php" )) html_error ( 'audl.php not found' );
			echo "<form action=\"audl.php?GO=GO\" method=post>\n";
			echo "<input type=hidden name=links value='" . implode ( "\r\n", $matches [1] ) . "'>\n";
			foreach ( array ( "useproxy", "proxy", "proxyuser", "proxypass" ) as $v )
				echo "<input type=hidden name=$v value=" . $_GET [$v] . ">\n";
			echo "<script language=\"JavaScript\">void(document.forms[0].submit());</script>\n</form>\n";
			flush ();
			exit ();
		}
                if ( ($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["megaupload_com"] ["user"] && $premium_acc ["megaupload_com"] ["pass"] ) )        
		{
			$this->DownloadPremium($link);
		}
                else{
                        $this->DownloadFree($link);
                }               	
	}
	private function DownloadFree($link) {
                global $Referer;
                if ($_GET ["password"]) {} else
                $post = array ();
		$post ["filepassword"] = $_POST ['password'];	
		$page = $this->GetPage($link,$this->cookie,$post,$Referer);
		if (stristr($page,'password protected')) {
                        ?>
	                <form method="post">
                        <div>The file Megaupload you're trying to download is password protected. Please enter the password to proceed.</div> 
		        <input type="hidden" name="link" value="<?php echo $link; ?>" />
		        <input type="text" name="password" id="password"/><input type="submit" value="Proceed" />
	                </form>
                        <?php
                        exit;
                        }
		is_present ( $page, "The file you are trying to access is temporarily unavailable" );
		if (! stristr ( $page, "id=\"captchaform" )) {
			$countDown = trim ( cut_str ( $page, "count=", ";" ) );
			$countDown = (! is_numeric ( $countDown ) ? 26 : $countDown);
			$Href = cut_str ( $page, 'downloadlink"><a href="', '"' );
			$Url = parse_url ( html_entity_decode($Href, ENT_QUOTES, 'UTF-8') );
			if (! is_array ( $Url )) {
				html_error ( "Download link not found", 0 );
			}
			insert_timer ( $countDown, "The file is being prepared.", "", true );
			$FileName = basename ( $Url ["path"] );
			$this->RedirectDownload($Href,$FileName,$this->cookie);
			exit ();
		}
	}
	private function DownloadPremium($link) {
		global $Referer, $premium_acc, $mu_cookie_user_value;
		
                $post = array ();
                $post ['login'] = 1;
                $post ['redir'] = 1;
                $post ["username"] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["megaupload_com"] ["user"];
                $post ["password"] = $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["megaupload_com"] ["pass"];
                $page = $this->GetPage('http://www.megaupload.com/?c=login',0,$post);
                
                $premium_cookie = trim ( cut_str ( $page, "Set-Cookie:", ";" ) );           
                if ($mu_cookie_user_value) {
                        $premium_cookie = 'user=' . $mu_cookie_user_value;
                } elseif ($_GET ["mu_acc"] == "on" && $_GET ["mu_cookie"]) {
                        $premium_cookie = 'user=' . $_GET ["mu_cookie"];
                } elseif (! stristr ( $premium_cookie, "user" )) {
                        html_error ( "Cannot use premium account", 0 );
                }
                $page = $this->GetPage($link,$premium_cookie,0,0);
		if ($_POST ["password"] ) {
		$post ["filepassword"] = $_POST ['password'];
                $page = $this->GetPage($link,$premium_cookie,$post,$Referer);
                }
                is_page ( $page );
                $Href = $link;
                $Referer = $link;
                if (stristr($page,'password protected')) {
                        ?>
	                <form method="post">
                        <div>The file Megaupload you're trying to download is password protected. Please enter the password to proceed.</div> 
		        <input type="hidden" name="link" value="<?php echo $link; ?>" />
		        <input type="text" name="password" id="password"/><input type="submit" value="Proceed" />
	                </form>
                        <?php
                        exit;
                        }
        if (stristr ( $page, "Location:" )) {
                $Href = trim ( cut_str ( $page, "Location: ", "\n" ) );
                $Url = parse_url ( html_entity_decode($Href, ENT_QUOTES, 'UTF-8') );
                $FileName = basename ( $Url ["path"] );                
                $this->RedirectDownload($Href,$FileName,encrypt($premium_cookie));
                
        } elseif ($page = cut_str ( $page, 'downloadlink">', '</div>' )) {
                $Href = cut_str ( $page, 'href="', '"' );
                $Referer = $link;
                $Url = parse_url ( html_entity_decode($Href, ENT_QUOTES, 'UTF-8') );
                $FileName = basename ( $Url ["path"] );                
                $this->RedirectDownload($Href,$FileName,encrypt($premium_cookie));
        } else {
                html_error ( "Download link not found", 0 );
        }
	}
}

// Updated by rajmalhotra on 10 Jan 2010 MegaUpload captcha is downloaded on server, then display
// Fixed by rajmalhotra on 20 Jan 2010 Fixed for Download link not found in happy hour
// Fixed by VinhNhaTrang 13.10.2010
?>
