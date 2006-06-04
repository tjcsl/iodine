<?php
class Cookalicious implements Module {

	private $template = 'pane.tpl';
	private $template_args = array();

	public function get_name() {
		return 'Cookalicious';
	}

	public function init_box() {
		return FALSE;
	}

	public function display_box($disp) {
	}

	public function init_pane() {
		global $I2_SQL;
		return FALSE:
			//$file = fopen('/home/braujac/results','r');
			////TODO: put a mysql query here setting $res to a list of distinct userids
		$ret = array();
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$user = new User($row['userid']);
			$ret[] = $user;
		}
		$ret = User::sort_users($ret);
		$this->template_args['users'] = $ret;
	}

	public function display_pane($display) {
		$display->disp($this->template,$this->template_args);
	}

}
?>
