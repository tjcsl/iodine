<?php
/**
 * Majora's Mask Countdown Clock
 */
class Majora extends Module {

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
