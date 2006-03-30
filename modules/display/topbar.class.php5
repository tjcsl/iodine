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
			$stories = Newsitem::get_all_items();
			$max = i2config_get('max_header_stories',3,'display');
			$i = 1;
			$tpl_stories = array();
			foreach($stories as $story) {
				if($max < $i) {
					break;
				}
				$tpl_stories[] = $story;
				$i++;
			$date = EighthSchedule::get_next_date();
			$activites = array();
			if($date) {
			        $activities = EighthActivity::id_to_activity(EighthSchedule::get_activities($I2_USER->uid, $date, 1));
			        }
			else {
			        $activities = array();
			}
			$dates = array($date => date("n/j/Y", @strtotime($date)), date("Y-m-d") => "today", date("Y-m-d", time() + 3600 * 24) => "tomorrow", "" => "none");
			}
			$disp->disp('header.tpl', array('news_posts' => $tpl_stories,'activities' => $activities, 'date' => $dates[$date]));
		} else {
			d('This user has minimized their header',6);
			$disp->disp('header-small.tpl');
		}
	}
}
?>
