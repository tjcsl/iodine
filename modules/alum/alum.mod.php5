<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2007 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage alum
* @filesource
*/

class Alum implements Module {

	function init_pane() {
		global $I2_USER,$I2_ARGS,$I2_SQL;

		return 'Alumni Intranet';
		
	}
	
	function display_pane($display) {
		global $I2_ARGS;

		if($I2_ARGS[1] == 'pswd') {
			Display::stop_display();
			$oldpass = $_REQUEST['oldpass'];
			$newpass = $_REQUEST['newpass'];
			echo 1;
			exit;
		}
		else {		
			$display->disp('alum_copy_pane.tpl');
		}
	}
	
	function init_box() {
		return FALSE;
	}

	function display_box($display) {
		return FALSE;
	}

	function get_name() {
		return 'Alum';
	}
}

?>
