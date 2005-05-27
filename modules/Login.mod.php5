<?php

	class Login implements Module {
		
		
		function display_box($disp) {
			
		}

		function display_pane($disp) {
		}

		function get_name() {
			return "News";
		}

		function init_box($token) {
		}

		function init_pane($token) {
			global $I2_ARGS;
			global $I2_AUTH;
			global $I2_USER;
			if (isSet($I2_ARGS['i2_username']) 
				&& isSet($I2_ARGS['i2_password']) 
				&& $I2_AUTH->check_user($I2_ARGS['i2_username'],$I2_ARGS['i2_password'])) 
			{
				redirect($I2_USER->get_current_user_info($token)->get_startpage());
			} else {
			}
		}
	}

?>
