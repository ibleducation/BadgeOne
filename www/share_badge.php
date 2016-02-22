<?php include("config.php"); ?>
<?php 
$request_uri = ( isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]!='' ) ? explode("/", $_SERVER["REQUEST_URI"]) : "";

if ( is_array($request_uri) && $request_uri!='' ) {
	
	$i = 0; $index = 0;
	foreach ($request_uri AS $request_val ) {
		if ( $request_val == "share_badge"  ) 
		{
			$index = $i;
		}
		$i +=1;
	}
}

$user_id 			= ( isset($request_uri[$index+1]) && is_numeric($request_uri[$index+1]) && $request_uri[$index+1]>0) ? $request_uri[$index+1] : 0;
$badge_crypted_id 	= ( isset($request_uri[$index+2]) && strlen($request_uri[$index+2])>0  && $request_uri[$index+2]!='' ) ? $request_uri[$index+2] : "";
$external 			= ( isset($request_uri[$index+3]) && strlen($request_uri[$index+3])>0  && $request_uri[$index+3]!='' ) ? $request_uri[$index+3] : "";

/* GET
$user_id  = (isset($_GET["u"]) && is_numeric($_GET["u"]) && strlen($_GET["u"])<12  && $_GET["u"]>0 ) ? $_GET["u"] : 0;
$badge_crypted_id = (isset($_GET["b"]) && strlen($_GET["b"])>0  && $_GET["b"]!='' ) ? $_GET["b"] : "";
$external = (isset($_GET["ext"]) && strlen($_GET["ext"])>0  && $_GET["ext"]!='' ) ? $_GET["ext"] : "";
*/

if ( $user_id>0 && $badge_crypted_id!='' && $external=='0') {
	switch ( $external ) 
	{
		default:
			//badges_earns on this server
			$badge_id = COMMONDB_MODULE::decrypt_id("badges_earns", $badge_crypted_id);
			$badge_id = COMMONDB_MODULE::get_selected_value("badges_earns", "earn_id", "WHERE earn_id='$badge_id' AND user_id='$user_id' AND deleted='0' AND enabled='1' AND show_public='1'");
			$arr_badge = ($badge_id>0) ? COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns",
					"earn_id AS item_id, user_id, institution, course AS badge_name, course_desc AS badge_description, badge_img_name AS badge_image, '' AS badge_image_url,
					date_created AS issued_on, '' AS assertion_evidence, '' AS badge_criteria, '' AS imported_from, DATE_FORMAT(date_created,'%Y%m%d%H%i%s') AS orderdate",
					"WHERE earn_id='$badge_id'","") : array();
			
			
			$badge_institution  = (isset($arr_badge[0]["institution"])) ?  $arr_badge[0]["institution"] : "";
			$badge_name		 	= (isset($arr_badge[0]["badge_name"])) ? $arr_badge[0]["badge_name"] :"";
			$badge_description	= (isset($arr_badge[0]["badge_description"])) ? $arr_badge[0]["badge_description"] : "";
			$badge_image		= (isset($arr_badge[0]["badge_image"])) ? $arr_badge[0]["badge_image"] : "";
			
			$show_badge_img    =  ( $badge_image!='') ? SERVER_HTTP_HOST."/"."fileearn.php?bgid=$badge_crypted_id&amp;".NOCACHE : "";
				
		break;
		
	}
}

if ($badge_id > 0 && count($arr_badge)>0 && $show_badge_img!='') {
	$site_name 			= "IBL BadgeOne";
	$this_url			= SERVER_HTTP_HOST."/share_badge/$user_id/$badge_crypted_id/$external/&title=$badge_name&summary=$badge_description";
?>
<!DOCTYPE html>
<html lang="en-US" prefix="og: http://ogp.me/ns#">
<head>
	<meta charset="UTF-8" />
    <!-- Open Graph tags -->
	<meta name="robots" content="noodp"/>
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article"/>
	<meta property="og:title" content="<?php echo $badge_name?>"/>
	<meta property="og:description" content="<?php echo $badge_description?>"/>
	<meta property="og:url" content="<?php echo $this_url?>"/>
	<meta property="og:image" content="<?php echo $show_badge_img?>" />
	<meta itemprop="image" content="<?php echo $show_badge_img?>" />
</head>
<body>
<img src="<?php echo $show_badge_img?>"> 
</body>
</html>
<?php } else { exit; } ?>