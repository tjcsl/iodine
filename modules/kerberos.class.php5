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
	public function __construct($user, $password, $realm) {
		$this->cache = self::get_ticket($user, $password, $realm);

		if(!$this->cache) {
			throw new I2Exception("Kerberos login for $user@$realm failed.");
		}

		if(!isset($_SESSION['logout_funcs']) || !is_array($_SESSION['logout_funcs'])) {
			$_SESSION['logout_funcs'] = array();
		}
		$_SESSION['logout_funcs'][] = array($this, 'destroy');
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
	public function destroy() {
		exec('kdestroy -c '.$this->cache);
	}

	/**
	* Checks to see if $password is a valid password for $user, for realm
	* $realm.
	*
	* @param string $user The user to authenticate.
	* @param string $password The user's password to check.
	* @param string $realm The realm to authenticate to.
	* @todo	This function only returns FALSE on failure, and does not give
	*	any indication as to why. Do we want to have something that
	*	does that?
	* @return bool TRUE if the authentication succeeded, FALSE otherwise
	*/
	public static function authenticate($user, $password, $realm=NULL) {
		
		if( $realm === NULL ) {
			$realm = i2config_get('default_realm','LOCAL.TJHSST.EDU','kerberos');
		}
		
		$cache = self::get_ticket($user, $password, $realm);
		if ($cache) {
			exec("kdestroy -c $cache");
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Requests a Kerberos ticket for $user in realm $realm, using password
	* $password.
	*
	* @param string $user The user to authenticate.
	* @param string $password The user's password to check.
	* @param string $realm The realm to authenticate to.
	* @todo	The cache file is generated randomly, but the algorithm to do
	*	that seems like it could be improved.
	* @return mixed	A string representing the absolute path to the cache
	*		file on a successful authentication, FALSE otherwise.
	*/
	public static function get_ticket($user, $password, $realm) {

		// Generates a cache name in the form /tmp/iodine-krb5-<username>-<randomstring>, where <randomstring> is 5 chars long
		$mtime = microtime();
		srand((float)(substr($mtime, 1+strpos($mtime, ' '))));
		$cache = "/tmp/iodine-krb5-$user-".substr(md5(''.rand()),0,5);
	
		$descriptors = array(0 => array('pipe', 'r'), 1 => array('file', '/dev/null', 'w'), 2 => array('file', '/dev/null', 'w'));

		$process = proc_open("kinit $user@$realm -c $cache", $descriptors, $pipes);
		if(is_resource($process)) {
			fwrite($pipes[0], $password);
			fclose($pipes[0]);
			
			$status = proc_close($process);
			
			if($status == 0) {
				return $cache;
			}
	        }
                return FALSE;	
	}
}

?>
