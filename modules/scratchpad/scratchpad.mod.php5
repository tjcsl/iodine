<?php
/**
* A module that allows persistent editable text across pages.
* @package modules
* @subpackage scratchpad
*/

class Scratchpad implements Module {

	private $template_args = array();
	private $text;

	public function init_box() {
		GLOBAL $I2_USER, $I2_SQL;
		$this->template_args['text'] = 'Loading...'; //$I2_SQL->query('SELECT padtext FROM scratchpad WHERE uid=%d',$I2_USER->uid)->fetch_single_value();
		return 'Scratchpad';
	}

	public function display_box($disp) {
		$disp->disp('scratchpad_box.tpl', $this->template_args);
		//$disp->disp('scratchpad_box.tpl', array());
	}
	
	/**
	* I2_ARGS accepted:
	*	I2_ARGS[1] = whether to save or load
	*	I2_ARGS[2] = text to save (if saving)
	*/
	public function init_pane() {
		global $I2_ARGS,$I2_SQL,$I2_USER;

		if (!(isset($I2_ARGS[1]) && $I2_ARGS[1])) {
			return FALSE;
		}

		switch ($I2_ARGS[1]) {
			case 'save':
				$I2_SQL->query('REPLACE INTO scratchpad (uid, padtext) VALUES (%d, %s)', $I2_USER->uid, $I2_ARGS[2]);
				return FALSE;
			case 'load':
				$this->text = $I2_SQL->query('SELECT padtext FROM scratchpad WHERE uid=%d', $I2_USER->uid)->fetch_single_value('padtext');
				return TRUE;
			case 'help':
				return 'What is Scratchpad?';
			default :
				return FALSE;
				
		}

	}
	
	function display_pane($disp) { //returns text to AJAX
		global $I2_ARGS;
		
		if($I2_ARGS[1]=="load") {
			Display::stop_display();
			echo $this->text;
			exit;
		}
		else {
			$disp->disp('scratchpad_pane.tpl', $this->template_args);
		}
	}

	function get_name() {
		return 'Scratchpad';
	}

	function is_intrabox() {
		return true;
	}
}
?>
