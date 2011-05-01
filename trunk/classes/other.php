<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}

function create_hosts_file($host_file = "hosts.php") {
	$fp = opendir ( HOST_DIR . 'download/' );
	while ( ($file = readdir ( $fp )) !== false ) {
		if (substr ( $file, - 4 ) == ".inc") {
			require_once (HOST_DIR . 'download/' . $file);
		}
	}
	if (! is_array ( $host )) {
		print lang(127);
	} else {
		$fs = fopen ( HOST_DIR . 'download/' . $host_file, "wb" );
		if (! $fs) {
			print lang(128);
		} else {
			fwrite ( $fs, "<?php\r\n\$host = array(\r\n" );
			$i = 0;
			foreach ( $host as $site => $file ) {
				if ($i != (count ( $host ) - 1)) {
					fwrite ( $fs, "'" . $site . "' => '" . $file . "',\r\n" );
				} else {
					fwrite ( $fs, "'" . $site . "' => '" . $file . "');\r\n?>" );
				}
				$i ++;
			}
			closedir ( $fp );
			fclose ( $fs );
		}
	}
}

function login_check() {
	global $options;
	if ($options['login']) {
		function logged_user($ul) {
			foreach ($ul as $user => $pass) {
				if ($_SERVER['PHP_AUTH_USER'] == $user && $_SERVER['PHP_AUTH_PW'] == $pass) { return true; }
			}
			return false;
		}
		if ($options['login_cgi']) {
			list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = @explode(':', base64_decode(substr((isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : $_SERVER['REDIRECT_HTTP_AUTHORIZATION']), 6)), 2);      
		}
		if (empty($_SERVER['PHP_AUTH_USER']) || !logged_user($options['users'])) {
			header ( 'WWW-Authenticate: Basic realm="RAPIDLEECH PLUGMOD"' );
			header ( "HTTP/1.0 401 Unauthorized" );
			include('deny.php');
			exit;
		}
	}
}

function is_present($lpage, $mystr, $strerror = "", $head = 0) {
	$strerror = $strerror ? $strerror : $mystr;
	if (stristr ( $lpage, $mystr )) {
		html_error ( $strerror, $head );
	}
}

function is_notpresent($lpage, $mystr, $strerror, $head = 0) {
	if (! stristr ( $lpage, $mystr )) {
		html_error ( $strerror, $head );
	}
}

function insert_location($newlocation) {
	if (isset ( $_GET ["GO"] ) && $_GET ["GO"] == "GO") {
		list ( $location, $list ) = explode ( "?", $newlocation );
		$list = explode ( "&", $list );
		foreach ( $list as $l ) {
			list ( $name, $value ) = explode ( "=", $l );
			$_GET [$name] = $value;
		}
	} else {
		global $nn;
		list ( $location, $list ) = explode ( "?", $newlocation );
		$list = explode ( "&", $list );
		print '<form action="'.$location.'" method="post">' . $nn;
		foreach ( $list as $l ) {
			list ( $name, $value ) = explode ( "=", $l );
			print '<input type="hidden" name="'.$name.'" value="'.$value.'" />' . $nn;
		}
		echo ('<script type="text/javascript">void(document.forms[0].submit());</script>');
		echo ('</form>');
		echo ('</body>');
		echo ('</html>');
		flush ();
	}
}

function pause_download() {
	global $pathWithName, $PHP_SELF, $_GET, $nn, $bytesReceived, $fs, $fp;
	$status = connection_status ();
	if (($status == 2 || $status == 3) && $pathWithName && $bytesReceived > - 1) {
		flock ( $fs, LOCK_UN );
		fclose ( $fs );
		fclose ( $fp );
	}
}

function cut_str($str, $left, $right) {
	$str = substr ( stristr ( $str, $left ), strlen ( $left ) );
	$leftLen = strlen ( stristr ( $str, $right ) );
	$leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
	$str = substr ( $str, 0, $leftLen );
	return $str;
}

// tweaked cutstr with pluresearch functionality
function cutter($str, $left, $right,$cont=1)
	{
    for($iii=1;$iii<=$cont;$iii++){
	$str = substr ( stristr ( $str, $left ), strlen ( $left ) );
	}
    $leftLen = strlen ( stristr ( $str, $right ) );
    $leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
    $str = substr ( $str, 0, $leftLen );
    return $str;
}

function write_file($file_name, $data, $trunk = 1) {
	if ($trunk == 1) {
		$mode = "wb";
	} elseif ($trunk == 0) {
		$mode = "ab";
	}
	$fp = fopen ( $file_name, $mode );
	if (! $fp) {
		return FALSE;
	} else {
		if (! flock ( $fp, LOCK_EX )) {
			return FALSE;
		} else {
			if (! fwrite ( $fp, $data )) {
				return FALSE;
			} else {
				if (! flock ( $fp, LOCK_UN )) {
					return FALSE;
				} else {
					if (! fclose ( $fp )) {
						return FALSE;
					}
				}
			}
		}
	}
	return TRUE;
}

function read_file($file_name, $count = -1) {
	if ($count == - 1) {
		$count = filesize ( $file_name );
	}
	$fp = fopen ( $file_name, "rb" );
	flock ( $fp, LOCK_SH );
	$ret = fread ( $fp, $count );
	flock ( $fp, LOCK_UN );
	fclose ( $fp );
	return $ret;
}

function pre($var) {
	echo "<pre>";
	print_r ( $var );
	echo "</pre>";
}

function getmicrotime() {
	list ( $usec, $sec ) = explode ( " ", microtime () );
	return (( float ) $usec + ( float ) $sec);
}

function html_error($msg, $head = 1) {
	global $PHP_SELF, $options;
	//if ($head == 1)
	if (! headers_sent ()) {
		include(TEMPLATE_DIR.'header.php');
	}
	echo ('<div align="center">');
	echo ('<span class="htmlerror"><b>' . $msg . '</b></span><br /><br />');
	if ($options['new_window']) { echo '<a href="javascript:window.close();">'.lang(378).'</a>'; }
	else { echo '<a href="'.$PHP_SELF.'">'.lang(13).'</a>'; }

	echo ('</div>');
	include(TEMPLATE_DIR.'footer.php');
	exit ();
}

function sec2time($time) {
	$hour = round ( $time / 3600, 2 );
	if ($hour >= 1) {
		$hour = floor ( $hour );
		$time -= $hour * 3600;
	}
	$min = round ( $time / 60, 2 );
	if ($min >= 1) {
		$min = floor ( $min );
		$time -= $min * 60;
	}
	$sec = $time;
	$hour = ($hour > 1) ? $hour . " ".lang(129)." " : ($hour == 1) ? $hour . " ".lang(130)." " : "";
	$min = ($min > 1) ? $min . " ".lang(131)." " : ($min == 1) ? $min . " ".lang(132)." " : "";
	$sec = ($sec > 1) ? $sec . " ".lang(133) : ($sec == 1 || $sec == 0) ? $sec . " ".lang(134) : "";
	return $hour . $min . $sec;
}

// Updated function to be able to format up to Yotabytes!
function bytesToKbOrMbOrGb($bytes) {
	if (is_numeric ( $bytes )) {
		$s = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
		$e = floor ( log ( $bytes ) / log ( 1024 ) );
		
		return sprintf ( '%.2f ' . $s [$e], @($bytes / pow ( 1024, floor ( $e ) )) );
	} else {
		$size = "Unknown";
	}
	return $size;
}

function updateListInFile($list) {
	if (count ( $list ) > 0) {
		foreach ( $list as $key => $value ) {
			$list [$key] = serialize ( $value );
		}
		if (! @write_file ( CONFIG_DIR . "files.lst", implode ( "\r\n", $list ) . "\r\n" ) && count ( $list ) > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	} elseif (@file_exists ( CONFIG_DIR . "files.lst" )) {
		// Truncate files.lst instead of removing it since we don't have full
		// read/write permission on the configs folder
		$fh = fopen(CONFIG_DIR.'files.lst','w');
		fclose($fh);
		return true;
	}
}

function _cmp_list_enums($a, $b) {
	return strcmp ( $a ["name"], $b ["name"] );
}

function file_data_size_time($file) {
	global $options;
	$size = $time = false;
	if (is_file($file)) {
		$size = @filesize($file);
		$time = @filemtime($file);
	}
	if ($size === false && $options['2gb_fix'] && file_exists($file) && !is_dir($file) && !is_link($file)) {
		if (substr(PHP_OS, 0, 3) !== "WIN") {
			@exec('stat'.(stristr(@php_uname('s'), 'bsd') !== false ? '-f %m ' : ' -c %Y ').escapeshellarg($file), $time, $tmp);
			if ($tmp == 0) { $time = trim(implode($time)); }
			@exec('stat'.(stristr(@php_uname('s'), 'bsd') !== false ? '-f %z ' : ' -c %s ').escapeshellarg($file), $size, $tmp);
			if ($tmp == 0) { $size = trim(implode($size)); }
		}
	}
	if ($size === false || $time === false) { return false; }
	return array($size, $time);
}

function _create_list() {
	global $list, $_COOKIE, $options;
	$glist = array ();
	if (($options['show_all'] === true) & ($_COOKIE ["showAll"] == 1)) {
		$dir = dir ( DOWNLOAD_DIR );
		while ( false !== ($file = $dir->read ()) ) {
			if (($tmp = file_data_size_time(DOWNLOAD_DIR.$file)) === false) { continue; }; list($size, $time) = $tmp;
			if ($file != "." && $file != ".." && (! is_array ( $options['forbidden_filetypes'] ) || ! in_array ( strtolower ( strrchr ( $file, "." ) ), $options['forbidden_filetypes'] ))) {
				$file = DOWNLOAD_DIR . $file;
				while (isset($glist[$time])) { $time ++; }
				$glist [$time] = array ("name" => realpath($file), "size" => bytesToKbOrMbOrGb($size), "date" => $time );
			}
		}
		$dir->close ();
		@uasort ( $glist, "_cmp_list_enums" );
	} else {
		if (@file_exists ( CONFIG_DIR . "files.lst" )) {
			$glist = file ( CONFIG_DIR . "files.lst" );
			foreach ( $glist as $key => $record ) {
				foreach ( unserialize ( $record ) as $field => $value ) {
					$listReformat [$key] [$field] = $value;
					if ($field == "date")
						$date = $value;
				}
				$glist [$date] = $listReformat [$key];
				unset ( $glist [$key], $glistReformat [$key] );
			}
		}
	}
	$list = $glist;
}

function checkmail($mail) {
	if (strlen ( $mail ) == 0) {
		return false;
	}
	if (! preg_match ( "/^[a-z0-9_\.-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|" . "edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-" . "9]{1,3}\.[0-9]{1,3})$/is", $mail )) {
		return false;
	}
	return true;
}
/* Fixed Shell exploit by: icedog */
function fixfilename($fname, $fpach = '') {
	$f_name = basename ( $fname );
	$f_dir = dirname ( eregi_replace ( "\.\./", "", $fname ) );
	$f_dir = ($f_dir == '.') ? '' : $f_dir;
	$f_dir = eregi_replace ( "\.\./", "", $f_dir );
	$fpach = eregi_replace ( "\.\./", "", $fpach );
	$f_name = eregi_replace ( "\.(php|hta|pl|cgi|sph)", ".xxx", $f_name );
	$ret = ($fpach) ? $fpach . DIRECTORY_SEPARATOR . $f_name : ($f_dir ? $f_dir . DIRECTORY_SEPARATOR : '') . $f_name;
	return $ret;
}
function getfilesize($f) {
	global $is_windows;
	$stat = stat ( $f );
	
	if ($is_windows)
		return sprintf ( "%u", $stat [7] );
	if (($stat [11] * $stat [12]) < 4 * 1024 * 1024 * 1024)
		return sprintf ( "%u", $stat [7] );
	
	global $max_4gb;
	if ($max_4gb === false) {
		$tmp_ = trim ( @shell_exec ( " ls -Ll " . @escapeshellarg ( $f ) ) );
		while ( strstr ( $tmp_, '  ' ) ) {
			$tmp_ = @str_replace ( '  ', ' ', $tmp_ );
		}
		$r = @explode ( ' ', $tmp_ );
		$size_ = $r [4];
	} else {
		$size_ = - 1;
	}
	
	return $size_;
}
function bytesToKbOrMb($bytes) {
	$size = ($bytes >= (1024 * 1024 * 1024 * 1024)) ? round ( $bytes / (1024 * 1024 * 1024 * 1024), 2 ) . " TB" : (($bytes >= (1024 * 1024 * 1024)) ? round ( $bytes / (1024 * 1024 * 1024), 2 ) . " GB" : (($bytes >= (1024 * 1024)) ? round ( $bytes / (1024 * 1024), 2 ) . " MB" : round ( $bytes / 1024, 2 ) . " KB"));
	return $size;
}
function defport($urls) {
	if ($urls ["port"] !== '' && isset ( $urls ["port"] ))
		return $urls ["port"];
	
	switch (strtolower ( $urls ["scheme"] )) {
		case "http" :
			return '80';
		case "https" :
			return '443';
		case "ftp" :
			return '21';
	}
}
function getSize($file) {
	$size = filesize ( $file );
	if ($size < 0)
		if (! (strtoupper ( substr ( PHP_OS, 0, 3 ) ) == 'WIN'))
			$size = trim ( `stat -c%s $file` );
		else {
			$fsobj = new COM ( "Scripting.FileSystemObject" );
			$f = $fsobj->GetFile ( $file );
			$size = $file->Size;
		}
	return $size;
}
function purge_files($delay) {
	if (file_exists ( CONFIG_DIR . "files.lst" ) && is_numeric ( $delay ) && $delay > 0) {
		$files_lst = file ( CONFIG_DIR . "files.lst" );
		$files_new = "";
		foreach ( $files_lst as $files_line ) {
			$files_data = unserialize ( trim ( $files_line ) );
			if (file_exists ( $files_data ["name"] ) && is_file ( $files_data ["name"] )) {
				if (time () - $files_data ["date"] >= $delay) {
					@unlink ( $files_data ["name"] );
				} else {
					$files_new .= $files_line;
				}
			} else {
			}
		}
		file_put_contents ( CONFIG_DIR . "files.lst", $files_new );
	}
}
// PHP4 compatibility
if (! function_exists ( "file_put_contents" ) && ! defined ( "FILE_APPEND" )) {
	define ( "FILE_APPEND", 1 );
	function file_put_contents($n, $d, $flag = false) {
		$mode = ($flag == FILE_APPEND || strtoupper ( $flag ) == "FILE_APPEND") ? "a" : "w";
		$f = @fopen ( $n, $mode );
		if ($f === false) {
			return 0;
		} else {
			if (is_array ( $d )) {
				$d = implode ( $d );
			}
			$bytes_written = fwrite ( $f, $d );
			fclose ( $f );
			return $bytes_written;
		}
	}
}
if (! function_exists ( "file_get_contents" )) {
	function file_get_contents($filename, $incpath = false) {
		if (false === $fh = fopen ( $filename, "rb", $incpath )) {
			trigger_error ( "file_get_contents() failed to open stream: No such file or directory", E_USER_WARNING );
			return false;
		}
		clearstatcache ();
		if ($fsize = @filesize ( $filename )) {
			$data = fread ( $fh, $fsize );
		} else {
			$data = "";
			while ( ! feof ( $fh ) ) {
				$data .= fread ( $fh, 8192 );
			}
		}
		fclose ( $fh );
		return $data;
	}
}

// Using this function instead due to some compatibility problems
function is__writable($path) {
	//will work in despite of Windows ACLs bug
	//NOTE: use a trailing slash for folders!!!
	//see http://bugs.php.net/bug.php?id=27609
	//see http://bugs.php.net/bug.php?id=30931
	

	if ($path {strlen ( $path ) - 1} == '/') // recursively return a temporary file path
		return is__writable ( $path . uniqid ( mt_rand () ) . '.tmp' );
	else if (is_dir ( $path ))
		return is__writable ( $path . '/' . uniqid ( mt_rand () ) . '.tmp' );
		// check tmp file for read/write capabilities
	$rm = file_exists ( $path );
	$f = @fopen ( $path, 'a' );
	if ($f === false)
		return false;
	fclose ( $f );
	if (! $rm)
		unlink ( $path );
	return true;
}

function link_for_file($filename, $only_link = FALSE, $style = '') {
	$inCurrDir = strstr(dirname($filename), ROOT_DIR) ? TRUE : FALSE;
	$PHP_SELF = !$PHP_SELF ? $_SERVER ["PHP_SELF"] : $PHP_SELF;
	if ($inCurrDir) {
		$Path = parse_url($PHP_SELF);
		$Path = substr($Path["path"], 0, strlen($Path["path"]) - strlen(strrchr($Path["path"], "/")));
		$Path = str_replace('\\', '/', $Path.substr(dirname($filename), strlen(ROOT_DIR)));
	}
	elseif (dirname($PHP_SELF.'safe') != '/') {
		$in_webdir_path = dirname(str_replace('\\', '/', $PHP_SELF.'safe'));
		$in_webdir_sub = substr_count($in_webdir_path, '/');
		$in_webdir_root = ROOT_DIR.'/';
		for ($i=1; $i <= $in_webdir_sub; $i++) {
			$in_webdir_path = substr($in_webdir_path, 0, strrpos($in_webdir_path, '/'));
			$in_webdir_root = realpath($in_webdir_root.'/../').'/';
			$in_webdir = (strpos(str_replace('\\', '/', dirname($filename).'/'), str_replace('\\', '/', $in_webdir_root)) === 0) ? TRUE : FALSE;
			if ($in_webdir) {
				$Path = dirname($in_webdir_path.'/'.substr($filename, strlen($in_webdir_root)));
				break;
			}
		}
	}
	else {
		$Path = FALSE;
		if ($only_link) { return ''; }
	}
	$basename = htmlentities(basename($filename));
	$Path = htmlentities($Path).'/'.rawurlencode(basename($filename));
	if ($only_link) { return 'http://'.urldecode($_SERVER['HTTP_HOST']).$Path; }
	elseif ($Path === FALSE) { return '<span>'.$basename.'</span>'; }
	else { return '<a href="'.$Path.'"'.($style !== '' ? ' '.$style : '').'>'.$basename.'</a>'; }
}

function lang($id) {
	global $options;
	include('languages/'.basename($options['default_language']).'.php');
	return $lang[$id];
}

#need to keep premium account cookies safe!
function encrypt($string)
{
	global $secretkey;
	if (!$secretkey) return html_error('Value for $secretkey is empty, please create a random one (56 chars max) in accounts.php!');
	require_once 'class.pcrypt.php';

	/*
	MODE: MODE_ECB or MODE_CBC
	ALGO: BLOWFISH
	KEY:  Your secret key :) (max lenght: 56)
	*/
	$crypt = new pcrypt(MODE_CBC, "BLOWFISH", "$secretkey");

	// to encrypt
	$ciphertext = $crypt->encrypt($string);

	return $ciphertext;
}

function decrypt($string)
{
	global $secretkey;
	if (!$secretkey) return html_error('Value for $secretkey is empty, please create a random one (56 chars max) in accounts.php!');
	require_once 'class.pcrypt.php';
	
	/*
	MODE: MODE_ECB or MODE_CBC
	ALGO: BLOWFISH
	KEY:  Your secret key :) (max lenght: 56)
	*/
	$crypt = new pcrypt(MODE_CBC, "BLOWFISH", "$secretkey");

	// to decrypt
	$decrypted  = $crypt->decrypt($string);

	return $decrypted;
}
?>