<?php
/**
* Just contains the definition for the module {@link TopBar}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Display
* @filesource
*/

/**
* The module to display the bar at the top of the page dynamically.
* @package core
* @subpackage Display
*/
class TopBar {
	public static function display($disp, $chrome, $nagging) {
		global $I2_USER, $I2_SQL;
		if($I2_USER->header=='TRUE' && $chrome && !$nagging) {
			$date = EighthSchedule::get_next_date();
			$arr = [];
			if($date) {
			        $activities = EighthActivity::id_to_activity(EighthSchedule::get_activities($I2_USER->uid, $date, 1), FALSE);
				$dates = array($date => date("n/j/Y", @strtotime($date)), date('Y-m-d') => 'today', date('Y-m-d', time() + 3600 * 24) => 'tomorrow', '' => 'none');
				if (isSet($activities)) {
					$arr['activities'] = $activities;
				} else {
					$arr['activities'] = [];
				}
				if (isSet($dates) && isSet($date)) {
					$arr['date'] = $dates[$date];
				} else {
					$arr['date'] = 'none';
				}
		        } else {
			        $arr['date'] = 'none';
			}
			$date = EighthSchedule::get_next_date(2*60*60);
			if($date){
				// Make a list of all the teacher slots this person is assigned to for 8th
				$hosts = $I2_SQL->query("SELECT sid FROM eighth_sponsors WHERE userid=%d",$I2_USER->uid)->fetch_col('sid');
				$arr['hosting'] = EighthSponsor::get_schedule_on($hosts,$date);
			}
			$disp->disp('header.tpl', $arr);
		} else {
			d('This user has minimized their header',6);
			$disp->disp('header-small.tpl');
		}
	}
}
?>
