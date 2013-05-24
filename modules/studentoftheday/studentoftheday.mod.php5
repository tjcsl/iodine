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
		global $I2_ROOT;
		$student = $this->user->name;
		$uidnumber = $this->user->iodineUidNumber;
		$disp->raw_display("<span style=\"font-weight: bold;font-size: 24px\">The student of the day is....</span>
			<br /> <span style='font-size: 32px; font-weight: bold'>$student!!!!!</span><br />
			<img src=\"$I2_ROOT/pictures/$uidnumber\" /><br />To retrieve your prize, send a box of cookies to the Intranet development team.");
	}
}
?>
