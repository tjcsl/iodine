<?php
//Standard includes
include "fast.php5";

//echo "Welcome ".$_SESSION['i2_username']."!";
//echo $_POST['message'];
//print_r($_SESSION);
if(!isset($_SESSION['chatkey'])) {
	$_SESSION['chatkey']=abs(intval(hexdec(uniqid())));
	echo $_SESSION['chatkey'];
}

$sendqueue = msg_get_queue(15);//f
if(isset($_POST['message'])) { //We have something to send.
	msg_send($sendqueue,$_SESSION['chatkey'],"".$_POST['message']);
}
$recievequeue = msg_get_queue(13);//e
$thowawayint=0;
$message="";
//returns true if has message, false if not. The NOWAIT part makes it timeout immediately if there is no message for it.
while(msg_receive($recievequeue,$_SESSION['chatkey']*0,$throwawayint,4096,$message,false,MSG_IPC_NOWAIT))
	echo "<br />".$message;
?>
