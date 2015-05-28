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
	
	    <div id='bread'><a href='/'>Home</a> > Users</div>
	    <div class='page_header'>Users</div>
	    
	    <!-- contents -->
	    <?php include "events.php";?>
		<?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="row col-lg-12 alert alert-danger" style="color:red; margin-top:10px;">'.$event_errors.'</div>'; }?>
		<?php if ( isset($event_success) && $event_success!="" ) { print '<div class="row col-lg-12  alert alert-success" style="color:green; margin-top:10px;">'.$event_success.'</div>'; }?>
		<?php $k_search_user = ( isset($_POST['k_user']) && strlen(trim($_POST['k_user']))>0 )  ? $_POST['k_user'] : ''; ?>
	
	    <div class="col-lg-12 col-md-12">
		<form action="#" method="post" class="form-inline">
			<div class="form-group"><label for="k_user">Find user</label>
				<input type="text" class="form-control" name="k_user" id="k_user" placeholder="name or email" required="required" value="<?php echo $k_search_user ?>">
			</div>
			<button type="submit" class="btn btn-info" title="search"><i class="fa fa-search"></i></button>
			<?php if ( $k_search_user!='' ) { ?><a href="./users.php" class="btn btn-danger" title="clear"><i class="fa fa-times-circle"></i></a><?php }?>
		</form>
	    </div>
	    
	    <div class="container"><div class="col-lg-12"><br></div></div>
	    
		<table class="table table-condensed">
		      <thead>
		        <tr class="valign-middle">
		          <th>#</th>
		          <th>Name</th>
		          <th>Email</th>
		          <th class="tocenter">Profile</th>
		          <th class="tocenter">Active</th>
		          <th>Created</th>
		          <th class="tocenter">Delete</th>
		        </tr>
		      </thead>
		      <tbody>
			<?php 
			$user_id	  = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
			$user_profile = ( isset($logged_profile) && $logged_profile!='' ) ? $logged_profile : '';
			$arr_profiles = array('general'=>"General",'issuer'=>"Issuer");
			$where_query = ( $k_search_user!='' ) ? " AND (name LIKE '%$k_search_user%' OR email LIKE '%$k_search_user%' ) " : "";
			$arr_users = COMMONDB_MODULE::get_arr_relations_lists_aliases("users","id_user,profile,email,name,activated,date_created","WHERE id_user!=$user_id AND profile!='admin' $where_query ORDER BY name DESC","");
			if (count($arr_users)>0){
				foreach ($arr_users as $item) {
				//allow delete
				$total_b_issue = COMMONDB_MODULE::count_values("badges_issuers","badge_id","WHERE user_id='".$item["id_user"]."'");
				$total_b_earn  = COMMONDB_MODULE::count_values("badges_earns","earn_id","WHERE user_id='".$item["id_user"]."'");
				$total_b_revok = COMMONDB_MODULE::count_values("badges_revocations","revocation_id","WHERE user_id='".$item["id_user"]."'");
				$allow_delete  = ( ($total_b_earn+$total_b_issue+$total_b_revok)>0 )  ? 0 : 1 ;
				$allow_change_profile  = ( $total_b_issue>0 )  ? 0 : 1 ;
			 ?>
			 <tr class="valign-middle">
		          <td><?php echo $item['id_user']?></td>
		          <td><strong><?php echo $item['name']?></strong></td>
		          <td><?php echo $item['email']?></td>
		          <td class="tocenter">
					  <?php if ( $allow_change_profile ==1 ) { ?>
					  <form action="#" method="post">
			          <input type="hidden" name="k_user" value="<?php echo $k_search_user?>">
			          <input type="hidden" name="event" value="change_user_profile">
					  <input type="hidden" name="user_id" value="<?php echo get_crypted_id($item["id_user"]);?>">
					  <select class="form-control form-inline" id="profile" name="profile" onchange="submit();">
			          <?php 
			          	foreach ($arr_profiles AS $kp=>$vp) {
			          		$sel_prof = ($kp == $item['profile']) ? 'selected="selected"' : '';
			          		print '<option value="'.$kp.'" '.$sel_prof.'>'.$vp.'</option>';
			          	}
			          ?>
			          </select></form>
			          <?php } else { ?>
			          <?php echo $arr_profiles[$item['profile']]?>
			          <?php } ?>
		          </td>
		          <td class="tocenter">
		          	  <?php $btn_active_css = ($item['activated']==1) ? "label-success" : "label-danger" ; $btn_active_txt = ($item['activated']==1) ? "YES" : "NO"; ?>
					  <form action="#" method="post">
			          <input type="hidden" name="k_user" value="<?php echo $k_search_user?>">
			          <input type="hidden" name="event" value="change_user_active">
					  <input type="hidden" name="user_id" value="<?php echo get_crypted_id($item["id_user"]);?>">
					  <button type="submit" class="label <?php echo $btn_active_css?>"><?php echo $btn_active_txt?></button>
					  </form>	          
		          </td>
		          <td><?php echo $item['date_created']?></td>
		          
		          <td class="tocenter">
		          	 <?php if ( $allow_delete == 0 ) { ?>
		          	  <a href="#" class="btn btn-default btn-sm" onclick="alert('Could not be deleted');return false;">Delete <i class="fa fa-trash"></i></a>
			         <?php } else { ?>
			          <form action="#" method="post" onsubmit="return checkConfirm('Are you sure?');">
				          <input type="hidden" name="k_user" value="<?php echo $k_search_user?>">
				          <input type="hidden" name="event" value="delete_user">
				          <input type="hidden" name="user_id" value="<?php echo get_crypted_id($item["id_user"]);?>">
				          <button type="submit" class="btn btn-danger btn-sm">Delete <i class="fa fa-trash"></i></button>
			          </form>
			         <?php }  ?>
		          </td>	          
		        </tr>
			<?php } } else { ?>
				<tr><td colspan="7" class="alert alert-danger">Nothing found</td></tr>
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
	    <?php  include("copyright.php"); ?>
	</div>

</body>
</html>
