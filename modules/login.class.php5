<?php

	class Login {
		
		private $loginfailed = FALSE;
		private $uname;
		private $pass;
		private $loggedin = FALSE;

		function display_pane($disp) {

			$disp->disp('login.tpl',array(
				'failed' => $this->loginfailed,
				'loggedin' => $this->loggedin,
				'uname' => $this->uname,
				'pass' => $this->pass
			));
		}

		function init_pane($token) {
			global $I2_ARGS, $I2_AUTH, $I2_USER, $I2_SQL, $_SESSION;
			if (isSet($I2_ARGS['login_username']) && isSet($I2_ARGS['login_password'])) { 
				if ($I2_AUTH->check_user($I2_ARGS['login_username'],$I2_ARGS['login_password'])) {
					$uarr = $I2_SQL->select($token,'users','uid,startpage',"username='%s'",array($I2_ARGS['login_username']))->fetch_array();
					set_i2var('i2_uid',$uarr['uid']);
					set_i2var('i2_username',$I2_ARGS['login_username']);
					if (!isset($I2_ARGS['i2_desired_module']))
						set_i2var('i2_desired_module',$uarr['startpage']);
					set_i2var('i2_login_time',time());
					$this->loggedin = TRUE;
					$this->uname = $I2_ARGS['login_username'];
					$this->pass = $I2_ARGS['login_password'];
					return FALSE;
				} else {
					$this->loginfailed = TRUE;
					$this->uname = $I2_ARGS['login_username'];
					return TRUE;
				}
			} else {
				$this->loginfailed = FALSE;
				return TRUE;
			}
			return TRUE;
		}
	}

?>
