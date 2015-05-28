<?php
$expire=time()-60*60*24*30;
setcookie("SEED", "", $expire);
setcookie("UID", "", $expire);
header("Location: ./");
exit;
?>