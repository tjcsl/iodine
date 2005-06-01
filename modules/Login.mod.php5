<?php

	class Login implements Module {
		
		private $loginfailed = false;
		private $faileduname;
		private $failedpass;
		
		function display_box($disp) {
			
		}

		function display_pane($disp) {
			$disp->disp('login.tpl',array(
				'failed' => $this->loginfailed,
				'failedname' => $this->faileduname,
				'failedpass' => $this->failedpass
			));
		}

		function get_name() {
			return "Login";
		}

		function init_box($token) {
		}

		function init_pane($token) {
			global $I2_ARGS, $I2_AUTH, $I2_USER, $I2_SQL;
			if (isSet($I2_ARGS['login_username']) && isSet($I2_ARGS['login_password'])) { 
				if ($I2_AUTH->check_user($I2_ARGS['login_username'],$I2_ARGS['login_password'])) {
					$uarr = $I2_SQL->select($token,'users','uid','username=%s',array($I2_ARGS['login_username']))->fetch_array();
					set_i2var('i2_uid',$uarr['uid']);
					set_i2var('i2_username',$I2_ARGS['login_username']);
					redirect(get_i2module($I2_ARGS['i2_after_login_module']));
				} else {
					$this->loginfailed = true;
					$this->faileduname = $I2_ARGS['login_username'];
					$this->failedpass = $I2_ARGS['login_password'];
					return;
				}
			} else {
				$this->loginfailed = false;
				return;
			}
		}
	}

?>
