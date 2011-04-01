<?php
//Standard includes
include "fast.php5";

//echo "Welcome ".$_SESSION['i2_userid']."!";
//echo $_POST['message'];
//print_r($_SESSION);
if(!isset($_SESSION['chatkey'])) {
	$_SESSION['chatkey']=abs(intval(hexdec(uniqid())));
	echo $_SESSION['chatkey'];
}

$memcache= new Memcache;
$memcache->connect('localhost',11211);
if(isset($_SESSION['i2_userid'])) {
	if(($times=$memcache->get('times_i'.$_SESSION['i2_userid']))===false) {
		$memcache->set('times_i'.$_SESSION['i2_userid'],array('lastactive'=>time(),'lastreallyactive'=>time()),false);
		$times=time();
	} else {
		if((!empty($_POST) && substr($_POST['message'],0,4)!="PONG") || substr($_GET['message'],0,4)=="ACIV") {
			$memcache->set('times_i'.$_SESSION['i2_userid'],array('lastactive'=>time(),'lastreallyactive'=>time()),false);
		} else {
			$memcache->set('times_i'.$_SESSION['i2_userid'],array('lastactive'=>time(),'lastreallyactive'=>$times['lastreallyactive']),false);
		}
	}
}
$sendqueue = msg_get_queue(15);//f
if(isset($_POST['message'])) { //We have something to send.
	if(substr($_POST['message'],0,6)=="IODINE") {
		$query = explode(" ",$_POST['message']);
		print_r($query);
		if(isset($query[1])) {
			switch(strtolower($query[1])) {
				case 'checkusers':
					$retval="IODINERETURN USERSTATUS";
					foreach ($query as $k=>$v) {
						if($k<2) continue; //Command and command
						if(substr($v,0,1)=='i' && is_numeric(substr($v,1))) { //Looks like an intranet user.
							if(($timeinfo=$memcache->get('times_'.$v))===false) {
								$retval.=" ".$v."!OFFLINE";
								continue;
							}
							if(time()-$timeinfo['lastreallyactive']<60) {
								$retval.=" ".$v."!ONLINE";
								continue;
							}
							if(time()-$timeinfo['lastactive']>10) {
								$retval.=" ".$v."!OFFLINE";
								continue;
							}
							$retval.=" ".$v."!AFK";
							continue;
						} else { //Someone else.
							$retval.=" ".$v."!ONLINE";
							// Default to displaying them as online.
							// We can't really do better than this right now.
						}
					}
					echo $retval;
					break;
				default:echo "IODINERETURN INVALID_QUERY INVALID_COMMAND";
					break;
			}
		} else {
			echo "IODINERETURN INVALID_QUERY NO_COMMAND";
		}
	} else {
		msg_send($sendqueue,$_SESSION['chatkey'],"".$_POST['message']."\r\n");
		// We do this to confirm recipt of the message.
		echo $_POST['message'];
	}
} else {
	$recievequeue = msg_get_queue(13);//e
	$thowawayint=0;
	$message="";
	//returns true if has message, false if not. The NOWAIT part makes it timeout immediately if there is no message for it.
	$fh=fopen("/tmp/chatlog",'a');
	/*if(!isset($_SESSION['number']))
		$_SESSION['number']=0;
	echo $_SESSION['number']++;*/
	if(msg_receive($recievequeue,$_SESSION['chatkey'],$throwawayint,4096,$message,false,MSG_IPC_NOWAIT))
	{
		$message=substr($message,0,stripos($message,chr(0)));
		fwrite($fh,$_SESSION['number']."-".strlen($message));
		fwrite($fh,$message);
		echo $message;
	}
	fclose($fh);
}
?>
