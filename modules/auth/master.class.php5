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
* The module that handles authentication with the master password.
* @package core
* @subpackage Auth
*/
class Master implements AuthType {

	/**
	* The login method required by the {@link AuthType} interface
	*/
	public function login($user, $pass) {
		$master_pass = i2config_get('master_pass',NULL,'master');
		if ($master_pass !== NULL && $pass == $master_pass) {
			try {
				$ldap = LDAP::get_generic_bind();
			} catch (Exception $e) {
				d("Login with master password failed. Check if LDAP and authuser are configured correctly.", 1);
				return FALSE;
			}
			if ($ldap->search_one(LDAP::get_user_dn(), "iodineUid=$user", array('iodineUidNumber'))->fetch_single_value() == NULL) {
				d("You can't log in as someone who doesn't exist.", 1);
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	* The reload method required by the {@link AuthType} interface
	*/
	public function reload() {
	}

	/**
	* The ldap-getting method required by the (@link AuthType) interface
	*
	* @return LDAP An LDAP object representing a simple bind (as manager,
	* 	if possible)
	*/
	public function get_ldap_bind() {
		if (i2config_get('can_bind_manager',0,'ldap')) {
			$manager_pw = i2config_get('admin_pw',NULL,'ldap');
			return LDAP::get_admin_bind($manager_pw);
		}
		else {
			return  LDAP::get_generic_bind();
		}
	}
}

?>
