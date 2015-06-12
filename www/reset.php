<?php
include("config.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include("head.php");?>
    <script src="js/forms.js"></script>
    <script>
        $( document ).ready(function() {
            resetmedude();
        });

    </script>
</head>
<body>

<div id="head">
    <div id="menu"><?php  include("menu.php"); ?></div>
</div>

<div id="main">

    <div id='bread'><a href='/'><?php echo __("Home")?></a> > <?php echo __("Password Reset")?></div>
    <div class='page_header'>Password Reset</div>

    <div id="msg1"><?php echo __("Please enter your email address below, and we will email instructions for setting a new password.")?></div>

    <div class='error_div' id='errordiv'></div>
    <div class='reset_div' id='resetdiv'>x</div>

    <form id='resetform' name='resetform' class='resetform' action='a_reset.php' method='post'>

        <input type=text name="email" class='forms toleft' placeholder="Email" id='emailreg' tabindex="1"><br>
        <span class="tips1">* <?php echo __("This is the email address you used to register")?></span><br>

        <input type="submit" value="Reset" class="greybutton" tabindex="2"><br>

    </form>

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