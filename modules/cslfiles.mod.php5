<?php
/**
* Allows for users to access their Computer Systems Lab files via Intranet.
*
* @package modules
*
*/
	class cslfiles implements Module {

		private static $logins = array();
		private $user;

		public function __construct() {
			if (isSet($_SERVER['csl_logins'])) {
				self::$logins = $_SERVER['logins'];
			}
			$_SERVER['csl_logins'] = array();
		}

		public static function open_shell($user, $password) {
			/*
			** Wholeheartedly stolen from auth.
			*/
			$descriptors = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
			$process = proc_open('pagsh', $descriptors, $pipes);
			if (is_resource($process)) {
				d("Activating CSL files as $user");
				fwrite($pipes[0], "kinit $user");
				fwrite($pipes[0], $password);
				stream_set_blocking($pipes[1],FALSE);
				stream_set_blocking($pipes[2],FALSE);
				$txt = self::readin($pipes[1]);
				d("CSL-Read: $txt");
				fwrite($pipes[0], 'aklog');
				fflush($pipes[0]);
				self::$logins[$user] = array($process,$pipes[0],$pipes[1],$pipes[2]);
				$_SERVER['csl_logins'] = self::$logins;
				d("CSL login as $user");
			} else {
				throw new i2exception("Failed to open pagsh!");
			}
			
			return false;
		}

		public static function readin($pipe) {
				$txt = '';
				while($ret = fread($pipe,2)) {
					$txt .= $ret;
				}
				return $txt;
		}

		public static function run_command($user,$cmd) {
			if (!isSet(self::$logins[$user])) {
				d("Bad username `$user' passed to run_command");
				return NULL;
			}
			fwrite(self::$logins[$user][1],$cmd);
			fflush(self::$logins[$user][1]);
			$txt = self::readin(self::$logins[$user][2]);
			d("Command $cmd gave output: $txt");
		}

		private static function logout($user) {
			if (!isSet(self::$logins[$user])) {
				return false;
			}
			fwrite(self::$logins[$user][1],'kdestroy');
			fflush(self::$logins[$user][1]);
			fclose(self::$logins[$user][1]);
			d('CSL-Read: '.fread(self::$logins[$user][2]));
			d('CSL-Read-Error: '.fread(self::$logins[$user][3]));
			proc_close(self::$logins[$user][0]);
			unset(self::$logins[$user]);
			return true;
		}

		public function init_box() {
			return "Systems Lab File Access";
		}

		public function display_box($disp) {
			if (isSet($this->user)) {
				$disp->smarty_assign('csl_user',$this->user);
				$_SESSION['csl_user'] = $this->user;
			} else if (isSet($_POST['csl_user']) && isSet($_POST['csl_pass']) && !isSet(self::$logins[$_POST['csl_user']])) {
				$this->user = $_POST['csl_user'];
				$_SESSION['csl_user'] = $this->user;
				$disp->smarty_assign('csl_user',$this->user);
				self::open_shell($_POST['csl_user'],$_POST['csl_pass']);
			}
			if (isSet($_POST['csl_cmd']) && isSet($this->user)) {
				if (self::run_command($this->user,$_POST['csl_command']) !== NULL) {
					$ps = self::$logins[$this->user];
					$out = self::readin($ps[1]);
					$err = self::readin($ps[$this->user][2]);
					$disp->smarty_assign(array("stdout"=>$out,"stderr"=>$err,"cmd"=>$_POST['csl_command']));
				}
			}
			$disp->disp('cslfiles_box.tpl');
		}

		public function get_name() {
			return "cslfiles";
		}

		public function init_pane() {
			return false;
		}

		public function display_pane($disp) {
				$disp->disp('cslfiles_pane.tpl');
		}

	
	}
?>
