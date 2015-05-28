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
	
	        <a href='/'>Home</a> > View Badge Issuer
	
	    </div>
	
	    <div class='page_header'>
	
	        View Badge Issuer
	
	    </div>
	
		<!-- content -->
		<?php 
		//user data
		$user_id	= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$user_profile 	= ( isset($logged_profile) && $logged_profile!='' ) ? $logged_profile : '';
		//oauth data
		$get_auth_client_id  = ($user_id>0) ? COMMONDB_MODULE::get_selected_value("oauth_clients", "client_id", "WHERE user_id='$user_id'") : "";
		$get_auth_secret_key = ($user_id>0) ? COMMONDB_MODULE::get_selected_value("oauth_clients", "client_secret", "WHERE user_id='$user_id'") :"";	
		//badge and controls
		$badge_id 	= ( isset($_GET["badge_id"]) && $_GET["badge_id"]!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_GET["badge_id"]) : '';
		$filter_badges  = ($user_id>0 && $user_profile=='admin') ? "" : "AND user_id='$user_id'";
		$check_b_id  	= COMMONDB_MODULE::get_selected_value("badges_issuers", "badge_id", "WHERE badge_id='$badge_id' $filter_badges");
		$allow_preview	= ($user_id>0 && $user_profile!='' && $user_profile!='general' && $badge_id>0 && $check_b_id==$badge_id )  ?  1 : 0; //not public - just users privileged
		
		$obj_bg 	= new COMMONDB_MODULE("badges_issuers", $badge_id);
		$cryted_id 	= ( $badge_id>0 ) ? $obj_bg->crypted_id : "";
		$user_id  	= ( $badge_id>0 ) ? $obj_bg->user_id : $user_id;
		$institution  	= $obj_bg->institution;
		$course  	= $obj_bg->course;
		$course_desc	= $obj_bg->course_desc;
		$enabled	= $obj_bg->enabled;
		$published	= $obj_bg->published;
		$deleted	= $obj_bg->deleted;
		
		//badge
		$badge_img_name	= $obj_bg->badge_img_name;
		$show_badge_img = ( $badge_id>0 && $badge_img_name!='') ? "fileissuer.php?bgid=$cryted_id&amp;".NOCACHE : "";
		
		//get params
		$arr_params	= ($badge_id>0) ? COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers_params","param_id,label,description","WHERE badge_id='$badge_id' AND deleted='0' ORDER BY param_id","") : array();
		$count_params 	= count($arr_params);
		?>
		
		<?php if ( $badge_id> 0 && $allow_preview==1) {?>
		<div class="container">
		
			<div class="col-lg-12"><br></div>
		
			<!-- badge preview -->
			<div class="col-lg-7">
				<!-- badge data -->
				<div class="col-lg-12">
					<div><?php if ( $badge_img_name !='') { ?><img src='<?php echo $show_badge_img?>' class="pull-left" style="padding:10px; padding-top:0;"><?php }?></div>
					<div>
						<p>Course: <strong><?php echo $course?></strong></p>
						<p><small><em><?php echo $course_desc?></em></small></p>
						<?php if($count_params>0) { ?>
						<p>
							<strong>Evidences</strong><br>
							<?php for ($i = 0; $i < $count_params; $i++) { ?>
								<p><strong><?php echo $arr_params[$i]['label']?></strong>: <?php echo $arr_params[$i]['description']?></p>
							<?php }?>
						</p>
						<?php }?>
					</div>
				</div>
				<!-- /badge data -->
			</div>
			<!-- /badge preview -->
			
			<!-- api provider data -->
			<div class="col-lg-5">
				<!--  badge provider data -->
				<div class="col-lg-12">		
					<?php if ( $user_profile !='general' ) { ?>
						
						<p>You will need this data in order to connect the API</p>
						<?php if ($get_auth_client_id!='' && $get_auth_secret_key!='') { ?>
							<div class="form-group">
								<label>This Badge provider user-key (user)</label>
								<p class="alert alert-warning"><?php echo $get_auth_client_id?></p>
							</div>
							<div class="form-group">
								<label>This Badge provider account secret-key (password)</label>
								<p class="alert alert-warning"><?php echo $get_auth_secret_key?></p>
							</div>
							<div class="form-group">
								<label>This Badge ID</label>
								<p class="alert alert-warning"><?php echo $badge_id?></p>
							</div>						
						<?php } else { ?>
						<div class="form-group"><p class="alert alert-danger">You can not use this functionality</p></div>
						<?php }  ?>
					<?php }  ?>
				</div>
				<!--  badge provider data -->
			</div>
			<!-- /api provider data -->
			
		</div>
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


