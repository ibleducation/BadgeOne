<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><?php include("head.php"); ?></head>
<body>

<div id="head"><div id="menu"><?php  include("menu.php"); ?></div></div>
<div id="private"> <?php  include("a_auth.php"); ?></div>

<div id="main">

	<div id='bread'><a href='/'><?php echo __("Home")?></a> > <?php echo __("My Profile")?></div>
	<div class='page_header'><?php echo __("My Profile")?></div>

    <!-- contents -->
    <?php if ( isset($logged_user) && $logged_user >0  && isset($logged_profile) && $logged_profile!='') { ?>
		<!-- user data -->
		<?php
		include("events.php");
		$user_id	= $logged_user;
		$user_profile = $logged_profile;
		//user data
		$obj_user 	= new COMMONDB_MODULE("users", $user_id);
			$fullname 			= $obj_user->name;
			$explode_fullname	= explode(" ", $fullname);
			$first_name			= ( isset($explode_fullname[0]) ) ? $explode_fullname[0] : '';
			$last_name			= ( isset($explode_fullname[0]) ) ? ltrim(substr($fullname, strlen($explode_fullname[0]))) : '';
			$email 				= $obj_user->email;
			$institution = $obj_user->institution;
			$institution_url = $obj_user->institution_url;
			$institution_email = $obj_user->institution_email;

			$picture = ($obj_user->picture!='') ? $obj_user->picture : "default.png";
			$picture_css = ($obj_user->picture!='') ? 'class="img-responsive thumbnail"' : "";
			$show_picture = "<img src=\"".APP_GENERAL_REPO_USERS_PICTURES."/$picture\" $picture_css>";

			$about_user = $obj_user->about_user;
			$url_website = $obj_user->url_website;
			$url_social_facebook = $obj_user->url_social_facebook;
			$url_social_twitter = $obj_user->url_social_twitter;
			$url_social_gplus = $obj_user->url_social_gplus;
			$url_social_linkedin = $obj_user->url_social_linkedin;

		//oauth data
		$get_auth_client_id  = COMMONDB_MODULE::get_selected_value("oauth_clients", "client_id", "WHERE user_id='$user_id'");
		$get_auth_secret_key = COMMONDB_MODULE::get_selected_value("oauth_clients", "client_secret", "WHERE user_id='$user_id'");
		//event
		$event_action = "update_user";
		?>

		<?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="row col-lg-12 alert alert-danger" style="color:red; margin-top:10px;">'.$event_errors.'</div>'; }?>
		<?php if ( isset($event_success) && $event_success!="" ) { print '<div class="row col-lg-12  alert alert-success" style="color:green; margin-top:10px;">'.$event_success.'</div>'; }?>
		<?php if ( isset($error_additional_user_info) && $error_additional_user_info!="" ) { print '<div class="row col-lg-12  alert alert-warning" style="color:gray; margin-top:10px;">'.$error_additional_user_info.'</div>'; }?>

		<form action="#" method="post" enctype="multipart/form-data" class="">
			<input type="hidden" name="event" id="event" value="<?php echo $event_action?>">
			<input type="hidden" name="check_user" id="check_user" value="<?php echo get_crypted_id($user_id)?>">

			<!-- main data -->
			<div class="col-lg-6">
				<div class="form-group"><h3><?php echo __("Main User Data")?> (* <?php echo __("Required")?>)</h3></div>

				<div class="form-group">
					<label for="earn_fullname"><?php echo __("Full name")?></label>
					<input type="text" name="fullname" id="fullname" value="<?php echo $fullname?>" class="form-control" required="required" placeholder="<?php echo __("Full name")?>" >
				</div>

				<div class="form-group">
					<label for="earn_email"><?php echo __("Email")?></label>
					<input type="email" name="email" id="email" value="<?php echo $email?>" class="form-control" required="required" placeholder="<?php echo __("Email")?>" disabled="disabled">
				</div>

				<div class="form-group">
					<label for="earn_email"><?php echo __("Password")?></label>
					<input type="text" name="new_pass" id="new_pass" value="" class="form-control" placeholder="<?php echo __("if you do not pretend to change your password do not fill this")?>">
					<small><em><?php echo __("min")?> <?php echo (defined('APP_USER_MIN_CHARS_PWD') && APP_USER_MIN_CHARS_PWD>0) ? APP_USER_MIN_CHARS_PWD : 4 ?> <?php echo __("chars")?></em></small>
				</div>
				
				<div class="form-group">
					<div class="col-lg-6">
						<label for="earn_insittution"><?php echo __("Picture")?></label>
						<input type="file" name="picture" id="picture">
						<br>
						<?php if ( $obj_user->picture!='' ){ ?>
						<input type="checkbox" name="del_picture" id="del_picture"> <span class="text-danger"><?php echo __("Check to delete picture")?></span>
						<?php } ?>
					</div>
					<div class="col-lg-6">
						<?php echo $show_picture ?>
					</div>
				</div>
				
				<div class="form-group">
					<label for="about_me"><?php echo __("About Me")?></label><br>
					<textarea name="about_user" id="about_user" style="width:100%" maxlength="600"><?php echo $about_user?></textarea>
				</div>

				<?php if ( $user_profile !='general' ) { ?>
				<div class="form-group"><h3><?php echo __("Main Institution Data")?> (* <?php echo __("Required")?>)</h3></div>
				<div class="form-group">
					<label for="earn_insittution"><?php echo __("Institution")?></label>
					<input type="text" name="institution" id="institution" value="<?php echo $institution?>" class="form-control" placeholder="<?php echo __("Institution")?>" >
				</div>

				<div class="form-group">
					<label for="earn_insittution"><?php echo __("URL Institution")?></label>
					<input type="url" name="institution_url" id="institution_url" value="<?php echo $institution_url?>" class="form-control" placeholder="<?php echo __("URL Institution")?>" >
				</div>

				<div class="form-group">
					<label for="earn_insittution"><?php echo __("Email Institution")?></label>
					<input type="email" name="institution_email" id="institution_email" value="<?php echo $institution_email?>" class="form-control" placeholder="<?php echo __("Email Institution")?>" >
				</div>
				<?php }?>

			</div>
			<!-- /main data -->

			<!-- api provider data -->
			<div class="col-lg-6">
			<?php if ( $user_profile !='general' ) { ?>
				<div class="form-group"><h3><?php echo __("Provider account information")?></h3></div>
				<p><?php echo __("You will need this data in order to connect the API")?></p>
				<?php if ($get_auth_client_id!='' && $get_auth_secret_key!='') { ?>
					<div class="form-group">
						<label><?php echo __("Your Badge provider user-key")?> (<?php echo __("user")?>)</label>
						<p class="alert alert-warning"><?php echo $get_auth_client_id?></p>
					</div>
					<div class="form-group">
						<label><?php echo __("Your Badge provider account secret-key")?> (<?php echo __("password")?>)</label>
						<p class="alert alert-warning"><?php echo $get_auth_secret_key?></p>
					</div>
				<?php } else { ?>
				<div class="form-group"><p class="alert alert-danger"><?php echo __("You can not use this functionality")?></p></div>
				<?php }  ?>
			<?php }  ?>
			
				<div class="form-group"><h3><?php echo __("Additional User Information")?></h3></div>

				<div class="form-group">
					<label for="url_website"><?php echo __("My WebSite")?> <span class="small">(url)</span></label>
					<input type="text" name="url_website" id="url_website" value="<?php echo $url_website?>" class="form-control" placeholder="<?php echo __("My Website")?>" >
				</div>
				
				<div class="form-group">
					<label for="url_social_facebook">Facebook <span class="small">(url)</span></label>
					<input type="url" name="url_social_facebook" id="url_social_facebook" value="<?php echo $url_social_facebook?>" class="form-control" placeholder="Facebook">
				</div>
				
				<div class="form-group">
					<label for="url_social_twitter">Twitter <span class="small">(url)</span></label>
					<input type="url" name="url_social_twitter" id="url_social_twitter" value="<?php echo $url_social_twitter?>" class="form-control" placeholder="Twitter" >
				</div>
				
				<div class="form-group">
					<label for="url_social_gplus">Goople Plus <span class="small">(url)</span></label>
					<input type="url" name="url_social_gplus" id="url_social_gplus" value="<?php echo $url_social_gplus?>" class="form-control" placeholder="Google Plus">
				</div>
				
				<div class="form-group">
					<label for="url_social_linkedin">Linkedin <span class="small">(url)</span></label>
					<input type="url" name="url_social_linkedin" id="url_social_linkedin" value="<?php echo $url_social_linkedin?>" class="form-control" placeholder=Linkedin">
				</div>
			
			</div>
			<!-- /api provider data -->

			<!--actions -->
			<div class="col-lg-12">
				<center><button type="submit" class="btn btn-success"><?php echo __("Update data")?> <i class="fa fa-forward"></i></button></center>
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
				<h3><?php echo __("Main Institution Information")?></h3>
				<div class="form-group">
					<label><?php echo __("Institution")?></label>
					<p class="alert alert-warning"><?php echo $obj_profile[0]['institution']?></p>
				</div>
				<div class="form-group">
					<label><?php echo __("URL Institution")?></label>
					<p class="alert alert-warning"><?php echo ($obj_profile[0]['institution_url']!='') ? "<a href='".$obj_profile[0]['institution_url']."' target='_blank'>".$obj_profile[0]['institution_url']."</a>":""?></p>
				</div>
				<div class="form-group">
					<label><?php echo __("Email Institution")?></label>
					<p class="alert alert-warning"><?php echo ($obj_profile[0]['institution_email']!='') ? "<a href='mailto:".$obj_profile[0]['institution_email']."'>".$obj_profile[0]['institution_email']."</a>":""?></p>
				</div>
			</div>

			<div class="col-lg-6 col-md-6">
				<h3><?php echo __("Profile Information")?></h3>
				<div class="form-group">
					<label><?php echo __("Profile")?></label>
					<p class="alert alert-warning"><?php echo $obj_profile[0]['profile']?></p>
				</div>
				<div class="form-group">
					<label><?php echo __("Activation Date")?></label>
					<p class="alert alert-warning"><?php echo $obj_profile[0]['date_activated']?></p>
				</div>
				<div class="form-group">
					<label><?php echo __("Lastupdate")?></label>
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
				<h3><?php echo __("System Information")?></h3>


				<div class="form-group"><h4><?php echo __("Check directories and required files")?></h4></div>

				<!-- check dirs and files -->
				<table class="table table-condensed small">
		      	<thead><tr><th><?php echo __("Info")?></th><th><?php echo __("Path")?></th><th><?php echo __("Exists")?></th><th><?php echo __("Writable")?></th></tr></thead>
		      	<tbody class="small">
	    		<?php
		    	$dirsfiles_to_check = array(
		    		//badges
		    		"app_repo" => array('name'=>__("General Repository"), 'dir'=> APP_GENERAL_REPO."/" , 'perms'=>'' ) ,
		    		"app_repo_issuers" => array('name'=>__("Repo for Issuers files"), 'dir'=> APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/" , 'perms'=>'writable' ),
		    		"app_repo_badges" => array('name'=>__("Repo for Badges files"), 'dir'=> APP_GENERAL_REPO_BADGES_EARN_LOCAL."/" , 'perms'=>'writable' ),
		    		"app_repo_images" => array('name'=>__("Repo for Badges images"), 'dir'=> APP_GENERAL_REPO_BADGES_IMG_LOCAL."/" , 'perms'=>'writable' ),
		    		"app_repo_revoked" => array('name'=>__("Repo for Badges revoked"), 'dir'=> APP_GENERAL_REPO_BADGES_REVOKED_LOCAL."/" , 'perms'=>'writable' ),
		    		//templates
		    		"app_repo_templates" => array('name'=>__("General Repository Templates"), 'dir'=> APP_GENERAL_REPO_TEMPLATES."/" , 'perms'=>'' ),
		    		"app_file_template_issuer" => array('name'=>__("Issuer Template"), 'dir'=> APP_BADGES_TEMPLATE_BADGE_ISSUER , 'perms'=>'' ),
		    		"app_file_template_asser" => array('name'=>__("Assertion Template"), 'dir'=> APP_BADGES_TEMPLATE_BADGE_ASSERTION , 'perms'=>'' ),
		    		"app_file_template_badge" => array('name'=>__("BadgeClass Template"), 'dir'=> APP_BADGES_TEMPLATE_BADGE_CLASS , 'perms'=>'' ),
		    		"app_file_issuer_json" => array('name'=>__("Issuer JSON master file"), 'dir'=> APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".BADGES_ISSUER_INSTITUTION_FILE_ID , 'perms'=>'' ),
		    	);

		    	foreach ( $dirsfiles_to_check AS $repo )
		    	{
		    		$if_exists 		= ( check_system_dir_files($repo['dir']) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> '.__("YES").'</span>'  : '<span class="label label-danger"><i class="fa fa-times"></i> '.__("NO").'</span>';
		    		$is_writable 	= ( $repo["perms"]=='writable' && check_system_dir_files($repo['dir'],$repo["perms"]) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> '.__("YES").'</span>'  : ( ($repo["perms"]=='writable') ? '<span class="label label-danger"><i class="fa fa-times"></i> '.__("NO").'</span>' : '<span class="label label-warning"> N/A</span>' );

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
				<div class="form-group"><?php echo __("Master Issuer Json File")?></div>
				<p class="small">
				<?php echo __("This is the content of the file")?> : <strong><?php echo IBL_OPENBADGES::show_path_issuer_json($logged_user, $logged_profile, $show_path='local'); ?></strong>
				<br><br>
				<?php 
				$show_contents_file_json = IBL_OPENBADGES::read_issuer_json($logged_user, $logged_profile);
				if ( $show_contents_file_json !='' ) {
					print "<pre>".$show_contents_file_json."</pre>";
				} else {
					print '<div class="alert alert-danger">'.__("The Json Issuer's file does not exists")."<br>".__("Update your profile in order to regenerate the Json file").'</div>';
				}
				?>
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

<div id='footer' class='wrapper'><?php include("footer.php"); ?></div>
<div id='copyright' class='wrapper'><?php  include("copyright.php"); ?></div>

</body>
</html>
