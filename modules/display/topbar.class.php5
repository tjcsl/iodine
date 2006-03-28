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
			$disp->disp('header.tpl');
		} else {
			d('This user has minimized their header',6);
			$disp->disp('header-smaller.tpl');
		}
	}
}
?>
