<?php
 if (!defined('RAPIDLEECH')) { require_once('index.html'); exit; }

###-FILE CONFIG
$download_dir = $options['download_dir']; 

###-VIEW-CONFIG
$navi_left = array(
 'showmtnconfig' => 'true', //Show Configuration at Movie Thumbnailer('true'=On, ''=Off);
 'showmtntext' => 'true',   //Show Text & Output Suffix at Movie Thumbnailer(true=On, ''=Off);
);

###-MTN-CONFIG
$mtn_cs = '3';            //Columns(1~5);
$mtn_rs = '3';            //Rows(1~10;
$mtn_w = '';              //Width(0~2000);
$mtn_h = '100';           //Minimum Height;
$mtn_T = '';              //Text;
$mtn_o = '_s';            //Output Suffix;
$mtn_k = '000000';        //Background Color('000000'=Black, '000099'=Blue, '006600'=Green, 'CC0000'=Red, 'FFFF00'=Yellow, 'FFFFFF'=White);
$mtn_j = '90';            //Jpeg Quality('80'=Low, '90'=Normal, '100'=Right);
$mtn_g = '1';             //Edge(0~5);
$mtn_I = '';              //Individual Shots('true'=On, ''=Off);
$mtn_i = 'true';          //Video Info('true'=On, ''=Off);
$mtn_Ts = '10';           //Video Info Size;
$mtn_Tc = 'FFFF00';       //Video Info Color('000000'=Black, '000099'=Blue, '006600'=Green, 'CC0000'=Red, 'FFFF00'=Yellow, 'FFFFFF'=White);
$mtn_f = 'palab.ttf';     //Video Info Font('blue.ttf', 'georgia.ttf', 'lsansuni.ttf', 'pala.ttf', 'palab.ttf', 'palabi.ttf', 'palai.ttf', 'tahomabd.ttf', 'xsuni.ttf');
$mtn_t = 'true';          //Time('true'=On, ''=Off);
$mtn_tc = 'FFFF00';       //Time Color('000000'=Black, '000099'=Blue, '006600'=Green, 'CC0000'=Red, 'FFFF00'=Yellow, 'FFFFFF'=White);
$mtn_ts = '000000';       //Time Shadow Color('000000'=Black, '000099'=Blue, '006600'=Green, 'CC0000'=Red, 'FFFF00'=Yellow, 'FFFFFF'=White);
$mtn_iL = '4';            //Location Info('1'=Lower Left, '4'=Upper Left);
$mtn_tL = '2';            //Location Time('1'=Lower Left, '1'=Lower Left, '2'=Lower Right, '3'=Upper Right, '4'=Upper Left);

?>