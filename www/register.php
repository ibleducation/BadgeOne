<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include("head.php"); ?>

	<script src="js/forms.js"></script>
    <script>
       $( document ).ready(function() {
            $('#password1').keyup(function()
            {
                $('#result').html(checkStrength($('#password1').val()))
            });
            regmedude();
        });
    </script>
</head>
<body>
	
	<div id="head"><div id="menu"><?php include("menu.php"); ?></div></div>
	
	<div id="main">

		<div id='bread'><a href='/'><?php echo __("Home")?></a> > <?php echo __("Create Free Account")?></div>
		<div class='page_header'><?php echo __("Create Account")?></div>

			<div id="msg1"><?php echo __("Please fill the form below to create a new")?> <span style='color:green'><?php echo __("free account")?></span>.</div>

			<div class='error_div' id='errordiv'></div>
			<div class='register_div' id='registerdiv'></div>

			<form id='regform' name='regform' class='regform' action='a_register.php' method='post'>
				<input type=text name="name" class='forms toleft' placeholder="<?php echo __("Name")?>" id='nameme' tabindex="1">
				<input type=password name="password" class='forms toright' placeholder="<?php echo __("Password")?>" id='password1' tabindex="3">
				<input type=text name="email" class='forms toleft' placeholder="<?php echo __("Email")?>" id='emailreg' tabindex="2">
				<input type=password name="password2" class='forms toright' placeholder="<?php echo __("Password Confirmation")?>" id='password2' tabindex="4">
				<div id='pstrength'><?php echo __("Your Password Strength")?></div>
				<input type="submit" value="<?php echo __("Register")?>" class="greybutton" tabindex="6"><br>
				<input type=checkbox style='margin-top:20px;' id='termcheck' tabindex="5"> <?php echo __("I accept the Terms of Service")?>

			</form>
	</div>

<div class="container"><div class="col-lg-12"><br></div></div>

<div id='footer' class='wrapper'><?php include("footer.php"); ?></div>
<div id='copyright' class='wrapper'><?php  include("copyright.php"); ?></div>

</body>
</html>