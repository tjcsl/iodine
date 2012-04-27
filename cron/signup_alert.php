<?php
include_once("./functions.inc.php5");
define('CONFIG_FILENAME','config.ini.php5');
load_module_map();
$I2_ERR = new Error();
$I2_SQL = new MySQL();
$I2_LDAP= LDAP::get_generic_bind();
$date = EighthSchedule::get_next_date();
if(!isset($date)) {
	//echo "There are no scheduled eighth periods. Closing...\r\n";
	exit();
}

$userquery = "";
if (date("H") == 11 && date("i") >= 28 && $date == date("Y-m-d")) {
	$userquery = "SELECT userid FROM eighth_alerts";
	$subj = "[Iodine-eighth] Signup Alert";
} else {
	$tomorrow = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
	if ((date("H") == 21 || (date("H") == 20 && date('i')>=58)) && $date == date("Y-m-d", $tomorrow)) {
		$userquery = "SELECT userid FROM eighth_night_alerts";
		$subj = "[Iodine-eighth] Night Signup Alert";
	} else {
		exit();
	}
}

$separator = "MAIL-" . md5(date("r",time()));
$def_aid=i2config_get('default_aid', 999, 'eighth');

foreach($I2_SQL->query($userquery)->fetch_all_single_values() as $id) {
	//echo $id."\r\n";
	try {
		$activities = EighthSchedule::get_activities($id, $date, 1,TRUE);
	} catch (I2Exception $e) {
		// Usually induced if a user is removed from iodine without being removed from the db first.
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
	// Use the i2_mail library function
	$user = new User($id);
	
	i2_mail($user->mail, $subj, "As of the time that this message is being sent, you have not signed up for one or more eighth periods on $date.\r\n");

}
//echo "Finished. Closing...";
?>
