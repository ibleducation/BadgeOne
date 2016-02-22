<?php if ( isset($user_id) && $user_id>0 ) { ?>
	<?php $imported_from ="MozillaBackPack"; ?>

	<!-- sincronize mozilla -->
		<div class="col-lg-12">
			<div class="col-lg-12">
				<div class="alert alert-warning">
					<?php  echo sprintf(__("This action performs a request in Mozilla with your current user email (%s), to retrieve the badges from your public collections."),$logged_email)?>
					<form action="#imported" method="post" class="pull-right">
					<input type="hidden" name="event" value="get_imported">
					<button type="input" class="btn btn-md btn-info"><i class="fa fa-refresh"></i> <?php echo __("Syncronize and import your badges from")." Mozilla OpenBadges"?></button>
					</form>
				</div>
			</div>
		</div>
	<!-- /sincronize mozilla -->

	<div class="row"><br><br></div>

	<!-- contents -->
	<?php if ( isset($event_errors_import) && $event_errors_import!="" ) { print '<div class="col-lg-12 alert alert-danger" style="color:red; margin-top:10px;"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>'.$event_errors_import.'</div><br>'; }?>
	<?php if ( isset($event_success_import) && $event_success_import!="" ) { print '<div class="col-lg-12  alert alert-success" style="color:green; margin-top:10px;"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>'.$event_success_import.'</div><br>'; }?>
	<table class="table table-condensed">
	      <thead>
	        <tr>
	          <th><?php echo __("From")?></th>
	          <th><?php echo __("Email")?></th>
	          <th><?php echo __("Issued On")?></th>
	          <th><?php echo __("Course")?></th>
	          <th><?php echo __("Institution")?></th>
	          <th class="tocenter"><?php echo __("Public")?></th>
	        </tr>
	      </thead>
	      <tbody>
		<?php
		$arr_earn_imported	= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_earns_imported","imported_id, imported_from, imported_email, badge_name, issuer_institution_name, show_public, date_created, assertion_orig_issued_on, assertion_issued_on","WHERE user_id='$user_id' AND imported_from='$imported_from'","imported_id");
		foreach ($arr_earn_imported as $item) { ?>
		   <tr>
	          <td><?php echo $item['imported_from']?></td>
	          <td><?php echo $item['imported_email']?></td>
	          <td><?php echo $item['assertion_issued_on']?></td>
	          <td><?php echo $item['badge_name']?></td>
	          <td><?php echo $item['issuer_institution_name']?></td>
	          
	          <td class="tocenter">
	          	  <?php $btn_show_public_css = ($item['show_public']==1) ? "label-success" : "label-danger" ; $btn_show_public_txt = ($item['show_public']==1) ? __("YES") : __("NO"); ?>
				  <form action="#imported" method="post">
		          <input type="hidden" name="imported_id" value="<?php echo get_crypted_id($item['imported_id'])?>">
		          <input type="hidden" name="event" value="set_public_imported">
				  <button type="submit" class="label <?php echo $btn_show_public_css?>"><?php echo $btn_show_public_txt?></button>
				  </form>	          
	          </td>
	        </tr>
		<?php }?>
		</tbody>
	</table>	
	<!-- /contents -->
<?php } ?>