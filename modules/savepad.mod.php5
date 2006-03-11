<?php
/**
* Saves Scratchpad text to the database.
*/
class SavePad implements Module {

	/**
	* Unused; required to implement {@link Module}
	*
	* @param Display $disp The Display object to use for output.
	*/
	public function display_box($disp) {
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	public function display_pane($disp) {
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	public function get_name() {
		return 'SavePad';
	}

	/**
	* Unused; required to implement {@link Module}
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
	*/
	public function init_box() {
		return FALSE;
	}

	/**
	* I2_ARGS accepted:
	*	I2_ARGS[0] = text to save
	*/
	public function init_pane() {
		global $I2_ARGS,$I2_SQL,$I2_USER;
		if (count($I2_ARGS) == 0) {
			return;
		}
		$I2_SQL->query("UPDATE scratchpad SET scratchtext='%s' WHERE username=%s", $I2_ARGS[1], $I2_USER->username);
		redirect('');
	}

?>
