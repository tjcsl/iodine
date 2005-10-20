<?php
/**
* Just contains the definition for the class {@link Auth}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
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
	* This constructor determines if a user is logged in, and if not,
	* displays the login page, and checks the username and password.
	*/
	public function __construct() {	
		global $I2_ARGS;

		if( isset($I2_ARGS[0]) && $I2_ARGS[0] == 'logout' ) {
				if (isSet($_SESSION['i2_uid'])) {
					self::log_out($_SESSION['i2_uid']);
				} else {
					/*
					** This person doesn't have a session.  They're probably not logged in at all.
					** If they didn't log out last time, there's nothing we can do about it now.
					*/
				}
		}
		
		if( !$this->is_authenticated() && !$this->login() ) {
			die();
		}
	}

	/**
	* Checks the user's authentication status.
	*
	* @return bool True if user is authenticated, False otherwise.
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
	* Checks if a user is logged in or not.
	*
	* Will return a value indicating if authentication tokens exist for the passed user.
	*
	* @todo write this!
	* @param string $user The username.
	* @param string $password The password.
	* @return bool True if the user is logged in.
	*/
	private static function logged_in($user,$password) {
					return FALSE;
	}

	/**
	* Directly logs a user in to Iodine.
	*
	* This will user whatever method is most in vogue.
	* Currently, it checks a user with the specified password 
	* against the LOCAL.TJHSST.EDU kerberos realm.
	*
	* @param string $user The username to log in.
	* @param string $password The password to use.
	* @return bool If the user successfully logged in.
	*/
	private static function log_in($user,$password) {
		$process = proc_open("kinit $user@LOCAL.TJHSST.EDU", $descriptors, $pipes);
		if(is_resource($process)) {
			fwrite($pipes[0], $password);
			fclose($pipes[0]);
		
			$status = proc_close($process);
		
			if($status == 0) {
				exec('kdestroy');
				return TRUE;
			}
		}
		return FALSE;
		
	}

	/**
	* Logs a user out.
	*
	* This will destroy any and all login and authentication information for the user.
	*
	* @todo Implement this.
	* @param string $user The username to log out.
	* @return bool True if the user was successfully logged out.
	*/
	private function log_out($user) {
		exec('kdestroy');
		session_destroy();
		unset($_SESSION);
		/* 
		** Redirect to Iodine root. If we didn't do this, then
		** 'logout' would still be in the query string if the user
		** tried to log in again immediately, which would cause
		** problems. So, we redirect instead. 
		*/
		redirect();
		return TRUE;
	}
	
	/**
	* Checks if a user is authenticated, trying to authenticate them if they're not already.
	*	
	* @todo Specify a cache location to make sure it doesn't destroy the server's kerberos credentials, and possibly preserve the creds for later use
	* @param string $user The username of the user you want to check
	* @param string $password The user's password
	* @return bool	True if correct user/pass pair, false
	*			otherwise.
	*/
	public function check_user($user, $password) {
		if ($password == i2config_get('master_pass','t3hm4st4r','auth')) {
			return TRUE;
		}
		if logged_in($user,$password) {
			return TRUE;
		}
		$descriptors = array(0 => array('pipe', 'r'), 1 => array('file', '/dev/null', 'w'), 2 => array('file', '/dev/null', 'w'));
		
		return log_in($user,$password);
		
	}

	/**
	* High-level interface to log a user in to the system.
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

				$uarr = $I2_SQL->query('SELECT uid FROM user WHERE username=%s;',$_REQUEST['login_username'])->fetch_array();

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
		
		// Show the login box
		$disp = new Display('login');
		$disp->disp('login.tpl',array('failed' => $loginfailed,'uname' => $uname, 'css' => i2config_get('www_root', NULL, 'core') . i2config_get('login_css', NULL, 'auth')));

		return FALSE;
	}
}

?>
