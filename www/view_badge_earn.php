<?php
include("config.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php

    include("head.php");

    ?>

</head>
<body>

<div id="head">
    <div id="menu">

        <?php

        include("menu.php");

        ?>

    </div>
</div>

<div id="main">

    <div id='bread'>

        <a href='/'>Home</a> > View Badge Earn

    </div>

    <div class='page_header'>

        View Badge Earn

    </div>

	<!-- content -->
	<?php 
	$user_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
	$earn_id 		= ( isset($_GET["badge_id"]) && $_GET["badge_id"]!="") ? COMMONDB_MODULE::decrypt_id("badges_earns", $_GET["badge_id"]) : '';
	$allow_preview	= 1; //always public : used by api and panel
	
	$obj_bg 	= new COMMONDB_MODULE("badges_earns", $earn_id);
		$cryted_id 		= $obj_bg->crypted_id;
		$user_id  		= $obj_bg->user_id;
		$institution  	= $obj_bg->institution;
		$course  		= $obj_bg->course;
		$course_desc	= $obj_bg->course_desc;
		$enabled		= $obj_bg->enabled;
		$published		= $obj_bg->published;
		$deleted		= $obj_bg->deleted;
		
		$earn_fullname	= $obj_bg->earn_fullname;
		$earn_email		= $obj_bg->earn_email;
		
		//badge
		$badge_img_name	= $obj_bg->badge_img_name;
		$show_badge_img =  ( $earn_id>0 && $badge_img_name!='') ? "fileearn.php?bgid=$cryted_id&amp;".NOCACHE : "";
		
		//get params
		$arr_params			= ($earn_id>0) ? COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns_params","param_id,label,content","WHERE earn_id='$earn_id' AND deleted='0' ORDER BY param_id","") : array();
		$count_params 		= count($arr_params);
		$url_download_badge_image = BADGES_IMAGE_GENERATOR_API_URL.APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$cryted_id.BADGES_ASSERTION_PREFIX_JSON_FILES;
	?>
	<?php if ( $earn_id> 0 && $allow_preview==1) {?>
	<div class="container">
	<div class="col-lg-12"><a href="<?php echo $url_download_badge_image?>" target="_blank" class="btn btn-info btn-lg pull-right"><i class="fa fa-download"></i> Download this Badge</a></div>
	<div style="max-width:600px;">
		<div class="col-lg-12">
			<div>
				<div><?php if ( $badge_img_name !='') { ?><img src='<?php echo $show_badge_img?>' class="pull-left" style="padding:10px; padding-top:0;"><?php }?></div>
				<div>
					<p>Name: <strong><?php echo $earn_fullname?></strong></p>
					<p>Course: <strong><?php echo $course?></strong></p>
					<p><small><em><?php echo $course_desc?></em></small></p>
					<?php if ( $count_params > 0 ) { ?>
					<p>
						<strong>Evidences</strong><br>
						<?php for ($i = 0; $i < $count_params; $i++) { ?>
							<p><strong><?php echo $arr_params[$i]['label']?></strong>: <?php echo $arr_params[$i]['content']?></p>
						<?php }?>
					</p>
					<?php }?>
				</div>
			</div>
		</div>
	</div></div>	
	<?php }?>    
	<!-- /content -->

</div>

<div id='footer' class='wrapper'>

    <?php

    include("footer.php");

    ?>

</div>
<div id='copyright' class='wrapper'>

    <?php

    include("copyright.php");

    ?>

</div>

</body>
</html>


