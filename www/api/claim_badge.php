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
	(array) $arr_result = array();
	(string)$new_earn_id= 0;

	//get values from post
	$bgid		  		= ( isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id']>0 ) ? $_POST['id'] : 0;
	$earn_first_name   	= ( isset($_POST['first_name']) && strlen(trim($_POST['first_name']))>0 ) ? $_POST['first_name'] : '';
	$earn_last_name   	= ( isset($_POST['last_name']) && strlen(trim($_POST['last_name']))>0 ) ? $_POST['last_name'] : '';
	$earn_fullname	 	= "$earn_first_name $earn_last_name";
	$earn_email   		= ( isset($_POST['email']) && strlen(trim($_POST['email']))>0 ) ? $_POST['email'] : '';
	
	//check user_id
	$earn_user_id 		= ($earn_email!='') ? get_selected_value("users","id_user","WHERE email='$earn_email'") : 0;
	$earn_user_id 		= ($earn_user_id>0) ? $earn_user_id : 0;
	
	//check email format
	$event_errors = ( preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+[.]+[a-zA-Z0-9-.]+$/", $earn_email) ) ? "" : "Email error";
	
	//process
	if ( $bgid>0 && $earn_first_name!='' && $earn_last_name!='' && $earn_email!='' && $event_errors=='') 
	{
		//1. step : validations
		$check_earn_exists 	 = get_selected_value("badges_earns", "earn_id", "WHERE badge_id='$bgid' AND earn_email='$earn_email' AND deleted='0'");
		$badge_enabled		 = get_selected_value("badges_issuers", "enabled", "WHERE badge_id='$bgid' AND deleted='0'");
		$badge_published	 = get_selected_value("badges_issuers", "published", "WHERE badge_id='$bgid' AND deleted='0'");
		$event_errors 		 = ( $badge_enabled == 1 ) ? $event_errors : "Badge not enabled"; 
		$event_errors 		 = ( $badge_published == 1 ) ? $event_errors : "Badge not published";
		
		if ( $event_errors == '' && $check_earn_exists==0 ) 
		{

			// 2. Create earn_id from badge_id
			$arr_badge = get_arr_data_from_db("badges_issuers","institution,institution_url,institution_image,institution_email,course,course_desc,course_url,badge_img,badge_img_extension,badge_img_type,badge_img_name","WHERE badge_id='$bgid'","");
			
			//control OBI institution required data : institution and institution_url
			$institution 		 = $arr_badge[0]["institution"];
			$institution_url 	 = $arr_badge[0]["institution_url"];
			$institution_img 	 = $arr_badge[0]["institution_image"];
			$institution_email 	 = $arr_badge[0]["institution_email"];
						
			$course 	 		 = $arr_badge[0]["course"];
			$course_desc 		 = $arr_badge[0]["course_desc"];
			$course_url 		 = $arr_badge[0]["course_url"];

			$badge_img 	 		 = $arr_badge[0]["badge_img"];
			$badge_img 			 = ( $badge_img!='' && substr($badge_img,0,1) == "'" ) ? substr($badge_img, 1) : $badge_img;
			$badge_img 			 = ( $badge_img!='' && substr($badge_img, -1) == "'" ) ? substr($badge_img, 0,-1) : $badge_img;
			$badge_img_extension = $arr_badge[0]["badge_img_extension"];
			$badge_img_type 	 = $arr_badge[0]["badge_img_type"];
			$badge_img_name 	 = $arr_badge[0]["badge_img_name"];
			
			$q = "INSERT INTO badges_earns (earn_id,badge_id,user_id,earn_email,earn_fullname,earn_firstname,earn_lastname,institution,institution_url,institution_image,institution_email,course,course_desc,course_url,
			badge_img_extension,badge_img_type,badge_img_name,date_created,created_by,lastupdate_by,lastupdate)
			VALUES ('','$bgid','$earn_user_id','$earn_email','$earn_fullname','$earn_first_name','$earn_last_name','$institution','$institution_url','$institution_img','$institution_email','$course','$course_desc','$course_url',
			'$badge_img_extension','$badge_img_type','$badge_img_name',NOW(),'$earn_user_id','$earn_user_id',NOW())";
			
			//control OBI institution required data
			$new_earn_id = ($institution!='' && $institution_url!='') ? launch_direct_system_query_get_lastId($q) : 0;

			if ( $new_earn_id > 0 ) {
				
				// 3. update img blob
				set_selected_value("badges_earns", "badge_img",$badge_img, "WHERE earn_id=$new_earn_id", $specialhtml='1');
				
				// 4. Check out params to earn
				$arr_params		= get_arr_data_from_db("badges_issuers_params","param_id,label,description,type","WHERE badge_id='$bgid' AND deleted='0' ORDER BY param_id","");
				$count_params	= count($arr_params);
				$params_error	= ""; //control params errors - OBI evidence needs to be url
				$params_error_json  = ""; //control params errors for json creation files
				
				if ( $count_params > 0 )
				{
					foreach ($arr_params AS $param) {
						
						$field_param = "evidence|".$param["param_id"];
						if ( isset($_POST[$field_param]) && strlen( trim($_POST[$field_param])) >0  ) {

							$earn_param_id	= $param['param_id'];
							$param_id		= $param['param_id'];
							$label 			= $param['label'];
							$description	= $param['description'];
							$type 			= $param['type'];

							//obi specs : verify evidence type url
							$content 		= cleanup_string($_POST[$field_param]);
							$content 		= trim($content); //clean url
							$validate_content = ($content!='' && validateURL($content)==1) ? 1 : 0;
							
							if ( $param_id>0 &&  $label!=''  && $validate_content==1 && $content!='' )
							{
								$q = "INSERT INTO badges_earns_params (param_id,earn_id,earn_param_id,label,description,content,type,date_created,created_by,lastupdate_by,lastupdate)
								VALUES('','$new_earn_id','$earn_param_id','$label','$description','$content','$type',NOW(),'$earn_user_id','$earn_user_id',NOW())";
								launch_direct_system_query($q);
							} else {
								if ($content!='' && $validate_content!=1) { $params_error = "1"; }								
							}
						}
					}
				}

				//
				// Create badges files
				//
				if ( $params_error == "" )
				{
					//DON NOT CHANGE ORDER
					// first: badge_class
					$create_badge_class_json = IBL_OPENBADGES::create_badgeclass_json($new_earn_id);
					// second : badge_asser
					$create_badge_asser_json = ( $create_badge_class_json == 1 ) ? IBL_OPENBADGES::create_badgeassertion_json($new_earn_id) : 0;
					$params_error_json 		 = ( $create_badge_class_json == 1 && $create_badge_asser_json ==1 )  ? "" : "error file creation";
				}
				
				//control params errors
				if ( $params_error !="" || $params_error_json !="" ) {
					//remove all - badge_earn and badge_params
					launch_direct_system_query("DELETE FROM badges_earns_params WHERE earn_id='$new_earn_id'");
					launch_direct_system_query("DELETE FROM badges_earns WHERE earn_id='$new_earn_id'");
					$new_earn_id = 0;
					$event_errors  = ( $params_error!='' ) ? "Evidence is not a valid URL" : "Could not create the files";
				}
				
			}
		}
	}
	
	if ( $event_errors == '' && $new_earn_id>0 ) {
		$arr_result = array ( 'success'=>'1', 'earn_id'=>"$new_earn_id" , "badge_url" => PATH_EARN_BADGE.get_crypted_id($new_earn_id) );
	} else {
		$arr_result = ( isset($check_earn_exists) && $check_earn_exists > 0 ) ? array ( 'success'=>'1', 'earn_id'=>"$check_earn_exists" , "badge_url" => PATH_EARN_BADGE.get_crypted_id($check_earn_exists) ) : array ( 'error'=>$event_errors );
	}

	echo json_encode ( $arr_result );
}
?>