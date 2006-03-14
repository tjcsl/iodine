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
		$this->template_args['text'] = $I2_SQL->query('SELECT padtext FROM scratchpad WHERE uid=%d',$I2_USER->uid)->fetch_single_value();
		return 'Scratchpad';
	}

	function display_box($disp) {
		$disp->disp('scratchpad_box.tpl', $this->template_args);
	}
	
	/**
	* I2_ARGS accepted:
	*	I2_ARGS[1] = text to save
	*/
	public function init_pane() {
		global $I2_ARGS,$I2_SQL,$I2_USER;

		if (!(isset($I2_ARGS[1]) && $I2_ARGS[1])) {
			return FALSE;
		}

		$I2_SQL->query("REPLACE INTO scratchpad (uid, padtext) VALUES (%d, %s)", $I2_USER->uid, $I2_ARGS[1]);

		return FALSE;
	}
	
	function display_pane($disp) {
	}

	function get_name() {
		return 'Scratchpad';
	}

	function is_intrabox() {
		return true;
	}
}
?>
