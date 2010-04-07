<?php
class StudentOfTheDay implements Module {
	private $user;
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

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

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
		$disp->raw_display("<span style=\"font-weight: bold;\">The student of the day is.... $student!!!!!</span><img src=\"$I2_ROOT/pictures/$uidnumber\" />");
	}

	public function init_box() {
		return false;
	}

	public function display_box($disp) {
	}
}
?>
