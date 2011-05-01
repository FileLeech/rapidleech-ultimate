<?PHP
//dirLIST v0.3.0 image scaling file
error_reporting(0);
$image_path = '../'.rawurldecode($_GET['image_path']);

$extension = strtolower(strrchr(basename($image_path), '.'));

switch($extension)
{
	case '.jpg': header('Content-type: image/jpeg'); break;
	case '.jpeg': header('Content-type: image/jpeg'); break;
	case '.png': header('Content-type: image/png'); break;
	case '.gif': header('Content-type: image/gif'); break;
}

$image_attribs = getimagesize($image_path);

$scaled_width = 584;
$scaled_height = 360;

//check if image is smaller than a required dimensions of 584 x 360...in which case just show the image
if($image_attribs[0] <= $scaled_width && $image_attribs[1] <= $scaled_height)
{
	readfile($image_path);
	exit;
}
else //if the image has not been displayed from the previous if statment
{
	$w_ratio = $scaled_width/$image_attribs[0];
	$h_ratio = $scaled_height/$image_attribs[1];
	
	if($w_ratio < $h_ratio)
	{
		$tn_width = $image_attribs[0]*$w_ratio;
		$tn_height = $image_attribs[1]*$w_ratio;
	}
	else
	{
		$tn_width = $image_attribs[0]*$h_ratio;
		$tn_height = $image_attribs[1]*$h_ratio;
	}

	if($extension == '.jpg' || $extension == '.jpeg')
	{
		$image_old = imagecreatefromjpeg($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		imagejpeg($image_new, NULL,100);
	}
	elseif($extension == '.png')
	{
		$image_old = imagecreatefrompng($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imagealphablending($image_new, false);
		imagesavealpha($image_new, true);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		imagepng($image_new, NULL,9);
	}
	elseif($extension == '.gif')
	{
		$image_old = imagecreatefromgif($image_path);
		$image_new = imagecreatetruecolor($tn_width,$tn_height);
		imageantialias($image_new,true);
		imagecopyresampled($image_new,$image_old,0,0,0,0,$tn_width,$tn_height, $image_attribs[0], $image_attribs[1]);
		imagegif($image_new);	
	}
	imagedestroy($image_new);
	imagedestroy($image_old);
	exit;
}
?>