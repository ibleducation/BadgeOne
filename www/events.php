<?php
/*****
 * EVENTS CONTROL
 */
//function to validate auth
$validate_auth = 1;

//function to validate auth
$event	= ( isset($_POST['event']) && $_POST['event']!='') ? $_POST['event'] : "";
$event_errors = "";
$event_success= "";
$update_additional_user_info = "";
$error_additional_user_info = "";

switch ($event) {

	//
	// ------------------ Badges ------------------- //
	//
	case "delete_earn":
		//0. step : check badge
		$earn_id 		= ( isset($_POST["earn_id"]) && $_POST["earn_id"]!="") ? COMMONDB_MODULE::decrypt_id("badges_earns", $_POST["earn_id"]) : '';
		$badge_id 		= ( $earn_id>0 ) ? COMMONDB_MODULE::get_value("badges_earns", "badge_id", $earn_id) : 0;

		//1 step : checks validations
		$user_id			= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : 0;
		$user_profile		= ( isset($logged_profile) && $logged_profile!='') ? $logged_profile : '';
		//check own badge
		$allow_deletion 	= ($earn_id>0 && $user_id>0) ? COMMONDB_MODULE::count_values("badges_earns","earn_id","WHERE earn_id='$earn_id' AND user_id='$user_id'") : 0;
		//check issuer badge control
		$allow_deletion 	= ($earn_id>0 && $user_id>0 && $badge_id>0 && $allow_deletion==0) ? COMMONDB_MODULE::count_values("badges_issuers","badge_id","WHERE badge_id='$badge_id' AND user_id='$user_id'") : $allow_deletion;
		//check admin control
		$allow_deletion		= ($user_profile=='admin') ? 1 : $allow_deletion;

		//revoke reason
		$check_user_earn_id	= COMMONDB_MODULE::get_value("badges_earns","user_id", $earn_id);
		$revocation_reason	= ($user_profile=='general' || $check_user_earn_id==$user_id) ? "Revoked by owner" : "Issued in error";

		//2. step : deletion as revocation
		if ( $earn_id> 0 && $allow_deletion > 0 ) {

			//remove from badges_earns -> move to badges_revocations
			//badges_earns_params not removed - update deleted values

			$obj_bg_earn = new COMMONDB_MODULE("badges_earns", $earn_id);
			$q = "INSERT INTO badges_revocations (
			revocation_reason,
			earn_id,
			badge_id,
			user_id,
			earn_email,
			earn_fullname,
			earn_firstname,
			earn_lastname,
			institution,
			institution_url,
			institution_image,
			institution_email,
			course,
			course_desc,
			course_url,
			badge_img_extension,
			badge_img_type,
			badge_img_name,
			grading,
			enabled,
			published,
			date_created,
			created_by,
			date_deleted,
			deleted_by,
			lastupdate_by,
			lastupdate )
			VALUES (
			'$revocation_reason',
			'".$obj_bg_earn->earn_id."',
			'".$obj_bg_earn->badge_id."',
			'".$obj_bg_earn->user_id."',
			'".$obj_bg_earn->earn_email."',
			'".$obj_bg_earn->earn_fullname."',
			'".$obj_bg_earn->earn_firstname."',
			'".$obj_bg_earn->earn_lastname."',
			'".$obj_bg_earn->institution."',
			'".$obj_bg_earn->institution_url."',
			'".$obj_bg_earn->institution_image."',
			'".$obj_bg_earn->institution_email."',
			'".$obj_bg_earn->course."',
			'".$obj_bg_earn->course_desc."',
			'".$obj_bg_earn->course_url."',
			'".$obj_bg_earn->badge_img_extension."',
			'".$obj_bg_earn->badge_img_type."',
			'".$obj_bg_earn->badge_img_name."',
			'".$obj_bg_earn->grading."',
			'".$obj_bg_earn->enabled."',
			'".$obj_bg_earn->published."',
			'".$obj_bg_earn->date_created."',
			'".$obj_bg_earn->created_by."',
			NOW(),
			$user_id,
			$user_id,
			NOW() )";

			//create a revoke badge
			$new_revoked_badge_id = COMMONDB_MODULE::launch_direct_system_query_get_lastId($q);

			//final actions
			if ( $new_revoked_badge_id>0 )
			{
				//update value image
				$badge_img 			 = $obj_bg_earn->badge_img;
				$badge_img 			 = ( $badge_img!='' && substr($badge_img,0,1) == "'" ) ? substr($badge_img, 1) : $badge_img;
				$badge_img 			 = ( $badge_img!='' && substr($badge_img, -1) == "'" ) ? substr($badge_img, 0,-1) : $badge_img;

				COMMONDB_MODULE::set_value("badges_revocations", "badge_img", $badge_img , $new_revoked_badge_id, $specialhtml='1');

				//update earns_params to delete
				COMMONDB_MODULE::launch_direct_system_query("UPDATE badges_earns_params SET deleted=1, deleted_by='$user_id', date_deleted=NOW(), lastupdate_by='$user_id' WHERE earn_id='$earn_id' ");

				//remove earn_id
				COMMONDB_MODULE::delete_value("badges_earns", $earn_id);

				// --- move JSON files to revoked dir --- //
				//move json badges assertions
				$file_json_badgeasser_from 	= APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".get_crypted_id($earn_id).BADGES_ASSERTION_PREFIX_JSON_FILES;
				$file_json_badgeasser_to   	= APP_GENERAL_REPO_BADGES_REVOKED_LOCAL."/".get_crypted_id($earn_id).BADGES_ASSERTION_PREFIX_JSON_FILES;
				move_files($file_json_badgeasser_from,$file_json_badgeasser_to);
				//move json badges class
				$file_json_badgeclass_from 	= APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".get_crypted_id($earn_id).BADGES_CLASS_PREFIX_JSON_FILES;
				$file_json_badgeclass_to 	= APP_GENERAL_REPO_BADGES_REVOKED_LOCAL."/".get_crypted_id($earn_id).BADGES_CLASS_PREFIX_JSON_FILES;
				move_files($file_json_badgeclass_from,$file_json_badgeclass_to);
				//move IMG files to revoked dir
				$file_image_assertion_from 	  = APP_GENERAL_REPO_BADGES_IMG_LOCAL."/".get_crypted_id($earn_id).BADGES_ASSERTION_IMAGE_PREFIX;
				$file_image_assertion_to 	  = APP_GENERAL_REPO_BADGES_REVOKED_LOCAL."/".get_crypted_id($earn_id).BADGES_ASSERTION_IMAGE_PREFIX;
				move_files($file_image_assertion_from,$file_image_assertion_to);

				//show result
				$event_success = __("Badge Earn deleted");
			} else {
				$event_errors = __("Could not be deleted");
			}

		} else {
			$event_errors = __("Could not be deleted");
		}
	break;

	case "delete_badge":
		//0. step : check badge
		$badge_id 		= ( isset($_POST["badge_id"]) && $_POST["badge_id"]!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_POST["badge_id"]) : '';

		//1 step : checks validations
		$user_id			= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : 0;
		$user_profile		= ( isset($logged_profile) && $logged_profile!='') ? $logged_profile : '';
		$allow_deletion 	= ($badge_id>0 && $user_id>0) ? COMMONDB_MODULE::count_values("badges_issuers","badge_id","WHERE badge_id='$badge_id' AND user_id='$user_id'") : 0;

		//2. step : deletion
		if ( $badge_id>0 && $allow_deletion > 0 ) {
			$q = "UPDATE badges_issuers SET deleted=1, deleted_by='$user_id', date_deleted=NOW(), lastupdate_by='$user_id' WHERE badge_id='$badge_id' LIMIT 1";
			COMMONDB_MODULE::launch_direct_system_query($q);
			$event_success = __("Badge deleted");
		} else {
			$event_errors = __("Could not be deleted");
		}
	break;

	case "new_badge":
		//1. step : check badge
		$new_badge_id 	= 0;
		$bage_id 		= "";
		$user_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : 0;
		$course			= ( isset($_POST["course"]) && strlen(trim($_POST["course"]))> 0 ) ? cleanup_string($_POST["course"]) : "";
		$course_desc	= ( isset($_POST["course_desc"]) && strlen(trim($_POST["course_desc"]))> 0 ) ? cleanup_string($_POST["course_desc"]) : "";
		$course_url		= ( isset($_POST["course_url"]) && strlen(trim($_POST["course_url"]))> 0 ) ? $_POST["course_url"] : "";
		$validate_curl 	= ($course_url!='' && validateURL($course_url)==1) ? 1 : 0;

		//get issuer data for this badge
		$institution = COMMONDB_MODULE::get_selected_value("users", "institution", "WHERE id_user='$user_id'");
		$institution_url = COMMONDB_MODULE::get_selected_value("users", "institution_url", "WHERE id_user='$user_id'");
		$institution_email = COMMONDB_MODULE::get_selected_value("users", "institution_email", "WHERE id_user='$user_id'");
		$institution_img= (defined('BADGES_ISSUER_INSTITUTION_IMAGE') && BADGES_ISSUER_INSTITUTION_IMAGE!='') ? BADGES_ISSUER_INSTITUTION_IMAGE : "";

		if ($user_id>0 && $institution!="" && $institution_url!="" && $course!="" && $course_desc!="" && $course_url!="" && $validate_curl==1)
		{
			$q = "INSERT INTO badges_issuers (badge_id,user_id,institution,institution_url,institution_image,institution_email,course,course_desc,course_url,date_created,created_by,lastupdate_by)
				VALUES ('','$user_id','$institution','$institution_url','$institution_img','$institution_email','$course','$course_desc','$course_url',NOW(),'$user_id','$user_id')";
			$new_badge_id = COMMONDB_MODULE::launch_direct_system_query_get_lastId($q);
			$event_success = "Your Badge has been created";
		} else {
			$event_errors = ($validate_curl==0) ? __("Could not proceed. Invalid syntax for Course Criteria URL") : __("Could not proceed");
		}

		//2. step: check image
		if ( $new_badge_id > 0 && isset($_FILES) ) {

			$badge_file_info = set_file_blob($fieldname='badge_img');
			$event_errors = ( isset($badge_file_info[0]) && $badge_file_info[0]=='error') ? $badge_file_info[1] : "";

			if ( $event_errors =='' && isset($badge_file_info[0]) && $badge_file_info[0]=='ok' ) {
				COMMONDB_MODULE::set_value("badges_issuers", "badge_img", $badge_file_info['badge_img'], $new_badge_id, $specialhtml='1');
				COMMONDB_MODULE::set_value("badges_issuers", "badge_img_extension", $badge_file_info['badge_img_extension'], $new_badge_id, 0);
				COMMONDB_MODULE::set_value("badges_issuers", "badge_img_type", $badge_file_info['badge_img_type'], $new_badge_id, 0);
				COMMONDB_MODULE::set_value("badges_issuers", "badge_img_name", $badge_file_info['badge_img_name'], $new_badge_id, 0);
			} else {
				$event_errors = ( isset($badge_file_info[1]) && $badge_file_info[1] =='none' ) ? __("Bage IMG") ." : ".__("Required") : "";
				$event_errors = ( isset($badge_file_info[1]) && $badge_file_info[1] =='size' ) ? __("Bage IMG") ." : ".__("Error size")." (".__("max.").(BADGES_IMAGE_MAX_SIZE/1024).__("kb").")" : $event_errors;
				$event_errors = ( isset($badge_file_info[1]) && $badge_file_info[1] =='extension' ) ? __("Bage IMG"). " : ".__("Error extension") : $event_errors;
				//control enable
				COMMONDB_MODULE::set_value("badges_issuers", "enabled", '0', $new_badge_id, 0);
				//control published
				COMMONDB_MODULE::set_value("badges_issuers", "published", '0', $new_badge_id, 0);
			}
		}

		//3. step: check params
		if ($new_badge_id>0) {
			for ($i = 0; $i < BADGES_PARAMS_NUM_MAX; $i++)
			{
				$field_label 		= "label-".$i;
				$field_description 	= "description-".$i;
				$set_label = ""; $set_description = "";
				if ( isset($_POST["$field_label"]) && $_POST["$field_label"]!='')
				{
					//check_max_params
					$count_params = COMMONDB_MODULE::count_values("badges_issuers_params","param_id","WHERE badge_id='$new_badge_id' AND deleted='0'");
					if ( $count_params < BADGES_PARAMS_NUM_MAX )
					{
						$field_label 		= cleanup_string($_POST["$field_label"]);
						$field_description 	= cleanup_string($_POST["$field_description"]);
						$q = "INSERT INTO badges_issuers_params (param_id,badge_id,label,description,type,enabled,date_created,created_by,lastupdate_by)
						VALUES('','$new_badge_id', '$field_label','$field_description','text',1,NOW(),$user_id,$user_id) ";
						COMMONDB_MODULE::launch_direct_system_query($q);
					} else {
						$event_errors = __("Max params reached");
					}
				}
			}
		}
	break;

	case "update_badge":
		//0. step : check badge
		$badge_id 		= ( isset($_POST["badge_id"]) && $_POST["badge_id"]!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_POST["badge_id"]) : '';
		$user_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : 0;
		$course			= ( isset($_POST["course"]) && strlen(trim($_POST["course"]))> 0 ) ? $_POST["course"] : "";
		$course_desc	= ( isset($_POST["course_desc"]) && strlen(trim($_POST["course_desc"]))> 0 ) ? cleanup_string($_POST["course_desc"]) : "";
		$course_url		= ( isset($_POST["course_url"]) && strlen(trim($_POST["course_url"]))> 0 ) ? $_POST["course_url"] : "";
		$validate_curl 	= ($course_url!='' && validateURL($course_url)==1) ? 1 : 0;

		//1. step : get institution information from badge user_id (owner)
		$badge_issuer_id = COMMONDB_MODULE::get_selected_value("badges_issuers", "user_id", "WHERE badge_id='$badge_id'");
		$institution = COMMONDB_MODULE::get_selected_value("users", "institution", "WHERE id_user='$badge_issuer_id'");
		$institution_url = COMMONDB_MODULE::get_selected_value("users", "institution_url", "WHERE id_user='$badge_issuer_id'");
		$institution_email = COMMONDB_MODULE::get_selected_value("users", "institution_email", "WHERE id_user='$badge_issuer_id'");
		$institution_img= (defined('BADGES_ISSUER_INSTITUTION_IMAGE') && BADGES_ISSUER_INSTITUTION_IMAGE!='') ? BADGES_ISSUER_INSTITUTION_IMAGE : "";

		//3 step : checks validations
		$total_badges_earns = ($badge_id>0) ? COMMONDB_MODULE::count_values("badges_earns","earn_id","WHERE badge_id='$badge_id'") : 0;

		if ( $badge_id > 0 && $total_badges_earns=='0' && $user_id> 0 && $institution!='' && $institution_url!='' && $course!='' && $course_desc!='' && $course_url!='' && $validate_curl==1)
		{
			//4. update badge info
			$q = "UPDATE badges_issuers SET institution='$institution',institution_url='$institution_url',institution_image='$institution_img',institution_email='$institution_email',course='$course',course_desc='$course_desc',course_url='$course_url',lastupdate_by='$user_id' WHERE badge_id='$badge_id'";
			COMMONDB_MODULE::launch_direct_system_query($q);
			$event_success = "Your Badge has been updated";

			//2. update image
			if ( $badge_id > 0 && isset($_FILES) ) {
				$badge_file_info = set_file_blob($fieldname='badge_img');
				$event_errors = ( isset($badge_file_info[0]) && $badge_file_info[0]=='error') ? $badge_file_info[1] : "";

				if ( $event_errors =='' && isset($badge_file_info[0]) && $badge_file_info[0]=='ok' ) {
					COMMONDB_MODULE::set_value("badges_issuers", "badge_img", $badge_file_info['badge_img'], $badge_id, $specialhtml='1');
					COMMONDB_MODULE::set_value("badges_issuers", "badge_img_extension", $badge_file_info['badge_img_extension'], $badge_id, 0);
					COMMONDB_MODULE::set_value("badges_issuers", "badge_img_type", $badge_file_info['badge_img_type'], $badge_id, 0);
					COMMONDB_MODULE::set_value("badges_issuers", "badge_img_name", $badge_file_info['badge_img_name'], $badge_id, 0);

					//control enabled
					COMMONDB_MODULE::set_value("badges_issuers", "enabled", '1', $badge_id, 0);
				} else {
					$event_errors = "";
					$event_errors = ( isset($badge_file_info[1]) && $badge_file_info[1] =='size' ) ? "Bage IMG : Error size" : $event_errors;
					$event_errors = ( isset($badge_file_info[1]) && $badge_file_info[1] =='extension' ) ? "Bage IMG : Error extension" : $event_errors;
					//control enabled
					($event_errors!='') ? COMMONDB_MODULE::set_value("badges_issuers", "enabled", '0', $badge_id, 0) : "";
					//control published
					($event_errors!='') ? COMMONDB_MODULE::set_value("badges_issuers", "published", '0', $badge_id, 0) : "";
				}
			}

			//5. update params
			if ($badge_id>0)
			{
				//update existing params
				$arr_params			= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers_params","param_id,label,description","WHERE badge_id='$badge_id' AND deleted='0' ORDER BY param_id","");
				$count_params 		= count($arr_params);
				$count_less_params 	= BADGES_PARAMS_NUM_MAX-$count_params;

				$i = 0;
				foreach ($arr_params AS $param)
				{
					$field_label 		= "label-$i-".$param['param_id'];
					$field_description 	= "description-$i-".$param['param_id'];
					if ( isset($_POST["$field_label"]) && $_POST["$field_label"]!='')
					{
						$param_id			= $param['param_id'];
						$field_label 		= cleanup_string($_POST["$field_label"]);
						$field_description 	= cleanup_string($_POST["$field_description"]);
						if ($param_id>0 &&  $field_label!='')
						{
							$q = "UPDATE badges_issuers_params SET label='$field_label', description='$field_description' WHERE badge_id='$badge_id' AND param_id='$param_id'";
							COMMONDB_MODULE::launch_direct_system_query($q);
						}
					}
					$i +=1;
				}

				//check new params
				if ( $count_less_params > 0 ) {
					for ($i = 0; $i < $count_less_params; $i++)
					{
						$field_label 		= "label-".$i;
						$field_description 	= "description-".$i;
						$set_label = ""; $set_description = "";
						if ( isset($_POST["$field_label"]) && $_POST["$field_label"]!='')
						{
							//check_max_params
							$count_params = COMMONDB_MODULE::count_values("badges_issuers_params","param_id","WHERE badge_id='$badge_id' AND deleted='0'");
							if ( $count_params < BADGES_PARAMS_NUM_MAX )
							{
								$field_label 		= cleanup_string($_POST["$field_label"]);
								$field_description 	= cleanup_string($_POST["$field_description"]);
								$q = "INSERT INTO badges_issuers_params (param_id,badge_id,label,description,type,enabled,lastupdate_by) VALUES('','$badge_id', '$field_label','$field_description','text',1,$user_id) ";
								COMMONDB_MODULE::launch_direct_system_query($q);
							}
						}
					}
				}
			}

		} else {
			$event_errors = ( $total_badges_earns > 0 ) ? __("You Could not modify this badged. Is being awarded.") : (  ($validate_curl==0) ? __("Could not proceed. Invalid syntax for Course Criteria URL") : __("Could not proceed" ) );
		}
	break;

	case "earn_badge":

		//0.0 editor
		$this_editor_id	= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : 0;

		//0.1 step : check badge
		$new_earn_id	= 0;
		$badge_id 		= ( isset($_POST["badge_id"]) && $_POST["badge_id"]!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_POST["badge_id"]) : '';
		$badge_enabled	= ( $badge_id>0) ? COMMONDB_MODULE::get_selected_value("badges_issuers", "enabled", "WHERE badge_id='$badge_id' AND deleted=0") : 0;

		//0.2 step : check user
		$earn_first_name= ( isset($_POST['first_name']) && strlen(trim($_POST['first_name']))>0 ) ? $_POST['first_name'] : '';
		$earn_last_name = ( isset($_POST['last_name']) && strlen(trim($_POST['last_name']))>0 ) ? $_POST['last_name'] : '';
		$earn_fullname	= ( $earn_first_name!='' && $earn_last_name!='' ) ? "$earn_first_name $earn_last_name" : '';
		$earn_fullname	= ( $earn_fullname=='' && isset($_POST["earn_fullname"]) && strlen(trim($_POST["earn_fullname"]))>0) ? $_POST["earn_fullname"] : "$earn_fullname";
		if ( $earn_fullname!='' && $earn_first_name=='' && $earn_last_name =='') {
			$explode_fullname	= explode(" ", $earn_fullname);
			$earn_first_name	= ( $earn_first_name=='') ?  ( ( isset($explode_fullname[0]) ) ? $explode_fullname[0] : '' ) : $earn_first_name;
			$earn_last_name		= ( $earn_last_name=='') ?  ( ( isset($explode_fullname[0]) ) ? ltrim(substr($earn_fullname, strlen($explode_fullname[0])) ) : '' ) : $earn_last_name;
		}

		$earn_email		= ( isset($_POST["earn_email"]) && strlen(trim($_POST["earn_email"]))>0) ? $_POST["earn_email"] : '';
		$earn_email		= isValidateEmailSyntax($earn_email);

		//check using email if user_id exists
		$user_id 		= ( $earn_email!='' ) ? COMMONDB_MODULE::get_selected_value("users","id_user","WHERE email='$earn_email'") : 0;

		//1. step : validations
		$check_earn_exists 	 = COMMONDB_MODULE::get_selected_value("badges_earns", "earn_id", "WHERE badge_id='$badge_id' AND earn_email='$earn_email' AND deleted=0");
		$check_earn_exists	 = ($check_earn_exists==0 && $user_id>0) ? COMMONDB_MODULE::get_selected_value("badges_earns", "earn_id", "WHERE badge_id='$badge_id' AND user_id='$user_id' AND deleted=0") : $check_earn_exists;
		$event_errors 		.= ( preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+[.]+[a-zA-Z0-9-.]+$/", $earn_email) ) ? "" : "Email error";
		$event_errors 		.= ( $check_earn_exists>0 ) ? "Duplicate: Not allowed" : "";

		if ( $badge_id>0 && $badge_enabled==1 && $event_errors=='' && $earn_fullname!='' && $earn_email!='' && $check_earn_exists ==0 ) {

			//2. get earn user_id if exists
			$earn_user_id 			 = ($user_id>0) ? $user_id : 0;

			//3. create earn_id
			$obj = new COMMONDB_MODULE("badges_issuers", $badge_id);
				$institution 		 = $obj->institution;
				$institution_url 	 = $obj->institution_url;
				$institution_img 	 = $obj->institution_image;
				$institution_email 	 = $obj->institution_email;
				$course 	 		 = $obj->course;
				$course_desc 		 = $obj->course_desc;
				$course_url 		 = $obj->course_url;
				$badge_img 	 		 = $obj->badge_img;
				$badge_img 			 = ( $badge_img!='' && substr($badge_img,0,1) == "'" ) ? substr($badge_img, 1) : $badge_img;
				$badge_img 			 = ( $badge_img!='' && substr($badge_img, -1) == "'" ) ? substr($badge_img, 0,-1) : $badge_img;
				$badge_img_extension = $obj->badge_img_extension;
				$badge_img_type 	 = $obj->badge_img_type;
				$badge_img_name 	 = $obj->badge_img_name;

			$q = "INSERT INTO badges_earns (earn_id,badge_id,user_id,earn_email,earn_fullname,earn_firstname, earn_lastname,institution,institution_url,institution_image,institution_email,course,course_desc,course_url,
			badge_img_extension,badge_img_type,badge_img_name,date_created,created_by,lastupdate_by,lastupdate)
			VALUES ('','$badge_id','$earn_user_id','$earn_email','$earn_fullname','$earn_first_name', '$earn_last_name','$institution','$institution_url','$institution_img','$institution_email','$course','$course_desc','$course_url',
			'$badge_img_extension','$badge_img_type','$badge_img_name',NOW(),'$this_editor_id','$this_editor_id',NOW())";

			//control OBI institution required data
			$new_earn_id = ($institution!='' && $institution_url!='') ? COMMONDB_MODULE::launch_direct_system_query_get_lastId($q) : 0;

			//4. create earn_id
			if ( $new_earn_id > 0 )
			{
				//special add blob img
				COMMONDB_MODULE::set_value("badges_earns", "badge_img",$badge_img, $new_earn_id, $specialhtml='1');

				//create params
				$arr_params			= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers_params","param_id,label,description,type","WHERE badge_id='$badge_id' AND deleted='0' ORDER BY param_id","");
				$count_params 		= count($arr_params);
				$params_error		= ""; //control params errors - OBI evidence needs to be url
				$params_error_json  = ""; //control params errors for json creation files

				$i = 0;
				foreach ($arr_params AS $param)
				{
					$field_content 	= "description-$i-".$param['param_id'];
					if ( isset($_POST["$field_content"]) && $_POST["$field_content"]!='')
					{
						$earn_param_id	= $param['param_id'];
						$param_id	= $param['param_id'];
						$label 		= $param['label'];
						$description= $param['description'];
						$type 		= $param['type'];

						//obi specs : verify evidence type url
						$content 	= cleanup_string($_POST["$field_content"]);
						$content 	= trim($content); //clean url
						$validate_content = ($content!='' && validateURL($content)==1) ? 1 : 0;

						if ( $param_id>0 &&  $label!='' && $validate_content==1 && $content!='' )
						{
							$q = "INSERT INTO badges_earns_params (param_id,earn_id,earn_param_id,label,description,content,type,date_created,created_by,lastupdate_by,lastupdate)
							VALUES('','$new_earn_id','$earn_param_id','$label','$description','$content','$type',NOW(),'$this_editor_id','$this_editor_id',NOW())";
							COMMONDB_MODULE::launch_direct_system_query($q);
						} else {
							if ($content!='' && $validate_content!=1) { $params_error = "1"; }
						}
					}
					$i +=1;
				}

				//
				// Create badges files
				//
				if ( $params_error == '' )
				{
					//DON NOT CHANGE ORDER
					// first: badge_class
					$create_badge_class_json = IBL_OPENBADGES::create_badgeclass_json($new_earn_id);
					// second : badge_asser
					$create_badge_asser_json = ( $create_badge_class_json == 1 ) ? IBL_OPENBADGES::create_badgeassertion_json($new_earn_id) : 0;
					$params_error_json = ( $create_badge_class_json == 1 && $create_badge_asser_json ==1 )  ? "" : "error file creation";
				}

				//control params errors
				if ( $params_error !=""	|| $params_error_json !="" )
				{
					//remove all - badge_earn and badge_params
					COMMONDB_MODULE::launch_direct_system_query("DELETE FROM badges_earns_params WHERE earn_id='$new_earn_id'");
					COMMONDB_MODULE::launch_direct_system_query("DELETE FROM badges_earns WHERE earn_id='$new_earn_id'");
					$new_earn_id = 0;
					$event_errors = ( $params_error!='' ) ? "The badge could not be created. Evidences syntax incorrect (url)." : "The badge could not be created. The json files could not be set.";
				} else {
					$event_success = __("The badge is being created");
				}

			} else {
				$event_errors = __("The badge could not be created");
			}
		} else {
			if ( $check_earn_exists > 0 ) {
				$event_errors = __("The badge could not be created, because it already exists.");
			} else {
				$event_errors = __("The badge could not be created. Some data is missing.");
			}

		}
	break;

	case "publish_badge":
		$this_badge_id		 = ( isset($_POST["badge_id"]) && $_POST["badge_id"]!='' ) ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_POST["badge_id"] )  : 0;
		$this_editor_id		 = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$check_editor_profile= COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$this_editor_id AND activated=1 ");
		$check_editor_issuer = COMMONDB_MODULE::get_selected_value("badges_issuers", "badge_id","WHERE user_id=$this_editor_id AND enabled=1 AND badge_id='$this_badge_id' ");
		$check_enable_badge  = COMMONDB_MODULE::get_selected_value("badges_issuers", "enabled","WHERE badge_id='$this_badge_id' ");
		$allow_change_publish = ($check_editor_issuer>0) ? 1 : 0;
		$allow_change_publish = ($check_editor_profile=='admin') ? 1 : $allow_change_publish;

		//whatever : if disabled do not allow publish
		$allow_change_publish  = ( $check_enable_badge==1 ) ? $allow_change_publish : 0;

		if ( $check_editor_profile != 'general' && $allow_change_publish ==1 ) {
			$old_value			= COMMONDB_MODULE::get_selected_value("badges_issuers","published","WHERE badge_id='$this_badge_id'");
			$new_value			= ( $old_value ==1 ) ? 0 : 1;
			$text_event			= ( $old_value ==1 ) ? "published" : "unpublished";
			COMMONDB_MODULE::set_value("badges_issuers", "published", "$new_value", $this_badge_id);
			COMMONDB_MODULE::set_value("badges_issuers", "lastupdate_by", "$this_editor_id",  $this_badge_id);
			$event_success = "The badge has been $text_event";
		} else {
			if ( $check_enable_badge == 0 ) {
				$event_errors = __("The badge could not be published because is disabled");
			} else {
				$event_errors = __("This action could not be performed");
			}
		}
	break;

	case "del_evidence_param":
		$this_param_id		 = ( isset($_POST["param_id"]) && is_numeric($_POST["param_id"]) && $_POST["param_id"]>0 ) ? $_POST["param_id"] : 0;
		$this_badge_id		 = ( isset($_POST["badge_id"]) && $_POST["badge_id"]!='' ) ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_POST["badge_id"] )  : 0;
		$this_editor_id		 = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$check_editor_profile= COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$this_editor_id AND activated=1 ");
		$check_editor_issuer = COMMONDB_MODULE::get_selected_value("badges_issuers", "badge_id","WHERE user_id=$this_editor_id AND badge_id='$this_badge_id' ");
		$total_b_earn  		 = COMMONDB_MODULE::count_values("badges_earns","earn_id","WHERE badge_id='".$this_badge_id."'");
		$total_b_revok 		 = COMMONDB_MODULE::count_values("badges_revocations","revocation_id","WHERE badge_id='".$this_badge_id."'");
		$total_dependencies  = $total_b_earn+$total_b_revok;
		$allow_deletion 	 = ($check_editor_issuer>0) ? 1 : 0;
		$allow_deletion   	 = ($check_editor_profile=='admin') ? 1 : $allow_deletion;
		$allow_deletion 	 = ($total_dependencies>0) ? 0 : $allow_deletion;

		if ( $allow_deletion ==1 )
		{
			$q = "UPDATE badges_issuers_params SET deleted='1', deleted_by='$this_editor_id', date_deleted=NOW(), lastupdate_by='$this_editor_id' WHERE param_id='$this_param_id' AND badge_id='$this_badge_id' ";
			COMMONDB_MODULE::launch_direct_system_query($q);
			$event_success = __("The evidence has been deleted");
		} else {
			$event_errors = __("This action could not be performed");
		}
	break;

	case "set_public_earn":
		$this_earn_id		 = ( isset($_POST["earn_id"]) && $_POST["earn_id"]!='' ) ? COMMONDB_MODULE::decrypt_id("badges_earns", $_POST["earn_id"] )  : 0;
		$this_editor_id		 = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$check_editor_profile= COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$this_editor_id AND activated=1 ");
		$check_editor_earned = COMMONDB_MODULE::get_selected_value("badges_earns", "earn_id","WHERE earn_id='$this_earn_id' AND user_id='$this_editor_id'");
		$allow_change_publish = ($check_editor_earned>0 && $check_editor_earned==$this_earn_id && $this_earn_id>0) ? 1 : 0;
	
		if ( $allow_change_publish ==1 ) {
			$old_value			= COMMONDB_MODULE::get_selected_value("badges_earns","show_public","WHERE earn_id='$this_earn_id'");
			$new_value			= ( $old_value ==1 ) ? 0 : 1;
			COMMONDB_MODULE::set_value("badges_earns", "show_public", "$new_value", $this_earn_id);
			COMMONDB_MODULE::set_value("badges_earns", "lastupdate_by", "$this_editor_id",  $this_earn_id);
			$event_success = "The badge has been updated";
		} else {
			$event_errors = __("This action could not be performed");
		}
	break;
		
	//
	// ------------------ Users ------------------- //
	//
	case "update_user":
		//TO-DO : evaluate name and email with dependencies : badges_json and more
		$this_editor_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$check_email_editor = COMMONDB_MODULE::get_selected_value("users", "email","WHERE id_user=$this_editor_id AND activated=1 ");
		$check_user			= ( isset($_POST['check_user']) && strlen(trim($_POST['check_user']))>0 ) ? COMMONDB_MODULE::decrypt_id("users", $_POST['check_user']) : '0';
		$check_editor_profile= COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$this_editor_id AND activated=1 ");

		//params to upade
		//user name
		$u_fullname			= ( isset($_POST['fullname']) && strlen(trim($_POST['fullname']))>0 ) ? $_POST['fullname'] : '';
		$u_user_name		= ($u_fullname!='') ? explode(" ", $u_fullname) : array();
		$u_firstname		= ($u_fullname!='' && isset($u_user_name[0]) ) ? $u_user_name[0] : '';
		$u_lastname			= ($u_fullname!='' && isset($u_user_name[0]) ) ? substr($u_fullname, strlen($u_firstname) ): '';
		//user email
		$u_email			= ( isset($_POST['email']) && strlen(trim($_POST['email']))>0 ) ? $_POST['email'] : '';
		$u_email			= isValidateEmailSyntax($u_email);
		//user pwd
		$min_pwd_chars		= (defined('APP_USER_MIN_CHARS_PWD') && APP_USER_MIN_CHARS_PWD>0) ? APP_USER_MIN_CHARS_PWD : 4;

		$u_pwd				= (isset($_POST['new_pass']) && strlen($_POST['new_pass']) > 0 ) ? $_POST['new_pass'] : '';
		$u_pwd_check		= (isset($_POST['new_pass']) && strlen($_POST['new_pass']) > ($min_pwd_chars-1) ) ? $_POST['new_pass'] : '';

		//institution data
		$institution			= ( isset($_POST['institution']) && strlen(trim($_POST['institution']))>0 ) ? $_POST['institution'] : '';
		$institution_url		= ( isset($_POST['institution_url']) && strlen(trim($_POST['institution_url']))>0 ) ? $_POST['institution_url'] : '';
		$institution_url_valid	= validateURL($institution_url);
		$institution_email		= ( isset($_POST['institution_email']) && strlen(trim($_POST['institution_email']))>0 ) ? isValidateEmailSyntax($_POST['institution_email']) : '';

		//user additional information

		//picture upload
		$old_picture = COMMONDB_MODULE::get_value("users", "picture", $this_editor_id);
		if ( isset($_FILES["picture"]) && $_FILES['picture']["name"]!='') {
			$objfile = $_FILES["picture"];
			$new_picture = get_crypted_id($this_editor_id).".".substr(strrchr(strtolower($objfile["name"]),'.'),1);
			$pathupload = APP_GENERAL_REPO_USERS_PICTURES;
			$max_size = (defined("USERS_PICTURES_MAX_SIZE_BYTES") && USERS_PICTURES_MAX_SIZE_BYTES!='') ? USERS_PICTURES_MAX_SIZE_BYTES : "";
			$allowed_extensions = (defined("USERS_PICTURES_ALLOW_EXTENSIONS") && USERS_PICTURES_ALLOW_EXTENSIONS!='') ? USERS_PICTURES_ALLOW_EXTENSIONS : "";
			$picture_upload = upload_global_files($objfile,$new_picture,$pathupload, $max_size, $allowed_extensions);
			if ( count($picture_upload) >0  ) {
				foreach ($picture_upload AS $error_value) {
					$error_additional_user_info .= __("Error Picture").": $error_value . <br>";
				} 
			} else {
				if ( $old_picture!='' && $old_picture!=$new_picture && file_exists(APP_GENERAL_REPO_USERS_PICTURES."/".$old_picture))
				{
					delete_files(APP_GENERAL_REPO_USERS_PICTURES."/".$old_picture);
				}				
				$update_additional_user_info = "picture='$new_picture',";
			}
		}
		//picture delete
		if ( isset($_POST["del_picture"]) ) {
			if ( $old_picture!='' && file_exists(APP_GENERAL_REPO_USERS_PICTURES."/".$old_picture)) 
			{
				delete_files(APP_GENERAL_REPO_USERS_PICTURES."/".$old_picture);
				$update_additional_user_info = "picture='',";
			}
			//prevent upload new imgs if deleted is check
			if ( isset($new_picture) && $new_picture!='' && file_exists(APP_GENERAL_REPO_USERS_PICTURES."/".$new_picture) ){
				delete_files(APP_GENERAL_REPO_USERS_PICTURES."/".$new_picture);
				$update_additional_user_info = "picture='',";	
			}
		}
		//other user data
		$arr_urls_user_info = array("url_website","url_social_facebook","url_social_twitter","url_social_gplus","url_social_linkedin");
		foreach ($arr_urls_user_info AS $url_value) {
			$check_url_data = ( isset($_POST[$url_value]) && strlen(trim($_POST[$url_value]))>0 ) ? $_POST[$url_value] : '';
			$update_additional_user_info .= ( validateURL($check_url_data) == 1 ) ? "$url_value='$check_url_data'," : "";
			$update_additional_user_info .= ( ($check_url_data!='' && validateURL($check_url_data) == 1 ) ||  $check_url_data == "") ? "$url_value='$check_url_data'," : "";
		 	$error_additional_user_info .= ($check_url_data !="" && validateURL($check_url_data) != 1 ) ? __("Error URL").": $check_url_data" : ""; 
		}
		
		$about_user =  ( isset($_POST['about_user']) && strlen(trim($_POST['about_user']))>0 ) ? htmlspecialchars($_POST['about_user'],ENT_QUOTES) : '';
		$update_additional_user_info .= "about_user='$about_user',";
		
		//evaluate_changes
		$allow_changes		= ($u_pwd_check!='' || $u_fullname!='' || $u_email!='' ) ? 1 : 0;

		if ( $check_user == $this_editor_id && $allow_changes == 1 ) {
			//update pwd is needed
			if ( $u_pwd_check!='' ) { COMMONDB_MODULE::set_value("users", "password",  md5($u_pwd_check), $this_editor_id); }

			//TO-DO: check for jsons and issued badges implications
			if ( $u_fullname!='' ) { COMMONDB_MODULE::set_value("users", "name",  $u_fullname, $this_editor_id); }

			// just for profiles : admin and issuer
			if ( $check_editor_profile =='admin' || $check_editor_profile =='issuer') {
				if ( $institution!='' && $institution_url!='' && $institution_url_valid==1 && $institution_email!='' ) {
					//updatedb
					COMMONDB_MODULE::launch_direct_system_query("UPDATE users SET $update_additional_user_info institution='$institution', institution_url='$institution_url', institution_email='$institution_email' WHERE id_user='$this_editor_id'");
					//update json issuer file
					IBL_OPENBADGES::create_issuer_json($this_editor_id);
				} else {
					$event_errors = __("Some Institution data is missing or have errors.");
				}
			} else {
				//general users update additional data
				if ( $update_additional_user_info != '' ) {
					COMMONDB_MODULE::launch_direct_system_query("UPDATE users SET $update_additional_user_info lastupdate=NOW() WHERE id_user='$this_editor_id'");
				}
			}

			//check partial errors
			$event_success = ($event_errors=='') ?  __("Your profile data has been updated") : "";

		} else {
			$event_errors = ( $u_pwd!='') ? __("Password could not be updated")." < ". $min_pwd_chars. __("chars") : __("Nothing to change");
		}
	break;

	case "change_user_profile":
		$this_editor_id		 = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$check_editor_profile= COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$this_editor_id AND activated=1 ");
		if ( $check_editor_profile == 'admin' ) {

			$requested_user_id	= ( isset($_POST['user_id']) && strlen(trim($_POST['user_id']))>0 ) ? COMMONDB_MODULE::decrypt_id("users", $_POST['user_id']) : '0';
			$new_profile		= ( isset($_POST['profile']) && strlen(trim($_POST['profile']))>0 ) ? $_POST['profile'] : '';
			$total_b_issue 		= COMMONDB_MODULE::count_values("badges_issuers","badge_id","WHERE user_id='".$requested_user_id."'");
			$old_profile		= ($requested_user_id>0) ? COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$requested_user_id") : '';
			//validation changes
			$allow_change_profile = ($requested_user_id>0 && $requested_user_id!=$this_editor_id && $total_b_issue==0 && $new_profile!='' && $old_profile!=$new_profile && $old_profile!='' && $new_profile!='admin' && $old_profile!='admin') ? 1 : 0;

			if ( $allow_change_profile == 1  ) {

				//update profile
				COMMONDB_MODULE::set_value("users", "profile", "$new_profile", $requested_user_id);

				//update institution data getting data from admin
				if ($new_profile =='issuer' )
				{
					$obj_issuer_main = new COMMONDB_MODULE("users", $this_editor_id);
					$institution =  $obj_issuer_main->institution;
					$institution_url =  $obj_issuer_main->institution_url;
					$institution_email = $obj_issuer_main->institution_email;

					COMMONDB_MODULE::launch_direct_system_query("UPDATE users SET institution='$institution', institution_url='$institution_url', institution_email='$institution_email' WHERE id_user='$requested_user_id'");
					//create json issuer file
					IBL_OPENBADGES::create_issuer_json($requested_user_id);
				} else {
					//remove json file if exists
					$unique_issuer_uid = get_crypted_id($requested_user_id);
					$issuer_class_file_path = APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".$unique_issuer_uid.ISSUER_CLASS_PREFIX_JSON_FILES;
					if ( file_exists($issuer_class_file_path) ) { unlink($issuer_class_file_path); }
				}

				$event_success = "The new profile is set";
			} else {
				$event_errors = __("This profile could not be changed");
			}

		} else {
			$event_errors = __("You could not grant this action");
		}
	break;

	case "change_user_active":
		$this_editor_id		 = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$check_editor_profile= COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$this_editor_id AND activated=1 ");
		if ( $check_editor_profile == 'admin' ) {
			$requested_user_id	= ( isset($_POST['user_id']) && strlen(trim($_POST['user_id']))>0 ) ? COMMONDB_MODULE::decrypt_id("users", $_POST['user_id']) : '0';
			$old_active			= ($requested_user_id>0) ? COMMONDB_MODULE::get_selected_value("users", "activated","WHERE id_user=$requested_user_id") : '';
			$new_active			= ( $old_active ==1 ) ? 0 : 1;
			$text_event			= ( $old_active ==1 ) ? __("deactivated") : __("activated");
			//validation changes
			$allow_change_active = ($requested_user_id>0 && $requested_user_id!=$this_editor_id && $old_active!='' ) ? 1 : 0;

			if ( $allow_change_active == 1  ) {
				COMMONDB_MODULE::set_value("users", "activated", "$new_active", $requested_user_id);
				$event_success = __("The user has been")." ".$text_event;
			} else {
				$event_errors = __("This user could not be")." ".$text_event;
			}
		} else {
			$event_errors = __("You could not grant this action");
		}
	break;

	case "delete_user":
		$this_editor_id		 = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$check_editor_profile= COMMONDB_MODULE::get_selected_value("users", "profile","WHERE id_user=$this_editor_id AND activated=1 ");
		if ( $check_editor_profile == 'admin' ) {

			$requested_user_id		= ( isset($_POST['user_id']) && strlen(trim($_POST['user_id']))>0 ) ? COMMONDB_MODULE::decrypt_id("users", $_POST['user_id']) : '0';
			$total_b_issue 			= COMMONDB_MODULE::count_values("badges_issuers","badge_id","WHERE user_id='".$requested_user_id."'");
			$total_b_earn  			= COMMONDB_MODULE::count_values("badges_earns","earn_id","WHERE user_id='".$requested_user_id."'");
			$total_b_revok 			= COMMONDB_MODULE::count_values("badges_revocations","revocation_id","WHERE user_id='".$requested_user_id."'");

			//validation changes
			$allow_delete  			= ( ($total_b_earn+$total_b_issue+$total_b_revok)>0 )  ? 0 : 1 ;
			if ( $allow_delete == 1  ) {
				COMMONDB_MODULE::delete_multiple_values("users","WHERE id_user='$requested_user_id'");
				COMMONDB_MODULE::delete_multiple_values("oauth_clients","WHERE user_id='$requested_user_id'");

				//remove json file if exists
				$unique_issuer_uid = get_crypted_id($requested_user_id);
				$issuer_class_file_path = APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".$unique_issuer_uid.ISSUER_CLASS_PREFIX_JSON_FILES;
				if ( file_exists($issuer_class_file_path) ) { unlink($issuer_class_file_path); }

				$event_success = __("The user has been deleted");
			} else {
				$event_errors = __("This user could not be deleted");
			}
		} else {
			$event_errors = __("You could not grant this action");
		}
	break;

	default : break;

}
?>
