<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
        $ref='http://ravishare.com/';
        $Url=parse_url($ref);
        $page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
        is_page($page);
        $upfrm = cut_str($page,'multipart/form-data" action="','cgi-bin/upload.cgi?');
        $uid = $i=0; while($i<12){ $i++;}
        $uid += floor(rand() * 10);
        $post['upload_type']= 'file';
        $post['sess_id']= $xfss;
        $post['file_0_descr']=$_REQUEST['descript'];
        $post['file_0_public']='1';
        $post['link_rcpt']='';
        $post['link_pass']='';
        $post['tos']='1';
        $post['submit_btn']=' Upload! ';
        $uurl= $upfrm.'/cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1&utype=anon&upload_type=file';
        $url=parse_url($upfrm.'/cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1&utype=anon&upload_type=file');
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

        $upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
        $locat=cut_str($upfiles,'rea name=\'fn\'>' ,'</textarea>');

        unset($post);
        $gpost['fn'] = "$locat" ;
        $gpost['st'] = "OK" ;
        $gpost['op'] = "upload_result" ;
        $Url=parse_url($ref);
        $page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $uurl, $cookies, $gpost, 0, $_GET["proxy"],$pauth);

        $ddl=cut_str($page,'<td><a href="','"');
        $del=cut_str($page,'killcode=','"');

        $download_link=$ddl;
        $delete_link= $ddl.'?killcode='.$del;

// Made By CHRISTIAN 20/07/2010 11:35
?>


