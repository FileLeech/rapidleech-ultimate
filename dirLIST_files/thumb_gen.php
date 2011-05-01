<?PHP
//dirLIST v0.3.0 thumbnail generator file
require('config.php');
$image_path = ($listing_mode == 0) ? "../".rawurldecode($_GET['image_path']) : 'ftp://'.$ftp_username.':'.$ftp_password.'@'.$ftp_host.$dir_to_browse.substr(rawurldecode($_GET['image_path']),1);

$extension = strrchr(basename($image_path), '.');

if($listing_mode == 0)
	$tn_path = 'thumbs/'.md5($image_path).'_'.filectime($image_path).$extension;
	
switch($extension)
{
	case ".jpg": header('Content-type: image/jpeg'); break;
	case ".jpeg": header('Content-type: image/jpeg'); break;
	case ".png": header('Content-type: image/png'); break;
	case ".gif": header('Content-type: image/gif'); break;
}

if($listing_mode == 0 && is_file($tn_path))
{
	readfile($tn_path);
	exit;
}
else
{
	$image_attribs = getimagesize($image_path);

	//check if image is smaller than a thumbnail...in which case just show the image
	if($image_attribs[0] <= 125 && $image_attribs[1] <= 125)
	{
		($listing_mode == 0) ? readfile('../'.$_GET['image_path']) : readfile($image_path);
		exit;
	}
	
	if($image_attribs[0] > $image_attribs[1])
	{
		$tn_width = 125;
		$tn_height = 125/$image_attribs[0]*$image_attribs[1];
	}
	else
	{
		$tn_height = 125;
		$tn_width = 125/$image_attribs[1]*$image_attribs[0];
	}
	
	$extension = strtolower($extension);
	
	if($extension == '.jpg' || $extension == '.jpeg')
	{
		$image_old = imagecreatefromjpeg($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		($listing_mode == 0) ? imagejpeg($image_new,$tn_path,100) : imagejpeg($image_new, NULL,100);
	}
	elseif($extension == '.png')
	{
		$image_old = imagecreatefrompng($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imagealphablending($image_new, false);
		imagesavealpha($image_new, true);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		($listing_mode == 0) ? imagepng($image_new,$tn_path,9) : imagepng($image_new, NULL,9);
	}
	elseif($extension == '.gif')
	{
		$image_old = imagecreatefromgif($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		($listing_mode == 0) ? imagegif($image_new,$tn_path) : imagegif($image_new);	
	}
	imagedestroy($image_new);
	imagedestroy($image_old);
	if($listing_mode == 0) readfile($tn_path);
	exit;
}
?>