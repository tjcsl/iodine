<?php
/**
* Just contains the definition for the class {@link Auth}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage Auth
* @filesource
*/

/**
* The auth module for Iodine.
* @package core
* @subpackage Auth
*/
class Auth {
	/**
	* The Auth class constructor.
	* 
	* @access public
	*/
	function __construct() {	
		global $I2_ARGS;

		if( isset($I2_ARGS[0]) && $I2_ARGS[0] == 'logout' ) {
			session_destroy();
			unset($_SESSION);
			/* Redirect to Iodine root. If we didn't do this, then
			'logout' would still be in the query string if the user
			tried to log in again immediately, which would cause
			problems. So, we redirect instead. */
			redirect();
		}
		
		if( !$this->is_authenticated() && !$this->login() ) {
			die();
		}
	}

	/**
	* Checks the user's authentication status.
	*
	* @return boolean True if user is authenticated, false otherwise.
	*/
	public function is_authenticated() {
		if (	isset($_SESSION['i2_uid']) 
			&& isset($_SESSION['i2_login_time']) 
			&& $_SESSION['i2_login_time'] <= time()+i2config_get('timeout',600,'login')) {
			$_SESSION['i2_login_time'] = time();
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	* Checks a user with the specified password.
	*
	* @todo Specify a cache location to make sure it doesn't destroy the server's kerberos credentials, and possible preserve the creds for later use
	* @param string $user The username of the user you want to check
	* @param string $password The user's password
	* @return boolean	True if correct user/pass pair, false
	*			otherwise.
	*/
	 
	public function check_user($user, $password) {
		$descriptors = array(0 => array('pipe', 'r'), 1 => array('file', '/dev/null', 'w'), 2 => array('file', '/dev/null', 'w'));

		$process = proc_open("kinit $user@LOCAL.TJHSST.EDU", $descriptors, $pipes);
		if(is_resource($process)) {
			fwrite($pipes[0], $password);
			fclose($pipes[0]);
			$status = proc_close($process);
		
			if($status == 0) {
				exec('kdestroy');
				return true;
			}
		}
		return false;
	}

	/**
	* Login a user to the system.
	*
	* Displays the login box if the user is not logged in, and then returns
	* FALSE. Returns TRUE if the user had successfully logged in on the
	* last attempt with the login box.
	*
	* @returns bool Whether or not the user has successfully logged in.
	*/
	public function login() {
		global $I2_SQL;

		if (isset($_REQUEST['login_username']) && isset($_REQUEST['login_password'])) {
		
			if ($this->check_user($_REQUEST['login_username'],$_REQUEST['login_password'])) {

				$uarr = $I2_SQL->query(true, 'SELECT uid FROM user WHERE username=%s;',$_REQUEST['login_username'])->fetch_array();

				$_SESSION['i2_uid'] = $uarr['uid'];
				$_SESSION['i2_username']= $_REQUEST['login_username'];
				$_SESSION['i2_login_time'] = time();
				
				return TRUE;
			} else {
				/* Attempted login failed */
				$loginfailed = TRUE;
				$uname = $_REQUEST['login_username'];
			}
		} else {
			$loginfailed = FALSE;
			$uname='';
		}
		
		$disp = new Display('login');
		$disp->disp('login.tpl',array('failed' => $loginfailed,'uname' => $uname, 'css' => i2config_get('www_root', NULL, 'core') . i2config_get('login_css', NULL, 'auth')));

		return FALSE;
	}
}

?>
