<div id='logo_space'>
	<a href='./' ><img src="img/logo.png" alt="logo" width="300" height="47" class="logo"/></a>
</div>

<?php
// selected menu
$arr_menu_logged = array(
	'1' => './dashboard.php',
	'2' => './users.php',
	'3' => './issuer.php',
	'4' => './my_earn.php',
	'5' => './my_profile.php',
	'6' => './logout.php',
	'7' => './badge_edit.php',
	'8' => './list_earn.php',
	'9' => './badge_earn.php',
	'9' => './badge_revoked.php',		
);
$key_logged 	= ( defined('CURRENT_PAGE') && CURRENT_PAGE!='' && array_search('./'.CURRENT_PAGE, $arr_menu_logged) ) ? array_search('./'.CURRENT_PAGE, $arr_menu_logged) : "";
//submenu pages - alter keys positions
$key_logged 	= ($key_logged==7 || $key_logged==8 || $key_logged==9) ? 3 : $key_logged;

$arr_menu_public = array(
	'1' => './register.php',
	'2' => './login.php',
	'3' => './reset.php',
);
$key_public = ( defined('CURRENT_PAGE') && CURRENT_PAGE!='' && array_search('./'.CURRENT_PAGE, $arr_menu_public) ) ? array_search('./'.CURRENT_PAGE, $arr_menu_public) : "";
?>

<div id="menu_space">

	<ul class='menu_item'>

        <?php  if( isset($_COOKIE["UID"]) && $_COOKIE["UID"]!='' ) { ?>
        
        	<li><a href='<?php echo $arr_menu_logged[1]?>'><span class="btn <?php echo ($key_logged==1) ? "btn-primary" : "btn-default"?>"><?php echo __("Dashboard")?></span></a></li>
        	<?php if ( isset($logged_user) && $logged_user >0  && isset($logged_profile) && $logged_profile=='admin') { ?>
        	<li><a href='<?php echo $arr_menu_logged[2]?>'><span class="btn <?php echo ($key_logged==2) ? "btn-primary" : "btn-default"?>"><?php echo __("Users")?></span></a></li>
        	<?php } ?>
        	
        	<?php if ( isset($logged_user) && $logged_user >0  && isset($logged_profile) && $logged_profile!='general') { ?>
			<li><a href='<?php echo $arr_menu_logged[3]?>'><span class="btn <?php echo ($key_logged==3) ? "btn-primary" : "btn-default"?>"><?php echo __("Issuer")?></span></a></li>
			<?php } ?>
			
			<li><a href='<?php echo $arr_menu_logged[4]?>'><span class="btn <?php echo ($key_logged==4) ? "btn-primary" : "btn-default"?>"><?php echo __("My Badges")?></span></a></li>

            <li><a href="<?php echo $arr_menu_logged[5]?>"><span class="btn <?php echo ($key_logged==5) ? "btn-primary" : "btn-default"?>"><?php echo __("My Profile")?></span></a></li>
            <li><a href='<?php echo $arr_menu_logged[6]?>' class='btn btn-primary btn-white btn-border3x btn-margin-left'><?php echo __("Logout")?> <i class="fa fa-sign-out"></i></a></li>

        <?php } else { ?>

            <li><a href='<?php echo $arr_menu_public[1]?>'><?php echo __("Create Free Account")?></a></li>
            <li><a href='<?php echo $arr_menu_public[2]?>' class='login_button'><?php echo __("Sign In")?></a></li>

        	<?php if ( isset($available_languages) && is_array($available_languages) && count($available_languages)>0 ) { ?>
        	<!-- multilang -->
        		<?php foreach ($available_languages as $langcode=>$ilang ) { ?>
					<li><a href='<?php echo CURRENT_PAGE?>?lang=<?php echo $langcode?>'><?php echo ($ilang['flag']!='') ? '<img src="'.$ilang['flag'].'" title="'.$ilang['name'].'">' : '' ?> </a></li>
        		<?php }?>
        	<!-- /multilang -->
        	<?php  } ?>

        <?php  } ?>
	</ul>

</div>