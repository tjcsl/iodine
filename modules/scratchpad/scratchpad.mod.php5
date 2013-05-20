<?php
/**
* A module that allows persistent editable text across pages.
* @package modules
* @subpackage scratchpad
*/

class Scratchpad extends Module {

	private $template_args = [];
	private $text;

	function init_box() {
		GLOBAL $I2_USER, $I2_SQL;
		$this->template_args['text'] = 'Loading...'; //$I2_SQL->query('SELECT padtext FROM scratchpad WHERE uid=%d',$I2_USER->uid)->fetch_single_value();
		return 'Scratchpad';
	}

	function display_box($disp) {
		$disp->disp('scratchpad_box.tpl', $this->template_args);
		//$disp->disp('scratchpad_box.tpl', []);
	}
	
	/**
	* I2_ARGS accepted:
	*	I2_ARGS[1] = whether to save or load
	*	I2_ARGS[2] = text to save (if saving)
	*/
	function init_pane() {
		global $I2_ARGS,$I2_SQL,$I2_USER,$I2_LOG, $HTTP_RAW_POST_DATA;

		if (!(isset($I2_ARGS[1]) && $I2_ARGS[1])) {
			return FALSE;
		}

		if(isset($HTTP_RAW_POST_DATA)) {
			//$I2_LOG->log_file('Scratch ('.$I2_ARGS[1].')'.$HTTP_RAW_POST_DATA);
		} else {
			//$I2_LOG->log_file('Scratch ('.$I2_ARGS[1].')');
		}

		switch ($I2_ARGS[1]) {
		case 'save':
			$arr = [];
			$text = $HTTP_RAW_POST_DATA;
			$I2_SQL->query('REPLACE INTO scratchpad (uid, padtext) VALUES (%d, %s)', $I2_USER->uid, $text);
			$this->text = $text;
			return FALSE;
		case 'load':
			$this->text = stripslashes($I2_SQL->query('SELECT padtext FROM scratchpad WHERE uid=%d', $I2_USER->uid)->fetch_single_value('padtext'));
			return TRUE;
		case 'help':
			return 'What is Scratchpad?';
		default :
			return FALSE;
		}
	}
	
	display_pane($disp) { //returns text to AJAX
		global $I2_ARGS;
		
		if($I2_ARGS[1]=='load') {
			Display::stop_display();
			echo "SCRATCHPAD_DATA::".$this->text;
			exit;
		}
		else if($I2_ARGS[1] == 'save') {
			echo "SCRATCHPAD_DATA::saved";
			exit;
		}
		else {
			//$disp->disp('scratchpad_pane.tpl', $this->template_args);
			Display::stop_display();
			echo "SCRATCHPAD_DATA::".$this->text;
			exit;
		}
	}

	function get_name() {
		return 'Scratchpad';
	}
}
?>
