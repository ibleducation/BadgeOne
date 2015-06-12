<?php
$form = get_form();

//required data
$seed		 =	$form["s"] ;
$email		 =	$form["e"] ;
$valid_email = ( strlen(trim($email))>0 && preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+[.]+[a-zA-Z0-9-.]+$/", $email) ) ?  1 : 0;

//reckeck data to prevent direct posts without js validation
if ( $valid_email==1 && strlen(trim($seed))>0 ) {

	$sdata = array($email,$seed);
	$stmt = $dbh->prepare("SELECT id_user FROM users WHERE email=? AND activated=0 AND activate_seed=? LIMIT 1");
	$stmt->execute($sdata);
	
	if($stmt->rowCount()>0) {

		//update user
		$rs = $stmt->fetch(PDO::FETCH_OBJ);
		$id_user = $rs->id_user;
		$date_now = date("Y-m-d H:i:s");
		
		//
		//update user
		//
		$seddme = md5(time());
		$sdata = array($seddme, $date_now, $id_user);
		$stmt = $dbh->prepare("UPDATE users SET seed=? , activated=1 , date_activated=? WHERE id_user = ? LIMIT 1");
		$stmt->execute($sdata);
		
		//setup cookie session
		$expire=time()+60*60*24*30;
		setcookie("UID", $id_user, $expire);
		setcookie("SEED", $seddme, $expire);
		
		print "<p class='alert alert-success'> <i class='fa fa-hand-o-up'></i> ".__("Activation Complete. You could Sign-In now")."</p>";
		
	} else {
		
		// error
		print "<p class='alert alert-warning'> <i class='fa fa-exclamation-triangle'></i> ".__("The activation is not longer available.")."</p>";
	}

} else { 
	//error
	print "<p class='alert alert-danger'> <i class='fa fa-exclamation-triangle'></i> ".__("Error. Incorrect credentials")."</p>";
}
?>