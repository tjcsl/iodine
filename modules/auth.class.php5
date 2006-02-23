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
	* Represents a standard successful login.
	*/
	const SUCCESS = 1;
	/**
	* Represents a successful login using the Iodine master password.
	*/
	const SUCCESS_MASTER = 2;

	/**
	* Whether encryption of the user's password in $_SESSION is enabled.
	*/
	private $encryption = 0;

	/**
	* The Auth class constructor.
	* 
	* This constructor determines if a user is logged in, and if not,
	* displays the login page, and checks the username and password.
	*/
	public function __construct() {	
		global $I2_ARGS;

		//$this->encryption = i2config_get('encryption','1','core');

		if( isset($I2_ARGS[0]) && $I2_ARGS[0] == 'logout' ) {
				if (isSet($_SESSION['i2_uid'])) {
					self::log_out();
				} else {
					/*
					** This person doesn't have a session.  They're probably not logged in at all.
					** If they didn't log out last time, there's nothing we can do about it now.
					*/
				}
				// Redirect to Iodine root. If we didn't do this, then
				// 'logout' would still be in the query string if the user
				// tried to log in again immediately, which would cause
				// problems. So, we redirect instead. 
				redirect();
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
			&& isset($_SESSION['i2_login_time'])) {
			if( $_SESSION['i2_login_time'] > time()+i2config_get('timeout',600,'login')) {
				$this->log_out();
				return FALSE;
			}

			$_SESSION['i2_login_time'] = time();
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Low-level check of a username against a password.
	*
	* This will check if $password is valid for user $user, using
	* whatever authentication method specified in config.ini under the
	* 'Auth' section.
	*
	* The authentication method specified in config.ini is the name of a
	* class. To log in a user, a new object of that class will be
	* instantiated. Two parameters will be passed to the constructor,
	* which are the same parameters passed to this method. It must throw an
	* {@link I2Exception} if the password is not valid.
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

		try {
			$_SESSION['i2_credentials'] = new $auth_method($user, $password);
		} catch( I2Exception $e ) {
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	* Logs a user out of the Iodine system.
	*
	* This logs out a user, performing the following tasks:
	* <ul>
	*  <li>Calls all of the functions in the $_SESSION['logout_funcs'] array</li>
	*  <li>Destroys all session information associated with the user</li>
	* </ul>
	*
	* Each item in the $_SESSION['logout_funcs'] is an array with two things:
	* The first index is the callback, either a function name or a callback in the
	* form of array('class','method');. The second index is an array of parameters.
	* So, if you wanted to call Class::stuff(1,2); when a user logs out, do:
	* <pre>$_SESSION['logout_funcs'][] = array(array('class','stuff'),array(1,2));</pre>
	*
	* The callbacks in logout_funcs are called when the user clicks the 'logout' link
	* or if their session times out and they try to access a page.
	*
	* @return bool TRUE if the user was successfully logged out.
	*/
	private function log_out() {
		global $I2_ARGS;
		
		foreach($_SESSION['logout_funcs'] as $callback) {
			if( is_callable($callback[0]) ) {
				call_user_func_array($callback[0], $callback[1]);
			}
			else {
				d('Invalid callback in the logout_funcs SESSION array, skipping it. Callback: '.print_r($callback,TRUE));
			}
		}
		
		session_destroy();
		unset($_SESSION);
		 
		return TRUE;
	}
	
	/**
	* Medium-level check of a password against a certain user.
	*
	* This method merely checks if the specified master password, and if
	* not, then it just calls {@link validate()} on the specified username
	* and password.
	*
	* @param string $user The username of the user you want to check
	* @param string $password The user's password
	* @return bool	Auth::SUCCESS_MASTER if the person passed the master
	*		password, Auth::SUCCESS if the person's actual password
	*		was passwrd, FALSE otherwise.
	*/
	public function check_user($user, $password) {
		if ($password == i2config_get('master_pass','t3hm4st4r','auth')) {
			return self::SUCCESS_MASTER;
		}
		
		if(self::validate($user,$password)) {
			return self::SUCCESS;
		}
		return FALSE;
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
		global $I2_SQL, $I2_ARGS;

		if(!isset($_SESSION['logout_funcs']) || !is_array($_SESSION['logout_funcs'])) {
			$_SESSION['logout_funcs'] = array();
		}

		//$this->cache_password($_REQUEST['login_password']);
			
		if (isset($_REQUEST['login_username']) && isset($_REQUEST['login_password'])) {
		
			if (($check_result = $this->check_user($_REQUEST['login_username'],$_REQUEST['login_password']))) {

				$uarr = $I2_SQL->query('SELECT uid FROM user WHERE username=%s;',$_REQUEST['login_username'])->fetch_array();

				if(!isset($uarr['uid'])) {
					// User authenticated successfully, but they are not in the database
					$loginfailed = 2;
					$uname = $_REQUEST['login_username'];
				}
				else {
					$_SESSION['i2_uid'] = $uarr['uid'];
					$_SESSION['i2_username'] = $_REQUEST['login_username'];
					
					// Do not cache the password if the master password was used.
					if($check_result != self::SUCCESS_MASTER) {
						$this->cache_password($_REQUEST['login_password']);
					}
					else {
						$_SESSION['i2_password'] = FALSE;
					}
					
					//$_REQUEST['login_password'] = '';
					
					$_SESSION['i2_login_time'] = time();
				
					redirect(implode('/', $I2_ARGS));
					return TRUE; //never reached
				}
			} else {
				// Attempted login failed
				$loginfailed = 1;
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

	/**
	* Encrypts a string with the given key.
	*
	* encrypt() takes $str, and uses $key to encrypt it. It uses the TripleDES in CBC mode as the encryption algorithm, with /dev/urandom as a random source.
	*
	* @return Array An array containing three elements. The first one is the encrypted string, the second is the key used (if it was altered at all from the one passed), and the third is the initialization vector used to encrypt the string. You will need all three of these items in order to decrypt the string again.
	*/
	public static function encrypt($str, $key) {
		$td = mcrypt_module_open(MCRYPT_TRIPLEDES,'',MCRYPT_MODE_CBC,'');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_DEV_URANDOM);
		$keysize = mcrypt_enc_get_key_size($td);
		$mkey = substr(md5($key),0,$keysize);
		mcrypt_generic_init($td,$mkey,$iv);
		$ret = mcrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return array($ret,$key,$iv);
	
	}
	/**
	* Decrypts a string with the given key and initialization vector.
	*
	* decrypt() takes $str, and uses $key and $iv to decrypt it (all items that are returned by encrypt()). It uses the TripleDES in CBC mode as the encryption algorithm.
	*
	* @return String The decrypted string.
	*/
	public static function decrypt($str, $key, $iv) {
		$td = mcrypt_module_open(MCRYPT_TRIPLEDES,'',MCRYPT_MODE_CBC,'');
		$keysize = mcrypt_enc_get_key_size($td);
		$key = substr(md5($key),0,$keysize);
		mcrypt_generic_init($td, $key, $iv);
		$ret = mdecrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return trim($ret);
	}

	/**
	* Gets the password of the logged in user.
	*
	* @return string The user's password, or FALSE on error (such as if we don't have enough information to decrypt it, indicating nobody has logged in yet).
	*/
	public function get_user_password() {
		if ($this->encryption == 0) {
			return $_SESSION['i2_password'];
		}
		if( !( isset($_SESSION['i2_password']) && isset($_SESSION['i2_auth_passkey']) && isset($_COOKIE['IODINE_PASS_VECTOR']))) {
			d('Unable to retrieve the user password!',3);
			return FALSE;
		}
		return self::decrypt($_SESSION['i2_password'], $_SESSION['i2_auth_passkey'].substr(md5($_SERVER['REMOTE_ADDR']),0,16), $_COOKIE['IODINE_PASS_VECTOR']);
	}

	/**
	* Caches a user's password.
	*
	* This stores an encrypted version of the user's password in
	* $_SESSION['i2_password'], the password key in
	* $_SESSION['i2_auth_passkey'], and the initialization vector used for
	* encryption in a client's cookie called IODINE_PASS_VECTOR.
	*/
	private function cache_password($pass) {
		if ($this->encryption == 0) {
			$_SESSION['i2_password'] = $pass;
			//throw new Exception();
			return;
		}
		$_SESSION['i2_auth_passkey'] = substr(md5(rand(0,i2config_get('rand_max',65025,'core'))),0,16);
		list($_SESSION['i2_password'], ,$iv) = self::encrypt($pass,$_SESSION['i2_auth_passkey'].substr(md5($_SERVER['REMOTE_ADDR']),0,16));
		setcookie('IODINE_PASS_VECTOR',$iv,0,'/',i2config_get('domain','iodine.tjhsst.edu','core'));
	}
}

?>
