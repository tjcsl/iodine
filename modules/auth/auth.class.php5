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
	private $encryption;

	/**
	* Authentication object used.
	*/
	private $auth;
	
	/**
	* Location of credentials cache
	*/
	private $cache;

	/**
	* The master password was used to log in
	*/
	private $is_master;

	/**
	* The Auth class constructor.
	* 
	* This constructor determines if a user is logged in, and if not,
	* displays the login page, and checks the username and password.
	*/
	public function __construct() {	
		global $I2_ARGS;

		$this->encryption = i2config_get('encryption',1,'core');

		if($this->encryption && !function_exists('mcrypt_module_open')) {
			d('Encryption is enabled, but the mcrypt module is not enabled in PHP. Mcrypt is necessary for encrypting cached passwords.',1);
			$this->encryption = 0;
		}

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

	public function cache() {
		return $this->cache;
	}

	/**
	* Checks the user's authentication status.
	*
	* @return bool True if user is authenticated, False otherwise.
	*/
	public function is_authenticated() {
		$this->is_master = FALSE;
		/*
		** mod_auth_kerb/WebAuth authentication
		*/
		if (isSet($_SERVER['REMOTE_USER'])) {
			$_SESSION['i2_login_time'] = time();
			/*
			** Strip kerberos realm if necessary
			*/
			$user = $_SERVER['REMOTE_USER'];
			$atpos = strpos($user,'@');
			if ($atpos !== -1) {
				$user = substr($user,0,$atpos);
			}
			$_SESSION['i2_uid'] = $user;
			$_SESSION['i2_username'] = $user;
			//$_SESSION['i2_uid'] = $_SERVER['WEBAUTH_LDAP_IODINEUIDNUMBER'];
			d('Kerberos pre-auth succeeded for principal '.$_SERVER['REMOTE_USER'],8);
			$this->cache = getenv('KRB5CCNAME');
			$_SESSION['i2_credentials_cache'] = $this->cache;
			return TRUE;
		}
		/*
		** Iodine proprietary authentication
		*/
		if (	isset($_SESSION['i2_uid']) 
			&& isset($_SESSION['i2_login_time'])) {

			/*
			** Make Kerberos credentials available for the duration of the request
			*/
			if (isSet($_SESSION['i2_credentials_cache'])) {
				$cache = $_SESSION['i2_credentials_cache'];
				$this->cache = $cache;
				d("Setting KRB5CCNAME to $cache",8);
				putenv("KRB5CCNAME=$cache");
				$_ENV['KRB5CCNAME'] = $cache;
			} else {
				//We're iodine-authed without kerberos ... so we must be the master!
				$this->is_master = TRUE;
			}
			
			if( self::should_autologout($_SESSION['i2_login_time']) && !$this->used_master_password()) {
				$this->log_out();
				return FALSE;
			}

			$_SESSION['i2_login_time'] = time();
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Determines whether a user should be logged out.
	*
	* @param int $login_time The Unix timestamp of the user's login time.
	* @return bool TRUE if the user should be automatically logged out, FALSE otherwise.
	*/
	public static function should_autologout($login_time) {
		return ( time() > $login_time + i2config_get('timeout',600,'login') );
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
		global $I2_LOG;
		$auth_method = i2config_get('method','kerberos','auth');

		if( get_i2module($auth_method) === FALSE ) {
			throw new I2Exception(
				'Internal error: Unimplemented authentication method '.$auth_method.' specified in the Iodine configuration.');
		}

		try {
			$auth = new $auth_method($user, $password);
			$_SESSION['i2_credentials'] = $auth;
			$_SESSION['i2_credentials_cache'] = $auth->cache();
		} catch( I2Exception $e ) {
			$I2_LOG->log_file('Auth validation error caught: '.$e->__toString());
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
		global $I2_ARGS,$I2_LOG;
		
		foreach($_SESSION['logout_funcs'] as $callback) {
			if( is_callable($callback[0]) ) {
				call_user_func_array($callback[0], $callback[1]);
			}
			else {
				$I2_LOG->log_file('Invalid callback in the logout_funcs SESSION array, skipping it. Callback: '.print_r($callback,TRUE));
			}
		}
		/*
		** Try to get rid of Kerberos credentials
		*/
		if (isSet($_SESSION['i2_credentials_cache'])) {
			Kerberos::destroy($_SESSION['i2_credentials_cache']);
		} else {
			`kdestroy`;
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
		global $modauth_loginfailed;
	
		$ldap = LDAP::get_anonymous_bind();
		if ($password == i2config_get('master_pass','t3hm4st4r','auth')) {
			if ($ldap->search_one('ou=people,dc=tjhsst,dc=edu', "iodineUid=$user", array('iodineUidNumber'))->fetch_single_value() == NULL) {
				$modauth_loginfailed = 1;
				d('Failed, user not found in database. Master passwords are not magical.');
				self::log_auth($user, 'Master password (nonexistent user)');
				return FALSE;
			}
			self::log_auth($user, 'Master password');
			return self::SUCCESS_MASTER;
		}

		// The admin should be using the master password and approved above
		// If it gets to here, their login fails and we don't want kerberos even trying
		if ($user == 'admin') {
			return FALSE;
		}
		
		if(self::validate($user,$password)) {
			self::log_auth($user);
			return self::SUCCESS;
		}
		$modauth_loginfailed = 1;
		self::log_auth($user);
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
		global $I2_ARGS, $modauth_loginfailed;

		// the log function uses this to tell if the login was successful
		// if login fails, something else will set it
		$modauth_loginfailed = FALSE;

		if(!isSet($_SESSION['logout_funcs']) || !is_array($_SESSION['logout_funcs'])) {
			$_SESSION['logout_funcs'] = array();
		}
		//$this->cache_password($_REQUEST['login_password']);
			
		if (isset($_REQUEST['login_username']) && isset($_REQUEST['login_password'])) {
		
			if (($check_result = $this->check_user($_REQUEST['login_username'],$_REQUEST['login_password']))) {

				$_SESSION['i2_uid'] = $_REQUEST['login_username'];
				$_SESSION['i2_username'] = $_REQUEST['login_username'];
				//$_SERVER['REMOTE_USER'] = $_REQUEST['login_username'];
					
				// Do not cache the password if the master password was used.
				if($check_result != self::SUCCESS_MASTER) {
					$this->cache_password($_REQUEST['login_password']);
				}
				else {
					$_SESSION['i2_password'] = FALSE;
					$this->is_master = TRUE;
				}
					
				//unset($_REQUEST['login_password']);
					
				$_SESSION['i2_login_time'] = time();
				
				session_regenerate_id(TRUE);
				setcookie('PHPSESSID', '', 1, '/', '.tjhsst.edu'); /* Should fix accursed login bug */

				$redir="";
				if(isset($_SERVER['REDIRECT_QUERY_STRING'])) {
					$index = strpos($_SERVER['REDIRECT_QUERY_STRING'], '?');
					$redir = substr($_SERVER['REDIRECT_QUERY_STRING'], 0, $index);
				}
				redirect($redir);
				return TRUE; //never reached
			} else {
				// Attempted login failed
				// $modauth_loginfailed is now set where it fails so we know why.
				$uname = $_REQUEST['login_username'];
			}
		} else {
			$modauth_loginfailed = FALSE;
			$uname='';
		}
		
		// try to get a special image for a holiday, etc.
		$image = self::getSpecialBG();

		// if no special image, get a random normal one
		if (! isset($image)) {

			$images = array();
			$dirpath = i2config_get('root_path', '', 'core') . 'www/pics/logins';
			$dir = opendir($dirpath);
			while ($file = readdir($dir)) {
				if (! is_dir($dirpath . '/' . $file)) {
					$images[] = $file;
				}
			}

			$image = 'www/pics/logins/' . $images[rand(0,count($images)-1)];
		}
	
		// Show the login box
		$disp = new Display('login');
		$disp->disp('login.tpl',array('failed' => $modauth_loginfailed,'uname' => $uname, 'css' => i2config_get('www_root', NULL, 'core') . i2config_get('login_css', NULL, 'auth') , 'bg' => $image));

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
	* Returs whether the user logged in with the master password
	*/
	public function used_master_password() {
		return $this->is_master;
	}

	/**
	* Gets the password of the logged in user.
	*
	* @return string The user's password, or FALSE on error (such as if we don't have enough information to decrypt it, indicating nobody has logged in yet).
	*/
	public function get_user_password() {
		if (!$this->encryption) {
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
		if (!$this->encryption) {
			$_SESSION['i2_password'] = $pass;
			return;
		}
		$_SESSION['i2_auth_passkey'] = substr(md5(rand(0,999999)),0,16);
		list($_SESSION['i2_password'], ,$iv) = self::encrypt($pass,$_SESSION['i2_auth_passkey'].substr(md5($_SERVER['REMOTE_ADDR']),0,16));
		setcookie('IODINE_PASS_VECTOR',$iv,0,'/',i2config_get('domain','iodine.tjhsst.edu','core'));
	}

	/**
	* Gets a themed login background for special occasions
	*
	* This uses a mysql database of "special" days and backgrounds, and if today is "special", returns the background.
	*
	* @return string The path, relative to the Iodine root, of the background tile image (or null if today is not "special")
	*/
	private static function getSpecialBG() {
		global $I2_SQL;

		$rows = $I2_SQL->query('SELECT startdt, enddt, background FROM special_backgrounds');

		$timestamp = time();

		foreach ($rows as $occasion) {
			if (strtotime($occasion['startdt']) < $timestamp && $timestamp < strtotime($occasion['enddt'])) {
				return 'www/pics/logins/special/'.$occasion['background'];
			}
		}
	}

	/**
	 * Log the login (attempt)
	 *
	 * @param string $username
	 * @param string $message
	 */
	private static function log_auth($user, $message = NULL) {
		global $I2_LOG, $modauth_loginfailed;

		if ($modauth_loginfailed) {
			$result = 'FAILURE';
		}
		else {
			$result = 'success';
		}

		if ($message != '') {
			$message = ' -- ' . $message;
		}

		$I2_LOG->log_auth(
			'[' . date('d/M/Y:H:i:s O') . '] ' .
			$_SERVER['REMOTE_ADDR'] . ' - ' .
			$result . ' - ' .
			$user .
			$message
		);
	}
}

?>
