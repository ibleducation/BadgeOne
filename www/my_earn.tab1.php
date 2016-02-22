<?php if ( isset($user_id) && $user_id>0 ) { ?>
	<!-- contents -->
	<?php if ( isset($event_errors) && $event_errors!="" ) { print '<div class="col-lg-12 alert alert-danger" style="color:red; margin-top:10px;"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>'.$event_errors.'</div><br>'; }?>
	<?php if ( isset($event_success) && $event_success!="" ) { print '<div class="col-lg-12  alert alert-success" style="color:green; margin-top:10px;"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>'.$event_success.'</div><br>'; }?>
	<table class="table table-condensed">
	      <thead>
	        <tr>
	          <th><?php echo __("Fullname")?></th>
	          <th><?php echo __("Email")?></th>
	          <th><?php echo __("Course")?></th>
	          <th><?php echo __("Institution")?></th>
	          <th class="tocenter"><?php echo __("Public")?></th>
	          <th class="tocenter"><?php echo __("Preview")?></th>
	        </tr>
	      </thead>
	      <tbody>
		<?php
		$arr_earn_list	= COMMONDB_MODULE::get_arr_relations_lists_aliases("badges_issuers bi,badges_earns be","be.earn_id AS earn_id,be.user_id AS user_id,be.earn_email AS earn_email, be.earn_fullname AS earn_fullname,bi.institution AS institution,bi.course AS course, be.show_public AS show_public","WHERE bi.badge_id=be.badge_id AND be.deleted=0 AND be.user_id='$user_id'","earn_id");
		foreach ($arr_earn_list as $item) { ?>
		   <tr>
	          <td><?php echo $item['earn_fullname']?></td>
	          <td><?php echo $item['earn_email']?></td>
	          <td><?php echo $item['course']?></td>
	          <td><?php echo $item['institution']?></td>
	          
	          <td class="tocenter">
	          	  <?php $btn_show_public_css = ($item['show_public']==1) ? "label-success" : "label-danger" ; $btn_show_public_txt = ($item['show_public']==1) ? __("YES") : __("NO"); ?>
				  <form action="#" method="post">
		          <input type="hidden" name="earn_id" value="<?php echo get_crypted_id($item['earn_id'])?>">
		          <input type="hidden" name="event" value="set_public_earn">
				  <button type="submit" class="label <?php echo $btn_show_public_css?>"><?php echo $btn_show_public_txt?></button>
				  </form>	          
	          </td>
	          
	 		  <td class="tocenter">
					<a href="view_badge_earn.php?badge_id=<?php echo get_crypted_id($item["earn_id"]);?>" target="_blank" class="btn btn-info btn-sm"><?php echo __("Preview")?> <i class="fa fa-search"></i></a>
	          </td>
	        </tr>
		<?php }?>
		</tbody>
	</table>	
	<!-- /contents -->
<?php } ?>