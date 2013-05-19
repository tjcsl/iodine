<?php
/**
* A module that allows interuser chat. Most of the interesting stuff is in the javascript.
* @package modules
* @subpackage chat
*/

class Chat extends Module {

	public function init_box() {
		GLOBAL $I2_USER, $I2_SQL;
		$this->template_args['friends'] = $I2_SQL->query('SELECT fid FROM friends WHERE uid=%d',$I2_USER->uid)->fetch_array();
		return 'Chat';
	}

	public function display_box($disp) {
		$disp->disp('chat_box.tpl', $this->template_args);
	}

	function get_name() {
		return 'Chat';
	}
}
?>
