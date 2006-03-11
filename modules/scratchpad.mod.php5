<?php
/**
* A module that allows persistent editable text across pages.
* @package modules
* @subpackage scratchpad
*/

class Scratchpad implements Module {

	private $template_args = array();

	function init_box() {
		GLOBAL $I2_USER, $I2_SQL;
		$this->template_args['text'] = $I2_SQL->query('SELECT padtext FROM scratchpad WHERE username=%s',$I2_USER->username)->fetch_single_value();
	
		return "Scratchpad";
	}

	function display_box($disp) {
		$disp->disp('scratchpad_box.tpl', $this->template_args);
	}
	
	function init_pane() {
		return "Scratchpad";
	}
	
	function display_pane($disp) {
	}

	function get_name() {
		return "Scratchpad";
	}

	function is_intrabox() {
		return true;
	}
}
?>
