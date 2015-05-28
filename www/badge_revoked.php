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

    <div id='bread'><a href='/'>Home</a> > Badges revoked</div>
    <div class='page_header'>Badges revoked</div>
    
    <?php 
    //
    // Get needed values
    //
    $sel_badge_id	= ( isset($_POST["badge_id"]) && $_POST["badge_id"]!='') ? $_POST["badge_id"] : '';
    $badge_id 		= ( $sel_badge_id!="") ? COMMONDB_MODULE::decrypt_id("badges_issuers", $sel_badge_id) : '0';
    $user_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
    $user_id	  	= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
    $user_profile 	= ( isset($logged_profile) && $logged_profile!='' ) ? $logged_profile : '';
    $k_search_user 	= ( isset($_POST['k_user']) && strlen(trim($_POST['k_user']))>0 )  ? $_POST['k_user'] : '';
    $k_search_user 	= ( isset($_POST['clear']) )  ? '' : $k_search_user; //clear form
    ?>

    <div class="col-lg-12 col-md-12">
    	<div class="pull-left">
			<form action="#" method="post" class="form-inline">
				<input type="hidden" id="badge_id" name="badge_id" value="<?php echo $sel_badge_id?>">
				<div class="form-group"><label for="k_user">Find user</label>
					<input type="text" class="form-control" name="k_user" id="k_user" placeholder="name or email" required="required" value="<?php echo $k_search_user ?>">
				</div>
				<button type="submit" class="btn btn-info" title="search"><i class="fa fa-search"></i></button>
				<?php if ( $k_search_user!='' ) { ?>
					<button type="submit" class="btn btn-danger" title="clear" id="clear" name="clear"><i class="fa fa-times-circle"></i></button>
				<?php }?>
			</form>
		</div>
		<div class="pull-right"><a href="./issuer.php" class="btn btn-info btn-md"><i class="fa fa-backward"></i> BACK TO ISSUER LIST</a></div>
    </div>
    
    <div class="container"><div class="col-lg-12"><br></div></div>
    
	<table class="table table-condensed">
	      <thead>
	      	<tr class="valign-middle">
	          <th>#uidrevk</th>
	          <th>Date</th>
	          <th>Reason</th>
	          <th>Name</th>
	          <th>Email</th>
	          <th>Revoked by</th>
	        </tr>
	      </thead>
	      <tbody>
		<?php 
		$arr_users = array();

		$where_query 	= ( $k_search_user!='' ) ? " AND (earn_fullname LIKE '%$k_search_user%' OR earn_email LIKE '%$k_search_user%' ) " : "";
		$arr_revk_list	= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_revocations","revocation_id,revocation_reason,earn_fullname,earn_email,date_deleted,deleted_by", "WHERE badge_id='$badge_id' $where_query ORDER BY date_deleted DESC","revocation_id");
		
		if (count($arr_revk_list)>0){
			foreach ($arr_revk_list as $item) {
				$revoked_by = ($item['deleted_by']>0) ? COMMONDB_MODULE::get_value("users", "name", $item["deleted_by"]) : "system";
		 ?>
		<tr class="valign-middle">
	          <td><small><?php echo get_crypted_id( $item['revocation_id']) ?></small></td>
	          <td><small><?php echo $item['date_deleted']?></small></td>
	          <td><small><?php echo $item['revocation_reason']?></small></td>
	          <td><small><?php echo $item['earn_fullname']?></small></td>
	          <td><small><?php echo $item['earn_email']?></small></td>
	          <td><small><?php echo $revoked_by?></small></td>
	        </tr>
		<?php } } else { ?>
			<tr><td colspan="6" class="alert alert-danger">Nothing found</td></tr>
		<?php } ?>
		</tbody>
	</table>
	<!-- /contents -->
</div>

<div class="container"><div class="col-lg-12"><br></div></div>

<div id='footer' class='wrapper'>
    <?php include("footer.php"); ?>
</div>
<div id='copyright' class='wrapper'>
    <?php include("copyright.php"); ?>
</div>

</body>
</html>
