<?php
/**
 * Majora's Mask Countdown Clock
 */
class Majora implements Module {

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
		return "Majora's Mask Graduation Counter";
	}

	function display_box($disp) {
		// Borrows from the graduation counter's config entry
		$template_args = array('time'=>i2config_get('gradtime',1308094200,'countdown')*1000);

		$disp->disp("majora_countdown_box.tpl", $template_args);
	}

	function get_name() {
		return "majora";
	}
}
?>
