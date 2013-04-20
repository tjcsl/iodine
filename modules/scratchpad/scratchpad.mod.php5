<?php
/**
* A module that allows persistent editable text across pages.
* @package modules
* @subpackage scratchpad
*/

class Scratchpad implements Module {

	private $template_args = array();
	private $text;

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

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

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
			$arr = array();
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
	
	function display_pane($disp) { //returns text to AJAX
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

	function is_intrabox() {
		return true;
	}
}
?>
