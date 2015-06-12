<?php 
//auth pages
if ( (!isset($logged_user) || $logged_user< 1)  )  {
	print '<div id="main">';
	print '<div class="alert alert-danger">'.__("You have not privileges to access this page").'</div>';
	print '</div>';
	exit;
}

//auth pages - perms
if ( (isset($logged_user) || $logged_user> 1)  )  {
	
	//allow
	$disallow_auth = 0 ; //admin grants all privileges
	
	//perms group users
	if ( $logged_profile !='admin' ) {
		
		switch (CURRENT_PAGE) {
			case "users.php" : $disallow_auth = 1 ; break; 
			case "issuer.php" :
			case "badge_edit.php" :
			case "list_earn.php" :
			case "badge_earn.php" :
			case "badge_revoked.php" :
					$disallow_auth = ($logged_profile=='issuer') ? 0 : 1 ; break;
			
			default: $disallow_auth = 0; break;
		}
	}
	
	if ( $disallow_auth == 1 ) {
		print '<div id="main">';
		print '<div class="alert alert-danger">'.__("You have not privileges to access this page").'</div>';
		print '</div>';
		exit;		
	}
} 
?>