<?php
include("config.php");

$local_debug_mode = 0;

$form = get_form();

$sdata = array($form["email"],md5($form["password"]));
$stmt = $dbh->prepare("SELECT * FROM users WHERE email = ? and password = ? limit 1");
$stmt->execute($sdata);
$rows = $stmt->fetch();

if($stmt->rowCount()>0){

    $expire=time()+60*60*24*30;
    setcookie("UID", $rows["id_user"], $expire);

    $seddme = md5(time());

    $sdata = array($seddme,$rows["id_user"],md5($form["password"]));
    $stmt = $dbh->prepare("UPDATE users SET seed = ? WHERE id_user = ? AND password = ? LIMIT 1");
    $stmt->execute($sdata);
    
    setcookie("SEED", $seddme, $expire);

    print "1";

} else {

    print "2";

}
?>
~