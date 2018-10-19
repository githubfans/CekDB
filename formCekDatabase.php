<?
error_reporting(1);
$token = md5(md5(date('YmdHis')));
// echo $token;
$whiteIP = file_get_contents('whiteIP.nug', FILE_USE_INCLUDE_PATH);
$ips = explode(PHP_EOL, $whiteIP);
// print_r($ips);
// print_r($_SERVER['REMOTE_ADDR']);

if (in_array($_SERVER['REMOTE_ADDR'], $ips)) 
{
    $_BOLEH = true;
}
else
{
    $_BOLEH = false;
}

// require_once('recaptchalib.php');
// Get a key from https://www.google.com/recaptcha/admin/create
$publickey = "6LcsyXUUAAAAABaCfdtqZmL4wZgt-RqGEGPH942t";
$privatekey = "6LcsyXUUAAAAACdaj3rkl4FFSJy0sTcuJYlJyfEU";
?>
<!DOCTYPE html>
<html>
<head>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <title>Cek koneksi Database dari <?=$_SERVER['SERVER_NAME']?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style type="text/css">
        body {padding: 20px;}
        fieldset, legend {border: 1px solid #ccc; font-size: 14px;}
        fieldset { padding: 10px; margin: 10px; text-align: right; background-color: #eed; display: inline-block; }
        legend {text-align: left; background-color: #ccc; font-weight: bold; overflow: hidden; padding: 2px 5px; width: 50%; }
        fieldset input {width: 100%; padding: 2px 4px;}
        form fieldset { width: 45%; }
    </style>
</head>
<body>


<fieldset class="col-md-4" style="text-align: left;">

    <h3>Cek koneksi Database</h3>
    <?
    if($_BOLEH == true)
    {
        echo '<br>lokasi script tester : '.$_SERVER['SERVER_NAME'];
        echo '<br>remote tester : '.$_SERVER['REMOTE_ADDR'];
        ?>
        <hr>note :
        <ul>
            <li>script dipasang di server yang ada <strong>mysqli</strong></li>
            <li>untuk cek host to host, hrs add ip host-tester di host-target</li>
        </ul>
        <?
    }
    else
    {
        ?>
        <h4>IP belum ditulis.</h4>
        <?
    }
    ?>
</fieldset>
<? if($_BOLEH == true) { ?>
<fieldset class="col-md-6" style="text-align: left;">
    <legend>RESULTS</legend>
    <?

    if(
            isset($_POST['host']) 
        and isset($_POST['username']) 
        and isset($_POST['password']) 
        and isset($_POST['database']) 
        and $_POST['g-recaptcha-response']!=''
        //and $token == $_GET['tokeen']
        )
    {
        echo '<pre>';
        //print_r($_POST);
        // print_r(__LINE__);
        foreach ($_POST as $key => $value) 
        {
            if($key!='g-recaptcha-response' and $key!='cek')
            {
                echo '<b>'.$key.'</b> = '.$value.'<br>';
            }
        }
        echo '</pre>';
     //    $link = mysqli_connect($_POST['host'], $_POST['username'], $_POST['password']);
     //    if (!$link) {
     //        echo '<hr>Could not connect: ' . mysqli_error();
     //    }
     //    else
     //    {
     //        echo '<hr>Connected successfully.';
     //    }

     //    $db_selected = mysqli_select_db($_POST['database'], $link);
     //    if (!$db_selected) {
     //            echo '<hr>Can\'t use <b>'.$_POST['database'].'</b> : ' . mysqli_error();
     //    }
     //    else 
     //    {
     //        echo '<hr><b>'.$_POST['database'].'</b> selected.';
     //    }
        
     //    mysqli_close($link);    
     //    // unset($_POST);
     //    // unset($_GET);
    	// echo '<hr>';

        $link = mysqli_connect($_POST['host'], $_POST['username'], $_POST['password'], $_POST['database'], '3306');

        if (!$link) {
            echo "<hr>Error: Unable to connect to MySQL." . PHP_EOL;
            echo "<hr>Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "<hr>Debugging error: " . mysqli_connect_error() . PHP_EOL;
            // exit;
        }
        else
        {

            echo "<hr>Success: A proper connection to MySQL was made! The <b>".$_POST['database']."</b> database is great." . PHP_EOL;
            echo "<hr>Host information: " . mysqli_get_host_info($link) . PHP_EOL;
            // $selDB = mysqli_select_db($link,$_POST['database']);

        }
        /* change db to world db */
        mysqli_select_db($link, "world");

        /* return name of current default database */
        if ($result = mysqli_query($link, "SELECT DATABASE()")) {
            $row = mysqli_fetch_row($result);
            printf("Default database is <b>%s</b>.\n", $row[0]);
            mysqli_free_result($result);
        }

        $test_query = "SHOW TABLES FROM ".$_POST['database'];
        $result = mysqli_query($link, $test_query);
        // print_r($test_query);
        // print_r($result);
        $tblCnt = 0; 
        while($tbl = mysqli_fetch_array($result)) {
          $tblCnt++;
          // echo $tbl[0]."<br />\n";
        }
        if (!$tblCnt) {
          echo "<hr>There are no tables<br />\n";
        } else {
          echo "<hr>There are <b>".$tblCnt." tables</b><br />\n";
        }

        // if (mysqli_connect_errno()) {
        //     echo mysqli_connect_error();
        //     // exit();
        // }else{
        //    //successful connection
        //     echo "Yes, successful";
        // }

        $_POST['g-recaptcha-response']=='';
        unset($_POST['g-recaptcha-response']);

    }
    else
    {
        ?>
        <h4>Lengkapi semua inputan.</h4>
        <?
        unset($_POST);
        unset($_GET);
    }

    ?>
</fieldset>
<form action="?tokeen=<?=$token?>" method="POST">
    <fieldset class="col-md-4">
        <legend>HOST</legend> <input type="text" name="host" value="<?=$_POST['host']?>">
    </fieldset>
    <fieldset class="col-md-4">
        <legend>USERNAME</legend> <input type="text" name="username" value="<?=$_POST['username']?>">
    </fieldset>
    <fieldset class="col-md-4">
        <legend>PASSWORD</legend> <input type="text" name="password" value="<?=$_POST['password']?>">
    </fieldset>
    <fieldset class="col-md-4">
        <legend>DATABASE</legend> <input type="text" name="database" value="<?=$_POST['database']?>">
    </fieldset>
    <fieldset class="col-md-4">
        <div class="g-recaptcha" data-sitekey="<?=$publickey?>"></div>
        <input type="submit" name="cek" value="CEK" class="btn-submit">
    </fieldset>
</form>
<? } ?>
</body>
</html>