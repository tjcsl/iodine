<?php
/**
* Allows for users to access their Computer Systems Lab files via Intranet.
*
*
* @todo We should really move Kerberos auth functions to a more abstracted layer
*/
	class cslfiles implements Module {

		private static $logins = array();
		private $krb5ccname;

		public function __construct() {
			global $I2_USER;
			$this->krb5ccname = '/tmp/iodine_krb5_'.md5($I2_USER->username);
		}

		public function init_box() {
			return 'Systems Lab File Access';
		}

		public function display_box($disp) {
			global $I2_USER;
			$disp->disp('cslfiles_box.tpl', array('csl_user' => $I2_USER->username));
		}

		public function get_name() {
			return "cslfiles";
		}

		public function init_pane() {
			return 'Computer Systems Lab File Access';
		}

		public function display_pane($disp) {
			if( isset($_SESSION['cslfiles_loggedin']) && $_SESSION['cslfiles_loggedin']) {
				// user already logged in, use previously generated tickets
				
			}
			elseif( ! $this->login() ) {
				// login to CSL failed
				$disp->disp('cslfiles_pane.tpl', array('error' => TRUE));
				return;
			}
			else {
				// successfully logged in
			}
			$disp->disp('cslfiles_pane.tpl');
		}

		public function login() {
			global $I2_USER;
			$user = $I2_USER->username;

			$pass = $_REQUEST['cslfiles_pass'];
			$_REQUEST['cslfiles_pass'] = '';

			$descriptors = array(0 => array('pipe','r'), 1 => array('pipe','w'), 2 => array('pipe','w'));
			$process = proc_open('/usr/bin/pagsh.openafs', $descriptors, $pipes);

			if(is_resource($process)) {
				// make sure to store ticket in a good place
				fwrite($pipes[0], 'export KRB5CCNAME='.$this->krb5ccname."\n");

				// obtain kerberos ticket
				fwrite($pipes[0], '/usr/bin/kinit '.$user."@CSL.TJHSST.EDU\n");
				fwrite($pipes[0], "$pass\n");
				fflush($pipes[0]);
				d('cslfiles: '.fgets($pipes[1]));
				fwrite($pipes[0], "/bin/ls -l /tmp\n");
				fflush($pipes[0]);
				d('cslfiles: '.fgets($pipes[1]));

				pclose($process);
				
			}
			else {
				// something bad happened, can't launch pagsh?
				throw new I2Exception('cslfiles: Cannot launch pagsh');
			}
		}

	
	}
?>
