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

    <div id='bread'><a href='/'>Home</a> > Issuer Panel</div>
    <div class='page_header'>Issuer : Managed badges <br></div>

    <!-- contents -->
    <?php include "events.php";?>
    <?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="row col-lg-12 alert alert-danger" style="color:red; margin-top:10px;">'.$event_errors.'</div>'; }?>
    <?php if ( isset($event_success) && $event_success!="" ) { print '<div class="row col-lg-12  alert alert-success" style="color:green; margin-top:10px;">'.$event_success.'</div>'; }?>
    <?php $k_search_issuer = ( isset($logged_profile) && $logged_profile=='admin' && isset($_POST['k_issuer']) && strlen(trim($_POST['k_issuer']))>0 )  ? $_POST['k_issuer'] : ''; ?>
    <?php $list_issuers = ( isset($logged_profile) && $logged_profile=='admin' ) ? COMMONDB_MODULE::get_arr_relations_lists_aliases("users","id_user,name,email","WHERE profile='issuer' ORDER BY name") : array();?>

    <div class="">
    	<?php if ( count($list_issuers)>0 ) {  ?>
    	<div class="pull-left">
    		<form action="#" method="post" class="form-inline">
			<div class="form-group"><label for="k_user">Select Issuer</label>
				<select id="k_issuer" name="k_issuer" class="form-control" required="required">
				<option value=''>--- Select ---</option>
				<?php foreach ($list_issuers as $issuer) {
					$k_selected = ( $k_search_issuer!='' && $k_search_issuer==$issuer["id_user"]) ? "selected='selected'" : "";
					print '<option value="'.$issuer['id_user'].'" '.$k_selected.'>'.$issuer['name'].' ( '.$issuer['email'].' )</option>';
				}?>
				</select>
			</div>
			<button type="submit" class="btn btn-info" title="search"><i class="fa fa-search"></i></button>
			<?php if ( $k_search_issuer!='' ) { ?><a href="./issuer.php" class="btn btn-danger" title="clear"><i class="fa fa-times-circle"></i></a><?php }?>
		</form>
    	</div>
    	<?php }   ?>
    	<div class="pull-right">
    		<form action="badge_edit.php" method="post"><button type="submit" class="btn btn-success btn-md">ADD NEW BADGE <i class="fa fa-star"></i></button></form>
    	</div>
    </div>
    
    <div class="container"><div class="col-lg-12"><br></div></div>
    
	<table class="table table-condensed">
	      <thead>
	        <tr class="valign-middle">
	          <th>#</th>
	          <th>Course</th>
	          <th class="tocenter">Enabled</th>
	          <th class="tocenter">Published</th>
	          <th class="tocenter">Edit</th>
	          <th class="tocenter">Preview</th>
	          <th class="tocenter">Earn</th>
	          <th class="tocenter">Revoke</th>
	          <th class="tocenter">Delete</th>
	        </tr>
	      </thead>
	      <tbody>
		<?php 
		$user_id = ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0';
		$filter_badges = ($user_id>0 && $logged_profile=='admin') ? "" : "AND user_id='$user_id'";
		$filter_badges = ($user_id>0 && $logged_profile=='admin' && $k_search_issuer!='') ? "AND user_id='$k_search_issuer'" : $filter_badges;
		$arr_badges_list= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers","badge_id,user_id,institution,course,course_desc,enabled,published","WHERE deleted=0 $filter_badges ORDER BY badge_id DESC","badge_id");
		if (count($arr_badges_list)>0) {
			foreach ($arr_badges_list as $item) {
			$total_b_earns = COMMONDB_MODULE::count_values("badges_earns","earn_id","WHERE badge_id='".$item["badge_id"]."'");
			$total_b_revok = COMMONDB_MODULE::count_values("badges_revocations","revocation_id","WHERE badge_id='".$item["badge_id"]."'");
			$total_b_count = $total_b_earns+$total_b_revok;
		?>
		<tr class="valign-middle">
	          <td><?php echo $item['badge_id']?></td>
	         
	          <td><strong><?php echo $item['course']?></strong><br><small><em><?php echo trim_text($item['course_desc'],65 )?></em></small></td>
	          
	          <td class="tocenter"><?php echo ($item['enabled']==1) ? "<span class=\"label label-success\">YES</span>" : "<span class=\"label label-danger\">NO</span>"?></td>
	          
	          <td class="tocenter">
	          	  <?php $btn_publish_css = ($item['published']==1) ? "label-success" : "label-danger" ; $btn_publish_txt = ($item['published']==1) ? "YES" : "NO"; ?>
				  <form action="#" method="post">
		          <input type="hidden" name="k_issuer" value="<?php echo $k_search_issuer?>">
		          <input type="hidden" name="event" value="publish_badge">
				  <input type="hidden" name="badge_id" value="<?php echo get_crypted_id($item["badge_id"]);?>">
				  <button type="submit" class="label <?php echo $btn_publish_css?>"><?php echo $btn_publish_txt?></button>
				  </form>	          
	          </td>
	          
	          <td class="tocenter">
				 <?php if ( $total_b_count > 0 ) { ?>
	         		<a href="#" class="btn btn-default clearmargin" onclick="alert('Could not be modified');return false;">Edit <i class="fa fa-edit"></i></a>
		          <?php } else { ?>
		          <form action="badge_edit.php" method="post" class="clearmargin"><input type="hidden" name="badge_id" value="<?php echo get_crypted_id($item["badge_id"]);?>"><button type="submit" class="btn btn-primary btn-sm clearmargin"> Edit <i class="fa fa-edit"></i></button></form>
		          <?php }  ?>
			  </td>	
	          
	          <td class="tocenter">
		          <a href="view_badge_issuer.php?badge_id=<?php echo get_crypted_id($item["badge_id"]);?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-search"></i></a>
	          </td>

	          <td class="tocenter">
	          	  <?php if ($item['enabled']==1) { ?>
			          <form action="list_earn.php" method="post">
			          <input type="hidden" name="badge_id" id="badge_id" value="<?php echo get_crypted_id($item["badge_id"]);?>">
			          <button type="submit" class="btn btn-info btn-sm" type="button"> Earn <span class="badge"><?php echo $total_b_earns?></span></button>
			          </form>
		          <?php } else {  ?>
		          	<a href="#" class="btn btn-default btn-sm clearmargin" onclick="alert('Badge not enabled');return false;">Earn <span class="badge"><?php echo $total_b_earns?></span></a>
		          <?php }   ?>
	          </td>
	          
	          <td class="tocenter">
	          	<?php if ($total_b_revok>0) {  ?>
			    	<form action="badge_revoked.php" method="post">
			        	<input type="hidden" name="badge_id" id="badge_id" value="<?php echo get_crypted_id($item["badge_id"]);?>">
						<button type="submit" class="btn btn-warning btn-sm" type="button"> Revk <span class="badge"><?php echo $total_b_revok?></span></button>
					</form>  
				<?php } else { ?>
					<a href="#" class="btn btn-default btn-sm clearmargin" onclick="alert('No data');return false;">Revk <span class="badge"><?php echo $total_b_revok?></span></a>         		
				<?php }?>
	          </td>
	          
	          <td class="tocenter">
	          	 <?php if ( $total_b_count > 0 ) { ?>
	          	  <a href="#" class="btn btn-default btn-sm" onclick="alert('Could not be deleted');return false;">Delete <i class="fa fa-trash"></i></a>
		         <?php } else { ?>
		          <form action="#" method="post" onsubmit="return checkConfirm('Are you sure?');">
		          <input type="hidden" name="event" value="delete_badge">
		          <input type="hidden" name="k_issuer" value="<?php echo $k_search_issuer?>">
		          <input type="hidden" name="badge_id" value="<?php echo get_crypted_id($item["badge_id"]);?>">
		          <button type="submit" class="btn btn-danger btn-sm">Delete <i class="fa fa-trash"></i></button>
		          </form>
		         <?php }  ?>
	          </td>
	        </tr>
		<?php } } else { ?>
			<tr><td colspan="9" class="alert alert-danger">Nothing found</td></tr>
		<?php } ?>
		
		</tbody>
	</table>
	<!-- /contents -->

</div>

<div class="container"><div class="col-lg-12"><br></div></div>

<div class="container"><div class="col-lg-12 alert alert-info">
	<blockquote>
		<li><strong>Enabled</strong>: The object contains all needed information to be published</li>
		<li><strong>Published</strong>: The object is released to be earned</li>
	</blockquote>
	<p>If you want to disable temporally or permanently the earn process, just unpublish the item.</p>
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
