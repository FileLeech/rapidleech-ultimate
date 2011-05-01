<?PHP
//dirLIST v0.3.0 thumbnail chaching file. Run this file to cache all the thumbnails under the parent listing directory
require("config.php");
set_time_limit(90);

if($listing_mode == 1)
	die('Image thumbnail chaching only works for HTTP listing');

echo 'This may take a while. If at any point the script stops, just do a refresh and it will continue from where it stopped';
ob_flush();

$image_files = array();

file_paths("../".$dir_to_browse);

foreach($image_files as $key => $val)
{
	$tn_path = 'thumbs/'.md5($val).'_'.filectime($val).strrchr(basename($val),'.');
	if(is_file($tn_path))
		continue;
	$image_path = $val;
	$extension = strrchr(basename($image_path), '.');
	$image_attribs = getimagesize($image_path);

	//check if image is smaller than a thumbnail...in which case just show the image
	if($image_attribs[0] <= 125 && $image_attribs[1] <= 125)
		continue;

	
	if($image_attribs[0] > $image_attribs[1])
		$ratio = 125/$image_attribs[0];
	else
		$ratio = 125/$image_attribs[1];
		
	$tn_width = $image_attribs[0]*$ratio;
	$tn_height = $image_attribs[1]*$ratio;
	
	$extension = strtolower($extension);
	
	if($extension == '.jpg' || $extension == '.jpeg')
	{
		$image_old = imagecreatefromjpeg($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		imagejpeg($image_new,$tn_path,100);
		imagedestroy($image_new);
	}
	elseif($extension == '.png')
	{
		$image_old = imagecreatefrompng($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imagealphablending($image_new, false);
		imagesavealpha($image_new, true);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		imagepng($image_new,$tn_path,9);
		imagedestroy($image_new);
	}
	elseif($extension == '.gif')
	{
		$image_old = imagecreatefromgif($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		imagegif($image_new,$tn_path);
		imagedestroy($image_new);
	}
	
}

function get_dir_content($path)
{
	$content = array();
	$dh  = opendir($path);
	while (false !== ($item = readdir($dh)))
	{
	 	$content[] = $item;
	}
	return $content;
}

function file_paths($path)
{
	global $image_files;
	global $thumb_types;
	$content = get_dir_content($path);

	//remove . and ..
	array_shift($content);
	array_shift($content);
	
	foreach($content as $key => $val)
	{
		$content_path = $path.$val;
		if(is_dir($content_path))
		{
			file_paths($content_path."/");
		}
		else
		{
			if(in_array(strrchr(basename($content_path),'.'), $thumb_types))
				$image_files[] = $content_path;
		}
	}
}
?>