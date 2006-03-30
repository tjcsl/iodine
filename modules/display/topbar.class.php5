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
			$max = i2config_get('max_header_stories',5,'display');
			$i = 1;
			$tpl_stories = array();
			foreach($stories as $story) {
				if($max < $i) {
					break;
				}
				$tpl_stories[] = $story;
				$i++;
			}
			$disp->disp('header.tpl', array('news_posts' => $tpl_stories));
		} else {
			d('This user has minimized their header',6);
			$disp->disp('header-small.tpl');
		}
	}
}
?>
