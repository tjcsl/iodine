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
	public static function display($disp, $chrome) {
		global $I2_USER;
		if($I2_USER->header && $chrome) {
			$date = EighthSchedule::get_next_date();
			$activites = array();
			if($date) {
			        $activities = EighthActivity::id_to_activity(EighthSchedule::get_activities($I2_USER->uid, $date, 1));
			        }
			else {
			        $activities = array();
			}
			$dates = array($date => date("n/j/Y", @strtotime($date)), date('Y-m-d') => 'today', date('Y-m-d', time() + 3600 * 24) => 'tomorrow', '' => 'none');
			$arr = array();
			if (isSet($activities)) {
				$arr['activities'] = $activities;
			} else {
				$arr['activities'] = array();
			}
			if (isSet($dates) && isSet($date)) {
				$arr['date'] = $dates[$date];
			} else {
				$arr['date'] = '';
			}
			$disp->disp('header.tpl', $arr);
		} else {
			d('This user has minimized their header',6);
			$disp->disp('header-small.tpl');
		}
	}
}
?>
