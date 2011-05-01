    <?php

    /*You need php but no database for this as it uses a text file.
    You can add a title, numbers of extra visitors, the refresh time and
    the text file.
    Put this in your root dir. The text file is automatically created
    for you so you need to load 1 file.
    Make sure the text file does not already exist.
    You can include this in a page by using <?php
    include("what_you_named_it .php");?> for html or
    include("what_you_named_it.php"); for php
    */

    /*This is the text you want to explain the number.
    Something like;- Visitors on this page or leave blank if the text is
    going into your document
    you need quotes " " and the semicolon ; even if this is empty */
    $explain = "User Online: ";
    $explain_ip = "<br />Your IP: ";

    /* Add online numbers. You can set this to 0 for actual numbers
    but rather than visitors feeling lonely you can put a number in to
    show that nember plus the actuals
    so if you put in 4 and have 2 actually on line the counter will show
    6 */
    $additions = 0;

    /*This is the refresh time in minutes. For example if you use 5 your
    numbers
    refresh every 5 minutes. The lower the number, the more accurate
    it is not advisable to make this lower than 1 */
    $timer = 10;

    /* Name of the file where all the data will be saved.
    Name this something creative but it must be a text file so xxx.txt.
    make sure this name is not already in the directory. The script
    creates this file */
    $filename = "configs/online.lst";

    //Do not edit under this line

    if (!$datei) $datei = dirname(__FILE__)."/$filename";
    $time = @time();
    $space = " ";
    $ip = getenv('REMOTE_ADDR');
    $string = "$ip|$time\n";
    $a = fopen("$filename", "a+");
    fputs($a, $string);
    fclose($a);

    $timeout = time()-(60*$timer);

    $all = "";
    $i = 0;
    $datei = file($filename);
    for ($num = 0; $num < count($datei); $num++) {
    $pieces = explode("|",$datei[$num]);

    if ($pieces[1] > $timeout) {
    $all .= $pieces[0];
    $all .= ",";
    }
    $i++;
    }

    $all = substr($all,0,strlen($all)-1);
    $arraypieces = explode(",",$all);
    $useronline = count(array_flip(array_flip($arraypieces)));

    // display how many people where activ within $timeout
    echo $explain;
    echo $useronline+$additions;
    echo $explain_ip;
    echo $ip;


    // Delete
    $dell = "";
    for ($numm = 0; $numm < count($datei); $numm++) {
    $tiles = explode("|",$datei[$numm]);
    if ($tiles[1] > $timeout) {
    $dell .= "$tiles[0]|$tiles[1]";
    }
    }

    if (!$datei) $datei = dirname(__FILE__)."/$filename";
    $time = @time();
    $ip = getenv('REMOTE_ADDR');
    $string = "$dell";
    $a = fopen("$filename", "w+");
    fputs($a, $string);
    fclose($a);
    ?>