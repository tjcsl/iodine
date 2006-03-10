<?php
/**
* A module that allows persistent editable text across pages.
* @package modules
* @subpackage scratchpad
*/

class Scratchpad implements Module {

	private $template;
	private $template_args = array();

	function init_box() {
		GLOBAL $I2_USER, $I2_SQL;
		$this->template = "scratchpad_box.tpl";
		$query="SELECT * FROM scratchpad WHERE username=%s";
		$this->template_args['text'] = $I2_SQL->query($query, $I2_USER->username)->fetch_array(Result::ASSOC)['padtext'];
	
		return "Scratchpad";
	}

	function display_box($disp) {
		$disp->disp($this->template, $this->template_args);
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
