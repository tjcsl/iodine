<?php
class StudentOfTheDay extends Module {

	private $user;

	public function get_name() {
		return "Student of the Day!";
	}

	public function init_pane() {
		$this->user = new User();
		return "Student of the Day!";
	}

	public function display_pane($disp) {
		$args['student'] = $this->user->name;
		$args['uidnumber'] = $this->user->iodineUidNumber;
		$disp->disp('studentoftheday.tpl',$args);
	}
}
?>
