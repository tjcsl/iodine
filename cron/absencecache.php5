#!/usr/bin/php
<?php
/**
 * Updates the cached SQL table of absence totals
 * which are used for absence email notifications.
 */
include_once("../functions.inc.php5");
define('CONFIG_FILENAME','../config.ini.php5');
load_module_map();
$I2_ERR = new Error();
$I2_SQL = new MySQL();
$I2_LDAP= LDAP::get_generic_bind();
$date = EighthSchedule::get_next_date();
if(!function_exists("ignore_userid")) {
function ignore_userid($uid) {
	return $uid < 1000;
}
}
function fix($uid, $abs) {
	global $I2_SQL;
	$I2_SQL->query("DELETE IGNORE FROM eighth_absences_cache WHERE userid=".(int)$uid.";");
	$I2_SQL->query("INSERT INTO eighth_absences_cache VALUES(".(int)$uid.", ".(int)$abs.");");
}

$users = $I2_SQL->query("SELECT userid, COUNT(DISTINCT bid) AS absences, eighth_absences_cache.absences AS cached FROM eighth_absentees LEFT JOIN eighth_absences_cache USING (userid) WHERE 1 GROUP BY userid;")->fetch_all_arrays();
if(isset($argv,$argv[1]) && $argv[1] == "fix") { echo "ADDING CURRENT ABSENCES TO DATABASE. TO STOP HIT CTRL-C NOW."; sleep(3); }
$n=0;
foreach($users as $user) {
	if(isset($argv, $argv[1]) && $argv[1] == "fix") {
		fix($user['userid'], $user['absences']);
	}//$I2_SQL->query("INSERT INTO eighth_absences_cache VALUES(".(int)$user['userid'].", ".(int)$user['absences'].");");
	if(ignore_userid($user["userid"])) continue;
	if($user["absences"] != $user["cached"]) {
		$diff = $user["absences"] - $user["cached"];
		echo $user["userid"].": Actual: ".$user["absences"]." Cached: ".$user["cached"]."\n";
		$n++;
	} //else echo $user["userid"]." OK: ".$user["absences"]."\n";
}
echo $n." updated entries.\n";

?>
