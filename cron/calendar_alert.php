<?php
include_once("./functions.inc.php5");
define('CONFIG_FILENAME','config.ini.php5');
load_module_map();
$I2_ERR = new Error();
$I2_SQL = new MySQL();
$I2_LDAP= LDAP::get_generic_bind();

$nextday = false;
$userquery = "";
if ((date("i") >= 58 || date("i")<=2)) {//If it's on the hour
	$userquery = "SELECT * FROM calendar_alerts WHERE hourbefore=TRUE";
	$subj = "[Iodine-calendar] Event Notification: ";
	if(date("H") == 21 || (date("H") == 20 && date('i')>=58)) {
		$userquery.= " OR nightbefore=TRUE";
		$nextday = true;
	}
} else {
	exit();
}

$separator = "MAIL-" . md5(date("r",time()));

$checkacts = Calendar::get_alert_events($nextday); //List of events to check against

$alerts = $I2_SQL->query($userquery)->fetch_all_arrays();
$userlist = [];
foreach($alerts as $row) {
	$userlist[]=$row['userid'];
}
User::cache_users($userlist,'mail');
foreach($alerts as $row) {
	try {
		$user = new User($id);
	} catch (I2Exception $e) { //User doesn't exist
		continue;
	}
	
	$notsigned=FALSE;
	foreach($activities as $activity)
		if($activity[0]==$def_aid) {
			$notsigned=TRUE;
			break;
		}
	if(!$notsigned)
		continue;
	// Mail out the news post to any users who have subscribed to the news and can read it.
	$headers = "From: " . i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion') . "\r\n";

	$headers .= "Content-Type: multipart/alternative; boundary=\"" . $separator . "\"";
	$messagecontents = "This is a reminder that you scheduled for the event $eventname on $date.\r\n";
	$message = "--" . $separator . "\r\nContent-Type: text/plain; charset=\"iso-8859-1\"\r\n";
	$message .= strip_tags($messagecontents);
	$message .= "\r\n--" . $separator . "\r\nContent-Type: text/html; charset=\"iso-8859-1\"\r\n";
	$message .= $messagecontents;

	if(gettype($user->mail)=="array") {
		foreach($user->mail as $mail) {
			//echo $mail."\r\n";
			mail($mail,$subj,$message,$headers);
		}
	} else {
		//echo $user->mail."\r\n";
		mail($user->mail,$subj,$message,$headers);
	}
}
//echo "Finished. Closing...";
?>
