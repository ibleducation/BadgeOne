<?php
/**
 * Class to interact with openbadges data
 * More info : https://github.com/mozilla/openbadges-specification/blob/master/Assertion/latest.md
 * Latest specs: https://openbadgespec.org/
 *
 */
class IBL_OPENBADGES
{
	/**
	 * IBL_OPENBADGES constructor
	 * @return object IBL_OPENBADGES
	 */
	public function __construct()
	{

	}

	/**
	 * Check earn_id
	 *  @param int $earned_badge_id
	 *  @return int
	 */
	public static function check_earned_badge_id($earned_badge_id='0'){
		return COMMONDB_MODULE::get_selected_value("badges_earns", "earn_id", "WHERE earn_id='$earned_badge_id' AND deleted='0'");
	}

	/**
	* Create OBI comp file
	* BadgeClass json
	*
	*  Notes from OBI specs:
	*   A collection of information about the accomplishment recognized by the Open Badge
	*   id = URL -> unique - online path to 'badge_class.json' file
	*   issuer = URL -> unique - online path institution issuer "issuer.json"
	*   criteria = URL -> path criteria course
	*
	*  Important:
	*    In order to create a badge_assertion this files are required
	*    - badge issuer json file
	*
	* @param int $earned_badge_id
	* @return int as boolean (1: true, 0:false)
	*/
	public static function create_badgeclass_json($earned_badge_id='0'){

		$new_earn_id 		= IBL_OPENBADGES::check_earned_badge_id($earned_badge_id);
		$check_template 	= ( file_exists( APP_BADGES_TEMPLATE_BADGE_CLASS ) )  ? 1 : 0;
		$check_issuer_json	= ( file_exists( APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".BADGES_ISSUER_INSTITUTION_FILE_ID ) )  ? 1 : 0;

		if ( $new_earn_id>0 && $check_template ==1 && $check_issuer_json==1 )
		{
			// 0. unique id : earn_badge_id
			$unique_badge_uid = get_crypted_id($new_earn_id);

			// 1. setting up paths
			$badges_class_file_json = $unique_badge_uid.BADGES_CLASS_PREFIX_JSON_FILES;
			$badges_class_file_path = APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".$badges_class_file_json; //local
			$badges_class_id 		= APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$badges_class_file_json;  //remote

			// 2. object data
			$obj_be	 	  = new COMMONDB_MODULE("badges_earns", $new_earn_id);

			// 3. prepare replacements
			$badges_class_issuer_badge_course_name = $obj_be->course;
			$badges_class_issuer_badge_course_description = $obj_be->course_desc;
			$badges_class_issuer_badge_course_image = BADGES_ASSERTION_BADGE_ISSUER_PATH_REMOTE_IMAGE.$unique_badge_uid;
			$badges_class_issuer_badge_course_url = $obj_be->course_url;
			$badges_class_issuer =BADGES_CLASS_ISSUER;
			$arr_params_badgeclas	= array(
					"BADGES_CLASS_CONTEXT" => BADGES_ASSERTION_CONTEXT,
					"BADGES_CLASS_TYPE" => BADGES_CLASS_TYPE,
					"BADGES_CLASS_ID" => $badges_class_id,
					"BADGES_CLASS_ISSUER_BADGE_COURSE_NAME" => $badges_class_issuer_badge_course_name,
					"BADGES_CLASS_ISSUER_BADGE_COURSE_DESCRIPTION" => $badges_class_issuer_badge_course_description,
					"BADGES_CLASS_ISSUER_BADGE_COURSE_IMAGE" => $badges_class_issuer_badge_course_image,
					"BADGES_CLASS_ISSUER_BADGE_COURSE_URL" => $badges_class_issuer_badge_course_url,
					"BADGES_CLASS_ISSUER" => $badges_class_issuer
			);

			// 4. doing replacements
			$contents_json_badgesclass = file_get_contents(APP_BADGES_TEMPLATE_BADGE_CLASS, FILE_USE_INCLUDE_PATH );
			if ( $contents_json_badgesclass !='' && count($arr_params_badgeclas)>0  ) {
				foreach ( $arr_params_badgeclas AS $key=>$val )
				{
					$contents_json_badgesclass = str_replace("%$key%","$val",$contents_json_badgesclass );
				}
			}

			// 5. write the new file
			if ( $contents_json_badgesclass!='' )
			{
				@file_put_contents( $badges_class_file_path , $contents_json_badgesclass );
			}

			// 6. validate json results
			$is_valid_file = is_valid_file_json($badges_class_file_path);
			return ( $is_valid_file == 0 ) ? 0 : 1;
		}
		return 0;
	}

	/**
	 * Create OBI comp file
	 * BadgeAssertion json
	 *
	 *  Notes from OBI specs:
	 *   Assertions are representations of an awarded badge,
	 *   used to share information about a badge belonging to one earner.
	 *	 id = URL -> unique - online path to 'assertion.json' file
	 *	 uid = URL -> unique - online path to 'badge_class.json' file
	 *
	 *  Important:
	 *    In order to create a badge_assertion this files are required
	 *    - badgeclass json file for this assertion
	 *    - badge issuer json file
	 *
	 * @param int $earned_badge_id
	 * @return int as boolean (1: true, 0:false)
	 */
	 public static function create_badgeassertion_json($earned_badge_id='0'){

		$new_earn_id 		= IBL_OPENBADGES::check_earned_badge_id($earned_badge_id);
		$check_template 	= ( file_exists( APP_BADGES_TEMPLATE_BADGE_ASSERTION ) )  ? 1 : 0;
		$check_issuer_json	= ( file_exists( APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".BADGES_ISSUER_INSTITUTION_FILE_ID ) )  ? 1 : 0;

		if ( $new_earn_id>0 && $check_template ==1 && $check_issuer_json==1 )
		{
			// 0. unique id : earn_badge_id
			$unique_badge_uid = get_crypted_id($new_earn_id);

			// 1. check badgeclass file
			$badges_class_file_path = APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".$unique_badge_uid.BADGES_CLASS_PREFIX_JSON_FILES; //local
			$badges_class_id 		= APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$unique_badge_uid.BADGES_CLASS_PREFIX_JSON_FILES; //remote

			$check_bclass_json	= ( file_exists( $badges_class_file_path ) )  ? 1 : 0;

			if ( $check_bclass_json == 1 )
			{
				// 2. setting up paths
				$badges_assertion_file_json = $unique_badge_uid.BADGES_ASSERTION_PREFIX_JSON_FILES;
				$badges_assertion_file_path = APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".$badges_assertion_file_json; //local
				$badges_assertion_id  = APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$badges_assertion_file_json; //remote

				// 3. object data
				$obj_be	 	  = new COMMONDB_MODULE("badges_earns", $new_earn_id);

				// 4. prepare replacements
				$badges_assertion_uid = "$unique_badge_uid";
				$badges_assertion_identity_salted = (defined('BADGES_ASSERTION_HASHED') && BADGES_ASSERTION_HASHED=='true' && defined('BADGES_ASSERTION_SALT') && BADGES_ASSERTION_SALT!='' ) ? hashEmailAddress($email_user=$obj_be->earn_email, $salt=BADGES_ASSERTION_SALT) : "";
				$badges_class_id = "$badges_class_id"; //url - end point of badgeclass file
				$badges_class_issuedon_date = date( strtotime($obj_be->date_created) ); //ISO 8601 date or a standard 10-digit Unix timestamp

				//TO-DO backed image
				//URL of an image representing this user’s achievement. This must be a PNG or SVG image, and should be prepared via the Baking specification.
				$badges_assertion_image ="";

				$badges_class_evidence = COMMONDB_MODULE::get_selected_value("badges_earns_params", "content", "WHERE earn_id='$new_earn_id'");
				$arr_params_assertion	= array(
						"BADGES_ASSERTION_CONTEXT" => BADGES_ASSERTION_CONTEXT,
						"BADGES_ASSERTION_ID" => $badges_assertion_id,
						"BADGES_ASSERTION_TYPE" => BADGES_ASSERTION_TYPE,
						"BADGES_ASSERTION_UID" => $badges_assertion_uid,
						"BADGES_ASSERTION_IDENTITY_TYPE" => BADGES_ASSERTION_IDENTITY_TYPE,
						"BADGES_ASSERTION_HASHED" => BADGES_ASSERTION_HASHED,
						"BADGES_ASSERTION_SALT" => BADGES_ASSERTION_SALT,
						"BADGES_ASSERTION_IDENTITY_SALTED" => $badges_assertion_identity_salted,
						"BADGES_CLASS_ID" => $badges_class_id,
						"BADGES_ASSERTION_VERIFY_METHOD" => BADGES_ASSERTION_VERIFY_METHOD,
						"BADGES_ASSERTION_ID" => $badges_assertion_id,
						"BADGES_CLASS_ISSUEDON_DATE" => $badges_class_issuedon_date,
						"BADGES_ASSERTION_IMAGE" => $badges_assertion_image,
						"BADGES_CLASS_EVIDENCE" => $badges_class_evidence,
				);

				// 5. doing replacements
				$contents_json_assertion = file_get_contents(APP_BADGES_TEMPLATE_BADGE_ASSERTION, FILE_USE_INCLUDE_PATH );
				if ( $contents_json_assertion !='' && count($arr_params_assertion)>0  ) {
					foreach ( $arr_params_assertion AS $key=>$val ) {
						$contents_json_assertion = str_replace("%$key%","$val",$contents_json_assertion );
					}
				}

				// 6. write the new file
				if ( $contents_json_assertion!='' )
				{
					@file_put_contents( $badges_assertion_file_path , $contents_json_assertion );
				}

				// 7. validate json results
				$is_valid_file = is_valid_file_json($badges_assertion_file_path);
				return ( $is_valid_file == 0 ) ? 0 : 1;
			}
		}
		return 0;
	}


	/**
	 * Destroys object itself
	 * @return void;
	 */
	public function __destruct()
	{
		$this->destroy();
	}

}
