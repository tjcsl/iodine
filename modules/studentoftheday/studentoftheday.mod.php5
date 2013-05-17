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

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
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
		$disp->raw_display("<span style=\"font-weight: bold;font-size: 24px\">The student of the day is....</span><br /> <span style='font-size: 32px; font-weight: bold'>$student!!!!!</span><br /><img src=\"$I2_ROOT/pictures/$uidnumber\" /><br />To retrieve your prize, send a box of cookies to the Intranet development team.");
	}

	public function init_box() {
		return false;
	}

	public function display_box($disp) {
	}
}
?>
