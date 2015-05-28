<?php include 'config.php';?>
<?php 
	$file_id = ( isset($_GET["bgid"]) && $_GET["bgid"]!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_GET["bgid"]) : '';
	if ($file_id>0) {
		
		$arr_file = show_file_blob($file_id,$type='issuer');
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