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

    <div id='bread'><a href='/'>Home</a> > Edit Badge</div>
    <div class='page_header'>Edit Badge</div>
    
    <div class="pull-right"><a href="./issuer.php" class="btn btn-info btn-md"><i class="fa fa-backward"></i> BACK TO ISSUER LIST</a></div>
    <div class="container"><div class="col-lg-12"><br></div></div>
    
	<!-- contents -->
	<?php 
	include("events.php");
	$badge_id 	= ( isset($_POST["badge_id"]) && $_POST["badge_id"]!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $_POST["badge_id"]) : '';
	$badge_id 	= ( $badge_id=='' && isset($_POST["event"]) && $_POST["event"]=='new_badge' && isset($new_badge_id) && $new_badge_id>0 ) ? $new_badge_id : $badge_id; 
	$user_id	= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
	$event_action	= ($badge_id>0) ? "update_badge" : "new_badge";
	
	//create object
	$obj_bg 	= new COMMONDB_MODULE("badges_issuers", $badge_id);
	$cryted_id  	= ( $badge_id>0 ) ? $obj_bg->crypted_id : "";
	$user_id  	= ( $badge_id>0 ) ? $obj_bg->user_id : $user_id;
	$institution  	= $obj_bg->institution;
	$course  	= $obj_bg->course;
	$course_desc	= $obj_bg->course_desc;
	$course_url	= $obj_bg->course_url;
	$enabled	= $obj_bg->enabled;
	$published	= $obj_bg->published;
	$deleted	= $obj_bg->deleted;

	//badge
	$badge_img_name	= $obj_bg->badge_img_name;
	$show_badge_img = ( $badge_id>0 && $badge_img_name!='') ? "fileissuer.php?bgid=$cryted_id&amp;".NOCACHE : "";
	
	//get params
	$arr_params		= ($badge_id>0) ? COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers_params","param_id,label,description","WHERE badge_id='$badge_id' AND deleted='0' ORDER BY param_id","") : array();
	$count_params 		= count($arr_params);
	$count_less_params 	= BADGES_PARAMS_NUM_MAX-$count_params;
	
	//get awars
	$total_badges_earns = ($badge_id>0) ? COMMONDB_MODULE::count_values("badges_earns","earn_id","WHERE badge_id='$badge_id'") : 0;
	?>
	
	<?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="row col-lg-12 alert alert-danger" style="color:red; margin-top:10px;">'.$event_errors.'</div>'; }?>
	<?php if ( isset($event_success) && $event_success!="" ) { print '<div class="row col-lg-12  alert alert-success" style="color:green; margin-top:10px;">'.$event_success.'</div>'; }?>

	<form action="#" method="post" enctype="multipart/form-data" class="">
		<input type="hidden" name="event" id="event" value="<?php echo $event_action?>">
		<input type="hidden" name="badge_id" id="badge_id" value="<?php echo $cryted_id?>">

		<!-- main data -->
		<div class="col-lg-6">
			
			<div class="form-group"><h3>Main Badge Data (* Required)</h3></div>
			
			<div class="form-group">
				<label for="institution">Institution</label>
				<p class="alert alert-warning"><?php echo BADGES_ISSUER_INSTITUTION_NAME?></p>
			</div>
			<div class="form-group">
				<label for="course">Course Name</label>
				<input type="text" name="course" id="course" value="<?php echo $course?>" class="form-control" required="required" placeholder="course name">
			</div>
			<div class="form-group">
				<label for="course">Course Criteria URL</label>
				<input type="url" name="course_url" id="course_url" value="<?php echo $course_url?>" class="form-control" required="required" placeholder="course criteria url">
				<small><em>URL of the criteria for earning the achievement.</em></small>
			</div>			
			<div class="form-group">
				<label for="course_desc">Course Description</label><br>
				<textarea id="course_desc" name="course_desc" class="form-control" required="required" rows="10" placeholder="description here"><?php echo $course_desc?></textarea>
			</div>
			<div class="form-group">
				<label for="badge_img">Badge Image</label>
				<input type="file" name="badge_img" id="badge_img" class=""> (allowed <?php echo BADGES_IMAGE_ALLOWED_EXTENSIONS?>)
			</div>
			<div class="form-group">
				<label>View Badge Image</label><br>
				<center><?php if ( $badge_img_name !='') { ?><img src='<?php echo $show_badge_img?>'><?php } else { ?><p class="">Image Required</p><?php }?></center>
			</div>
		</div>
		<!-- /main data -->
			
		<!-- params -->
		<div class="col-lg-6">
		<div class="form-group"><h3>Evidence (Optional)</h3></div>
		<div class="form-group">URL of the work that the recipient did to earn the achievement. This can be a page that links out to other pages if linking directly to the work is infeasible.</div>
	
		<?php for ($i = 0; $i < $count_params; $i++) { ?>
			<div class="form-group small">
				<div class="col-lg-2"><label for="label-<?php echo $i?>-<?php echo $arr_params[$i]['param_id'] ?>">Label</label></div>
				<div class="col-lg-10"><input type="text" id="label-<?php echo $i?>-<?php echo $arr_params[$i]['param_id']?>" name="label-<?php echo $i?>-<?php echo $arr_params[$i]['param_id']?>" value="<?php echo $arr_params[$i]['label']?>" class="form-control input-sm" maxlength="80"></div>
			</div>
			<div class="form-group small">
				<div class="col-lg-2"><label for="label-<?php echo $i?>">Description</label></div>
				<div class="col-lg-10"><input type="text" id="description-<?php echo $i?>-<?php echo $arr_params[$i]['param_id']?>" name="description-<?php echo $i?>-<?php echo $arr_params[$i]['param_id']?>" value="<?php echo $arr_params[$i]['description']?>" class="form-control input-sm" maxlength="200"></div>
			</div>
			<div class="form-group"><div class="col-lg-12"><br><a href="#" class="pull-right btn btn-xs btn-danger" onclick="return del_evidence(<?php echo $arr_params[$i]['param_id']?>);"><i class="fa fa-trash-o"></i> Remove evidence</a></div></div>
			<div class="form-group"><div class="col-lg-12"><hr></div></div>
		<?php }?>
		
		<?php for ($i = 0; $i < $count_less_params; $i++) { ?>
			<div class="form-group small">
				<div class="col-lg-2"><label for="label-<?php echo $i?>">Label</label></div>
				<div class="col-lg-10"><input type="text" id="label-<?php echo $i?>" name="label-<?php echo $i?>" value="" class="form-control input-sm" maxlength="80"></div>
			</div>
			<div class="form-group small">
				<div class="col-lg-2"><label for="label-<?php echo $i?>">Description</label></div>
				<div class="col-lg-10"><input type="text" id="description-<?php echo $i?>" name="description-<?php echo $i?>" value="" class="form-control input-sm" maxlength="200"></div>
			</div>
			<div class="form-group"><div class="col-lg-12"><hr></div></div>
		<?php }?>
		
		</div>
		<!-- /params -->

		<!--actions -->
		<div class="col-lg-12">
			<center>
				<button type="submit" class="btn btn-primary"><?php echo ($badge_id>0) ? "UPDATE BADGE" : "CREATE A NEW BADGE" ?> <i class="fa fa-forward"></i></button>
				<?php if ( $badge_id>0 ) { ?>
				<div class="pull-right"><a href="./badge_edit.php" class="btn btn-success"> ADD NEW BADGE <i class="fa fa-star"></i></a></div> 
				<?php } ?>
			</center>
		</div>
		<!--/actions -->
	
	</form>	
	<!-- /contents -->

</div>

<div class="container"><div class="col-lg-12"><br></div></div>

	<!--  control evidences -->
	<div id="placeholder_del_evidence" style="display:none;"></div>
	<script type="text/javascript">
	function del_evidence(id) {
		if ( id > 0 ) 
		{
			$("#placeholder_del_evidence").append('<form id="frm_del_evidence" action="badge_edit.php" method="POST">');
			$("#placeholder_del_evidence form").append('<input type="hidden" id="event" name="event" value="del_evidence_param"/>');
			$("#placeholder_del_evidence form").append('<input type="hidden" name="badge_id" id="badge_id" value="<?php echo ($badge_id>0) ? get_crypted_id($badge_id):'' ?>"/>');
			$("#placeholder_del_evidence form").append('<input type="hidden" id="param_id" name="param_id" value="'+id+'"/>');
			$("#placeholder_del_evidence form").append('</form>');
			$("#frm_del_evidence").submit();
		}
	}
	</script>
	<!--  /control evidences -->

<div id='footer' class='wrapper'>
    <?php include("footer.php"); ?>
</div>
<div id='copyright' class='wrapper'>
    <?php  include("copyright.php"); ?>
</div>

</body>
</html>
