<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include("head.php"); ?>
    <link rel="stylesheet" href="css/portfolio.css" type="text/css" />
</head>
<body>

<div id="head">
    <div id="menu">
        <?php  include("menu.php"); ?>
   </div>
</div>

<div id="private"> <?php  include("a_auth.php"); ?></div>

<?php $user_id		= ( isset($logged_user) && $logged_user>0 ) ? $logged_user : '0'; ?>
<div id="main">

    <div id='bread'><a href='/'>Home</a> > My Badges</div>

    <div class='page_header'>My Badges</div>
    <?php include "events.php";?>

    <div class="pull-right">
    	<a href="myportfolio.php?u=<?php echo $user_id?>" target="_blank" class="btn btn-md btn-info"><i class="fa fa-eye"></i> <?php echo __("VIEW MY PUBLIC PORTFOLIO")?></a>
    </div>    
    <?php $active_tab = ( isset($event) && ($event=="get_imported" || $event=="set_public_imported" ) ) ? 2 : 1; ?>
	<div id="wrapper">
		<div class="container" id="tabContainer">
		    <ul class="nav nav-tabs">
		        <li class="nav <?php echo ($active_tab!=2) ? "active" : ""?>"><a href="#earned" data-toggle="tab"><?php echo __("Earned")." ".__("on this server")?></a></li>
		       	<li class="nav <?php echo ($active_tab==2) ? "active" : ""?>"><a href="#imported" data-toggle="tab"><?php echo __("Imported")." ".__("from")." Mozilla Open Badges"?></a></li> 
		    </ul>
		    <!-- tabs -->
		    <div class="tab-content tabscontent">
		        <div class="tab-pane fade <?php echo ($active_tab!=2) ? "in active" : ""?>" id="earned"><?php include ('my_earn.tab1.php'); ?></div>
		        <div class="tab-pane fade <?php echo ($active_tab==2) ? "in active" : ""?>" id="imported"><?php include ('my_earn.tab2.php'); ?></div>
		    </div>
		    <!-- /tabs -->
		</div>
	</div>

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