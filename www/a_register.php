<?php
include("config.php");

$form = get_form();

//required data
(int) 		$allow_noconfirm_registration = ( defined('APP_ALLOW_NOCONFIRM_REGISTRATION') && APP_ALLOW_NOCONFIRM_REGISTRATION==1 ) ? 1  : 0;
(string) 	$email		 =	strtolower( $form["email"] ) ; //to lower
(int) 		$valid_email = ( strlen(trim($email))>0 && preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+[.]+[a-zA-Z0-9-.]+$/", $email) ) ?  1 : 0;
(string) 	$name		 = $form["name"];
(string) 	$upwd		 = $form["password"];


//reckeck data to prevent direct posts without js validation
if ( $valid_email==1 && strlen(trim($name))>0 && strlen(trim($upwd))>0 ) {

	$sdata = array($email);
	$stmt = $dbh->prepare("SELECT * FROM users WHERE email = ?");
	$stmt->execute($sdata);
	$rows = $stmt->fetchAll();

	if($stmt->rowCount()>0){

	    // error
	    print "2";

	} else {
			//params
			$date_now			= date("Y-m-d H:i:s");
			$seddme				= md5(time());
			$institution		= BADGES_ISSUER_INSTITUTION_NAME;
			$institution_url 	= BADGES_ISSUER_INSTITUTION_URL;
			$institution_image	= BADGES_ISSUER_INSTITUTION_IMAGE;
			$institution_email	= BADGES_ISSUER_INSTITUTION_EMAIL;
			$activated 			= ( $allow_noconfirm_registration == 1 ) ? 1 : 0;
			$activate_seed 		= ( $allow_noconfirm_registration == 1 ) ? '' : "$seddme";
			$date_activated		= ( $allow_noconfirm_registration == 1 ) ? $date_now : NULL;
			$date_created		= $date_now;

	        //add new user and seed data
	        $sdata = array($email,$name,$institution,$institution_url,$institution_image,$institution_email,md5($upwd),$seddme,$activated,$activate_seed,$date_created,$date_activated);
	        $stmt = $dbh->prepare("INSERT INTO users (id_user,email,name,institution,institution_url,institution_image,institution_email,password,seed,activated,activate_seed,date_created,date_activated) VALUES ('',?,?,?,?,?,?,?,?,?,?,?,?)");
	        $stmt->execute($sdata);

	        //get user_id
	        $user_id = $dbh->lastInsertId();

	        //setup oauth2 client
	        $client_id 		= $email."-".rand_chars();
	        $client_secret 	= rand_chars(8) ."-". rand_chars(4) ."-". rand_chars(4) ."-". rand_chars(4) ."-". rand_chars(12);
	        $sdata = array($client_id,$client_secret,$user_id);
	        $stmt = $dbh->prepare("INSERT INTO oauth_clients (client_id,client_secret,user_id) VALUES(?,?,?) ");
	        $stmt->execute($sdata);

	        //sendemail
	        if ( $allow_noconfirm_registration !=1 )
	        {
	        	$to = "$email";
	        	$subject = APP_PREFIX." ".__("Confirm your account");
	        	$url_activation = SERVER_HTTP_HOST."/account_activation.php?s=$seddme&e=$email";
	        	$message = __("To activate your account, click this link :")."<br><a href='".$url_activation."'>".$url_activation."</a>";
						$header = "MIME-Version: 1.0" . "\r\n";
						$header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	        	$header .= "From: ".APP_EMAIL."\n";
	        	$params = "-f".APP_EMAIL."";
	        	mail($to, $subject, $message, $header,$params);
	        }

	        //autologin
	        if ( $allow_noconfirm_registration ==1 )
	        {
		        //setup cookie session
		        $expire=time()+60*60*24*30;
	    	    setcookie("UID", $user_id, $expire);
	        	setcookie("SEED", $seddme, $expire);

	        	print "11";

	        } else {
	        	//pending activation
	        	print "1";
	        }
	}

} else {
	//error
	print "2";
}
?>
