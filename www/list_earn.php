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

    <div id='bread'><a href='/'><?php echo __("Home")?></a> > <?php echo __("Earns of Issuer Badges")?></div>
    <div class='page_header'><?php echo __("List of Earned Badges")?></div>
    
    <?php 
    //
    // Get needed values
    //
    $sel_badge_id	= ( isset($_POST["badge_id"]) && $_POST["badge_id"]!='') ? $_POST["badge_id"] : '';
    $badge_id 		= ( $sel_badge_id!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $sel_badge_id) : '0';
    $user_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
    ?>
    
    <div class="pull-left">
		<form action="badge_earn.php" method="post">
		<input type="hidden" name="badge_id" id="badge_id" value="<?php echo $sel_badge_id;?>">
		<button type="submit" class="btn btn-success" type="button"> <i class="fa fa-star"></i> <?php echo __("EARN A NEW BADGE")?> </button>
		</form>
    </div>
	<div class="pull-right"><a href="./issuer.php" class="btn btn-info btn-md"><i class="fa fa-backward"></i> <?php echo __("BACK TO ISSUER LIST")?> </a></div>

	<!-- contents -->
    <?php include "events.php";?>
	<?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="row col-lg-12 alert alert-danger" style="color:red; margin-top:10px;">'.$event_errors.'</div>'; }?>
	<?php if ( isset($event_success) && $event_success!="" ) { print '<div class="row col-lg-12  alert alert-success" style="color:green; margin-top:10px;">'.$event_success.'</div>'; }?>
    	
	<table class="table table-condensed">
	      <thead>
	        <tr>
	          <th>#</th>
	          <th><?php echo __("UserID")?></th>
	          <th><?php echo __("Fullname")?></th>
	          <th><?php echo __("Email")?></th>
	          <th><?php echo __("Course")?></th>
	          <th><?php echo __("Institution")?></th>
	          <th class="tocenter"><?php echo __("Preview")?></th>
	          <th class="tocenter" title="Json File Badge Assertion">JAsser</th>
	          <th class="tocenter" title="Json File Badge Class">JClass</th>
	          <th class="tocenter"><?php echo __("Revoke")?></th>
	        </tr>
	      </thead>
	      <tbody>
		<?php
		$filter_badges  = ($user_id>0 && $logged_profile=='admin') ? "" : "AND bi.user_id='$user_id'";
		$arr_earn_list	= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers bi,badges_earns be","be.earn_id AS earn_id,be.user_id AS user_id,be.earn_email AS earn_email, be.earn_fullname AS earn_fullname,bi.institution AS institution,bi.course AS course","WHERE bi.badge_id=be.badge_id AND bi.badge_id='$badge_id' AND be.deleted=0 $filter_badges","earn_id");
		foreach ($arr_earn_list as $item) { ?>
		<?php 
			//check json files
			$badges_assertion_file_path = APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".get_crypted_id($item['earn_id']).BADGES_ASSERTION_PREFIX_JSON_FILES;
			$badges_assertion_file_path = (file_exists($badges_assertion_file_path)) ? $badges_assertion_file_path : "#";
			$badges_assertion_css 		= ($badges_assertion_file_path=='#') ? "btn-danger" : "btn-warning";
			$badges_assertion_target	= ($badges_assertion_file_path=='#') ? " onclick=\"alert('".__("None")."');return false;\" " : ' target="_blank" ';
			
			$badges_class_file_path = APP_GENERAL_REPO_BADGES_EARN_LOCAL."/".get_crypted_id($item['earn_id']).BADGES_CLASS_PREFIX_JSON_FILES;
			$badges_class_file_path = (file_exists($badges_class_file_path)) ? $badges_class_file_path : "#";
			$badges_class_css 		= ($badges_class_file_path=='#') ? "btn-danger" : "btn-warning"; 
			$badges_class_target	= ($badges_class_file_path=='#') ? " onclick=\"alert('".__("None")."');return false;\" " : ' target="_blank" ';
		?>
		   <tr>
	          <td><?php echo $item['earn_id']?></td>
	          <td><?php echo $item['user_id']?></td>
	          <td><?php echo $item['earn_fullname']?></td>
	          <td><?php echo $item['earn_email']?></td>
	          <td><?php echo $item['course']?></td>
	          <td><?php echo $item['institution']?></td>
	          <td class="tocenter"><a href="view_badge_earn.php?badge_id=<?php echo get_crypted_id($item["earn_id"]);?>" target="_blank" class="btn btn-info" title="<?php echo __("Preview")?>"><i class="fa fa-search"></i></a></td>
	         
	          <th class="tocenter"><a href="<?php echo $badges_assertion_file_path?>" <?php echo $badges_assertion_target?> class="btn <?php echo $badges_assertion_css?>"><i class="fa fa-file-code-o"></i></a></th>
	          <th class="tocenter"><a href="<?php echo $badges_class_file_path?>" <?php echo $badges_class_target?> class="btn <?php echo $badges_class_css?>"><i class="fa fa-file-code-o"></i></a></th>
	         
	          <td class="tocenter">
		          <form action="#" method="post" onsubmit="return checkConfirm('<?php echo __("Are you sure?")?>');">
		          <input type="hidden" name="event" value="delete_earn">
		          <input type="hidden" name="badge_id" value="<?php echo $sel_badge_id;?>">
		          <input type="hidden" name="earn_id" value="<?php echo get_crypted_id($item["earn_id"]);?>">
		          <button type="submit" class="btn btn-danger"><?php echo __("Revoke")?> <i class="fa fa-trash"></i></button>
		          </form>
	          </td>
	        </tr>
		<?php }?>
		</tbody>
	</table>	
	<!-- /contents -->

</div>

<div class="container"><div class="col-lg-12"><br></div></div>

<div class="container"><div class="col-lg-12">
	<p class="alert alert-warning">
		<b><?php echo __("More Info")?></b><br>
		* JAsser => <?php echo __("Links to Json File")?> "Badge Assertion" (standards openbadges)<br>
		* JBClass => <?php echo __("Links to Json File")?> "Badge Class" (standards openbadges)<br>	
		* <?php echo __("Online tool")?> => <?php echo __("validate")." ".__("json structure")?>: <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a> <br>
		* <?php echo __("Online tool")?> => <?php echo __("validate")?> openbadges Assertion: <a href="http://validator.openbadges.org/" target="_blank">http://validator.openbadges.org/</a> <br>
	</p>
</div></div>

<div class="container"><div class="col-lg-12"><br></div></div>

<div id='footer' class='wrapper'>
    <?php include("footer.php"); ?>
</div>
<div id='copyright' class='wrapper'>
    <?php  include("copyright.php"); ?>
</div>

</body>
</html>