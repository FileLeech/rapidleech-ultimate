<?php
 if (!defined('RAPIDLEECH')) { require_once('index.html'); exit; }

if($navi_left["showmtnconfig"]){
$mtn_cs=$_POST['cs'];
$mtn_rs=$_POST['rs'];
$mtn_w=$_POST['w'];
$mtn_h=$_POST['h'];
if($navi_left["showmtntext"]){
$mtn_T=$_POST['T'];
$mtn_o=$_POST['o'];
}
$mtn_k=$_POST['k'];
$mtn_j=$_POST['j'];
$mtn_g=$_POST['g'];
$mtn_I=$_POST['I'];
$mtn_i=$_POST['i'];
$mtn_Ts=$_POST['Ts'];
$mtn_Tc=$_POST['Tc'];
$mtn_f=$_POST['f'];
$mtn_t=$_POST['t'];
$mtn_tc=$_POST['tc'];
$mtn_ts=$_POST['ts'];
$mtn_iL=$_POST['iL'];
$mtn_tL=$_POST['tL'];
}

if ($_POST['video']!="")
{
	if ($_POST['all']=="true")
	{
		$video = vidlist($download_dir);
	}
	else
	{
		$video=array();		
		$video[0] = $_POST['video'];
	}
	if ($mtn_cs>0 && $mtn_cs<6)
	{
		$c=$mtn_cs;
	}
	else
	{
		$c=" 1";
	}
	if ($mtn_rs>0 && $mtn_rs<11)
	{
		$r=$mtn_rs;
	}
	else
	{
		$r=" 1";
	}
foreach ($video as $vdo)
{
	$cmd=getcwd()."/mtn/mtn";
	if ($mtn_i=="")
	{
		$cmd.=" -i";
	}
	if ($mtn_t=="")
	{
		$cmd.=" -t";
	}
	if ($mtn_w!="" && $mtn_w>0 && $mtn_w<2001)
	{
		$cmd.=" -w $mtn_w";
	}
	$cmd.=" -c ".$c." -r ".$r." -h ".$mtn_h." -T '".$mtn_T."' -o '".$mtn_o.".jpg' -k ".$mtn_k." -j ".$mtn_j." -g ".$mtn_g." '".$mtn_I."' -F '".$mtn_Tc.":".$mtn_Ts.":'mtn/font/".$mtn_f."':".$mtn_tc.":".$mtn_ts.":".$mtn_Ts."' -f 'mtn/font/".$mtn_f."' -b 0.60 -B 0.0 -C 6000 -D 8 -L '".$mtn_iL.":".$mtn_tL."' -E 0.0 '".getcwd()."/$download_dir/".$vdo."'";
	shell_exec($cmd);
	$ext=strtolower(strrchr($vdo,'.'));
	$vdofile=str_ireplace($ext,$mtn_o.".jpg",$vdo);
	if (file_exists(getcwd()."/$download_dir/".$vdofile))
	{
         $image = $download_dir."$vdofile";
		echo '<h2>'.$vdo.'</h2>';
	}
	else
	{
		echo '<BR />Error in generating <b><i>'.$vdo.'</i></b> <BR />';
	}

             echo "<a href=".$download_dir.$vdofile."><img src=\"$image\"></a>";
            
}
}
?>
