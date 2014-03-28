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
	* What auth mechanism was used to login
	*/
	private $auth_type;

	private $template_args = [];

	private $modauth_err;
	private $modauth_loginfailed;

	/**
	* The Auth class constructor.
	*
	* This constructor determines if a user is logged in, and if not,
	* displays the login page, and checks the username and password.
	*/
	public function __construct() {
		global $I2_ARGS;
		$this->encryption = i2config_get('pass_encrypt',1,'core');

		if($this->encryption && !function_exists('mcrypt_module_open')) {
			d('Encryption is enabled, but the mcrypt module is not enabled in PHP. Mcrypt is necessary for encrypting cached passwords.',1);
			$this->encryption = 0;
		}

		if( isset($I2_ARGS[0]) && $I2_ARGS[0] == 'logout' ) {
				//if (isset($_SESSION['i2_uid'])) {
				if (isset($_SESSION['i2_username'])) {
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
		if( isset($I2_ARGS[0]) && $I2_ARGS[0] == 'feeds' ) {
			return true;
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
		public function is_authenticated($skipcheck=FALSE) {
			global $I2_ARGS;
			if(!$skipcheck &&isset($I2_ARGS[0]) && ($I2_ARGS[0]=='feeds' || ($I2_ARGS[0]=='calendar')))
				return true;
			$this->is_master = FALSE;
			/*
			 ** mod_auth_kerb/WebAuth authentication
			 */
			if (isset($_SERVER['REMOTE_USER'])) {
				$_SESSION['i2_login_time'] = time();
				/*
				 ** Strip kerberos realm if necessary
				 */
				$user = $_SERVER['REMOTE_USER'];
				$atpos = strpos($user,'@');
				if ($atpos !== -1) {
					$user = substr($user,0,$atpos);
				}
				//$_SESSION['i2_uid'] = strtolower($user);
				$_SESSION['i2_username'] = strtolower($user);
				//$_SESSION['i2_uid'] = $_SERVER['WEBAUTH_LDAP_IODINEUIDNUMBER'];
				d('Kerberos pre-auth succeeded for principal '.$_SERVER['REMOTE_USER'],8);
				$this->cache = getenv('KRB5CCNAME');
				return TRUE;
			}
			/*
			 ** Iodine proprietary authentication (of all kinds)
			 */
			//if (	isset($_SESSION['i2_uid'])
			if (	isset($_SESSION['i2_username'])
					&& isset($_SESSION['i2_login_time'])) {

				$this->auth_type = $_SESSION['auth_type'];
				$this->auth = $_SESSION['auth'];
				$this->auth->reload();

				if (self::should_autologout($_SESSION['i2_login_time'])) {
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
		public static function should_autologout($login_time,$i2_username=NULL) {
			if ( (isset($_SESSION['i2_username']) && $_SESSION['i2_username'] == 'eighthoffice') || $i2_username=='eighthoffice') {
				return FALSE;
			}
			return ( time() > $login_time + i2config_get('timeout',600,'login') );
		}

		/**
		 * Low-level check of a username against a password.
		 *
		 * This will check if $password is valid for user $user, using
		 * the authentication method(s) specified in config.ini under the
		 * 'Auth' section.
		 *
		 * The config.ini file contains a 'methods =' directive, which should
		 * give a comma-seperated list of authentication methods. Each method
		 * must be the name of a class implelementing the AuthType interface.
		 * The methods will be tried in the order listed until one succeeds.
		 *
		 * @param string $user The username to log in.
		 * @param string $password The password to use.
		 * @return bool	TRUE is the user has been logged in successfully, FALSE
		 *		otherwise.
		 */
		private static function validate($user,$password,$auth_methods=NULL) {
			if($auth_methods==NULL)
				$auth_methods = explode(',', i2config_get('methods',NULL,'auth'));

			foreach ($auth_methods as $auth_method) {
				if( get_i2module($auth_method) === FALSE ) {
					throw new I2Exception(
							'Internal error: Unimplemented authentication method '.$auth_method.' specified in the Iodine configuration.');
				}

				$auth = new $auth_method();
				if ($auth->login($user, $password)) {
					$_SESSION['auth_type'] = $auth_method;
					$_SESSION['auth'] = $auth;
					self::log_auth($user, TRUE, $auth_method);
					return TRUE;
				}
			}

			self::log_auth($user, FALSE, 'overall'); 
			return FALSE;
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
			global $I2_LOG;

			foreach($_SESSION['logout_funcs'] as $callback) {
				if( is_callable($callback[0]) ) {
					call_user_func_array($callback[0], $callback[1]);
				}
				else {
					$I2_LOG->log_file('Invalid callback in the logout_funcs SESSION array, skipping it. Callback: '.print_r($callback,TRUE));
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
		 * @return bool	TRUE, FALSE otherwise.
		 */
		public function check_user($user, $password) {

			// make sure the user exists in ldap not just kerberos.
			$ldap = LDAP::get_generic_bind();
			if ($ldap->search_one(LDAP::get_user_dn(), "iodineUid=$user", 'iodineUidNumber')->fetch_single_value() == NULL) {
				$this->modauth_loginfailed = 3;
				return FALSE;
			}

			// The admin should be using the master password and approved above
			// If it gets to here, their login fails and we don't want kerberos even trying
			if ($user == 'admin') {
				$val = self::validate($user,$password,['master']);
				if(!$val) $this->modauth_loginfailed = 3;
				return $val;
			}

			if(self::validate($user,$password)) {
				return TRUE;
			}

			$this->modauth_loginfailed = 1;
			return FALSE;
		}

		/**
		 * Get an appropriate LDAP bind
		 *
		 * Asks the auth method that the user was logged in with to get the
		 * correct bind from LDAP. This is because the bind is dependant on the
		 * auth method; for example, Kerberos will get a bind using GSSAPI,
		 * while the master password will get a simple bind.
		 *
		 * @return LDAP An LDAP object representing an appropriate LDAP bind
		 */
		public function get_ldap_bind() {
			return $this->auth->get_ldap_bind();
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
			global $I2_ROOT, $I2_ARGS, $I2_QUERY, $I2_API, $I2_AJAX;

			// the log function uses this to tell if the login was successful
			// if login fails, something else will set it
			$this->modauth_loginfailed = FALSE;

			if(!isset($_SESSION['logout_funcs']) || !is_array($_SESSION['logout_funcs'])) {
				$_SESSION['logout_funcs'] = [];
			}
			//$this->cache_password($_REQUEST['login_password']);
			if (isset($_REQUEST['login_username']) && isset($_REQUEST['login_password'])) {
				if ($this->check_user($_REQUEST['login_username'],$_REQUEST['login_password'])) {

					//$_SESSION['i2_uid'] = strtolower($_REQUEST['login_username']);
					$_SESSION['i2_username'] = strtolower($_REQUEST['login_username']);
					//$_SERVER['REMOTE_USER'] = $_REQUEST['login_username'];

					// Do not cache the password if the master password was used.
					if($this->auth_type != 'master') {
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
					setcookie('fortune',exec("fortune -s"),1,'/','.tjhsst.edu');
					$_SESSION['firstload'] = true;
					$redir="";
					if(isset($_SERVER['REDIRECT_QUERY_STRING'])) {
						$index = strpos($_SERVER['REDIRECT_QUERY_STRING'], '?');
						$redir = substr($_SERVER['REDIRECT_QUERY_STRING'], 0, $index);
					}
					if(sizeof($I2_QUERY) > 0) {
						$redir.="?".http_build_query($I2_QUERY);
					}
					redirect($redir,sizeof($_POST)>2);//If we have additional post fields, prompt to allow relay, and relay if allowed.
					return TRUE; //never reached
				} else {
					// Attempted login failed
					// $modauth_loginfailed is now set where it fails so we know why.
					$uname = $_REQUEST['login_username'];

					if(isset($I2_ARGS[0]) && $I2_ARGS[0] == 'api') {
						$I2_API->init();
						$I2_API->logging = false;
						$I2_API->startElement('auth');
						$I2_API->startElement('error');
						$I2_API->writeElement('success',$this->modauth_loginfailed==1?'false':'true');
						$I2_API->writeElement('loginerror',$this->modauth_err);
						$I2_API->writeElement('id',$this->modauth_loginfailed);
						$I2_API->writeElement('message','Login failed.');
						$I2_API->writeElement('login_base_url',$I2_ROOT);
						$I2_API->endElement();
						$I2_API->endElement();
						exit(0);
					}
				}
			} else {
				$this->modauth_loginfailed = FALSE;
				$uname='';
			}

			self::init_backgrounds();
			try {
				$this->template_args['emerg'] = News::get_emerg_message();
			} catch(Exception $e) {
				$this->template_args['emerg'] = "<!-- Exception thrown running News::get_emerg_message -->";
			}
			// Show the login box
			$this->template_args['failed'] = $this->modauth_loginfailed;
			$this->template_args['uname'] = $uname;


			if(isset($this->modauth_err)) {
				d($this->modauth_err, 5);
				$this->template_args['err'] = $this->modauth_err;
			}

			self::init_schedule();
			// Save any post data that we get and pass it to the html. (except for a password field)
			$str="";
			foreach (array_keys($_POST) as $post) {
				if($post!="password" && $post!="login_password")
					if(is_array($_POST[$post])) {
						foreach($_POST[$post] as $p) {
							$str.="<input type='hidden' name='".$post."[]' value='".$p."' />";
						}
					} else {
						$str.="<input type='hidden' name='".$post."' value='".$_POST[$post]."' />";
					}
			}
			$this->template_args['posts']=$str;

			$this->template_args['querystring'] = (sizeof($I2_QUERY) > 0 ? '?' . http_build_query($I2_QUERY) : '');

			$disp = new Display('login');

			$disp->smarty_assign('backgrounds', self::get_background_images());
			//FIXME: all these special cases should not be in the login() function.
			if(isset($I2_ARGS[0]) && $I2_ARGS[0]=='api') {
				self::auth_api();
				exit(0);
			} else if(isset($I2_ARGS[0],$I2_ARGS[1]) && $I2_ARGS[0] == 'ajax' && $I2_ARGS[1]=='dayschedule') {
				$I2_AJAX->returnResponse($I2_ARGS[1]);
			} else {
				//$disp->disp('login.tpl', $this->template_args);
			if(time() > 1396274400000) { /* Mon Mar 31 2014 10:00:00 GMT-0400 (EDT) */	
				$disp->disp('login-gc.tpl', $this->template_args);
			} else $disp->disp('login.tpl', $this->template_args);
				//$disp->disp('fb.tpl', $template_args);
				//$disp->disp('windows.tpl', $template_args);
			}

			return FALSE;
	}
	private function auth_api() {
		global $I2_API, $I2_ARGS;
		$I2_API->init();
		$I2_API->logging = false;
		if(isset($I2_ARGS[1]) && $I2_ARGS[1] == 'dayschedule') {
			$module = 'dayschedule';
			$mod = new $module();
			$I2_API->startDTD($module);
			$I2_API->writeDTDElement($module,'(body,error,debug)');
			if($mod->api_build_dtd()==false) {
				// no module-specific dtd
				$I2_API->writeDTDElement('body','(#PCDATA)');
			}
			$I2_API->writeDTDElement('error','(#PCDATA)');
			$I2_API->writeDTDElement('debug','(#PCDATA)');
			$I2_API->endDTD();
			$I2_API->startElement($module);
			$I2_API->writeElement('loggedin', 0);
			$mod->api();
			exit(0);
		}
		$I2_API->startElement('auth');
		$I2_API->startElement('error');
		$I2_API->writeElement('message','You are not logged in.');
		$I2_API->writeElement('login_base_url',$I2_ROOT);
		$I2_API->endElement();
		$I2_API->endElement();
	}
	private function init_backgrounds() {
		global $I2_QUERY, $I2_FS_ROOT;
		// try to get a special image for a holiday, etc.
		$imagearr = self::getSpecialBG();
		$image = $imagearr[0];
		$imagejs = $imagearr[1];
		$url_prefix = "www/pics/logins/";
		if(isset($I2_QUERY['background']) && !strstr($I2_QUERY['background'], "..") && $I2_QUERY['background'] !== 'random') {
			d("Custom background set in query: ".$I2_QUERY['background'], 8);
			$image = $url_prefix.$I2_QUERY['background'];
			$_COOKIE['background'] = $I2_QUERY['background'];
			setcookie("background", $I2_QUERY['background'], time()+60*60*24*30);
		}
		if(isset($_COOKIE['background']) && !strstr($_COOKIE['background'], "..") && $_COOKIE['background'] !== 'random') {
			d("Custom background loaded from cookie: ".$_COOKIE['background'], 8);
			$image = $url_prefix.$_COOKIE['background'];
		}
		if(isset($_COOKIE['background']) && (isset($I2_QUERY['background']) && $I2_QUERY['background'] == 'random')) {
			setcookie("background", "", time()-3600);
			unset($_COOKIE['background']);
		}
		if(isset($image) && !@file_exists($I2_FS_ROOT . $image)) {
			d("Background image ({$image}) did not exist.", 8);
			unset($image);
			setcookie("background", "", time()-3600);
			unset($_COOKIE['background']);
		}
		// if no special image, get a random normal one
		if (! isset($image)) {

			$images = [];
			$dirpath = $I2_FS_ROOT . $url_prefix;
			$dir = opendir($dirpath);
			while ($file = readdir($dir)) {
				if (! is_dir($dirpath . '/' . $file)) {
					$images[] = $file;
				}
			}

			$image = $url_prefix . $images[rand(0,count($images)-1)];
			d("Using random background image {$image}", 8);
		}
		$this->template_args['bg'] = $image;
		$this->template_args['bgjs'] = $imagejs;

	}
	private function init_schedule() {
		global $I2_QUERY;
		$ds = new DaySchedule();
		$ds->init_login();
		d('auth args'.print_r($ds->get_args(),1), 0);
		$this->template_args = array_merge($this->template_args, $ds->get_args());
		$this->template_args['type'] = 'login';
	}
	/**
	* Gets all of the background images that can be used on Iodine.
	*
	* @return Array An array containing the URLs of pictures in www/pics/logins.
	*/
	public function get_background_images() {
		global $I2_FS_ROOT;
			$images = [];
			$dirpath = $I2_FS_ROOT . 'www/pics/logins';
			$dir = opendir($dirpath);
			while ($file = readdir($dir)) {
				if (! is_dir($dirpath . '/' . $file)) {
					$images[] = $file;
				}
			}
		return $images;
	}

	/**
	* Encrypts a string with the given key.
	*
	* encrypt() takes $str, and uses $key to encrypt it. It uses Rijndael 128 in CBC mode as the encryption algorithm, with /dev/urandom as a random source.
	*
	* @return Array An array containing three elements. The first one is the encrypted string, the second is the key used (if it was altered at all from the one passed), and the third is the initialization vector used to encrypt the string. You will need all three of these items in order to decrypt the string again.
	*/
	public static function encrypt($str, $key) {
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_DEV_URANDOM);
		$keysize = mcrypt_enc_get_key_size($td);
		$mkey = substr(hash('sha256',$key),0,$keysize);
		mcrypt_generic_init($td,$mkey,$iv);
		$ret = mcrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return array($ret,$key,$iv);

	}

	/**
	* Decrypts a string with the given key and initialization vector.
	*
	* decrypt() takes $str, and uses $key and $iv to decrypt it (all items that are returned by encrypt()). It uses Rijndael 128 in CBC mode as the encryption algorithm.
	*
	* @return String The decrypted string.
	*/
	public static function decrypt($str, $key, $iv) {
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
		$keysize = mcrypt_enc_get_key_size($td);
		$key = substr(hash('sha256',$key),0,$keysize);
		mcrypt_generic_init($td, $key, $iv);
		$ret = mdecrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return trim($ret);
	}

	/**
	* Gets the method used to log in
	*
	* @return string The auth method used
	*/
	public function get_auth_method() {
		return $this->auth_type;
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
		global $I2_DOMAIN;

		if (!$this->encryption) {
			$_SESSION['i2_password'] = $pass;
			return;
		}
		$_SESSION['i2_auth_passkey'] = substr(md5(rand(0,999999)),0,16);
		list($_SESSION['i2_password'], ,$iv) = self::encrypt($pass,$_SESSION['i2_auth_passkey'].substr(md5($_SERVER['REMOTE_ADDR']),0,16));
		setcookie('IODINE_PASS_VECTOR',$iv,0,'/',$I2_DOMAIN);
	}

	/**
	* Gets a themed login background for special occasions
	*
	* This uses a mysql database of "special" days and backgrounds, and if today is "special", returns the background.
	*
	* @return string The path, relative to the Iodine root, of the background tile image (or null if today is not "special")
	*/
	private static function getSpecialBG() {
		global $I2_SQL, $I2_CACHE;

		$rows = unserialize($I2_CACHE->read(get_class(),'special_backgrounds'));
		if($rows === FALSE) {
			$rows = $I2_SQL->query('SELECT startdt, enddt, background, js FROM special_backgrounds ORDER BY priority DESC')->fetch_all_arrays();
			$I2_CACHE->store(get_class(),'special_backgrounds',serialize($rows));
		}

		$timestamp = time();

		foreach ($rows as $occasion) {
			if (strtotime($occasion['startdt']) < $timestamp && $timestamp < strtotime($occasion['enddt'])) {
				return array('www/pics/logins/special/'.$occasion['background'],'www/js/logins/special/'.$occasion['js']);
			}
		}
	}

	/**
	 * Log the login (attempt)
	 *
	 * @param string $username
	 * @param string $message
	 */
	private static function log_auth($user, $success, $method) {
		global $I2_LOG;

		if ($success) {
			$result = 'success';
		}
		else {
			$result = 'FAILURE';
		}

		$I2_LOG->log_auth(
			'[' . date('d/M/Y:H:i:s O') . '] ' .
			$_SERVER['REMOTE_ADDR'] . ' - ' .
			$result . ' - ' .
			$user . ' -- ' .
			$method
		);
	}

	/**
	 * Get the user's active kerberos realm.
	 * When using multiple realms in the config, this lets afs know
	 * which you want to check against for login.
	 *
	 * $return string Realm name, or FALSE on failure.
	 */
	function get_realm() {
		if($this->auth_type!="kerberos")
			return FALSE;
		return $this->auth->get_realm();
	}
}

?>
