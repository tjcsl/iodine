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

	private $template;
	private $template_args;
	private $sched;

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	public function init_box() {
		global $I2_USER;
		$this->sched = new Schedule($I2_USER);
		return 'Your Classes';
	}

	public function display_box($display) {
		$display->assign('schedule',$sched);
		$display->disp('box.tpl');
	}

	public function init_pane() {
		global $I2_ARGS,$I2_USER;

		return FALSE;
	}

	public function display_pane($display) {
		$display->assign_array($this->template_args);
		$display->disp($this->template);
	}

	public function get_name() {
		return 'ScheduleNavigator';
	}

}

?>
