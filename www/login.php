<?php
include("config.php");
//control autologin
if( isset($logged_user) && $logged_user>0 && isset($_COOKIE["UID"]) && isset($_COOKIE["SEED"]) && $_COOKIE["UID"]!='' && $_COOKIE["SEED"]!=''){
	header("Location: ./dashboard.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include("head.php"); ?>

    <script src="js/forms.js"></script>
    <script>
        $( document ).ready(function() {
            logmedude();
        });
    </script>
</head>
<body>

<div id="head"><div id="menu"><?php  include("menu.php"); ?></div></div>

<div id="main">

    <div id='bread'>
        <a href='/'><?php echo __("Home")?></a> > <?php echo __("Sign in")?>
    </div>

    <div class='page_header'>
       <?php echo __("Sign in")?>
    </div>


    <div class='error_div' id='errordiv'></div>
    <div class='login_div' id='logindiv'></div>

    <form id='logform' name='logform' class='logform' action='a_login.php' method='post'>
        <input type=text name="email" class='forms toleft' placeholder="<?php echo __("Email")?>" id='emailreg' tabindex="1"><br>
        <input type=password name="password" class='forms toleft' placeholder="<?php echo __("Password")?>" id='password1' tabindex="2"><br>
        <input type="submit" value="<?php echo __("Sign In")?>" class="greybutton" tabindex="3"><br>
    </form>

</div>

<div id='footer' class='wrapper'><?php include("footer.php"); ?></div>
<div id='copyright' class='wrapper'><?php include("copyright.php"); ?></div>

</body>
</html>