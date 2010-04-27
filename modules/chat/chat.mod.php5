<?php
/**
* A module that allows interuser chat. Most of the interesting stuff is in the javascript.
* @package modules
* @subpackage chat
*/

class Chat implements Module {

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

	public function init_box() {
		GLOBAL $I2_USER, $I2_SQL;
		$this->template_args['friends'] = $I2_SQL->query('SELECT fid FROM friends WHERE uid=%d',$I2_USER->uid)->fetch_array();
		return 'Chat';
	}

	public function display_box($disp) {
		$disp->disp('chat_box.tpl', $this->template_args);
	}
	
	public function init_pane() {
		return false;
	}
	
	function display_pane($disp) {
		return false;
	}

	function get_name() {
		return 'Scratchpad';
	}

	function is_intrabox() {
		return true;
	}
}
?>
