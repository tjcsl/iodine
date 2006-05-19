<?php

class Pictures implements Module {

	function display_box($disp) {
	}
	
	function display_pane($disp) {
		global $I2_ARGS, $I2_LDAP;
		$method = $I2_ARGS[1];
		$args = array();
		for($i = count($I2_ARGS) - 1; $i > 1; $i -= 2) {
			$args[$I2_ARGS[$i - 1]] = $I2_ARGS[$i];
		}
		Display::stop_display();
		var_dump($I2_LDAP->search('ou=people,dc=tjhsst,dc=edu', '(iodineUid=asmith)')->fetch_binary_value('freshmanPhoto'));

		/*$user = new User($args['uid']);
		if($photo = $user->juniorPhoto) {
			header("Content-type: image/jpeg");
			echo $photo;
		}*/
	}
	
	function get_name() {
		return "Pictures";
	}

	function init_box() {
		return FALSE;
	}

	function init_pane() {
		return "";
	}
}
?>
