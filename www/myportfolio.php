<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

    <?php include("head.php"); ?>

    <link rel="stylesheet" href="css/portfolio.css" type="text/css" />
</head>
<body>

<div id="head">
    <div id="menu"><?php  include("menu.php"); ?></div>
</div>

<?php  if( isset($_COOKIE["UID"]) && $_COOKIE["UID"]!='' ) { ?>

<?php print $_COOKIE["UID"]; }?>

<?php 
$portfolio_uid = (isset($_GET["u"]) && is_numeric($_GET["u"]) && strlen($_GET["u"])<12  && $_GET["u"]>0 ) ? $_GET["u"] : 0;
$is_this_portfolio_logged_user_id  = ( isset($_COOKIE["UID"]) && $_COOKIE["UID"]!='' && $portfolio_uid == $_COOKIE["UID"] ) ? 1 : 0;

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
	
	//badges from badgeone 
	$arr_badges_badgeone =
	COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns",
		 "earn_id AS item_id, user_id, institution, course AS badge_name, course_desc AS badge_description, badge_img_name AS badge_image, '' AS badge_image_url,
			date_created AS issued_on, '' AS assertion_evidence, '' AS badge_criteria, '' AS imported_from, DATE_FORMAT(date_created,'%Y%m%d%H%i%s') AS orderdate",
		 "WHERE user_id='$portfolio_uid' AND deleted='0' AND enabled='1' AND show_public='1'","");
	
	//badges imported
	$arr_badges_imported =
	COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns_imported",
			"imported_id AS item_id, user_id, issuer_institution_name AS institution, badge_name, badge_description, badge_image AS badge_image, badge_imageUrl AS badge_image_url,  
			assertion_issued_on AS issued_on, assertion_evidence, badge_criteria, imported_from, DATE_FORMAT(assertion_issued_on,'%Y%m%d%H%i%s') AS orderdate",
			"WHERE user_id='$portfolio_uid' AND show_public='1'","");
	
	//merge badges
	$arr_merge_badges = array_merge($arr_badges_badgeone,$arr_badges_imported);
	$arr_merge_badges = (count($arr_merge_badges)>0) ? sort_by_array_key($arr_merge_badges,'orderdate','DESC') : array();
	//var_dump($arr_merge_badges);
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
		    </ul>
		
		    <!-- Tab panes -->
		    <div class="tab-content tabscontent">
		        <div class="tab-pane fade in active" id="earned">
		        
				<!-- earned -->
				<?php  if ( $portfolio_uid > 0 && count($arr_merge_badges) > 0 ) { ?>
					<div class="container" style="margin-top:20px;">
					
					<div class="row form-group" style="width:90%">
			
						<div class="col-lg-3 form-group" id="show-lateral-panel" style="display:none;">
						<?php 
						
						$i =1;
						foreach ($arr_merge_badges AS $item) 
						{
							$item_id			= $item["item_id"];
							$cryted_id 			= get_crypted_id( $item["item_id"] );
							$user_id  		    = $item["user_id"];
							$institution	    = $item["institution"];
							$badge_name		 	= $item["badge_name"];
							$badge_description	= $item["badge_description"];
							$badge_image		= $item["badge_image"];
							$badge_image_url	= $item["badge_image_url"];
							if ( $item["imported_from"] == '' ) {
								$show_badge_img    =  ( $badge_image!='') ? "fileearn.php?bgid=$cryted_id&amp;".NOCACHE : "";
								$badge_evidence = "";
								$arr_params		= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns_params","param_id,label,content","WHERE earn_id='$item_id' AND deleted='0' ORDER BY param_id","");
								$count_params 		= count($arr_params);
								$url_download_badge_image = BADGES_IMAGE_GENERATOR_API_URL.APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$cryted_id.BADGES_ASSERTION_PREFIX_JSON_FILES;
								$url_linkedin = ( $is_this_portfolio_logged_user_id == 1 ) ? "https://www.linkedin.com/shareArticle?mini=true&url=".SERVER_HTTP_HOST.""."/share_badge/$user_id/$cryted_id/0/&title=$badge_name" : "";
							} else {
								$show_badge_img    = ( $badge_image_url!='') ? "$badge_image_url&amp;".NOCACHE :  ( ($badge_image!='') ? "$badge_image&amp;".NOCACHE : "" );
								$badge_evidence    = ($item["assertion_evidence"]!='') ? $item["assertion_evidence"] : $item["badge_criteria"];
								$count_params 	   = 0;
								$url_download_badge_image = ( $badge_image_url!='') ? "$badge_image_url&amp;".NOCACHE : "";
								$url_linkedin="";
							}
							?>
							<div class="showpanel" id="showpanel-<?php echo $i?>">
				            <div class="panel panel-info" style="display:none;" id="bpanel-<?php echo $i?>">
				                <div class="panel-heading">
				                	<a href="#viewcompletebadge" onclick="hideEarnBadge(<?php echo $i?>);" class="pull-right"><label for="toggle-<?php echo $i?>"><span class="fa fa-times"></span></label></a>
				                	<h3 class="panel-title"><?php echo $badge_name?></h3>
				                </div>
				                <div class="panel-image hide-panel-body">
									<?php if ( $badge_image !='') { ?><img src='<?php echo $show_badge_img?>' class="panel-image-preview"><?php }?>            
				                </div>
				                <div class="panel-body">
				                    <h4>Institution</h4>
				                    <p class="small"><?php echo $institution?></p>

				                    <h4>Title</h4>
				                    <p class="small"><?php echo $badge_name?></p>

				                    <h4>Description</h4>
				                    <p class="small"><?php echo $badge_description?></p>
				                    
				                    <?php if ( $count_params > 0 ) { ?>
				                    <h4><?php echo __("Evidences")?></h4>
										<?php for ($y = 0; $y < $count_params; $y++) { ?>
										<p class="small"><strong><?php echo $arr_params[$y]['label']?></strong>: <?php echo $arr_params[$y]['content']?></p>
										<?php }?>
				                    <?php }?>
				                    
				                    <?php if ( $badge_evidence!='' ) { ?>
				                    <h4><?php echo __("Evidences")?></h4>
										<p class="small"><?php echo $badge_evidence?></p>
				                    <?php }?>
				                </div>
				                <div class="panel-footer text-center panel-info">
				                    <a href="#viewcompletebadge" onclick="hideEarnBadge(<?php echo $i?>);"><label for="toggle-<?php echo $i?>"><span class="fa fa-times"></span></label></a>
				                    <?php if ($url_download_badge_image!=''){ ?><a href="<?php echo $url_download_badge_image?>" target="_blank" title="<?php echo __("Download this Badge")?>"><span class="fa fa-download"></span></a><?php }?>
				                    <?php if ($url_linkedin!=''){ ?><a href="<?php echo $url_linkedin?>" target="_blank" title="<?php echo __("Share on Linkedin")?>"><span class="fa fa-linkedin"> share</span></a><?php }?>
				                </div>
				            </div></div>
						<?php 
						$i +=1;
						} ?>	
						</div>
			
						<div class="col-lg-9 form-group">
						<?php 
						$i =1; $x =1;
						foreach ($arr_merge_badges AS $item) 
						{
							$cryted_id 			= get_crypted_id( $item["item_id"] );
							$institution	    = $item["institution"];
							$badge_name		 	= $item["badge_name"];
							$badge_description	= $item["badge_description"];
							$badge_image		= $item["badge_image"];		
							$badge_image_url	= $item["badge_image_url"];
							if ( $item["imported_from"] == '' ) {
								$show_badge_img    =  ( $badge_image!='') ? "fileearn.php?bgid=$cryted_id&amp;".NOCACHE : "";
								$url_download_badge_image = BADGES_IMAGE_GENERATOR_API_URL.APP_GENERAL_REPO_BADGES_EARN_REMOTE."/".$cryted_id.BADGES_ASSERTION_PREFIX_JSON_FILES;
								$url_linkedin = ( $is_this_portfolio_logged_user_id == 1 ) ? "https://www.linkedin.com/shareArticle?mini=true&url=".SERVER_HTTP_HOST.""."/share_badge/$user_id/$cryted_id/0/&title=$badge_name" : "";
							} else{
								$show_badge_img    = ( $badge_image_url!='') ? "$badge_image_url&amp;".NOCACHE :  ( ($badge_image!='') ? "$badge_image&amp;".NOCACHE : "" );
								$url_download_badge_image = ( $badge_image_url!='') ? "$badge_image_url&amp;".NOCACHE : "";
								$url_linkedin="";
							}
						?>
				        <div class="col-xs-12 col-md-3">
				            <div class="panel panel-default">
				                <div class="panel-heading">
				                    <h3 class="panel-title"><?php echo $badge_name?></h3>
				                </div>
				                <div class="panel-image hide-panel-body">
									<?php if ( $badge_image !='') { ?>
										<a href="#viewcompletebadge" onclick="showEarnBadge(<?php echo $i?>);"><img src='<?php echo $show_badge_img?>' class="panel-image-preview"></a>
									<?php }?>	                    
				                </div>
				                <div class="panel-footer text-center">
				                    <a href="#viewcompletebadge" onclick="showEarnBadge(<?php echo $i?>);"><span class="fa fa-eye"></span></a>
				                    <?php if ($url_download_badge_image!=''){ ?><a href="<?php echo $url_download_badge_image?>" target="_blank" title="<?php echo __("Download this Badge")?>"><span class="fa fa-download"></span></a><?php }?>
				                    <?php if ($url_linkedin!=''){ ?><a href="<?php echo $url_linkedin?>" target="_blank" title="<?php echo __("Share on Linkedin")?>"><span class="fa fa-linkedin"> share</span></a><?php }?>
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


		        
		        </div>
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