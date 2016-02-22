<?php
/**
 * Class to interact with mozilla backpack data
 * More info : 
 *   https://github.com/mozilla/openbadges-backpack/wiki
 *   https://github.com/mozilla/openbadges-backpack/wiki/Using-the-Displayer-API
 */

class IBL_MOZILLABACKPACK
{
	/**
	 * IBL_MOZILLABACKPACK constructor
	 * @return object IBL_OPENBADGES
	 */
	public function __construct()
	{

	}

	/**
	 * Fetch userId displayer
	 * 
	 * @param string $email
	 * @return int
	 */
	public static function convert_email_to_diplayer_id($imported_email=''){
		$displayer_id = 0;
		if ( $imported_email != '' ) {

			$url = "https://backpack.openbadges.org/displayer/convert/email";

			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, "email=".urlencode($imported_email)."");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_HEADER, false);
			$response = trim(curl_exec($ch));
			curl_close($ch);
			
			//json decode the response
			if ( $response !='' ) 
			{
				$json = json_decode($response);
				//check if it's a valid response 
				if( is_object($json ) AND isset( $json->userId ) )
				{
					$displayer_id = ( $json->userId !='' ) ?  $json->userId : $displayer_id;
				}
			}
		}
		return $displayer_id;
	}
	
	/**
	 * Fetch Collections displayer
	 * 
	 * @param int $displayer_id
	 * @param string $email (optional) 
	 * @return array $collections
	 */
	public static function fetch_collections($displayer_id=0,$imported_email=''){
		
		$collections = array();

		//convert email if displayer_id = 0		
		$displayer_id = ($displayer_id == 0 && $imported_email!='')  ? IBL_MOZILLABACKPACK::convert_email_to_diplayer_id($imported_email) : $displayer_id;
		
		if ( $displayer_id > 0  ) {
	
			$url = "https://backpack.openbadges.org/displayer/".$displayer_id."/groups.json";
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_HEADER, false);
			$response = trim(curl_exec($ch));
			curl_close($ch);			
			
			//json decode the response
			if ( $response !='' )
			{
				$json = json_decode($response);
				//check if it's a valid response AND isset( $json->userId )
				if( is_object($json ) && isset( $json->userId ) && isset( $json->groups ) )
				{
					if ( count($json->groups) >0 ) {
						foreach ( $json->groups AS $group ){
							if ( is_object($group) && isset($group->groupId) && $group->groupId!='' && isset($group->badges) && $group->badges >0 ){
								$collections[$displayer_id][$group->groupId] = array("name"=>$group->name,"badges"=>$group->badges);
							}
						}
					}
				}
			}
		}
		return $collections;
	}	
	
	/**
	 * Fectch Mozilla OpenBadges
	 *
	 * @param array $collections (optional)
	 * @param int $displayer_id
	 * @param string $email (optional)
	 * @return array $badges
	 */
	public static function fetch_badges( $collections=array(), $displayer_id='', $imported_from='', $imported_email='') {
		$res_import_badges = array();
		$get_arr_badges = array();
		$error = array();
	
		//user app data
		$user_id    = ( isset($_COOKIE["UID"]) && $_COOKIE["UID"]>0 ) ? $_COOKIE["UID"] : 0;
		$user_email = ( $user_id > 0 ) ? COMMONDB_MODULE::get_value("users", "email", $user_id) : "";
		$imported_email  = ( $imported_email =='') ? $user_email : $imported_email;
		$imported_user_id  = ( $displayer_id !='') ? $displayer_id : IBL_MOZILLABACKPACK::convert_email_to_diplayer_id($imported_email);
		$imported_from = ($imported_from=='') ? "MozillaBackPack" : "";
		
		if ( $user_id >0 && $user_email!='' && $imported_user_id!='' && $imported_email!='' && $imported_from!='' ) 
		{
			$collections = ( count($collections) == 0 && ( $displayer_id!='' || $imported_email=='' )  ) ? IBL_MOZILLABACKPACK::fetch_collections($displayer_id,$imported_email) : $collections;
			
			if ( count($collections)> 0 ) {
				foreach ( $collections AS $coll_user_id=>$groups  )
				{
					$displayer_id =  $coll_user_id;
					foreach ( $groups AS $group_id=>$group){
						$url = "https://backpack.openbadges.org/displayer/" . $displayer_id . "/group/" . $group_id. ".json";
						$get_arr_badges = IBL_MOZILLABACKPACK::get_group_badges_data($url,$user_id,$user_email,$imported_user_id,$imported_from,$imported_email);
						
						if ( isset($get_arr_badges["error"]) ) {
							$error = array("error"=>"curl");
						} else {
							$res_import_badges = array_merge($res_import_badges,$get_arr_badges);
						}
						
					}
				}
			}
		}
		return (count($error)>0) ? $error : $res_import_badges;
	}

	/**
	 * Check if exists the Badge
	 * 
	 * @param string $imported_from
	 * @param string $assertion_id
	 */
	public static function check_exists_imported_badge ($user_id=0, $imported_from='',$assertion_id='') {
		return COMMONDB_MODULE::get_selected_value("badges_earns_imported", "imported_id", "WHERE user_id='$user_id' AND imported_from='$imported_from' AND assertion_id='$assertion_id' ");
	}
	
	/**
	 * Import Mozilla OpenBadges
	 *
	 * @param array $collections (optional)
	 * @param int $displayer_id
	 * @param string $email (optional)
	 * @return array $badges
	 */
	public static function import_badges( $arr_badges=array(), $user_id=0, $imported_user_id=0, $imported_from='',$imported_email='' ) {

		$res   = "error";
		$error = ( count($arr_badges)>0 && isset($arr_badges["error"]) ) ? 1 : 0 ;
		$imported_from = ($imported_from=='') ? "MozillaBackPack" : "";
		
		if ( $error ==0 && $imported_user_id!='' && $imported_from!='' ) 
		{
			switch ($imported_from) 
			{
				case "MozillaBackPack":

					if ( count($arr_badges)> 0 ) 
					{
						foreach ( $arr_badges AS $item ) 
						{
							$arr_new_badges[] = $item["assertion_id"];  
						
							//check imported or created on this server
							$imported_id 	= IBL_MOZILLABACKPACK::check_exists_imported_badge ($user_id, $imported_from, $item["assertion_id"] );
							$check_file_earned_on_this_server = APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".$item["assertion_uid"].BADGES_ASSERTION_PREFIX_JSON_FILES;
							
							if ( $imported_id > 0 || file_exists($check_file_earned_on_this_server ) ) {
								//remove duplicates
								if ( $imported_id > 0  && file_exists($check_file_earned_on_this_server ) ) {
									$qdelb = "DELETE FROM badges_earns_imported WHERE user_id='$user_id' AND imported_from='$imported_from' AND imported_id='$imported_id'";
									COMMONDB_MODULE::launch_direct_system_query($qdelb);
								} 
							} else {
								//does not exists
								$q = "INSERT INTO badges_earns_imported 
										(
										user_id, 
										user_email,
										imported_from,
										imported_email,
										imported_user_id,
										assertion_lastValidated,
										assertion_hostedUrl,
										assertion_id,
										assertion_uid,
										assertion_type,
										assertion_recipient,
										assertion_evidence,
										assertion_image,
										assertion_orig_issued_on,
										assertion_issued_on,
										assertion_orig_expires,
										assertion_expires,
										badge_id,
										badge_type,
										badge_name,
										badge_image,
										badge_imageUrl,
										badge_description,
										badge_criteria,
										badge_issuer,
										issuer_institution_id,
										issuer_institution_type,
										issuer_institution_name,
										issuer_institution_url,
										issuer_institution_email,
										date_created,
										created_by,
										lastupdate_by
										) 
										VALUES 
										(
										'".$item["user_id"]."', 
										'".$item["user_email"]."',
										'".$item["imported_from"]."',
										'".$item["imported_email"]."',
										'".$item["imported_user_id"]."',
										'".$item["assertion_lastValidated"]."',
										'".$item["assertion_hostedUrl"]."',
										'".$item["assertion_id"]."',
										'".$item["assertion_uid"]."',
										'".$item["assertion_type"]."',
										'".$item["assertion_recipient"]."',
										'".$item["assertion_evidence"]."',
										'".$item["assertion_image"]."',
										'".$item["assertion_orig_issued_on"]."',
										'".$item["assertion_issued_on"]."',
										'".$item["assertion_orig_expires"]."',
										'".$item["assertion_expires"]."',												
										'".$item["badge_id"]."',
										'".$item["badge_type"]."',
										'".$item["badge_name"]."',
										'".$item["badge_image"]."',
										'".$item["badge_imageUrl"]."',
										'".$item["badge_description"]."',
										'".$item["badge_criteria"]."',
										'".$item["badge_issuer"]."',
										'".$item["issuer_institution_id"]."',
										'".$item["issuer_institution_type"]."',
										'".$item["issuer_institution_name"]."',
										'".$item["issuer_institution_url"]."',
										'".$item["issuer_institution_email"]."',
										NOW(),
										'".$item["user_id"]."',
										'".$item["user_id"]."'
										
										)";
								$exec_query = COMMONDB_MODULE::launch_direct_system_query($q);
							}
						}

						//sync non imported badges (in remote server could be deleted or unpublished)	
						$arr_db_badges = COMMONDB_MODULE::get_list("badges_earns_imported","WHERE user_id='$user_id' AND imported_from='$imported_from'","imported_id","assertion_id");
						$arr_diff_to_delete = array_diff($arr_db_badges, $arr_new_badges);

						if ( count($arr_diff_to_delete) > 0  ) {
							foreach ( $arr_diff_to_delete AS $k=>$del_assertion_id) {
								$qdel = "DELETE FROM badges_earns_imported WHERE user_id='$user_id' AND imported_from='$imported_from' AND assertion_id='$del_assertion_id'";
								COMMONDB_MODULE::launch_direct_system_query($qdel);
							}
						} 
						$res =  __("Synchronization completed");
					} else {
						//delete all
						COMMONDB_MODULE::delete_multiple_values("badges_earns_imported","WHERE user_id='$user_id' AND imported_from='$imported_from'");
						$res =  __("Synchronization completed");
					}
				break;
			}
		}
		return $res;
	}
	
	/**
	 * Fetch mozilla open badges by group
	 * 
	 * @param string $url_group
	 */
	public static function get_group_badges_data($url_group='', $user_id=0, $user_email='', $imported_user_id=0, $imported_from='',$imported_email='') {

		$html= "";
		$arr_imported_badges = array();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_group);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		//error control
		if( curl_exec($ch) === false )
		{
			$arr_imported_badges = array("error"=>"curl");
		} else 	{
			$response = trim(curl_exec($ch));
		}
		curl_close($ch);
	
		$json = ( isset($response) ) ? json_decode($response) : "";
		
		if( is_object($json ) AND isset( $json->badges ) )
		{
			//create array data
			foreach( $json->badges as $badge )
			{
				//validation - hosted
				$lastValidated = ( isset($badge->lastValidated) ) ? $badge->lastValidated : "";
				$hostedUrl = ( isset($badge->hostedUrl) ) ?  $badge->hostedUrl : "";
				
				//assertion - req
				$assertion_uid = $badge->assertion->uid; //Unique Identifier. Expected to be locally unique on a per-origin basis, not globally unique
				$assertion_recipient = $badge->assertion->recipient;
				$assertion_orig_issued_on = $badge->assertion->issued_on; //DateTime - Either an ISO 8601 date or a standard 10-digit Unix timestamp
				$assertion_issued_on = ($assertion_orig_issued_on!='') ? IBL_MOZILLABACKPACK::convert_date_issued_on( $assertion_orig_issued_on ) : $assertion_orig_issued_on;

				//assertion - opt
				$assertion_id = ( isset($badge->assertion->id) ) ? $badge->assertion->id : "";
				$assertion_type = ( isset($badge->assertion->type) ) ? $badge->assertion->type : "";
				$assertion_evidence = ( isset($badge->assertion->evidence) ) ? $badge->assertion->evidence : "";
				$assertion_image = $badge->assertion->image;  //opt
				$assertion_orig_expires = ( isset($badge->assertion->expires) ) ? $badge->assertion->expires : "";		
				$assertion_expires = ($assertion_orig_expires!='') ? IBL_MOZILLABACKPACK::convert_date_issued_on( $assertion_orig_expires ) : $assertion_orig_expires;
				
				//badge -  req
				$badge_name = $badge->assertion->badge->name;
				$badge_description = $badge->assertion->badge->description;
				$badge_image = $badge->assertion->badge->image;
				$badge_criteria = $badge->assertion->badge->criteria;
				$badge_issuer	= (isset($badge->assertion->badge->issuer)) ? json_encode($badge->assertion->badge->issuer) : ""; //object
				
				//badge -  opt
				$badge_id = $badge->assertion->badge->id;
				$badge_type = $badge->assertion->badge->type;
				$badge_imageUrl = $badge->imageUrl; //opt

				//issuer -req
				$institution_name = $badge->assertion->badge->issuer->name;
				$institution_url = $badge->assertion->badge->issuer->url;
				
				//issuer -opt
				$institution_id = $badge->assertion->badge->issuer->id;
				$institution_type = $badge->assertion->badge->issuer->type;
				$institution_email = $badge->assertion->badge->issuer->email;
				
				//order to show data
				$show_badge_image = ($badge_image!='') ? $badge_image : ( ($badge_imageUrl!='') ? $badge_imageUrl : $assertion_image  ) ;
				$show_badge_evidence = ($assertion_evidence!='') ? $assertion_evidence : $badge_criteria;
				
				
				//user data
				$arr_imported_badges[$assertion_id]['user_id'] = $user_id;
				$arr_imported_badges[$assertion_id]['user_email'] = $user_email;
				$arr_imported_badges[$assertion_id]['imported_from'] = $imported_from;
				$arr_imported_badges[$assertion_id]['imported_email'] = $imported_email;
				$arr_imported_badges[$assertion_id]['imported_user_id'] = $imported_user_id;

				//retrieved data from import
				$arr_imported_badges[$assertion_id]['assertion_lastValidated'] = $lastValidated;
				$arr_imported_badges[$assertion_id]['assertion_hostedUrl'] = $hostedUrl;
				$arr_imported_badges[$assertion_id]['assertion_id'] = $assertion_id;
				$arr_imported_badges[$assertion_id]['assertion_uid'] = $assertion_uid;
				$arr_imported_badges[$assertion_id]['assertion_type'] = $assertion_type;
				$arr_imported_badges[$assertion_id]['assertion_recipient'] = $assertion_recipient;
				$arr_imported_badges[$assertion_id]['assertion_evidence'] = $assertion_evidence;
				$arr_imported_badges[$assertion_id]['assertion_image'] = $assertion_image;
				$arr_imported_badges[$assertion_id]['assertion_orig_issued_on'] = $assertion_orig_issued_on;
				$arr_imported_badges[$assertion_id]['assertion_issued_on'] = $assertion_issued_on;
				$arr_imported_badges[$assertion_id]['assertion_orig_expires'] = $assertion_orig_expires;
				$arr_imported_badges[$assertion_id]['assertion_expires'] = $assertion_expires;
				$arr_imported_badges[$assertion_id]['badge_id'] = $badge_id;
				$arr_imported_badges[$assertion_id]['badge_type'] = $badge_type;
				$arr_imported_badges[$assertion_id]['badge_name'] = $badge_name;
				$arr_imported_badges[$assertion_id]['badge_image'] = $badge_image;
				$arr_imported_badges[$assertion_id]['badge_imageUrl'] = $badge_imageUrl;
				$arr_imported_badges[$assertion_id]['badge_description'] = $badge_description;
				$arr_imported_badges[$assertion_id]['badge_criteria'] = $badge_criteria;
				$arr_imported_badges[$assertion_id]['badge_issuer'] = $badge_issuer;
				$arr_imported_badges[$assertion_id]['issuer_institution_id'] = $institution_id;
				$arr_imported_badges[$assertion_id]['issuer_institution_type'] = $institution_type;
				$arr_imported_badges[$assertion_id]['issuer_institution_name'] = $institution_name;
				$arr_imported_badges[$assertion_id]['issuer_institution_url'] = $institution_url;
				$arr_imported_badges[$assertion_id]['issuer_institution_email'] = $institution_email;
			}
		}
		return $arr_imported_badges;
	}	
	
	/**
	 * Convert date issued_on 
	 *  Either an ISO 8601 date or 
	 *  a standard 10-digit Unix timestamp
	 *  
	 * @param string $strdate
	 * @return DateTime
	 */
	public static function convert_date_issued_on( $strdate ) {
		/*
		Date and time expressed according to ISO 8601:
		Date: 	2016-01-11
		Combined date and time in UTC: 	
			2016-01-11T17:31:10+00:00
			2016-01-11T17:31:10Z
			20160111T173110Z
		Week: 	2016-W02
		Date with week number: 	2016-W02-1
		Ordinal date: 	2016-011
		*/
		if ( strlen($strdate)>0 ) {
			if ( preg_match('/^\d+$/', $strdate) && strlen($strdate)==10 )
			{
				//unix timestamp
				date_default_timezone_set("UTC");
				return date('Y-m-d H:i:s', $strdate );
			} else {
				date_default_timezone_set("UTC");
				if ( strlen($strdate)<10 ) {
					return $strdate;
				} else {
					return date("Y-m-d H:i:s", strtotime($strdate));
				}
			}
		}
		return $strdate;
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
?>