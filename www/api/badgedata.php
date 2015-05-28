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
	(array)	$arr_res    = array ( 'success'=>'0' );
	(array) $arr_params = array ( );

	//retry data from post
	$datatype   = ( isset($_POST['datatype']) ) ? $_POST['datatype'] : '';
	$bgid       = ( isset($_POST['bgid']) AND is_numeric($_POST['bgid']) AND $_POST['bgid']>0 ) ? $_POST['bgid'] : 0;

	if ( $datatype!='' && $bgid>0 ) 
	{
		switch ($datatype) 
		{
			case "info":
				try {
		            $db = new PDO(PDO_DSN, DB_USER, DB_PASS, $arr_pdo_attrs );
				    $q = "SELECT institution, course, course_desc FROM badges_issuers WHERE badge_id='$bgid' AND deleted='0' AND enabled='1' LIMIT 1";
		            $stmt = $db->prepare($q);
				    $stmt->execute();
		            if( $stmt->rowCount() > 0 )
				    {
						while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
							$arr_res = array( 'success'=>'1', 'bgid'=> (int) $bgid, 'institution'=>$rs->institution, 'course'=>$rs->course, 'course_desc'=>$rs->course_desc, 'bgimage'=>PATH_IMG_BADGE.$bgid );
						}	
					}
				} catch (PDOException $e) {
					$arr_res = array ( 'success'=>'0' );
				}
			break;

			case "params":
				//default
				$arr_params[] = array( 'param_id'=>'0' );
				try {
		            $db = new PDO(PDO_DSN, DB_USER, DB_PASS, $arr_pdo_attrs );
				    $q = "SELECT p.param_id, p.label AS label, p.description AS label_desc, p.type AS type, p.required AS required , b.enabled AS enabled, b.published AS published
					FROM badges_issuers_params p, badges_issuers b 
					WHERE b.badge_id='$bgid' AND b.badge_id=p.badge_id AND b.deleted='0' AND b.enabled='1' AND p.enabled='1' AND p.deleted='0'";
		            $stmt = $db->prepare($q);
				    $stmt->execute();
		            if( $stmt->rowCount() > 0 )
				    {
						while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
							$arr_params[] = array( 'param_id'=>$rs->param_id,'label'=>$rs->label, 'description'=>$rs->label_desc, 'type'=>$rs->type, 'required'=>$rs->required );
						}
					} else {
						$arr_params[] = array( 'param_id'=>'0' );
					}
					
					$arr_res = array ( 'success'=>'1', 'params'=>$arr_params );
					
				} catch (PDOException $e) {
					$arr_res = array ( 'success'=>'0', 'params'=>$arr_params );
				}
			break;

			default:
			break;
		}
	}
	
	echo json_encode ( $arr_res );
}
?>