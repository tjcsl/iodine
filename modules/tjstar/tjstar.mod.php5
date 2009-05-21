<?php
/**
 * Shows useful links for the TJStar Research Symposium
 */
class TJStar implements Module {

	private $template_args = array();

	function init_pane() {
		return FALSE;
	}

	function display_pane($disp) {
		//do nothing
	}

	function init_box() {
		global $I2_USER;
		$this->template_args["uid"] = $I2_USER->uid;
		return "TJStar";
	}

	function display_box($disp) {
		$disp->disp("tjstar_box.tpl", $this->template_args);
	}

	function get_name() {
		return "TJStar";
	}
}
?>
