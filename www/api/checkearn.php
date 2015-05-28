<?php
// include our OAuth2 Server object
require_once __DIR__.'/server.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
	$arr_res = array ( 'error'=>'auth');
	echo json_encode($arr_res);
} else {

	//include app config if not included before
	if ( !defined('DB_NAME') ) { require_once __DIR__.'/config.php'; }

	//init data
	(array) $arr_result = array ( 'error'=>'None' );
	(string)$earn_id = '0';
	
	//get values from post
	$bgid		  		= ( isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id']>0 ) ? $_POST['id'] : 0;
	$earn_email   		= ( isset($_POST['email']) && strlen(trim($_POST['email']))>0 ) ? $_POST['email'] : '';
	
	if ( $earn_email!='' && $bgid>0 ) 
	{
		// 1. check earn_id exists
		$check_earn_exists 	 = get_selected_value("badges_earns", "earn_id", "WHERE badge_id='$bgid' AND earn_email='$earn_email' AND deleted='0'");
		$earn_id			 = ( $check_earn_exists>0 ) ?  $check_earn_exists : $earn_id;
		$arr_result 		 = ( $earn_id>0 ) ? array ( 'success'=>'1', 'earn_id'=>"$earn_id" , "badge_url" => PATH_EARN_BADGE.get_crypted_id($earn_id) ) : $arr_result;
	}
	echo json_encode ( $arr_result );
}
?>