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
class Kerberos implements AuthType {
	private $cache;
	private $realms = array();
	private $realm;

	public function __construct($realm=NULL) {
		$this->realms[] = $realm;
		if ($this->realms[0] == NULL) {
			$this->realms = explode(",",i2config_get('default_realm','LOCAL.TJHSST.EDU','kerberos'));
		}
		$this->cache = NULL;
	}

	/**
	* The login method required by the {@link AuthType} interface
	*/
	public function login($user, $pass, $realm=NULL) {
		if ($realm === NULL) {
			foreach($this->realms as $realm) {
				$this->cache = self::get_ticket($user, $pass, $realm);
		
				if(!$this->cache) {
					continue;
				}
				else {
					$this->realm=$realm;
					$_SESSION['logout_funcs'][] = array(
									array('Kerberos', 'destroy'),
									array($this->cache)
								);
					return TRUE;
				}
			}
			return FALSE;
		}

		$this->cache = self::get_ticket($user, $pass, $realm);

		if(!$this->cache) {
			return FALSE;
		}
		else {
			$this->realm=$realm;
			$_SESSION['logout_funcs'][] = array(
							array('Kerberos', 'destroy'),
							array($this->cache)
						);
			return TRUE;
		}
	}

	/**
	* The reload method required by the {@link AuthType} interface
	*/
	public function reload() {
		$cache = $this->cache;
		d("Setting KRB5CCNAME to $cache",8);
		putenv("KRB5CCNAME=$cache");
		$_ENV['KRB5CCNAME'] = $cache;
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
	* The ldap-getting method required by the (@link AuthType) interface
	*
	* @return LDAP An LDAP object representing a GSSAPI bind.
	*/
	public function get_ldap_bind() {
		// Get a GSSAPI bind
		return LDAP::get_gssapi_bind();
	}

	/**
	* Destroys the Kerberos tokens associated with this Kerberos object.
	*/
	public static function destroy($cache) {
		exec('kdestroy -c '.$cache);
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
	private static function get_ticket($user, $password, $realm) {
		// Generates a cache name in the form /tmp/iodine-krb5-<randomstring>, where <randomstring> is 16 chars long
		$cache = tempname('/tmp/iodine-krb5-');

		$descriptors = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));

		$env = array('KRB5CCNAME' => $cache);

		$user = escapeshellcmd(strtolower($user));

		$process = proc_open("/usr/bin/kinit $user@$realm", $descriptors, $pipes, NULL, $env);
		if(is_resource($process)) {
			fwrite($pipes[0], "$password\n");
			fclose($pipes[0]);

			$output = fread($pipes[1], 1024);
			fclose($pipes[1]);
			$output2 = fread($pipes[2], 1024);
			fclose($pipes[2]);
			
			$status = proc_close($process);

			// If kinit didn't create the cache file don't run kgetcred
			if(!file_exists($cache))
				return FALSE;
			exec('export KRB5CCNAME='.$cache.';/usr/bin/kgetcred ldap/iodine.tjhsst.edu@CSL.TJHSST.EDU');
			
			if($status == 0) {
				d("Kerberos authorized $user@$realm",8);
				return $cache;
			}
	   }
	return FALSE;
	}

	/**
	 * Gets the active realm for this object, or false otherwise.
	 *
	 * @return string The realm of the object, or FALSE.
	 */
	function get_realm() {
		if(!isset($this->realm))
			return FALSE;
		return $this->realm;
	}
}

?>
