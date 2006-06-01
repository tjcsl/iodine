<?php

class ScheduleNavigtator implements Module {

	private $template;
	private $template_args;
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
