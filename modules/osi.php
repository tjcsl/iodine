<?php
	require "imstatus.php";
	echo "<img src=\"/thumbnail/0x0/aim" . (im_status($_REQUEST['protocol'], $_REQUEST['sn']) ? "online.png" : "offline.png") . "\" />";
?>
