<?PHP
/*
+-----------------------------------------------------------------------------------+
|																					|
|	dirLIST - PHP Directory Lister Version 0.3.0									|
|	Copyright © 2009 Hamdiya														|
|	Support:hamdiya.dev@gmail.com													|
|																					|
|	dirLIST is free software; you can redistribute it and/or modify					|
|	it under the terms of the GNU General Public License as published by			|
|	the Free Software Foundation; either version 2 of the License, or				|
|	(at your option) any later version.												|
|																					|
|	This program is distributed in the hope that it will be useful,					|
|	but WITHOUT ANY WARRANTY; without even the implied warranty of					|
|	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the					|
|	GNU General Public License for more details.									|
|																					|		
|	You should have received a copy of the GNU General Public License				|
|	along with this program; if not, write to the Free Software						|
|	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA		|
|																					|
+-----------------------------------------------------------------------------------+

  = = = = = = = = = = = = = = = = = = = = = = = = = =
  U  S  E  R    C  O  N  F  I  G  U  R  A  T  I  O  N   
  = = = = = = = = = = = = = = = = = = = = = = = = = =

	You can start by placing this file and the accompanying dirLIST_files folder
	in the directory you wish to display and you should be all set. If you want,
	you can change a few settings in the 'dirLIST_files/config.php' file for
	further customisation.

  = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
  U  S  E  R    C  O  N  F  I  G  U  R  A  T  I  O  N    -    D  O  N  E  
  = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
*/
define('RAPIDLEECH', 'yes');
error_reporting(0);
set_time_limit(0);
session_start();
define('CONFIG_DIR', 'configs/');
require_once(CONFIG_DIR.'setup.php');
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
define ('CREDITS', '<span class="rev-dev">Integrated in </span><a href="http://www.rapidleech.com/"  target="_blank">RapidLeech</a><br><small class="small-credits">By jmsmarcelo</small><br />');
// Include other useful functions
require_once('classes/other.php');

login_check();

include(TEMPLATE_DIR.'header.php');

require("dirLIST_files/config.php");
require("dirLIST_files/functions.php");

$url_folder = base64_decode(trim($_GET['folder']));
if(!empty($_GET['folder']))
	$dir_to_browse .= $url_folder."/";

//Load time
if($load_time == 1)
	$start_time = array_sum(explode(" ",microtime()));

//Get colour scheme
if(isset($_SESSION['color_scheme_session']))
	$color_scheme_code = $_SESSION['color_scheme_session'];
$color_scheme = color_scheme($color_scheme_code);

//Set the view mode: thumbnails or list
if(isset($_SESSION['view_mode_session']))
	$view_mode = $_SESSION['view_mode_session'];
	
//Set the display language
if(isset($_SESSION['lang_id']))
{
	$local_text = set_local_text($_SESSION['lang_id']);
	$lang_id = $_SESSION['lang_id'];
}
else
{
	$local_text = set_local_text($default_language);
	$lang_id = $default_language;
}
?><center>
<title>File Manager</title>
<style type="text/css">
<!--
<?PHP echo 'body,td,th {font-family: Tahoma, Verdana;font-size: 10pt;}
a:link {text-decoration: none;color: '.$color_scheme['link_content']['link'].';}
a:visited {text-decoration: none;color: '.$color_scheme['link_content']['visited'].';}
a:hover {text-decoration: underline;color: '.$color_scheme['link_content']['hover'].';}
a:active {text-decoration: none;color: '.$color_scheme['link_content']['active'].';}
a.sort:link{text-decoration: underline;color: '.$color_scheme['link_sort']['link'].';}
a.sort:visited{text-decoration: underline;color: '.$color_scheme['link_sort']['visited'].';}
a.sort:hover{text-decoration: underline;color: '.$color_scheme['link_sort']['hover'].';}
a.sort:active{text-decoration: none;color: '.$color_scheme['link_sort']['active'].';}
.top_row {color: '.$color_scheme['top_row']['color'].';font-weight: bold;font-size: 14px;background-color: '.$color_scheme['top_row']['bg'].';}
.folder_bg {background-color: '.$color_scheme['main_table']['folder_bg'].';}
.file_bg1 {background-color: '.$color_scheme['main_table']['file_bg1'].';}
.file_bg2 {background-color: '.$color_scheme['main_table']['file_bg2'].';}';?>
.table_border {border: 1px dashed #666666;}
.path_font {font-family: "Courier New", Courier, monospace;}
.banned_font {font-size: 9px;}
.error {border-top-width: 2px;border-bottom-width: 2px;border-top-style: solid;border-bottom-style: solid;border-top-color: #FF666A;border-bottom-color: #FF666A;}
#color_scheme {cursor:pointer;}
.option_style {font-family: Verdana, Tahoma;font-size: 11px;}
.language_selection {height: 22px;	width: 182px;background-color:<?PHP echo $color_scheme['main_table']['file_bg1']; ?>;	border: 1px dashed #666666;}
.selected_lang {background-color:<?PHP echo $color_scheme['main_table']['file_bg2']; ?>;}
#file_edit_box {position:absolute;width: 150px;display:none;}
-->
</style>
<?PHP if($view_mode == 0) { //enable the javascript required for thumbnail view?>
<script type="text/javascript">
var images_paths = [];
var thumb_counter = 0;

function display_thumbs() {
	more_thumbs = true;
	
	while(more_thumbs){
		thumb = document.getElementById('img_thumb_'+thumb_counter);
		if(document.getElementById('img_thumb_'+thumb_counter) != null){
			images_paths.push(thumb.getAttribute('link_att'));
			get_thumb(thumb_counter);
			thumb_counter++;
		}
		else
			more_thumbs = false;
	}
}

function get_thumb(id) 
{
	var xhr;
	try	{ xhr = new XMLHttpRequest();}
	catch(e)
	{
		try
		{
			xhr = new ActiveXObject("Msxml12.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	xhr.onreadystatechange = function()
	{
		if(xhr.readyState == 4)
		{
			document.getElementById('img_thumb_'+id).setAttribute('src', 'dirLIST_files/thumb_gen.php?image_path='+document.getElementById('img_thumb_'+id).getAttribute('link_att'));
		}
	}
	xhr.open("GET", "dirLIST_files/thumb_gen.php?image_path="+images_paths[id], true);
	xhr.send(null);
}
</script>
<?PHP } ?>
<script type="text/javascript">
function change_color_scheme(id)
{
	window.location = 'dirLIST_files/chng_clschm.php?folder=<?PHP echo $_GET['folder']; ?>&id='+id;
}

function set_language()
{
	window.location = 'dirLIST_files/set_language.php?folder=<?PHP echo $_GET['folder']; ?>&lang_id='+document.getElementById('language_selection').value;
}
</script>
<?PHP if($_SESSION['logged_in']) { ?>
<script type="text/javascript">
var selected_item_type;
var selected_item_id;
var mouse_x;
var mouse_y;

var ms_ie = document.all?true:false;

if (!ms_ie) document.captureEvents(Event.MOUSEMOVE)

document.onmousemove = update_mouse_xy;

function update_mouse_xy(e)
{
	if(ms_ie)
	{
		mouse_x = event.clientX + document.body.scrollLeft;
		mouse_y = event.clientY + document.body.scrollTop;
	}
	else
	{
		mouse_x = e.pageX;
		mouse_y = e.pageY;
	}
	
	return true
}

function show_div(item_type, item_id)
{
	selected_item_type = item_type;
	selected_item_id = item_id;
	
	x = mouse_x;
	y = mouse_y;
	
	//some browsers may return negative values
	if(x < 0) x = 0;
	if(y < 0) y = 0;
	
	document.getElementById('file_edit_box').style.display = 'block';
	document.getElementById('file_edit_box').style.left = x-8+'px';
	document.getElementById('file_edit_box').style.top = y-8+'px';
}

function mouse_out_handler(event)
{
	var toElement = null;
	
	if(event.relatedTarget)
		toElement = event.relatedTarget;
	else if(event.toElement)
		toElement = event.toElement;
	
	while (toElement && toElement.tagName != "DIV")
		toElement = toElement.parentNode;
	
	if(!toElement)
		document.getElementById('file_edit_box').style.display = 'none';
}

function check_for_update() 
{
	var xhr;
	try	{ xhr = new XMLHttpRequest();}
	catch(e)
	{
		try
		{
			xhr = new ActiveXObject("Msxml12.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
				return false;
			}
		}
	}
	xhr.onreadystatechange = function()
	{
		document.getElementById('checking_gif').style.display = 'block';
		document.getElementById('update_link_container').innerHTML = '';
		if(xhr.readyState == 4)
		{
			document.getElementById('checking_gif').style.display = 'none';
			if(xhr.responseText == 1)
			{
				document.getElementById('update_link_container').innerHTML = '<a href="http://dir-list.sourceforge.net/process/redir_to_update.php" target="_blank"><?PHP echo $local_text['update_available']; ?>!</a>';
			}
			else
			{
				document.getElementById('update_link_container').innerHTML = '<?PHP echo $local_text['no_update_found']; ?>'
			}
		}
	}
	xhr.open("GET", "dirLIST_files/version_check.php?version=0.3.0", true);
	xhr.send(null);
}
</script>
<?PHP } ?>
</head>
<body <?PHP if($view_mode == 0) echo 'onload="display_thumbs();"';?>>
<p>
<!-- Output basic HTML code -done -->
<?PHP
//Open FTP connection
if($listing_mode == 1)
{
	$ftp_stream = ftp_connect($ftp_host) or die(display_error_message("<b>Could not connect to FTP host</b>"));
	@ftp_login($ftp_stream, $ftp_username, $ftp_password) or die(display_error_message("<b>Could not login to FTP host</b>"));
}
//Open FTP connection -done
$folder_exists = true;
//Check if directory exists
if($listing_mode == 0)
	$folder_exists = (!is_dir($dir_to_browse)) ? false : true; //HTTP
elseif($listing_mode == 1 && PHP_VERSION >= 5)
	$folder_exists = (!is_dir('ftp://'.$ftp_username.':'.$ftp_password.'@'.$ftp_host.$dir_to_browse)) ? false : true; //FTP

if($folder_exists == false)
{
	echo display_error_message("<b>Error:</b> Folder specified does not exist. This could be because you manually entered the folder name in the URL or you don't have permission to access this folder");
	exit;
}
//Chcek if directory exists -done

//This is a VERY important security feature. It prevents people from browsing directories above $dir_to_browse and any excluded folders. Edit this part at your own risk
if(count(explode("../",$folder)) > 1 || in_array(basename($url_folder), $exclude))
{
	echo display_error_message("<b>Access Denied</b>");
	exit;
}

if(strlen($url_folder) == 2 && $url_folder == "..")
{
	echo display_error_message("<b>Access Denied</b>");
	exit;
}
//Seurity feature -done

//Calculate table dimensions
$table_width = 50+$width_of_files_column+$width_of_sizes_column+$width_of_dates_column;

//Breadcrumbs and admin logout link
echo '<table width="'.$table_width.'" border="0" cellspacing="0" cellpadding="0"><tr><td>';
$this_file_name = basename($_SERVER['PHP_SELF']);
$this_file_size = filesize($this_file_name);
echo $local_text['index_of'].': <a href="'.$this_file_name.'">home</a>/';
if(!empty($url_folder))
{
	$folders_in_url = explode("/", $url_folder);
	$folders_in_url_count = count($folders_in_url);
	for($i=0;$i<$folders_in_url_count;$i++)
	{
		$temp = "";
		for($j=0;$j<$i+1;$j++)
		{
			$temp .= "/".$folders_in_url[$j];
		}
		$temp = substr($temp, 1);
		echo '<a href="'.$this_file_name.'?folder='.base64_encode($temp).'">'.$folders_in_url[$i].'</a>/';
	}
}
echo '</td>';
if($_SESSION['logged_in'])
	echo '<td width="150" align="right"><a href="dirLIST_files/admin_login.php?logout=true&folder='.$_GET['folder'].'">Admin Logout</a><br><img id="checking_gif" src="dirLIST_files/checking.gif" style="display:none"/><span id="update_link_container"><a id="update_link" href="#" onclick="check_for_update();">'.$local_text['check_for_update'].'</a><span></td>';
elseif($admin_login_link == 1)
	echo '<td width="90" align="right"><a href="dirLIST_files/admin_login.php?folder='.$_GET['folder'].'">Admin Login</a></td>';

echo '</tr></table><br />';
//Breadcrumbs -done

//Any upload error is displayed here
switch(base64_decode($_GET['err']))
{
	case "upload_banned": echo display_error_message("<b>Upload failed, banned file type</b>")."<br/>";break;
	case "upload_error": echo display_error_message("<b>Upload failed, an unknown error occured</b>")."<br />";break;
	case "size": echo display_error_message("<b>File size exceeded limit. Max allowed is ".max_upload_size()."B</b>")."<br />";break;
	case "nofile": echo display_error_message("<b>Please select a file to upload!</b>")."<br />";break;
}
//Any upload error is displayed here -done

//Change excluded extensions to lowercase if $case_sensative_ext is disabled
if($case_sensative_ext == 0)
	foreach($exclude_ext as $key => $val)
		$exclude_ext[$key] = strtolower($val);

//Initialize arrays
$folders = array();
$files = array();
//initialize arrays -done

//Get directory content seperatiung files and folders into 2 arrays and filtering them to remove those exlcluded
$dir_content = get_dir_content($dir_to_browse);

$folders['name'] = $dir_content['folders']['name'];
$folders['date'] = $dir_content['folders']['date'];
$folders['link'] = $dir_content['folders']['link'];
$files['name'] = $dir_content['files']['name'];
$files['size'] = $dir_content['files']['size'];
$files['date'] = $dir_content['files']['date'];
$files['link'] = $dir_content['files']['link'];
$images_detected = $dir_content['images_detected'];
$media_detected = $dir_content['media_detected'];

//The folder size calculation has not been placed inside the get_dir_content function so as not to affect it's speed. This is important becasue folder_size calls upon get_dir_content
if($view_mode == 1)
{
	if($show_folder_size_http == 1 && $listing_mode == 0)
		foreach($folders['name'] as $key => $val)
			$folders['size'][$key] = folder_size($dir_to_browse.$folders['name'][$key]);
	elseif($show_folder_size_ftp == 1 && $listing_mode == 1)
		foreach($folders['name'] as $key => $val)
			$folders['size'][$key] = folder_size($dir_to_browse.$folders['name'][$key]);
	else
		$folders['size'][$key] = array();
}
//Get directory content -done

//Sort the folders and files array
//User sorted
if(isset($_SESSION['sort']))
{
	$sort_by = $_SESSION['sort']['by'];
	$sort_order = $_SESSION['sort']['order'];
}

if(!empty($folders['name']))
{
	if($sort_by == 0)
	{
		natcasesort($folders['name']);
		$folders_sorted = $folders['name'];
	}
	elseif($sort_by == 1 && $listing_mode == 0 && $show_folder_size_http == 1)//Sort by size for HTTP listing
	{
		asort($folders['size'], SORT_NUMERIC);
		$folders_sorted = $folders['size'];
	}
	elseif($sort_by == 1 && $listing_mode == 1 && $show_folder_size_ftp == 1)//Sort by size for FTP listing
	{
		asort($folders['size'], SORT_NUMERIC);
		$folders_sorted = $folders['size'];
	}
	else
		$folders_sorted = sort_by_date($folders['date']);
	
	if($sort_order == 1)
		$folders_sorted = array_reverse($folders_sorted, TRUE);
		
}
else
	$folders_sorted = array();//if there are no folders in the current directory
	
if(!empty($files['name']))
{
	if($sort_by == 0)
	{
		natcasesort($files['name']);//natcasesort preserves the array keys
		$files_sorted = $files['name'];
	}
	elseif($sort_by == 1)
	{
		asort($files['size'], SORT_NUMERIC);
		$files_sorted = $files['size'];
	}
	else
		$files_sorted = sort_by_date($files['date'], $sort_order);	
	
	if($sort_order == 1)
		$files_sorted = array_reverse($files_sorted, TRUE);
}
else
	$files_sorted = array();//if there are no files in the current directory
//Sort the folders and files array -done

//Icons
if($view_mode == 0)
{
	$files_icons_array = icons($files['name'], $view_mode);
	$folder_icon = ($view_mode == 0) ? '<img border="0" src="dirLIST_files/icons_large/folder.png">':'<img src="dirLIST_files/icons/folder.gif"> ';
}
elseif($view_mode == 1 && $file_icons)
{
	$files_icons_array = icons($files['name'], $view_mode);
	$folder_icon = ($view_mode == 0) ? '<img border="0" src="dirLIST_files/icons_large/folder.png">':'<img src="dirLIST_files/icons/folder.gif"> ';
}
//Icons -done

//Hide file extensions if enabled
$files['name_with_ext'] = $files['name'];
if($hide_file_ext == 1)
{
	foreach($files['name'] as $key => $val)
	{
		$files['name'][$key] = remove_ext($val);
	}
}
//Hide file extensions if enabled -done

if(!empty($folders['name']) || !empty($files['name'])) { ?>

<table width="<?PHP echo $table_width; ?>" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="320" rowspan="2" valign="top"><?PHP if($legend == 1){ ?>
            <table class="table_border" width="320" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td height="27" class="top_row"><?PHP echo $local_text['key']; ?></td>
                    <td width="50" align="center"><?PHP echo $local_text['folder']; ?></td>
                    <td class="folder_bg" width="25">&nbsp;</td>
                    <td width="30" align="center"><?PHP echo $local_text['file']; ?></td>
                    <td class="file_bg1" width="25">&nbsp;</td>
                    <td width="11" align="center">|</td>
                    <td class="file_bg2" width="25">&nbsp;</td>
                </tr>
            </table>
        	<?PHP } echo '<br />'; if($statistics == 1) { ?>
            <a href="#" onClick="if(document.getElementById('statistics').style.display == 'none'){ document.getElementById('statistics').style.display = 'block'; } else { document.getElementById('statistics').style.display = 'none'; }"><?PHP echo $local_text['show_hide_stats']; ?><br></a>
            <div id="statistics" style="display:none">
                <br /><table width="320" border="0" cellpadding="5" class="table_border">
                    <tr>
                        <td width="95" class="top_row"><?PHP echo $local_text['total_folders']; ?></td>
                        <td><?PHP echo count($folders['name']); ?>, <?PHP echo $local_text['consuming']; ?>: <?PHP echo letter_size(array_sum($folders['size'])); ?></td>
                    </tr>
                    <tr>
                        <td width="95" class="top_row"><?PHP echo $local_text['total_files']; ?></td>
                        <td><?PHP echo count($files['name']); ?>, <?PHP echo $local_text['consuming']; ?>: <?PHP echo letter_size(array_sum($files['size'])); ?></td>
                    </tr>
                    <tr>
                        <td width="95" class="top_row"><?PHP echo $local_text['total_files_and_folders']; ?></td>
                        <td><?PHP echo (count($folders['name'])+count($files['name'])); ?>, <?PHP echo $local_text['consuming']; ?>: <?PHP echo letter_size((array_sum($files['size'])+array_sum($folders['size']))); ?></td>
                    </tr>
                </table><br />
            </div><?PHP } ?>
        </td>
        
        <td width="222" rowspan="2" align="center" valign="top">
        
            <table width="175" border="0" cellpadding="2" cellspacing="2" class="table_border">
                    <?PHP if($view_mode_user_selectable == 1) { ?>
                    <tr>
                    <td height="27" align="center" class="file_bg2"><a href="dirLIST_files/change_view.php?folder=<?PHP echo $_GET['folder']; ?>"><?PHP echo ($view_mode == 0) ? $local_text['switch_to_list'] : $local_text['switch_to_thumbnail']; ?></a></td>
                    </tr>
					<?PHP } ?>
                    <?PHP if($images_detected == 1 && $enable_gallery == 1) { ?>
                    <tr>
                    <td height="27" align="center" class="file_bg2"><a href="#" onClick="window.open('dirLIST_files/gallery.php?folder=<?PHP echo $_GET['folder']; ?>', null, 'scrollbars = 0, status = 1, height = 650, width = 750, resizable = 1, location = 0')"><?PHP echo $local_text['launch_gallery']; ?></a></td>
                    </tr>
					<?PHP } ?>
                    <?PHP if($media_detected == 1 && $enable_media_player == 1) { ?>
                    <tr>
                    <td height="27" align="center" class="file_bg2"><a href="#" onClick="window.open('dirLIST_files/media_player.php?folder=<?PHP echo $_GET['folder']; ?>', null, 'scrollbars = 0, status = 1, height = 240, width = 430, resizable = 1, location = 0')"><?PHP echo $local_text['launch_media_player']; ?></a></td>
                    </tr>
					<?PHP } ?>
            </table>
        </td>
        
		<?PHP if($color_scheme_user_selectable == 1) { ?>
        <td width="184" height="40" align="right" valign="top">
                <table width="182" border="0" cellpadding="2" cellspacing="2" class="table_border">
                    <tr>
                        <td id="color_scheme" width="24" height="27" bgcolor="#006699" onClick="change_color_scheme(0);">&nbsp;</td>
                        <td id="color_scheme" width="24" bgcolor="#840000" onClick="change_color_scheme(1);">&nbsp;</td>
                        <td id="color_scheme" width="24" bgcolor="#005300" onClick="change_color_scheme(2);">&nbsp;</td>
                        <td id="color_scheme" width="24" bgcolor="#FFE500" onClick="change_color_scheme(3);">&nbsp;</td>
                        <td id="color_scheme" width="24" bgcolor="#995100" onClick="change_color_scheme(4);">&nbsp;</td>
                        <td id="color_scheme" width="24" bgcolor="#333333" onClick="change_color_scheme(5);">&nbsp;</td>
                    </tr>
                </table>
        </td><?PHP } ?>
	<?PHP if($language_user_selectable == 1) { ?>
    <tr>
	  <td align="right" valign="top">
      <select name="language_selection" id="language_selection" onChange="set_language()" class="language_selection">
        <option selected="selected"><?PHP echo $local_text['select_language']; ?>...</option>
        <option class="option_style<?PHP if($lang_id == '0') echo ' selected_lang'; ?>" value="0"><?PHP echo $local_text['english']; ?></option>
        <option class="option_style<?PHP if($lang_id == '1') echo ' selected_lang'; ?>" value="1"><?PHP echo $local_text['french']; ?></option>
        <option class="option_style<?PHP if($lang_id == '2') echo ' selected_lang'; ?>" value="2"><?PHP echo $local_text['german']; ?></option>
        <option class="option_style<?PHP if($lang_id == '3') echo ' selected_lang'; ?>" value="3"><?PHP echo $local_text['spanish']; ?></option>
      </select>
      </td>
	</tr>
    <?PHP } ?>
</table>

<table width="725" border="0" cellspacing="5" cellpadding="5">
    <tr>
    	<td width="<?PHP echo ($view_mode == 0) ? '414':$width_of_files_column; ?>" class="top_row"><a class="sort" href="dirLIST_files/sort.php?by=name&folder=<?PHP echo $_GET['folder']; ?>"><?PHP echo $local_text['name']; ?></a></td>
    	<td width="<?PHP echo ($view_mode == 0) ? '128':$width_of_sizes_column; ?>" class="top_row"><a class="sort" href="dirLIST_files/sort.php?by=size&folder=<?PHP echo $_GET['folder']; ?>"><?PHP echo $local_text['size']; ?></a></td>
    	<td width="<?PHP echo ($view_mode == 0) ? '128':$width_of_dates_column; ?>" class="top_row"><a class="sort" href="dirLIST_files/sort.php?by=date&folder=<?PHP echo $_GET['folder']; ?>"><?PHP echo $local_text['date_uploaded']; ?></a></td>
  </tr>
</table>
<?PHP 
if($view_mode == 0) //thumbnail mode
{
	$cells_thumbs = array();
	$cells_names = array();
	$folders_counter = 0;
	$files_counter = 0;
	$img_thumbs_counter = 0;
	
	foreach($folders_sorted as $key => $val)//This part is for the folders
	{
		$cells_thumbs[] = '<td class="folder_bg table_border" width="128" height="140" align="center" valign="middle"><a href="'.$this_file_name.'?folder='.base64_encode($folders['link'][$key]).'"><img border="0" src="dirLIST_files/icons_large/folder.png"></a></td>'."\n";
		
		$cell_name = '';
		
		$cell_name .= '<td height="30" align="center" valign="top">';
		
		if($_SESSION['logged_in'])
		$cell_name .= '<div style="float:left">';
		
		$cell_name .= '<a href="'.$this_file_name.'?folder='.base64_encode($folders['link'][$key]).'">'.chunk_split($folders['name'][$key], 15, "<br />").'</a>';
		
		if($_SESSION['logged_in'])
			$cell_name .= '</div><div style="float:right"><img border="0" src="dirLIST_files/edit_files/edit.png" onclick="show_div(0, \''.$folders_counter.'\');" style="cursor:pointer"></div>';
		

		
		$cells_names[] = $cell_name.'</td>'."\n";
		
		$folders_counter++;
	}
	
	foreach($files_sorted as $key => $val)//This part is for the files
	{
		$file_class = ($files_counter%2 == 0) ? "file_bg1" : "file_bg2";
		$file_link = ($limit_download_speed == 1 || $listing_mode == 1) ? 'dirLIST_files/download.php?file='.base64_encode($files['link'][$key]):$files['link'][$key];
		
		if(in_array(strtolower(strrchr($files['name'][$key], '.')), $thumb_types) && $display_image_thumbs == 1)
		{	
			//signifies it's an image and a thumbnail is to be displayed
			$cells_thumbs[] = '<td class="'.$file_class.' table_border" width="128" height="140" align="center" valign="middle"><a href="'.$file_link.'" target="_blank" ><img id="img_thumb_'.$img_thumbs_counter.'" link_att="'.rawurlencode($files['link'][$key]).'" src="dirLIST_files/icons_large/loading'.$color_scheme_code.'.gif" border="0" /></a></td>'."\n";
			$img_thumbs_counter++;
		}
		else
		{
			$cells_thumbs[] = '<td class="'.$file_class.' table_border" width="128" height="140" align="center" valign="middle"><a href="'.$file_link.'"><img border="0" src="dirLIST_files/icons_large/'.$files_icons_array[$key].'" /></a></td>'."\n";
		}
		
		$cell_name = '';
		$cell_name .= '<td height="30" align="center" valign="top">';
		
		if($_SESSION['logged_in'])
			$cell_name .= '<div style="float:left">';
		
		$cell_name .= '<a href="'.$file_link.'">'.chunk_split($files['name'][$key], 15, "<br />").'</a>';
		
		if($_SESSION['logged_in'])
			$cell_name .= '</div><div style="float:right"><img border="0" src="dirLIST_files/edit_files/edit.png" onclick="show_div(1, \''.$files_counter.'\');" style="cursor:pointer"></div>';
		
		$cells_names[] = $cell_name.'</td>'."\n";
		
		$files_counter++;
	}
	
	echo '<table width="725" border="0" cellspacing="5" cellpadding="5">';
	
	$items = 0;
	
	$total_items = count($cells_names);
	$number_of_rows = ceil($total_items/5);
	
	for($i=0;$i<$number_of_rows;$i++)
	{
		echo '<tr>';
		for($j=(5*$i);$j<(5*($i+1));$j++)
		{
			echo (!empty($cells_thumbs[$j])) ? $cells_thumbs[$j] : '<td></td>';
		}
			
		echo '</tr><tr>';
		
		for($j=(5*$i);$j<(5*($i+1));$j++)
			echo (!empty($cells_names[$j])) ? $cells_names[$j] : '<td></td>';
		echo '</tr>';
	}
	
	echo '</table>';

}
else //list mode
{
	echo '<table width="725" border="0" cellspacing="5" cellpadding="5">';
	$count = 0;
	foreach($folders_sorted as $key => $val)
	{
		echo '<tr class="folder_bg"><td width="'.$width_of_files_column.'">';
		echo '<div style="float:left;width:'.($width_of_files_column-40).'px">';
		if($file_icons == 1)
			echo '<img src="dirLIST_files/icons/folder.gif">';

		echo ' <a href="'.$this_file_name.'?folder='.base64_encode($folders['link'][$key]).'">'.$folders['name'][$key].'</a></div>';
		if($_SESSION['logged_in'])
			echo '<div style="float:right"><img border="0" src="dirLIST_files/edit_files/edit.png" onclick="show_div(0, \''.$count.'\');" style="cursor:pointer"></div>';
		
		echo '</td>';
		echo '<td width="'.$width_of_sizes_column.'">';
		
		if($listing_mode == 0 && $show_folder_size_http == 1)
			echo letter_size($folders['size'][$key]);
		elseif($listing_mode == 1 && $show_folder_size_ftp == 1)
			echo letter_size($folders['size'][$key]);
		else
			echo '-';
		
		echo '</td>
		<td width="'.$width_of_dates_column.'">'.$folders['date'][$key].'</td></tr>';
		$count++;
	}
	$count = 0;
	foreach($files_sorted as $key => $val)
	{
		if($count%2 == 0) $file_class = "file_bg1"; else $file_class = "file_bg2";
			echo '<tr class="'.$file_class.'">
		<td width="'.$width_of_files_column.'">';
		echo '<div style="float:left;width:'.($width_of_files_column-40).'px">';
		if($file_icons == 1)
			echo '<img src="dirLIST_files/icons/'.$files_icons_array[$key].'">';
		
		$file_link = ($limit_download_speed == 1 || $listing_mode == 1) ? 'dirLIST_files/download.php?file='.base64_encode($files['link'][$key]) :$files['link'][$key];
		
		echo ' <a href="'.$file_link.'">'.$files['name'][$key].'</a></div>';

		if($_SESSION['logged_in'])
			echo '<div style="float:right"><img border="0" src="dirLIST_files/edit_files/edit.png" onclick="show_div(1, \''.$count.'\');" style="cursor:pointer"></div>';
			
		echo '</td>';
		
		echo '<td width="'.$width_of_sizes_column.'">'.letter_size($files['size'][$key]).'</td>
		<td width="'.$width_of_dates_column.'">'.$files['date'][$key].'</td></tr>';
		$count++;
		echo '';

	}
	echo '</table>';
	}
//Palce the content into a table -done
}

//Output if the directory is empty
if(empty($folders['name']) && empty($files['name'])) 
echo display_error_message('No files or folders in this directory: <span class="path_font"><b>'.$url_folder.'</b></span>');
//Output if the directory is empty -done

//Display load time
if($load_time == 1)
	echo "<br>".$local_text['this_page_loaded_in']." ".sprintf("%.3f", array_sum(explode(" ",microtime())) - $start_time)." ".$local_text['seconds'];

//File uploading
if($file_uploads == 1 && $listing_mode == 0) { ?>
<br />
<br />
<table width="<?PHP echo $table_width; ?>" border="0" cellpadding="2" cellspacing="2" class="table_border">
    <tr class="top_row">
      <td>
      <form action="dirLIST_files/process_upload.php" method="post" enctype="multipart/form-data" name="upload_form" id="upload_form">
  
        <input name="file" type="file" id="file" size="40" />
        <input name="submit" type="submit" id="submit" value="<?PHP echo $local_text['upload']; ?>" />
        <input name="folder" type="hidden" id="folder" value="<?PHP echo $_GET['folder']; ?>" /><?PHP echo $local_text['filesize_limit']; ?>: <?PHP echo max_upload_size(); ?>B
<?PHP 
if($display_banned_files == 1)
{
	echo '<br /><span class="banned_font">'.$local_text['banned_files'].': ';
	foreach($banned_file_types as $val)
	{
		$string .= substr($val, 1)." | ";	
	}
	echo substr($string, 0, -3);
	echo '</span>';
}
?></form></td></tr>
</table>

<?PHP 
//File uploading -done
} ?>
<br />
<!-- Output basic HTMl code -->
<div style="width:<?PHP echo $table_width; ?>px; text-align:center">
<a href="http://dir-list.sourceforge.net/" target="_blank">dirLIST - PHP Directory Lister v0.3.0</a>
</div>
<?PHP if($_SESSION['logged_in']) { ?>
<div id="file_edit_box" onMouseOut="mouse_out_handler(event);">
  <table width="100%" border="0" class="table_border" height="75">
    <tr>
    <td align="center" class="file_bg2" onClick="ren();" style="cursor:pointer"><img src="dirLIST_files/edit_files/rename.png" alt="rename" name="rename" width="32" height="32" id="rename" style="cursor:pointer" /></td>
    <td align="center" class="file_bg2" onClick="delete_item();" style="cursor:pointer">
      <p><img src="dirLIST_files/edit_files/delete.png" alt="delete" name="detele" width="32" height="32" border="0" id="detele"/></p></td>
    </tr>
  </table>
</div>
<script type="text/javascript">
var js_files_and_folders_base64 = [
	[<?PHP 
	 foreach($folders_sorted as $key => $val)
	 	$folders_string_base64 .= '\''.base64_encode($folders['name'][$key]).'\',';
	echo substr($folders_string_base64, 0, -1);
	 ?>],
	[<?PHP 
	 foreach($files_sorted as $key => $val)
	 	$files_string_base64 .= '\''.base64_encode($files['name'][$key]).'\',';
	echo substr($files_string_base64, 0, -1);
	 ?>]
];

var js_files_and_folders = [
	[<?PHP 
	 foreach($folders_sorted as $key => $val)
	 	$folders_string .= '\''.$folders['name'][$key].'\',';
	echo substr($folders_string, 0, -1);
	 ?>],
	[<?PHP 
	 foreach($files_sorted as $key => $val)
	 	$files_string .= '\''.$files['name'][$key].'\',';
	echo substr($files_string, 0, -1);
	 ?>]
];

function delete_item()
{
	item_name = js_files_and_folders[selected_item_type][selected_item_id];
	item_name_base64 = js_files_and_folders_base64[selected_item_type][selected_item_id];
	if(confirm("********<?PHP echo $local_text['warning']; ?>!********\n\n<?PHP echo $local_text['no_go_back']; ?>\n\n"+'<?PHP echo $local_text['sure_to_del']; ?> ` '+item_name+' ` ?')) window.location = 'dirLIST_files/edit_files/delete.php?folder=<?PHP echo $_GET['folder']; ?>&item_name='+item_name_base64;
}

function ren()
{
	item_name_base64 = js_files_and_folders_base64[selected_item_type][selected_item_id];
	window.open('dirLIST_files/edit_files/rename.php?folder=<?PHP echo $_GET['folder']; ?>&item_name='+item_name_base64, null, 'scrollbars = 0, status = 1, height = 135, width = 470, resizable = 1, location = 0');
}
</script>
<?PHP } ?>
<?php
print CREDITS;
?>
</body>
</center>