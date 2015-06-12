#!/usr/bin/php
<?php
/**
 * Sends emails to those students who have recieved an absence.
 * Specifically, emails when the cached value of absences 
 * in eighth_absences_cache (which was run the last time the
 * command ran) is different than COUNT(userid) in eighth_absences
 */
$em_title = "[Intranet] You recieved an eighth period absence";
$em_msg = <<<EOF
<p>At of the time this message was sent, you, %NAME%, have %NUM% eighth period absence%NUMS%:</p>

%DESC%

<p>To clear an absence print the <a href="%ROOT%/eighth/vcp_schedule/absences/uid/%UID%">absence information page on Intranet</a>. Have the teacher listed sign next to the activity to indicate that you were present. If you went to another activity, that teacher will need to sign as well as indicating what activity you attended as an alternative to the one that shows up in intranet. Bring the signed page to the 8th period office within two weeks of an absence to clear it. </p>

<p>If you have any questions about this process, contact the Eighth Period Office.</p>
<br />
<br />
This message was automatically sent by the <a href="%ROOT%">TJ Intranet</a>. If you are not the person listed above, email <a href="mailto:intranet@tjhsst.edu">intranet@tjhsst.edu</a>
EOF;
include_once("../functions.inc.php5");
define('CONFIG_FILENAME','../config.ini.php5');
load_module_map();
/*
$I2_LOG = new Logging();
$I2_ERR = new Error();
$I2_SQL = new MySQL();
$I2_CACHE = new Cache();
$I2_API = new Api();
$I2_LDAP= LDAP::get_generic_bind();
*/
define('MEMCACHE_SERVER', 'localhost');
define('MEMCACHE_PORT', '11211');
define('MEMCACHE_DEFAULT_TIMEOUT', strtotime("1 hour"));
$I2_ERR = new Error();
$I2_SQL = new MySQL(); $I2_CACHE = new Cache();
$I2_LDAP= LDAP::get_generic_bind();
$ignoreuid = 10000;
function ignore_userid($uid) {
	global $ignoreuid;
	return $uid < $ignoreuid;
}
$users = $I2_SQL->query("SELECT eighth_absentees.*, COUNT(DISTINCT eighth_absentees.bid) AS absences, eighth_absences_cache.absences AS cached FROM eighth_absentees LEFT OUTER JOIN eighth_absences_cache ON (eighth_absentees.userid=eighth_absences_cache.userid) WHERE eighth_absentees.userid > ".$ignoreuid." GROUP BY eighth_absentees.userid")->fetch_all_arrays();
echo "* Got SQL\n";
$emailed = [];
$toemail = [];
foreach($users as $user) {
	if(!ignore_userid($user["userid"]) && !array_key_exists($user["userid"], $emailed) && $user["absences"] != $user["cached"]) {
		$diff = $user["absences"] - $user["cached"];
		if($diff > 0) { // Don't email if the absence was cleared
			echo "{$user['userid']} has $diff new absences (now {$user['absences']} from {$user['cached']})\n";
			$I2_USER = $userobj = new User($user['userid']);
			$userdata = User::rawdata($user['userid'], "ou=people,dc=tjhsst,dc=edu");
			$acts = EighthActivity::id_to_Activity(EighthSchedule::get_absences($user['userid']));
			$desc = "<table><tr><th>Date</th>\t<th>Block</th>\t<th>Activity</th>\t<th>Sponsor ID</th>\t</tr>\n";
			foreach($acts as $act) {
				$desctmp = "<tr><td>%DATE%</td>\t<td>%BLOCK%</td>\t<td>%AID%</td>\t<td>%SPONSOR%</td></tr>\n";
				try {
				$sp = EighthSponsor::id_to_sponsor($act->block_sponsors_name_short);
				$spn = $sp->name;
				} catch(Exception $err){$spn = $act->block_sponsors_name_short;}
				$desc.=str_replace(array(
					"%DATE%", "%BLOCK%", "%AID%", "%SPONSOR%"
					), array(
						$act->block->date, $act->block->block, $act->name_r, $act->aid, $spn
					), $desctmp);
			}
			$desc.="</table>\n";
			$tmpmsg = str_replace(array(
				"%NAME%", "%NUM%", "%NUMS%", "%DESC%", "%UID%", "%ROOT%"
			), array(
				$userobj->cn, $user['absences'], ($user['absences']>1?'s':''), $desc, $user['userid'], "https://iodine.tjhsst.edu/"
			), $em_msg);
			$toemail[] = [$userdata['mail'], $em_title, $tmpmsg];
			echo "Going to email ".(is_array($userdata['mail'])?$userdata['mail'][0]:$userdata['mail']);
			//$emailed[] = $user['userid'];
		}
	}
}
echo "\nFound ".sizeof($toemail)." users..\n";
if(isset($argv,$argv[1]) && $argv[1] == "mail") echo "SENDING EMAIL NOTIFICATIONS in 5 SECONDS\n";
sleep(5);
foreach($toemail as $mailparam) {
    print_r($mailparam[0]);
	if(isset($argv,$argv[1]) && $argv[1] == "mail") i2_mail($mailparam[0], $mailparam[1], $mailparam[2]);
}
echo "Updating database counts\n\n";
sleep(5);
system('php ./absencecache.php5 fix');
?>
