<?php
include_once("./functions.inc.php5");
define('CONFIG_FILENAME','config.ini');
load_module_map();
$I2_ERR = new Error();
$I2_SQL = new MySQL();
$I2_LDAP= LDAP::get_generic_bind();
$date = EighthSchedule::get_next_date();
//echo $date;
if(!isset($date)) {
	//echo "There are no scheduled eighth periods. Closing...\r\n";
	exit();
}
if($date!=date("Y-m-d")) {
	//echo "There are no scheduled eighth periods today. Closing...\r\n";
	exit();
}

// This table defines the relationship between carriers and sms gateways.
$gateways = array(
	'acs'=>'@msg.acsalaska.com',
	'alltel'=>'@message.Alltel.com',
	'aql'=>'@text.aql.com',
	'atandt'=>'@txt.att.net',
	'bellmobile'=>'@txt.bell.ca',
	'boost'=>'@myboostmobile.com',
	'bouygues'=>'@mms.bouyguestelecom.fr',
	'loop'=>'@bplmobile.com',
	'cellone'=>'@mobile.celloneusa.com',
	'cingular_postpaid'=>'@cingular.com',
	'centennial'=>'@cwemail.com',
	'cincinatti'=>'@gocbw.com',
	'cingular_prepaid'=>'@cingulartext.com',
	'cricket'=>'@sms.mycricket.com',
	'fido'=>'@fido.ca',
	'gci'=>'@mobile.gci.net',
	'globalstar'=>'@msg.globalstarusa.com',
	'goldenstate'=>'@gscsms.com',
	'helio'=>'@myhelio.com',
	'ice'=>'@ice.cr',
	'iridium'=>'@msg.iridium.com',
	'metropcs'=>'@mymetropcs.com',
	'mts'=>'@text.mtsmobility.com',
	'nextel'=>'@messaging.nextel.com',
	'pioneer'=>'@zsend.com',
	'pocket'=>'@sms.pocket.com',
	'qwest'=>'@qwestmp.com',
	'southcentral'=>'@rinasms.com',
	'sprint_pcs'=>'@messaging.sprintpcs.com',
	'sprint_nextel'=>'@page.nextel.com',
	'straighttalk'=>'@VTEXT.COM',
	'syringa'=>'@rinasms.com',
	'tmobile'=>'@tmomail.net',
	'unicel'=>'@utext.com',
	'uscellular'=>'@email.uscc.net',
	'verizon'=>'@vtext.com',
	'viaero'=>'@viaerosms.com',
	'virgin'=>'@vmobl.com'
);

$subj = "Signup Alert";
$separator = "MAIL-" . md5(date("r",time()));
$def_aid=i2config_get('default_aid', 999, 'eighth');

foreach($I2_SQL->query("SELECT userid FROM eighth_alerts")->fetch_all_single_values() as $id) {
	//echo $id."\r\n";
	$activities = EighthSchedule::get_activities($id, $date, 1,TRUE);
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
	$headers .= "Reply-To: " . i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion') . "\r\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"" . $separator . "\"";
	$messagecontents = "As of the time that this message is being sent, you have not signed up for one or more eighth periods on $date.\r\n";
	$emailmessage = "--" . $separator . "\r\nContent-Type: text/plain; charset=\"iso-8859-1\"\r\n";
	$emailmessage .= strip_tags($messagecontents);
	$emailmessage .= "\r\n--" . $separator . "\r\nContent-Type: text/html; charset=\"iso-8859-1\"\r\n";
	$emailmessage .= $messagecontents;

	$user = new User($id);
	//We need something along thse lines:
	/*
	foreach ($user->smsaccounts as $acc) {
		$address = $acc[0].$gateways[$acc[1]][0];
		$message = $gateways[$acc[1]][1];
		$message = str_replace('NUMBER',$acc[0],$message);
		$message = str_replace('SUBJECT',$subj,$message);
		$message = str_replace('CONTENTS',$messagecontents,$message);
		$message = str_replace('
		mail(
	}
	*/
	/*
	if(gettype($user->mail)=="array") {
		foreach($user->mail as $mail) {
			//echo $mail."\r\n";
			mail($mail,$subj,$message,$headers);
		}
	} else {
		//echo $user->mail."\r\n";
		mail($user->mail,$subj,$message,$headers);
	}*/
}
//echo "Finished. Closing...";
?>
