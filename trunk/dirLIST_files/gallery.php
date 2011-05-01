<?PHP
//dirLIST v0.3.0 gallery file
define('RAPIDLEECH', 'yes');
error_reporting(0);
set_time_limit(0);
session_start();
define('CONFIG_DIR', '../configs/');
require_once(CONFIG_DIR.'setup.php');
require_once('../classes/other.php');

login_check();


require('functions.php');
require('config.php');


$folder = '../'.$dir_to_browse.base64_decode($_GET['folder']);
if(!is_dir($folder)) die("<b>Error:</b> Folder specified does not exist. This could be because you manually entered the folder name in the URL or you don't have permission to access this folder");

$content = get_dir_content($folder);

foreach($content['files']['name'] as $key => $val)
{
	if(in_array(strtolower(strrchr($val, '.')), array('.jpg', '.jpeg', '.png', '.gif')))
	{
		$path = $folder.'/'.$val;
		$images_paths[] = $path;
		
		@$js_images_names .= '\''.$val.'\', ';
		@$js_images_file_sizes .= '\''.letter_size(filesize($path)).'\', ';
		
		$dimensions = getimagesize($path);
		$images_widths[] = $dimensions[0];
		$images_heights[] = $dimensions[1];
		
		@$js_images_heights .= '\''.$dimensions[1].'\', ';
		@$js_images_widths .= '\''.$dimensions[0].'\', ';
		@$js_images_download_link .= '\''.base64_encode($dir_to_browse.base64_decode($_GET['folder']).'/'.$val).'\', ';
	}
}

$js_images_names = substr($js_images_names, 0, -2);
$js_images_file_sizes = substr($js_images_file_sizes, 0, -2);
$js_images_heights = substr($js_images_heights, 0, -2);
$js_images_widths = substr($js_images_widths, 0, -2);
$js_images_download_link = substr($js_images_download_link, 0, -2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pictures Gallery</title>
<style type="text/css">
<!--
.body {
	background-color:#010e17;
} 
.header_text {
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 24px;
	font-weight: lighter;
	color: #CECECE;
	line-height: 40px;
}
.help_text {
	font-family: Verdana;
	font-size: 11px;
	font-weight: bold;
	text-transform: capitalize;
	color: #E5E5E5;
}
.nav_text {
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 9px;
	font-weight: bold;
	text-transform: uppercase;
	color: #CECECE;
	padding-right: 5px;
	padding-left: 5px;
	line-height: 20px;
}
.details {	color: #FFF;
	font-family: Verdana;
	font-size: 10px;
	font-weight: bold;
	line-height: 20px;
}
.scroll_arrow {
	padding-right: 5px;
	padding-left: 5px;
}
.thumbnails_box {
	position: relative;
	width: 600px;
	height: 120px;
	overflow: hidden;
}
.nav_button {
	margin-top: 6px;
}
.selected_image_style {
	border: 1px solid #484848;
	padding: 2px;
}
.thumbnail_style {
	border: 1px solid #484848;
	margin-top: 6px;
	margin-bottom: 3px;
	margin-right: 3px;
	margin-left: 3px;
}
.thumbnail_style_over {
	border: 2px solid #FFFFFF;
	margin-top: 6px;
	margin-bottom: 3px;
	margin-right: 3px;
	margin-left: 3px;
}
-->
</style>
<script type="text/javascript">
var images_names = [<?PHP echo $js_images_names; ?>];
var images_file_sizes = [<?PHP echo $js_images_file_sizes; ?>];
var images_heights = [<?PHP echo $js_images_heights; ?>];
var images_widths = [<?PHP echo $js_images_widths; ?>];
var images_download_links = [<?PHP echo $js_images_download_link; ?>];
var selected_image = 0;
var total_images = <?PHP echo count($images_paths); ?>;

function rollover(name, state)
{
		document.getElementById(name).setAttribute('src', 'gallery_files/'+name+state+'.png');
}

function change_thumbnail_border(name, state)
{
	if(state == '_over')
		document.getElementById(name).setAttribute('height', document.images[name].height+4);
	else
		document.getElementById(name).setAttribute('height', document.images[name].height-4);
	
	document.getElementById(name).setAttribute('class', 'thumbnail_style'+state);
}

function swap_image(id, button)
{
	if(button == 'next')
	{
		if(selected_image == total_images-1)
			selected_image = -1;
		selected_image++;
	}
	else if(button == 'prev')
	{
		if(selected_image == 0)
			selected_image = total_images;
		selected_image--;
	}
	else
		selected_image = id;
	
	document.getElementById('selected_image').setAttribute('src', 'gallery_files/show_scaled_image.php?image_path=<?PHP echo $folder."/"; ?>'+escape(images_names[selected_image]));
	update_image_details();
}
function update_image_details()
{
	document.getElementById('image_name').innerHTML = images_names[selected_image];
	document.getElementById('image_file_size').innerHTML = images_file_sizes[selected_image];
	document.getElementById('image_height').innerHTML = images_heights[selected_image];
	document.getElementById('image_width').innerHTML = images_widths[selected_image];
}
function download_image()
{
	window.location = 'download.php?file='+images_download_links[selected_image];
}

//The following code is for the scrolling thumbnails
scrollStep=4 // Scrolling speed

var timerLeft;
var timerRight;

function toLeft(id){
  document.getElementById(id).scrollLeft=0
}

function scrollContentLeft(id){
  clearTimeout(timerRight) 
  document.getElementById(id).scrollLeft+=scrollStep
  timerRight=setTimeout("scrollContentLeft('"+id+"')",10)
}

function scrollContentRight(id){
  clearTimeout(timerLeft)
  document.getElementById(id).scrollLeft-=scrollStep
  timerLeft=setTimeout("scrollContentRight('"+id+"')",10)
}

function toRight(id){
  document.getElementById(id).scrollLeft=document.getElementById(id).scrollWidth
}

function stopScroll(){
  clearTimeout(timerRight) 
  clearTimeout(timerLeft)
}
</script>
</head>

<body class="body" onload="swap_image(0);">
<table width="695" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="47" rowspan="2">&nbsp;</td>
    <td width="543"><span class="header_text">Pictures Gallery</span></td>
    <td width="60" align="center"><span class="nav_text" onmouseover="document.getElementById('help_text').style.display='block'" onmouseout="document.getElementById('help_text').style.display='none'" style="cursor:help">[?]</span><span class="nav_text" onclick="window.close()" style="cursor: pointer">[X]</span></td>
    <td width="48" rowspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="details"><span id="image_name"></span> | <span id="image_file_size"></span> | <span id="image_width"></span> x <span id="image_height"></span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="390" colspan="2" align="center" valign="middle" class="selected_image_style"><img onclick="download_image();" style="cursor:pointer" src="" alt="image" name="selected_image" id="selected_image" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  onmouseover="rollover('left_scroll', '_over');scrollContentRight('thumbnails');" onmouseout="rollover('left_scroll','');stopScroll();"><img src="gallery_files/left_scroll.png" alt="scroll left" name="left_scroll" width="34" height="34" class="scroll_arrow" id="left_scroll" /></td>
    <td colspan="2"><div class="thumbnails_box" id="thumbnails">
      <table height="100%" border="0" cellpadding="0" cellspacing="0">
        <tr align="center" valign="middle" width="100%">
        <?PHP
		foreach($images_paths as $key => $val)
		{
			echo '<td onmouseover="change_thumbnail_border(\'thumb_'.$key.'\',\'_over\')" onmouseout="change_thumbnail_border(\'thumb_'.$key.'\',\'\')" onclick="swap_image('.$key.')"><img id="thumb_'.$key.'" src="thumb_gen.php?image_path='.urlencode(substr($val, 3)).'" class="thumbnail_style" ';
			
			$w_ratio = 125/$images_widths[$key];
			$h_ratio = 125/$images_heights[$key];
			
			if($w_ratio < $h_ratio)
			{
				$tn_width = $images_widths[$key]*$w_ratio;
				$tn_height = $images_heights[$key]*$w_ratio;
			}
			else
			{
				$tn_width = $images_widths[$key]*$h_ratio;
				$tn_height = $images_heights[$key]*$h_ratio;
			}
			
			if($tn_height > 80 && $h_ratio < 1) echo 'height="80" ';
			
			echo '/></td>'."\n";
		}
		?>
        </tr>
      </table>
    </div></td>
    <td align="left" onmouseover="rollover('right_scroll', '_over');scrollContentLeft('thumbnails');" onmouseout="rollover('right_scroll','');stopScroll();"><img src="gallery_files/right_scroll.png" alt="scroll right" name="right_scroll" width="34" height="34" class="scroll_arrow" id="right_scroll" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="253" height="55">&nbsp;</td>
        <td width="37" align="center" onmouseover="rollover('prev_arrow', '_over');" onmouseout="rollover('prev_arrow','');" onclick="swap_image('', 'prev');"><img src="gallery_files/prev_arrow.png" alt="previous image" name="prev_arrow" width="34" height="34" class="nav_button" id="prev_arrow" /><br />
          <span class="nav_text">prev</span></td>
        <td width="20">&nbsp;</td>
        <td width="37" align="center" onmouseover="rollover('next_arrow', '_over');" onmouseout="rollover('next_arrow','');" onclick="swap_image('', 'next');"><img src="gallery_files/next_arrow.png" alt="next image" name="next_arrow" width="34" height="34" class="nav_button" id="next_arrow" /><br />
          <span class="nav_text">next</span></td>
        <td align="right" class="help_text"><span id="help_text" style="display:none">CLICK IMAGE TO DOWNLOAD</span></td>
      </tr>
    </table></td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>