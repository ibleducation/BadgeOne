<?php include("config.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
	
 include("head.php");

?>
</head>
<body>
	
	<div id="head">
		<div id="menu">
			
			<?php
				
			include("menu.php");
				
			?>			
			
		</div>
	</div>
	
	<div id="main">
	
		<div id='bread'>
			
			<a href='/'>Home</a> > Account Activation
			
		</div>
		
		<div class='page_header'>
			
			Account Activation
			
		</div>
		
		<!-- contents -->
		<?php include 'a_activation.php';?>
		<!-- contents -->
	
	</div>
	
	<div id='footer' class='wrapper'>
		
		<?php
		
			include("footer.php");
			
		?>
		
	</div>
	<div id='copyright' class='wrapper'>
		
		<?php
		
			include("copyright.php");
			
		?>
		
	</div>
	
</body>
</html>