<?php
include("config.php");

$form = get_form();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php

    include("head.php");

    ?>
    <script>

        $( document ).ready(function() {


            changepassdude();


        });

    </script>
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

        <a href='/'>Home</a> > Password Change

    </div>

    <div class='page_header'>

        Password Change

    </div>

    <div id="msg1">Please enter your new password.</div>

    <div class='error_div' id='errordiv'></div>
    <div class='change_div' id='changediv'></div>


    <?php


    $sdata = array($form["etoken"],$form["key"]);
    $stmt = $dbh->prepare("select * from users where md5(email) = ? and reset_seed = ?  and reset_seed_date > NOW() - interval 1 day limit 1");
    $stmt->execute($sdata);
    $rows = $stmt->fetch();


    if($stmt->rowCount()>0){

    ?>


    <form id='changepassform' name='changepassform' class='resetform' action='a_changepass.php' method='post'>

        <input type=password name="password" class='forms toleft' placeholder="New Password" id='password1' tabindex="1"><br>
        <input type=password name="password2" class='forms toleft' placeholder="Confirm Password" id='password2' tabindex="2"><br>
        <input type="hidden" name="etoken" value="<?php print $form["etoken"];?>">
        <input type="hidden" name="key" value="<?php print $form["key"];?>">
        <input type="submit" value="Change Password" class="greybutton" tabindex="3"><br>

    </form>

    <?php

    }else{

    ?>

        <div id="msg1">Reset links have a 24 Hours limit .</div>

    <?php

    }

    ?>


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
