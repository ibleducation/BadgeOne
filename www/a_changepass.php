<?php
include("config.php");



$form = get_form();

$sdata = array($form["etoken"],$form["key"]);
$stmt = $dbh->prepare("select * from users where md5(email) = ? and reset_seed = ?  and reset_seed_date > NOW() - interval 1 day limit 1");
$stmt->execute($sdata);
$rows = $stmt->fetch();


if($stmt->rowCount()>0){

    $sdata = array(md5($form["password"]), $form["etoken"],$form["key"]);
    $stmt = $dbh->prepare("update users set password = ? , reset_seed = '' where  md5(email) = ? and reset_seed = ? ");
    $stmt->execute($sdata);


    print 1;


}else{


    print 2;


}

?>