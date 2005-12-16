<?php
/**
* Just contains the definition for the class {@link Kerberos}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @package core
* @subpackage Auth
* @filesource
*/

/**
* The Kerberos module for Iodine, handles authenticating to Kerberos Realms.
* @package core
* @subpackage Auth
*/
class Kerberos {
	private $cache;
	/**
	* The Kerberos class constructor. Throws an I2Exception on failed login.
	*/
	public function __construct($user, $password, $realm=NULL) {
		if( $realm === NULL ) {
			$realm = i2config_get('default_realm','LOCAL.TJHSST.EDU','kerberos');
		}

		$this->cache = self::get_ticket($user, $password, $realm);

		if(!$this->cache) {
			throw new I2Exception("Kerberos login for $user@$realm failed.");
		}

		$_SESSION['logout_funcs'][] = array(
						array('Kerberos', 'destroy'),
						array($this->cache)
					);
	}

	/**
	* Returns the path to the current Kerberos cache.
	*
	* @return string The path to the cache.
	*/
	public function cache() {
		return $this->cache;
	}

	/**
	* Destroys the Kerberos tokens associated with this Kerberos object.
	*/
	public static function destroy($cache) {
		exec('kdestroy -c '.$cache);
	}

	/**
	* Checks to see if $password is a valid password for $user, for realm
	* $realm.
	*
	* @param string $user The user to authenticate.
	* @param string $password The user's password to check.
	* @param string $realm The realm to authenticate to.
	* @return bool TRUE if the authentication succeeded, FALSE otherwise
	*/
	public static function authenticate($user, $password, $realm) {
		try {
			$creds = new Kerberos($user, $password, $realm);
		} catch (I2Exception $e) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	* Requests a Kerberos ticket for $user in realm $realm, using password
	* $password.
	*
	* @param string $user The user to authenticate.
	* @param string $password The user's password to check.
	* @param string $realm The realm to authenticate to.
	* @return mixed	A string representing the absolute path to the cache
	*		file on a successful authentication, FALSE otherwise.
	*/
	public static function get_ticket($user, $password, $realm) {
		// Generates a cache name in the form /tmp/iodine-krb5-<randomstring>, where <randomstring> is 16 chars long
		$cache = tempname('/tmp/iodine-krb5-');

		$descriptors = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));

		$env = array('KRB5CCNAME' => $cache);

		$process = proc_open("/usr/bin/kinit $user@$realm", $descriptors, $pipes, NULL, $env);
		if(is_resource($process)) {
			fwrite($pipes[0], "$password\n");
			fclose($pipes[0]);

			$output = fread($pipes[1], 1024);
			fclose($pipes[1]);
			$output2 = fread($pipes[2], 1024);
			fclose($pipes[2]);
			
			$status = proc_close($process);
			
			if($status == 0) {
				return $cache;
			}
			d('Kerberos return: '.$status.', output: '.$output.', output2: '.$output2);
	        }
                return FALSE;	
	}
}

?>
