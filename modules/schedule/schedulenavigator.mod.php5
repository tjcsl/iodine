<?php
/**
* Just contains the definition for the {@link Module} {@link ScheduleNagivator}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage StudentDirectory
* @filesource
*/

/**
* A {@link Module} to navigate the schedules.
* @package modules
* @subpackage StudentDirectory
*/
class ScheduleNavigator implements Module {

	private $sched;

	public function init_box() {
		global $I2_USER;
		$this->sched = new Schedule($I2_USER);
		return 'Your Classes';
	}

	public function display_box($display) {
		$display->assign('schedule',$sched);
		$display->disp('box.tpl');
	}

	public function get_name() {
		return 'ScheduleNavigator';
	}

}

?>
