<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include("head.php"); ?>
</head>
<body>

<div id="head">
    <div id="menu">
        <?php  include("menu.php"); ?>
   </div>
</div>

<div id="private"> <?php  include("a_auth.php"); ?></div>

<div id="main">

    <div id='bread'>

        <a href='/'>Home</a> > My Profile

    </div>

    <div class='page_header'>

        My Profile

    </div>

    <!-- contents -->
    <?php if ( isset($logged_user) && $logged_user >0  && isset($logged_profile) && $logged_profile!='') { ?>    
		<!-- user data -->
		<?php 
		include("events.php");
		$user_id = $logged_user;
		$user_profile = $logged_profile;
		//user data
		$obj_user = new COMMONDB_MODULE("users", $user_id);
		$fullname = $obj_user->name;
		$explode_fullname = explode(" ", $fullname);
		$first_name = ( isset($explode_fullname[0]) ) ? $explode_fullname[0] : '';
		$last_name = ( isset($explode_fullname[0]) ) ? ltrim(substr($fullname, strlen($explode_fullname[0]))) : '';
		$email = $obj_user->email;
		//oauth data
		$get_auth_client_id  = COMMONDB_MODULE::get_selected_value("oauth_clients", "client_id", "WHERE user_id='$user_id'");
		$get_auth_secret_key = COMMONDB_MODULE::get_selected_value("oauth_clients", "client_secret", "WHERE user_id='$user_id'");
		//event
		$event_action = "update_user";
		?>
		
		<?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="row col-lg-12 alert alert-danger" style="color:red; margin-top:10px;">'.$event_errors.'</div>'; }?>
		<?php if ( isset($event_success) && $event_success!="" ) { print '<div class="row col-lg-12  alert alert-success" style="color:green; margin-top:10px;">'.$event_success.'</div>'; }?>
		
		<form action="#" method="post" enctype="multipart/form-data" class="">
			<input type="hidden" name="event" id="event" value="<?php echo $event_action?>">
			<input type="hidden" name="check_user" id="check_user" value="<?php echo get_crypted_id($user_id)?>">

			<!-- main data -->
			<div class="col-lg-6">
				<div class="form-group"><h3>Main User Data (* Required)</h3></div>
				
				<div class="form-group">
					<label for="earn_fullname">Full name</label>
					<input type="text" name="fullname" id="fullname" value="<?php echo $fullname?>" class="form-control" required="required" placeholder="full name" disabled="disabled">
				</div>
				
				<div class="form-group">
					<label for="earn_email">Email</label>
					<input type="email" name="email" id="email" value="<?php echo $email?>" class="form-control" required="required" placeholder="email" disabled="disabled">
				</div>
				
				<div class="form-group">
					<label for="earn_email">Password</label>
					<input type="text" name="new_pass" id="new_pass" value="" class="form-control" placeholder="if you don't pretend to change your password don't fill this">
					<small><em>min <?php echo (defined('APP_USER_MIN_CHARS_PWD') && APP_USER_MIN_CHARS_PWD>0) ? APP_USER_MIN_CHARS_PWD : 4 ?> chars</em></small>
				</div>
				
			</div>
			<!-- /main data -->

			<!-- api provider data -->
			<div class="col-lg-6">
			<?php if ( $user_profile !='general' ) { ?>
				<div class="form-group"><h3>Provider account information</h3></div>
				<p>You will need this data in order to connect the API for edx-platform</p>
				<?php if ($get_auth_client_id!='' && $get_auth_secret_key!='') { ?>
					<div class="form-group">
						<label>Your Badge provider user-key (user)</label>
						<p class="alert alert-warning"><?php echo $get_auth_client_id?></p>
					</div>
					<div class="form-group">
						<label>Your Badge provider account secret-key (password)</label>
						<p class="alert alert-warning"><?php echo $get_auth_secret_key?></p>
					</div>
				<?php } else { ?>
				<div class="form-group"><p class="alert alert-danger">You can not use this functionality</p></div>
				<?php }  ?>
			<?php }  ?>
			</div>
			<!-- /api provider data -->

			<!--actions -->
			<div class="col-lg-12">
				<center><button type="submit" class="btn btn-success">Update data <i class="fa fa-forward"></i></button></center>
			</div>
			<!--/actions -->
		
		</form>
		<!-- /user data -->
		
		<!-- profile data -->
		<?php 
		$obj_profile = COMMONDB_MODULE::get_arr_relations_lists_aliases ('users', 'profile,date_activated,lastupdate,institution,institution_url,institution_image,institution_email', "WHERE id_user='$logged_user'");
		if ( $user_profile !='general' ) {
		?>
		<div class="row col-lg-12"><hr></div>
		<div class="col-lg-12 col-md-12">
			<div class="col-lg-6 col-md-6">
				<h3>Main Institution Information</h3>
				<div class="form-group">
					<label>Institution</label>
					<p class="alert alert-warning"><?php echo $obj_profile[0]['institution']?></p>
				</div>
				<div class="form-group">
					<label>URL Institution</label>
					<p class="alert alert-warning"><?php echo ($obj_profile[0]['institution_url']!='') ? "<a href='".$obj_profile[0]['institution_url']."' target='_blank'>".$obj_profile[0]['institution_url']."</a>":""?></p>
				</div>
				<div class="form-group">
					<label>Email Institution</label>
					<p class="alert alert-warning"><?php echo ($obj_profile[0]['institution_email']!='') ? "<a href='mailto:".$obj_profile[0]['institution_email']."'>".$obj_profile[0]['institution_email']."</a>":""?></p>
				</div>				
			</div>
		
			<div class="col-lg-6 col-md-6">
				<h3>Profile Information</h3>
				<div class="form-group">
					<label>Profile</label>
					<p class="alert alert-warning"><?php echo $obj_profile[0]['profile']?></p>
				</div>
				<div class="form-group">
					<label>Activation Date</label>
					<p class="alert alert-warning"><?php echo $obj_profile[0]['date_activated']?></p>
				</div>
				<div class="form-group">
					<label>Lastupdate</label>
					<p class="alert alert-warning"><?php echo $obj_profile[0]['lastupdate']?></p>
				</div>
			</div>
			
		</div>
		<?php  } ?>
		<!-- profile data -->
		
		<!-- system data -->
		<?php 
		$obj_profile = COMMONDB_MODULE::get_arr_relations_lists_aliases ('users', 'profile,date_activated,lastupdate,institution,institution_url,institution_image,institution_email', "WHERE id_user='$logged_user'");
		if ( $user_profile =='admin' ) {
		?>
		<div class="row col-lg-12"><hr></div>
		<div class="col-lg-12 col-md-12">
			<div class="col-lg-6 col-md-6">
				<h3>System Information</h3>
				
				
				<div class="form-group"><h4>Check directories and required files</h4></div>
				
				<!-- check dirs and files -->
				<table class="table table-condensed small">
		      	<thead><tr><th>Info</th><th>Path</th><th>Exists</th><th>Writable</th></tr></thead>
		      	<tbody class="small">    		
	    		<?php 
		    	$dirsfiles_to_check = array(
		    		//badges
		    		"app_repo" => array('name'=>'General Repository', 'dir'=> APP_GENERAL_REPO."/" , 'perms'=>'' ) ,
		    		"app_repo_issuers" => array('name'=>'Repo for Issuers files', 'dir'=> APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/" , 'perms'=>'' ),
		    		"app_repo_badges" => array('name'=>'Repo for Badges files', 'dir'=> APP_GENERAL_REPO_BADGES_EARN_LOCAL."/" , 'perms'=>'writable' ),
		    		"app_repo_images" => array('name'=>'Repo for Badges images', 'dir'=> APP_GENERAL_REPO_BADGES_IMG_LOCAL."/" , 'perms'=>'writable' ),
		    		"app_repo_revoked" => array('name'=>'Repo for Badges revoked', 'dir'=> APP_GENERAL_REPO_BADGES_REVOKED_LOCAL."/" , 'perms'=>'writable' ),
		    		//templates
		    		"app_repo_templates" => array('name'=>'General Repository Templates', 'dir'=> APP_GENERAL_REPO_TEMPLATES."/" , 'perms'=>'' ),
		    		"app_file_template_issuer" => array('name'=>'Issuer Template', 'dir'=> APP_BADGES_TEMPLATE_BADGE_ISSUER , 'perms'=>'' ),
		    		"app_file_template_asser" => array('name'=>'Assertion Template', 'dir'=> APP_BADGES_TEMPLATE_BADGE_ASSERTION , 'perms'=>'' ),
		    		"app_file_template_badge" => array('name'=>'BadgeClass Template', 'dir'=> APP_BADGES_TEMPLATE_BADGE_CLASS , 'perms'=>'' ),
		    		"app_file_issuer_json" => array('name'=>'Issuer JSON master file', 'dir'=> APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".BADGES_ISSUER_INSTITUTION_FILE_ID , 'perms'=>'' ),
		    	);
		    	
		    	foreach ( $dirsfiles_to_check AS $repo ) 
		    	{
		    		$if_exists 		= ( check_system_dir_files($repo['dir']) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> YES</span>'  : '<span class="label label-danger"><i class="fa fa-times"></i> NO</span>';
		    		$is_writable 	= ( $repo["perms"]=='writable' && check_system_dir_files($repo['dir'],$repo["perms"]) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> YES</span>'  : ( ($repo["perms"]=='writable') ? '<span class="label label-danger"><i class="fa fa-times"></i> NO</span>' : '<span class="label label-warning"> N/A</span>' );
		    		
		    		print '<tr>';
		    			print '<td>'.$repo['name'].'</td>';
		    			print '<td>'.$repo['dir'].'</td>';
		    			print '<td class="tocenter">'.$if_exists.'</td>';
		    			print '<td class="tocenter">'.$is_writable.'</td>';
		    		print '</tr>';
		    	}
	    		?>
	    		</tbody>
	    		</table>
	    		<!-- /check dirs and files -->
			</div>
		
			<div class="col-lg-6 col-md-6">
			<!-- check main json issuer file -->
				<h3><br></h3>

				<div class="form-group">Master Issuer Json File</div>
    			<p class="small">
    			This is the content of the file : <strong><?php echo APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".BADGES_ISSUER_INSTITUTION_FILE_ID ?></strong>
    			<br><br>
			<pre>{
			"@context": "<?php echo BADGES_ISSUER_CONTEXT?>",
			"id" : "<?php echo BADGES_ISSUER_INSTITUTION_ID?>",
			"@type": "IssuerOrg",
			"name": "<?php echo BADGES_ISSUER_INSTITUTION_NAME?>",
			"url": "<?php echo BADGES_ISSUER_INSTITUTION_URL?>",
			"email": "<?php echo BADGES_ISSUER_INSTITUTION_EMAIL?>",
			}
			</pre>
			</p>
			<!-- /check main json issuer file -->

			</div>
			
		</div>
		<?php  } ?>
		<!-- system data -->
				
	<?php } ?>
	<!-- /contents -->    

</div>


<div class="container"><div class="col-lg-12"><br></div></div>

<div id='footer' class='wrapper'>
    <?php include("footer.php"); ?>
</div>
<div id='copyright' class='wrapper'>
    <?php  include("copyright.php"); ?>
</div>

</body>
</html>
