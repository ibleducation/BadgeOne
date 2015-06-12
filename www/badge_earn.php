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

    <div id='bread'><a href='/'><?php echo __("Home")?></a> > <?php echo __("Earn a new Badge")?></div>
    <div class='page_header'><?php echo __("Earn a new Badge")?></div>

    <!-- contents -->
	<?php 
	include("events.php");
	$sel_badge_id	= ( isset($_POST["badge_id"]) && $_POST["badge_id"]!="") ? $_POST["badge_id"] : ''; 
	$badge_id 		= ( $sel_badge_id!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $sel_badge_id) : '0';
	$user_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
	$event_action	= ($badge_id>0) ? "earn_badge" : "earn_badge";
	
	//earn granted check
	$earn_user_id  		= $user_id;
	//$earn_granted_id	= ($badge_id>0 && $earn_user_id>0 ) ? COMMONDB_MODULE::get_selected_value("badges_earns", "earn_id", "WHERE badge_id='$badge_id' AND user_id='$earn_user_id'") : 0;
	$earn_granted_id	= 0;
	$earn_granted_id	= ($earn_granted_id==0 && isset($new_earn_id) && $new_earn_id>0) ? $new_earn_id : $earn_granted_id;
	$earn_granted_id	= ($earn_granted_id==0 && $badge_id>0 && isset($_POST["earn_email"]) && $_POST["earn_email"]!='') ? COMMONDB_MODULE::get_selected_value("badges_earns", "earn_id", "WHERE badge_id='$badge_id' AND earn_email='".$_POST["earn_email"]."'") : $earn_granted_id;
	$url_earn_badge		= ($earn_granted_id>0) ? "view_badge_earn.php?badge_id=".get_crypted_id($earn_granted_id) : "";
	
	//create object
	$obj_bg 	= new COMMONDB_MODULE("badges_issuers", $badge_id);
		$cryted_id  	= ( $badge_id>0 ) ? $obj_bg->crypted_id : "";
		$user_id  		= ( $badge_id>0 ) ? $obj_bg->user_id : $user_id;
		$institution  	= $obj_bg->institution;
		$course  		= $obj_bg->course;
		$course_desc	= $obj_bg->course_desc;
		$enabled		= $obj_bg->enabled;
		$published		= $obj_bg->published;
		$deleted		= $obj_bg->deleted;
	
		//badge
		$badge_img_name	= $obj_bg->badge_img_name;
		$show_badge_img =  ( $badge_id>0 && $badge_img_name!='') ? "fileissuer.php?bgid=$cryted_id&amp;".NOCACHE : "";
		
		//get params
		$arr_params			= ($badge_id>0) ? COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers_params","param_id,label,description","WHERE badge_id='$badge_id' AND deleted='0' ORDER BY param_id","") : array();
		$count_params 		= count($arr_params);
		
		//defaults
		$earn_fullname 	= ( isset($_POST["earn_fullname"]) && $_POST["earn_fullname"]!='') ? $_POST["earn_fullname"] : "";
		$earn_email 	= ( isset($_POST["earn_email"]) && $_POST["earn_email"]!='') ? $_POST["earn_email"] : "";
	?>
	
    <div class="pull-right">
		<form action="list_earn.php" method="post">
		<input type="hidden" name="badge_id" id="badge_id" value="<?php echo $sel_badge_id?>">
		<button type="submit" class="btn btn-info" type="button"> <i class="fa fa-backward"></i> <?php echo __("BACK TO EARN LIST")?></button>
		</form>    
    </div>	
	

	<?php if ( $badge_id > 0 && $enabled==1 && $deleted==0 )  {?>
	
		<?php if ( $earn_granted_id > 0 )  {?>
	
		<?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="row col-lg-12 alert alert-danger" style="color:red; margin-top:10px;">'.$event_errors.'</div>'; }?>
		<?php if ( isset($event_success) && $event_success!="" ) { print '<div class="row col-lg-12  alert alert-success" style="color:green; margin-top:10px;">'.$event_success.'</div>'; }?>
	
		<h3><?php echo __("View the new Badge")?> </h3>
		<h4><?php echo __("Course")?> : <?php echo ($badge_id>0) ? "$course" : ""?></h4>
		
		<div class="col-lg-6">
			<?php echo "<a href='".$url_earn_badge."' class='btn btn-info' target='_blank'>".__("VIEW THE BADGE")." <i class='fa fa-search'></i></a>"?>
		</div>
		<div class="col-lg-6">
			<form action="badge_earn.php" method="post">
			<input type="hidden" name="badge_id" id="badge_id" value="<?php echo $sel_badge_id;?>">
			<button type="submit" class="btn btn-success" type="button"> <i class="fa fa-star"></i> <?php echo __("EARN A NEW BADGE")?> </button>
			</form>		
		</div>
		
		<?php } else { ?>
	
		<h3><?php echo __("Course")?> : <?php echo ($badge_id>0) ? "$course" : ""?></h3>
		
		<?php if ( isset($event_errors) && $event_errors!="" ) { print '<p class="" style="color:red;">'.$event_errors.'</p>'; }?>
		<?php if ( isset($event_success) && $event_success!="" ) { print '<p class="" style="color:green;">'.$event_success.'</p>'; }?>
			
		<form action="#" method="post" enctype="multipart/form-data" class="">
			<input type="hidden" name="event" id="event" value="<?php echo $event_action?>">
			<input type="hidden" name="badge_id" id="badge_id" value="<?php echo $cryted_id?>">

			<!-- main data -->
			<div class="col-lg-6">
				<div class="form-group"><h3><?php echo __("User Data")?> (* <?php echo __("Required")?>)</h3></div>
				
				<div class="form-group">
					<label for="earn_fullname"><?php echo __("Full name")?></label>
					<input type="text" name="earn_fullname" id="earn_fullname" value="<?php echo $earn_fullname?>" class="form-control" required="required" placeholder="<?php echo __("Full name")?>">
				</div>
				
				<div class="form-group">
					<label for="earn_email"><?php echo __("Email")?></label>
					<input type="email" name="earn_email" id="earn_email" value="<?php echo $earn_email?>" class="form-control" required="required" placeholder="<?php echo __("Email")?>">
				</div>
			</div>
			<!-- /main data -->
						
			<!-- params -->
			<div class="col-lg-6">
			<?php if ($count_params > 0 ) {  ?>
				<div class="form-group"><h3><?php echo __("Evidence")?> (<?php echo __("Optional")?>)</h3></div>
				<?php for ($i = 0; $i < $count_params; $i++) { ?>
				<div class="form-group small">
					<label for="label-<?php echo $i?>-<?php echo $arr_params[$i]['param_id'] ?>"><?php echo $arr_params[$i]['label']?></label>
					<small><?php echo $arr_params[$i]['description']?></small><br>
					<input type="text" id="description-<?php echo $i?>-<?php echo $arr_params[$i]['param_id']?>" name="description-<?php echo $i?>-<?php echo $arr_params[$i]['param_id']?>" value="" class="form-control" placeholder="http:// <?php echo __("or")?> https://">
					<small>(*) <?php echo __("Required")?> <?php echo __("valid URL")?></small>
				</div>
				<?php }?>
			<?php }?>
			</div>

			<!--actions -->
			<div class="col-lg-12">
				<center><button type="submit" class="btn btn-success"><?php echo __("EARN A NEW BADGE")?> <i class="fa fa-forward"></i></button></center>
			</div>
			<!--/actions -->
		
		</form>
	<?php } ?>
	
	<?php } else { ?>
	<p class="" style="color:red"><?php echo __("Badge does not exists or is not enabled")?></p>
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