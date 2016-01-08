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

	//link registered user with earned|revoked badges before registration
	$link_user_earned_badges = COMMONDB_MODULE::launch_direct_system_query("UPDATE badges_earns SET user_id='".$rows["id_user"]."' WHERE earn_email='".$form["email"]."' AND user_id='0'");
	$link_user_revoked_badges = COMMONDB_MODULE::launch_direct_system_query("UPDATE badges_revocations SET user_id='".$rows["id_user"]."' WHERE earn_email='".$form["email"]."' AND user_id='0'");

    print "1";

} else {

    print "2";

}
?>
~
