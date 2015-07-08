<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><?php include("head.php"); ?></head>
<body>

<div id="head"><div id="menu"><?php  include("menu.php"); ?></div></div>

<div id="private"> <?php  include("a_auth.php"); ?></div>

<div id="main">

    <div id='bread'><a href='/'><?php echo __("Home")?></a> > <?php echo __("Dashboard")?></div>
    <div class='page_header'><?php echo __("Dashboard")?></div>

    <!-- contents -->

    <?php if ( isset($logged_user) && isset($logged_profile) ) { IBL_OPENBADGES::autogenerate_issuer_json($logged_user, $logged_profile); } ?>

    <div class="container"><div class="col-lg-12"><br></div></div>

    <div class="col-lg-3 col-md-3">
    	<div class="list-group text-center">
    		<a href="./my_profile.php" class="list-group-item list-group-item-success">
    			<h4 class="list-group-item-heading"><?php echo __("My Profile")?></h4>
    			<p class="list-group-item-text"><i class="fa fa-key fa-4x"></i></p>
    			<p class="list-group-item-text"><br></p>
  			</a>
  		</div>
    </div>
	
    <div class="col-lg-3 col-md-3">
    	<div class="list-group text-center">
    		<a href="./my_earn.php" class="list-group-item active">
    			<h4 class="list-group-item-heading"><?php echo __("My Badges")?></h4>
    			<p class="list-group-item-text"><i class="fa fa-picture-o fa-4x"></i></p>
    			<p class="list-group-item-text"><br></p>
  			</a>
  		</div>
    </div>
    
    <?php if ( isset($logged_profile) && $logged_profile!='general') { ?>
    <div class="col-lg-3 col-md-3">
    	<div class="list-group text-center">
    		<a href="./issuer.php" class="list-group-item list-group-item-info">
    			<h4 class="list-group-item-heading"><?php echo __("Issue Badges")?></h4>
    			<p class="list-group-item-text"><i class="fa fa-graduation-cap fa-4x"></i></p>
    			<p class="list-group-item-text"><br></p>
  			</a>
  		</div>
    </div>
    <?php } ?>
    
    <?php if ( isset($logged_profile) && $logged_profile=='admin') { ?>
    <div class="col-lg-3 col-md-3">
    	<div class="list-group text-center">
    		<a href="./users.php" class="list-group-item list-group-item-warning">
    			<h4 class="list-group-item-heading"><?php echo __("Users")?></h4>
    			<p class="list-group-item-text"><i class="fa fa-users fa-4x"></i></p>
    			<p class="list-group-item-text"><br></p>
  			</a>
  		</div>
    </div>
	<?php } ?>
    
    <!-- /contents -->

</div>

<div class="container"><div class="col-lg-12"><br></div></div>

<div id='footer' class='wrapper'><?php include("footer.php"); ?></div>
<div id='copyright' class='wrapper'><?php  include("copyright.php"); ?></div>

</body>
</html>