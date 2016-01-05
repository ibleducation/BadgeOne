<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include("head.php"); ?>
</head>
<body>

<style>
.panel-image {position: relative;}
.panel-image img.panel-image-preview {width: 100%; border-radius: 4px 4px 0px 0px;}
.panel-image label {display: block; position: absolute; top: 0px; left: 0px; height: 100%; width: 100%;}
.panel-heading ~ .panel-image img.panel-image-preview { border-radius: 0px; }
.panel-body { overflow: hidden; }
.panel-image ~ input[type=checkbox] { position:absolute; top:- 30px; z-index: -1;}
.panel-image ~ input[type=checkbox] ~ .panel-body { height: 0px; padding: 0px;}
.panel-image ~ input[type=checkbox]:checked ~ .panel-body { height: auto; padding: 15px;}
.panel-image ~ .panel-footer a { padding: 0px 10px; font-size: 1.3em; color: rgb(100, 100, 100);}
</style>

<div id="head">
    <div id="menu">
        <?php  include("menu.php"); ?>
   </div>
</div>
	
<a name="viewcompletebadge"></a>
<div id="main">
	<!-- contents -->
	<?php 
	$portfolio_uid = (isset($_GET["u"]) && is_numeric($_GET["u"]) && strlen($_GET["u"])<12  && $_GET["u"]>0 ) ? $_GET["u"] : 0;

	if ( $portfolio_uid > 0 ) 
	{
		$arr_data_user = COMMONDB_MODULE::get_arr_relations_lists_aliases("users","id_user,name,email","WHERE id_user='$portfolio_uid'","id_user");
		$fullname = $arr_data_user[$portfolio_uid]["name"];
		$email = $arr_data_user[$portfolio_uid]["email"];
		
		print "<h1>$fullname</h1>";
			
        $where_data_earns = "WHERE user_id='$portfolio_uid' AND deleted='0' AND enabled='1' AND show_public='1'";
		$arr_data_earns   = COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns","earn_id,user_id","$where_data_earns","earn_id");
		
		$arr_data_earns   = COMMONDB_MODULE::get_list("badges_earns","$where_data_earns","earn_id");
		//var_dump($arr_data_earns);
		
		if ( count($arr_data_earns) > 0 ) 
		{
		?>
		
		<div class="container" style="margin-top:20px;">
		
		<div class="row form-group">

			<div class="col-lg-3 form-group" id="show-lateral-panel" style="display:none;">
			<?php 
			$i =1;
			foreach ($arr_data_earns AS $earn_id) 
			{
				 $obj_bg 	= new COMMONDB_MODULE("badges_earns", $earn_id);
				 $cryted_id 		= $obj_bg->crypted_id;
				 $user_id  		    = $obj_bg->user_id;
				 $institution  	    = $obj_bg->institution;
				 $course  		    = $obj_bg->course;
				 $course_desc	    = $obj_bg->course_desc;
				 $earn_fullname	    = $obj_bg->earn_fullname;
				 $earn_email	    = $obj_bg->earn_email;
				 //badge
				 $badge_img_name	= $obj_bg->badge_img_name;
				 $show_badge_img    =  ( $badge_img_name!='') ? "fileearn.php?bgid=$cryted_id&amp;".NOCACHE : "";
				 //get params
				 $arr_params		= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns_params","param_id,label,content","WHERE earn_id='$earn_id' AND deleted='0' ORDER BY param_id","");
				 $count_params 		= count($arr_params);
				 $url_download_badge_image = BADGES_IMAGE_GENERATOR_API_URL.APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$cryted_id.BADGES_ASSERTION_PREFIX_JSON_FILES;
			?>
				<div class="showpanel" id="showpanel-<?php echo $i?>">
	            <div class="panel panel-info" style="display:none;" id="bpanel-<?php echo $i?>">
	                <div class="panel-heading"><h3 class="panel-title"><?php echo $course?></h3></div>
	                <div class="panel-image hide-panel-body">
						<?php if ( $badge_img_name !='') { ?><img src='<?php echo $show_badge_img?>' class="panel-image-preview"><?php }?>	                    
	                </div>
	                <div class="panel-body">
	                    <h4>Institution</h4>
	                    <p class="small"><?php echo $institution?></p>
	                    <h4>Title</h4>
	                    <p class="small"><?php echo $course?></p>
	                    
	                    <h4>Description</h4>
	                    <p class="small"><?php echo $course_desc?></p>
	                    
	                    <?php if ( $count_params > 0 ) { ?>
	                    <h4><?php echo __("Evidences")?></h4>
							<?php for ($y = 0; $y < $count_params; $y++) { ?>
							<p class="small"><strong><?php echo $arr_params[$y]['label']?></strong>: <?php echo $arr_params[$y]['content']?></p>
							<?php }?>
	                    <?php }?>
	                </div>
	                <div class="panel-footer text-center panel-info">
	                    <a href="#viewcompletebadge" onclick="hideEarnBadge(<?php echo $i?>);"><label for="toggle-<?php echo $i?>"><span class="fa fa-times"></span></label></a>
	                    <a href="<?php echo $url_download_badge_image?>" target="_blank" title="<?php echo __("Download this Badge")?>"><span class="fa fa-download"></span></a>
	                </div>
	            </div></div>
			<?php 
			$i +=1;
			} ?>	
			</div>

			<div class="col-lg-9 form-group">
			<?php 
			$i =1; $x =1;
			foreach ($arr_data_earns AS $earn_id) 
			{
				 $obj_bg 	= new COMMONDB_MODULE("badges_earns", $earn_id);
				 $cryted_id 		= $obj_bg->crypted_id;
				 $course  		    = $obj_bg->course;
				 //badge
				 $badge_img_name	= $obj_bg->badge_img_name;
				 $show_badge_img    =  ( $badge_img_name!='') ? "fileearn.php?bgid=$cryted_id&amp;".NOCACHE : "";
				 $url_download_badge_image = BADGES_IMAGE_GENERATOR_API_URL.APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$cryted_id.BADGES_ASSERTION_PREFIX_JSON_FILES;
			?>
	        <div class="col-xs-12 col-md-3">
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h3 class="panel-title"><?php echo $course?></h3>
	                </div>
	                <div class="panel-image hide-panel-body">
						<?php if ( $badge_img_name !='') { ?>
							<a href="#viewcompletebadge" onclick="showEarnBadge(<?php echo $i?>);"><img src='<?php echo $show_badge_img?>' class="panel-image-preview"></a>
						<?php }?>	                    
	                </div>
	                <div class="panel-footer text-center">
	                    <a href="#viewcompletebadge" onclick="showEarnBadge(<?php echo $i?>);"><span class="fa fa-eye"></span></a>
	                    <a href="<?php echo $url_download_badge_image?>" target="_blank" title="<?php echo __("Download this Badge")?>"><span class="fa fa-download"></span></a>
	                </div>
	            </div>
	        </div>	
			<?php echo ( $x == 4 ) ? '<div class="col-md-12"><hr></div>' : ''; ?>
			<?php 
			$x = ($x==4) ? 1 : $x+1;
			$i +=1;
			} ?>
			</div>
			
		</div></div>
		<?php
		}
	}
	?>
	<!-- /contents -->
</div>

<div class="container"><div class="col-lg-12"><br></div></div>

<script>
function hideEarnBadge(id){
	$("#show-lateral-panel").hide();
	$(".showpanel").hide();
	$("#showpanel-"+id).hide();
	$("#bpanel-"+id).hide();		
}

function showEarnBadge(id){
	$("#show-lateral-panel").show();
	$(".showpanel").hide();
	$("#showpanel-"+id).show();
	$("#bpanel-"+id).show();
}
</script>

<div id='footer' class='wrapper'>
    <?php include("footer.php"); ?>
</div>
<div id='copyright' class='wrapper'>
    <?php  include("copyright.php"); ?>
</div>

</body>
</html>