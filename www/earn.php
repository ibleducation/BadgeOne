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
    <div class='page_header'><?php echo __("Earns of Issuer Badges")?></div>

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
	          <th><?php echo __("Preview")?></th>
	          <th><?php echo __("Delete")?></th>
	        </tr>
	      </thead>
	      <tbody>
		<?php
		$user_id		=  ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$arr_earn_list	= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers bi,badges_earns be","be.earn_id AS earn_id,be.user_id AS user_id,be.earn_email AS earn_email, be.earn_fullname AS earn_fullname,bi.institution AS institution,bi.course AS course","WHERE bi.badge_id=be.badge_id AND be.deleted=0 AND bi.user_id='$user_id'","earn_id");
		foreach ($arr_earn_list as $item) { ?>
		   <tr>
	          <td><?php echo $item['earn_id']?></td>
	          <td><?php echo $item['user_id']?></td>
	          <td><?php echo $item['earn_fullname']?></td>
	          <td><?php echo $item['earn_email']?></td>
	          <td><?php echo $item['course']?></td>
	          <td><?php echo $item['institution']?></td>
	 			<td>
	 			  <a href="view_badge_earn.php?badge_id=<?php echo get_crypted_id($item["earn_id"]);?>" target="_blank"><?php echo __("Preview")?></a>
	          </td>
	          <td>
		          <form action="#" method="post" onsubmit="return checkConfirm('<?php echo __("Are you sure?")?>');">
		          <input type="hidden" name="event" value="delete_earn">
		          <input type="hidden" name="earn_id" value="<?php echo get_crypted_id($item["earn_id"]);?>">
		          <button type="submit" class=""><?php echo __("Delete")?></button>
		          </form>
	          </td>
	        </tr>
		<?php }?>
		</tbody>
	</table>	
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