<?php

include("config.php");

$form = get_form();


$seddme = md5(time()+$form["email"]+md5($form["email"]));

$sdata = array($seddme,$form["email"]);
$stmt = $dbh->prepare("UPDATE users SET reset_seed = ? , reset_seed_date = now() WHERE email = ? limit 1");
$stmt->execute($sdata);


$subject = APP_PREFIX." Password Reset";

$body = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"" />
        <title>'.APP_NAME.' reset password request</title>
    </head>
    <body>

        <a href="http://'.DOCUMENT_HTTP.'/changepass?key='.$seddme.'&etoken='.md5($form["email"]).'">Reset Your Password Link</a>

    </body>

</html>

';

$to = $form["email"];
$subject = APP_PREFIX." ".__("Change your password");
$url_activation = SERVER_HTTP_HOST."/account_activation.php?s=$seddme&e=$email";

$header = "MIME-Version: 1.0" . "\r\n";
$header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$header .= "From: ".APP_EMAIL."\n";
$params = "-f".APP_EMAIL."";


if (mail($to, $subject, $body, $header,$params)) {

    print 1;

} else {

    print 2;

}




?>
