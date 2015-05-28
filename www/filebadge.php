<?php include 'config.php';?>
<?php 
	$badge_id = ( isset($_GET["bgid"]) && $_GET["bgid"]!="") ? $_GET["bgid"] : '';
	$earn_id  = ( isset($_GET["eaid"]) && $_GET["eaid"]!="") ? $_GET["eaid"] : '';
	
	if ($badge_id>0 || $earn_id>0 ) {
		
		$file_id 	= ( $badge_id>0 ) ? $badge_id : $earn_id;
		$type_badge = ( $badge_id>0 ) ? "issuer" : "earner";   
		
		$arr_file = show_file_blob($file_id,$type=$type_badge);
		//var_dump($arr_file);
		
		if ( count($arr_file) > 0 ) {
			
			$file_content = $arr_file[$file_id]["badge_img"]; 
			//remove quotes
			$file_content = ( $file_content!='' && substr($file_content,0,1) == "'" ) ? substr($file_content, 1) : $file_content;
			$file_content = ( $file_content!='' && substr($file_content, -1) == "'" ) ? substr($file_content, 0,-1) : $file_content;
				
			$file_type	  = $arr_file[$file_id]["badge_img_type"];
			$file_name	  = $arr_file[$file_id]["badge_img_name"];

			if (!empty($file_content)) {
				// show the image.
				header("Content-type: $file_type");
				echo $file_content;
			}
		}
	}
?>