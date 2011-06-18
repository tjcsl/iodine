<?php
/**
 * Majora's Mask Countdown Clock
 */
class Majora implements Module {

	private $template_args = array();

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

	function init_pane() {
		return false;
	}

	function display_pane($disp) {
	}

	function init_box() {
		return "Majora's Mask Countdown";
	}

	function display_box($disp) {
		// Right now the start date is set in the template :/
		// Feel free to fix that at some point 
		$disp->disp("majora_countdown_box.tpl", $this->template_args);
	}

	function get_name() {
		return "majora";
	}
}
?>
