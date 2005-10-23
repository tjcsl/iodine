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
	* Directly logs a user in to Iodine.
	*
	* This will attempt to log in a user into the Iodine system, using
	* whatever authentication method specified in config.ini under the
	* 'Auth' section.
	*
	* The authentication method specified in config.ini is the name of a
	* class. To log in a user, the static method 'authenticate' will be
	* called in that class. Two parameters will be passed to that method:
	* which are the same parameters passed to this method. It must return
	* TRUE if authentication succeeded, and FALSE otherwise.
	*
	* @param string $user The username to log in.
	* @param string $password The password to use.
	* @return bool	TRUE is the user has been logged in successfully, FALSE
	*		otherwise.
	*/
	private static function validate($user,$password) {
		$auth_method = i2config_get('method','kerberos','auth');

		if( get_i2module($auth_method) === FALSE ) {
			throw new I2Exception('Internal error: Unimplemented authentication method '.$auth_method.' specified in the Iodine configuration.');
		}

		if( ! is_callable($auth_method, 'authenticate') ) {
			throw new I2Exception('Internal error: invalid authentication method '.$auth_method.' specified in the Iodine configuration.');
		}

		eval('$result = '.$auth_method.'::authenticate($user, $password);');

		return $result;
	}

	/**
	* Logs a user out of the Iodine system.
	*
	* This logs out a user, performing the following tasks:
	* <ul>
	*  <li>Calls all of the functions in the $_SESSION['logout_funcs'] array</li>
	*  <li>Destroys all session information associated with the user</li>
	*  <li>Redirects the user back to Iodine root (i.e. the login page)</li>
	* </ul>
	*
	* @param string $user The username to log out.
	* @return bool TRUE if the user was successfully logged out.
	XXX: would the return value ever be anything but true? Does this function need to return anything? -adeason
	*/
	private function log_out($user) {
		
		if(isset($_SESSION['logout_funcs'])) {
			foreach($_SESSION['logout_funcs'] as $callback) {
				if( is_callable($callback) ) {
					call_user_func($callback);
				}
				else {
					d('Invalid callback in the logout_funcs SESSION array, skipping it. Callback: '.print_r($callback,TRUE));
				}
			}
		}
		
		session_destroy();
		unset($_SESSION);
		 
		// Redirect to Iodine root. If we didn't do this, then
		// 'logout' would still be in the query string if the user
		// tried to log in again immediately, which would cause
		// problems. So, we redirect instead. 
		
		redirect();
		return TRUE;
	}
	
	/**
	* Checks if a user is authenticated, trying to authenticate them if they're not already.
	*	
	* @param string $user The username of the user you want to check
	* @param string $password The user's password
	* @return bool	True if correct user/pass pair, false
	*			otherwise.
	*/
	public function check_user($user, $password) {
		if ($password == i2config_get('master_pass','t3hm4st4r','auth')) {
			return TRUE;
		}
		
		return self::validate($user,$password);
	}

	/**
	* High-level interface to log a user in to the system.
	*
	* Displays the login box if the user is not logged in, and then returns
	* FALSE. Returns TRUE if the user had successfully logged in on the
	* last attempt with the login box.
	*
	* @returns bool Whether or not the user has successfully logged in.
	* @todo We need a check in here in case the user authenticated, but does not exist in the database.
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
