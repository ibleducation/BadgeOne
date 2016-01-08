<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include("head.php"); ?>
    <link rel="stylesheet" href="css/portfolio.css" type="text/css" />
</head>
<body>

<div id="head">
    <div id="menu">
        <?php  include("menu.php"); ?>
   </div>
</div>
	
<?php 
$portfolio_uid = (isset($_GET["u"]) && is_numeric($_GET["u"]) && strlen($_GET["u"])<12  && $_GET["u"]>0 ) ? $_GET["u"] : 0;
$arr_data_earns= array();

if ( $portfolio_uid > 0 )
{
	$arr_urls_user_info = array (
			"url_website" => "fa fa-sitemap",
			"url_social_facebook" => "fa fa-facebook",
			"url_social_twitter" => "fa fa-twitter",
			"url_social_gplus" => "fa fa-google-plus",
			"url_social_linkedin" => "fa fa-linkedin",
	);
	
	$arr_data_user = COMMONDB_MODULE::get_arr_relations_lists_aliases("users","id_user,name,email,picture,about_user,url_website,url_social_facebook,url_social_twitter,url_social_gplus,url_social_linkedin","WHERE id_user='$portfolio_uid'","id_user");
	$fullname = $arr_data_user[$portfolio_uid]["name"];
	$email = $arr_data_user[$portfolio_uid]["email"];
	$about_user = $arr_data_user[$portfolio_uid]["about_user"];

	$picture = ($arr_data_user[$portfolio_uid]["picture"]!='') ? $arr_data_user[$portfolio_uid]["picture"] : "default.png";
	$picture_css = ($arr_data_user[$portfolio_uid]["picture"]!='') ? 'class="img-responsive thumbnail"' : "";
	$show_picture = "<img src=\"".APP_GENERAL_REPO_USERS_PICTURES."/$picture\" $picture_css>"; 

	$social_links = "";
	foreach ($arr_urls_user_info AS $key=>$ico){
		$url_social_data  = $arr_data_user[$portfolio_uid][$key];
		$social_links .= ( $url_social_data!='' ) ? '<a href="'.$url_social_data.'" target="_blank" class="btn btn-md btn-default"><i class="'.$ico.'"></i></a> ': "";
	}
	
	$where_data_earns = "WHERE user_id='$portfolio_uid' AND deleted='0' AND enabled='1' AND show_public='1'";
	$arr_data_earns   = COMMONDB_MODULE::get_list("badges_earns","$where_data_earns","earn_id");
}
?>

<a name="viewcompletebadge"></a>
<div id="main">
	
	<div class="row"><div class="col-lg-12">
		<div class="col-lg-3" style="padding-top:20px;"><?php echo (isset($picture)) ? "$show_picture" : "";?></div>
		<div class="col-lg-4"><?php echo (isset($fullname) )? "<h1>$fullname</h1>" : "" ?></div>
		<div class="col-lg-5">
			<?php if (isset($about_user)) { ?>
				<h3><?php echo __("About me")?></h3>
				<p><?php echo nl2br($about_user)?></p>
			<?php } ?>
			<?php if (isset($social_links) && $social_links!="") { ?>
				<p><?php echo $social_links?></p>
			<?php } ?>
		</div>
	</div></div>

	<div id="wrapper">
		<div class="container" id="tabContainer">
		    <ul class="nav nav-tabs">
		        <li class="nav active"><a href="#earned" data-toggle="tab"><?php echo __("Earned")?></a></li>
		       <!-- <li class="nav"><a href="#given" data-toggle="tab"><?php echo __("Given")?></a></li>  --> 
		    </ul>
		
		    <!-- Tab panes -->
		    <div class="tab-content tabscontent">
		        <div class="tab-pane fade in active" id="earned">
		        
				<!-- earned -->
				<?php  if ( $portfolio_uid > 0 && count($arr_data_earns) > 0 ) { ?>
					<div class="container" style="margin-top:20px;">
					
					<div class="row form-group" style="width:90%">
			
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
				                <div class="panel-heading">
				                	<a href="#viewcompletebadge" onclick="hideEarnBadge(<?php echo $i?>);" class="pull-right"><label for="toggle-<?php echo $i?>"><span class="fa fa-times"></span></label></a>
				                	<h3 class="panel-title"><?php echo $course?></h3>
				                </div>
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
				<?php }?>
				<!-- /earned -->		        
		        
		        </div>
		        
		        <!-- <div class="tab-pane fade" id="given"></div> -->
		    </div>
		</div>
	</div>

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
