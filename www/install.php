<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include("head.php"); ?>
</head>
<body>

<div id="head"><div id="menu"><div id='logo_space'><a href='./' ><img src="img/logo.png" alt="logo" width="300" height="47" class="logo"/></a></div></div></div>

<div id="private"></div>

<div id="main">
    <div class='page_header'>Install and Setup your application<br></div>
	<p>This content is only available in English</p>

    <!-- contents -->
    <div class="col-lg-12">

    	<!-- check database -->
    	<div class="col-lg-12">
    	<div class="form-group"><h3>Install the database (MYSQL) and change configuration data</h3></div>
    		<p>
    			<b>(*) Setup your database settings on files: <span class="label label-info">/config.php</span> and <span class="label label-info">/api/config.php</span> </b><br>
    			
    			<pre>define("DB_NAME", "<?php echo DB_NAME ?>");
define("DB_USER", "<?php echo DB_USER ?>");
define("DB_PASS", "<?php echo DB_PASS ?>");</pre>
    			
    			
    			
    			<b>Database name default:</b> <?php echo DB_NAME ?><br>
    			<b>Database user default:</b> <?php echo DB_USER ?><br>
    			<b>Database passwd default:</b> <?php echo DB_PASS ?><br>
    		
    		</p>
    		<br>
    		
    		<p>
    			<b>Create your system database (console commands)</b>
    			<pre>create database <?php echo DB_NAME ?>;</pre>
    			<pre>GRANT ALL PRIVILEGES ON <?php echo DB_NAME?>.* TO <?php echo DB_USER?>@'localhost' IDENTIFIED BY '<?php echo DB_PASS?>';</pre>
    		</p>
    		<br>
    		
    		<p>
    			<b>Dump into your database the file with inittial data: <b>badgeone_sql_model.dump.dump</b></b>
    			<pre>mysql <?php echo DB_NAME?> < badgeone_sql_model.dump  -u<?php echo DB_USER?> -p<?php echo DB_PASS?></pre>
    		</p>
    		<br>    		

    	</div>
    	<!-- check database -->
<?php 
try {
	$dbh = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'> ERROR: YOU MUST CONFIGURE YOUR DATABASE IN ORDER TO CONTINUE </div>"; exit;
}    	
?>
    	<!-- check dirs and files -->
    	<div class="col-lg-12"> 
    		<div class="form-group"><h3>Check directories and required files</h3></div>
			
			<table class="table table-condensed">
	      	<thead>
	        	<tr><th>Info</th><th>Path</th><th>Exists</th><th>Writable</th></tr>
	      	</thead>
	      	<tbody>    		
    		
    		<?php 
	    	$dirsfiles_to_check = array(
	    		//badges
	    		"app_repo" => array('name'=>'General Repository', 'dir'=> APP_GENERAL_REPO."/" , 'perms'=>'' ) ,
	    		"app_repo_issuers" => array('name'=>'Repo for Issuers files', 'dir'=> APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/" , 'perms'=>'writable' ),
	    		"app_repo_badges" => array('name'=>'Repo for Badges files', 'dir'=> APP_GENERAL_REPO_BADGES_EARN_LOCAL."/" , 'perms'=>'writable' ),
	    		"app_repo_images" => array('name'=>'Repo for Badges images', 'dir'=> APP_GENERAL_REPO_BADGES_IMG_LOCAL."/" , 'perms'=>'writable' ),
	    		"app_repo_revoked" => array('name'=>'Repo for Badges revoked', 'dir'=> APP_GENERAL_REPO_BADGES_REVOKED_LOCAL."/" , 'perms'=>'writable' ),
	    		//templates
	    		"app_repo_templates" => array('name'=>'General Repository Templates', 'dir'=> APP_GENERAL_REPO_TEMPLATES."/" , 'perms'=>'' ),
	    		"app_file_template_issuer" => array('name'=>'Issuer Template', 'dir'=> APP_BADGES_TEMPLATE_BADGE_ISSUER , 'perms'=>'' ),
	    		"app_file_template_asser" => array('name'=>'Assertion Template', 'dir'=> APP_BADGES_TEMPLATE_BADGE_ASSERTION , 'perms'=>'' ),
	    		"app_file_template_badge" => array('name'=>'BadgeClass Template', 'dir'=> APP_BADGES_TEMPLATE_BADGE_CLASS , 'perms'=>'' ),
	    	);
	    	
	    	foreach ( $dirsfiles_to_check AS $repo ) 
	    	{
	    		$if_exists 		= ( check_system_dir_files($repo['dir']) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> YES</span>'  : '<span class="label label-danger"><i class="fa fa-times"></i> NO</span>';
	    		$is_writable 	= ( $repo["perms"]=='writable' && check_system_dir_files($repo['dir'],$repo["perms"]) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> YES</span>'  : ( ($repo["perms"]=='writable') ? '<span class="label label-danger"><i class="fa fa-times"></i> NO</span>' : '<span class="label label-warning"> N/A</span>' );
	    		
	    		print '<tr>';
	    			print '<td>'.$repo['name'].'</td>';
	    			print '<td>'.$repo['dir'].'</td>';
	    			print '<td>'.$if_exists.'</td>';
	    			print '<td>'.$is_writable.'</td>';
	    		print '</tr>';
	    	}
    		?>
    		</tbody>
    		</table>
    		
    		<p class="small">
    			<span class="alert alert-danger">Change the perms of a directory when 'writable' flag is required || <span class="label label-danger"><i class="fa fa-times"></i> NO </span> </span>
    			<blockquote class="small">
    				example:<br>
    				sudo chown www-data:user files/badges/earns <br>
    				sudo chmod 775 files/badges/earns<br>
					<br>
    				sudo chown www-data:user files/badges/images <br>
    				sudo chmod 775 files/badges/images<br>
					<br>
    				sudo chown www-data:user files/badges/issuers <br>
    				sudo chmod 775 files/badges/issuers<br>
					<br>
    				sudo chown www-data:user files/badges/revoked <br>
    				sudo chmod 775 files/badges/revoked<br>
    			</blockquote>
    		</p>
    		
    		<br>
    		
    	</div>
    	<!-- /check dirs and files -->
    	
    	
    	<!-- check config -->
    	<div class="col-lg-12"> 
    		<div class="form-group"><h3>Setup Issuer params</h3></div>
			<p class="alert alert-warning"> Before start with this application verify and configure correctly these settings with yours</p>
			<?php 
			$arr_settings = array(
				"app_email" => array('file'=>'config.php','name'=>'APP_EMAIL', 'value'=>APP_EMAIL),
				"issuer_institution_name" => array('file'=>'settings.php','name'=>'BADGES_ISSUER_INSTITUTION_NAME', 'value'=>BADGES_ISSUER_INSTITUTION_NAME), 	
				"issuer_institution_url" => array('file'=>'settings.php','name'=>'BADGES_ISSUER_INSTITUTION_URL', 'value'=>BADGES_ISSUER_INSTITUTION_URL),
				"issuer_institution_email" => array('file'=>'settings.php','name'=>'BADGES_ISSUER_INSTITUTION_EMAIL', 'value'=>BADGES_ISSUER_INSTITUTION_EMAIL),
				"issuer_file_json" => array('file'=>'settings.php','name'=>'BADGES_ISSUER_INSTITUTION_FILE_ID', 'value'=>BADGES_ISSUER_INSTITUTION_FILE_ID),
			);
			?>
			<table class="table table-condensed">
	      	<thead>
	        	<tr><th>File</th><th>Param</th><th>Value</th><th>Required</th></tr>
	      	</thead>
	      	<tbody>			
			<?php 
				foreach ($arr_settings AS $settings){
					$required = '<span class="label label-success">YES</span>';
					print '<tr>';
					print '<td>'.$settings['file'].'</td>';
					print '<td>'.$settings['name'].'</td>';
					print '<td>'.$settings['value'].'</td>';
					print '<td>'.$required.'</td>';
					print '</tr>';					
					
				}
			?>
			</tbody>
			</table>
			
			<br>
			
			<div class="form-group"><h3>Setup Master Issuer File</h3></div>
			<p>The issuer master file needs to be placed under the directory: <b><?php echo APP_GENERAL_REPO_BADGES_ISSUER_LOCAL?></b></p>
			<p>The issuer master file needs to be named as <em>BADGES_ISSUER_INSTITUTION_FILE_ID</em> : <b><?php echo BADGES_ISSUER_INSTITUTION_FILE_ID?></b></p>
			<p>The issuer master file needs to be a valid JSON file : <b><?php echo BADGES_ISSUER_INSTITUTION_FILE_ID?></b></p>
			
			<table class="table table-condensed">
	      	<thead>
	        	<tr><th>Info</th><th>Path</th><th>Exists</th><th>Writable</th></tr>
	      	</thead>
	      	<tbody>
    		
    		<?php 
	    	$configuration = array(
	    		//badges
	    		"app_file_issuer" => array('name'=>'Issuer Master JSON file', 'dir'=> APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".BADGES_ISSUER_INSTITUTION_FILE_ID , 'perms'=>'' ) ,
	    	);
	    	
	    	foreach ( $configuration AS $conf ) 
	    	{
	    		$if_exists 		= ( check_system_dir_files($conf['dir']) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> YES</span>'  : '<span class="label label-danger"><i class="fa fa-times"></i> NO</span>';
	    		$is_writable 	= ( $conf["perms"]=='writable' && check_system_dir_files($conf['dir'],$conf["perms"]) == 1) ? '<span class="label label-success"><i class="fa fa-check"></i> YES</span>'  : ( ($conf["perms"]=='writable') ? '<span class="label label-danger"><i class="fa fa-times"></i> NO</span>' : '<span class="label label-warning"> N/A</span>' );
	    		
	    		print '<tr>';
	    			print '<td>'.$conf['name'].'</td>';
	    			print '<td>'.$conf['dir'].'</td>';
	    			print '<td>'.$if_exists.'</td>';
	    			print '<td>'.$is_writable.'</td>';
	    		print '</tr>';
	    	}
    		?>
    		</tbody>
    		</table>

    		
    		<div class="form-group"><b>Edit or Update your Issuer Master File with your configuration</b></div>
    		This is the final result for  <strong><?php echo APP_GENERAL_REPO_BADGES_ISSUER_LOCAL."/".BADGES_ISSUER_INSTITUTION_FILE_ID ?></strong>
<pre>{
"@context": "<?php echo BADGES_ISSUER_CONTEXT?>",
"id" : "<?php echo BADGES_ISSUER_INSTITUTION_ID?>",
"@type": "IssuerOrg",
"name": "<?php echo BADGES_ISSUER_INSTITUTION_NAME?>",
"url": "<?php echo BADGES_ISSUER_INSTITUTION_URL?>",
"email": "<?php echo BADGES_ISSUER_INSTITUTION_EMAIL?>",
}
</pre>
    		
    		
    		<p class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> The data defined in JSON issuer file must be the same as configured in the system, if not, the system will not work propertly</p>
    		<p class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Never change the configuration data once the system is released</p>
    		
    	</div>
    	<!-- /check config -->
    	
    	
    	<!-- check database  -->
    	
    	<div class="col-lg-12"><a name="create_admin_user"></a> 
    		<div class="form-group"><h3>Check database and create the admin user</h3></div>
			
			<?php 
				$user_admin = COMMONDB_MODULE :: get_selected_value("users", "id_user", "WHERE profile='admin' AND activated=1");
				$obj_admin = new COMMONDB_MODULE("users", $user_admin);
				//check issuer data
				$admin_issuer 		=  ( $obj_admin->institution!='' && $obj_admin->institution == BADGES_ISSUER_INSTITUTION_NAME ) ? BADGES_ISSUER_INSTITUTION_NAME : '';
				$admin_issuer_url 	=  ( $obj_admin->institution_url!='' && $obj_admin->institution_url == BADGES_ISSUER_INSTITUTION_URL ) ? BADGES_ISSUER_INSTITUTION_URL : '';
				$admin_issuer_email =  ( $obj_admin->institution_email!='' && $obj_admin->institution_email == BADGES_ISSUER_INSTITUTION_EMAIL ) ? BADGES_ISSUER_INSTITUTION_EMAIL : '';
				$admin_issuer_data_ok = ($admin_issuer!='' && $admin_issuer_url!='' && $admin_issuer_email!='') ? 1 : 0; 
			?>

			<?php if ( $user_admin > 0 ) { ?> 
			<p class="label label-success"> USER ADMIN EXISTS. REVIEW THE ISSUER DATA ADMIN IF IS NEEDED </p>
			<?php } else {  ?>
			<p class="label label-danger"> CREATE THE ADMIN USER WHEN YOU FINISH YOUR CONFIGURATION (NEVER BEFORE)</p>
			<?php }  ?>
			<br><br>
			
			<div class="form-group"><strong>Check if the admin user exists, if not create a new one</strong></div>
			
			<div class="form-group">
				
				<label>Admin user : <?php echo ( $user_admin> 0 ) ? '<span class="label label-success">YES</span>' : '<span class="label label-danger">NO</span>'?></label>
				<?php if ( $user_admin > 0 )  { ?>
				<div class="alert alert-warning">uid: <?php echo $obj_admin->id_user?> | name: <?php echo $obj_admin->name?> | email: <?php echo $obj_admin->email?></div>
				<?php }  else { ?> 
				<div class="alert alert-warning">
				Create a new admin user:<br>
				Default password it will be: admin123<br>
				The admin email it will be your user identification

				<form action="install.php#create_admin_user" method="post" class="form-inline">
					<input type="hidden" name="event" value="create_admin">
					Fullname: <input type="text" name="a_fullname" id="a_fullname" value="" class="form-control" placeholder="admin fullname" required="required">
					Email: <input type="email" name="a_email" id="a_email" value="" class="form-control" placeholder="admin email" required="required">
					<button type="submit" class="btn btn-info">CREATE ADMIN USER</button>
				</form>				
				
				<?php 
					//create admin user
					if ( $user_admin ==0 && isset($_POST["event"]) && $_POST["event"]=="create_admin") {
						
						$u_fullname			= ( isset($_POST['a_fullname']) && strlen(trim($_POST['a_fullname']))>0 ) ? $_POST['a_fullname'] : '';
						$u_email			= ( isset($_POST['a_email']) && strlen(trim($_POST['a_email']))>0 ) ? $_POST['a_email'] : '';
						$u_email			= isValidateEmailSyntax($u_email);
						
						if ( $u_fullname!='' && $u_email!='' ){
							$date_now= date("Y-m-d H:i:s");
							$sdata = array('admin',$u_email,$u_fullname,BADGES_ISSUER_INSTITUTION_NAME,BADGES_ISSUER_INSTITUTION_URL,BADGES_ISSUER_INSTITUTION_EMAIL,md5('admin123'),1,$date_now,$date_now);
							$stmt = $dbh->prepare("INSERT INTO users (id_user,profile,email,name,institution,institution_url,institution_email,password,activated,date_created,date_activated) VALUES ('',?,?,?,?,?,?,?,?,?,?)");
							$stmt->execute($sdata);

							$new_admin_id = $dbh->lastInsertId();

							if ( $new_admin_id > 0 ) {
								
								//setup oauth2 client
								$client_id 		= $u_email."-".rand_chars();
								$client_secret 	= rand_chars(8) ."-". rand_chars(4) ."-". rand_chars(4) ."-". rand_chars(4) ."-". rand_chars(12);
								$sdata = array($client_id,$client_secret,$new_admin_id);
								$stmt = $dbh->prepare("INSERT INTO oauth_clients (client_id,client_secret,user_id) VALUES(?,?,?) ");
								$stmt->execute($sdata);

								echo "<br>";
								echo "<p class='alert alert-success'>
								New user admin created.<br> 
								Now you could sing-in : user: $u_email | password: admin123 <br> 
								Sing-in : <a href=\"/login.php\">HERE</a>
								</p>";

								//create json issuer file
								IBL_OPENBADGES::create_issuer_json($new_admin_id);

								//next step update data - first action - do not need corrections
								$user_admin = $new_admin_id;
								$admin_issuer_data_ok = 1;
								
							} else {
								echo "<br>";
								echo "<p class='alert alert-danger'>Something wrong happen. Please try again.</p>";
							}
							
						} else {
							echo "<br>";
							echo "<p class='alert alert-danger'>Something wrong happen. Please try again.</p>";
						}
					}
				?>
				</div>
				<?php }  ?>
			</div>
			
			<?php if ( $user_admin > 0 )  { ?>
			<div class="form-group"><a name="update_admin_user"></a>
				<label>Issuer Admin data : <?php echo ( $user_admin> 0 && $admin_issuer_data_ok==1) ? '<span class="label label-success">OK</span>' : '<span class="label label-danger">KO</span>'?></label>
				<?php if ( $admin_issuer_data_ok == 0 )  { ?>
				<br>
				<?php if ( $user_admin>0 && isset($_POST["event"]) && $_POST["event"]=="update_admin") { } else { ?>
				Click on this button to correct admin user information
				<form action="install.php#update_admin_user" method="post" class="form-inline">
					<input type="hidden" name="event" value="update_admin">
					<button type="submit" class="btn btn-info">ADMIN USER CORRECTIONS</button>
				</form>
				<?php } ?> 
					
				<?php 
				//correct admin user data
				if ( $user_admin>0 && isset($_POST["event"]) && $_POST["event"]=="update_admin") {
					$sdata = array(BADGES_ISSUER_INSTITUTION_NAME,BADGES_ISSUER_INSTITUTION_URL,BADGES_ISSUER_INSTITUTION_EMAIL,$user_admin);
					$stmt = $dbh->prepare("UPDATE users SET institution=?, institution_url=? , institution_email=? WHERE id_user=? ");
					$stmt->execute($sdata);			

					echo "<br>";
					echo "<p class='alert alert-success'>Reload the page to verify all data <a href='install.php' class='btn btn-success'> <i class='fa fa-share'></i> RELOAD </a> </p>";
				}
				?>
				
				
				<?php } ?> 
			</div>
			<?php } ?> 

    	</div>
    	<!-- /check dirs and files -->    	
    	
    
    </div>
    
    
    <div class="container"><div class="col-lg-12"><br></div></div>
    
    <div class="container"><div class="col-lg-12"><p class="alert alert-info medium tocenter"><i class="fa fa-info-circle"></i> IF EVERYTHING IS CORRECT, <b>DON'T FORGET TO REMOVE THE INSTALL.PHP PAGE</b></p></div></div>
    
    <div class="container"><div class="col-lg-12"><br></div></div>
	<!-- /contents -->

</div>



<div class="container"><div class="col-lg-12"><br></div></div>

<div id='footer' class='wrapper'><?php include("footer.php"); ?></div>
<div id='copyright' class='wrapper'><?php  include("copyright.php"); ?></div>

</body>
</html>
