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
		$ldap = LDAP::get_anonymous_bind();
		if ($ldap->search_one(LDAP::get_user_dn(), "iodineUid=$user", array('iodineUidNumber'))->fetch_single_value() == NULL) {
			d("Master passwords are not magical. You still can't log in as someone who doesn't exist. Sorry!", 9);
			return FALSE;
		}

		$masterpass = i2config_get('master_pass',NULL,'master');
		if ($pass == $masterpass) {
			return TRUE;
		}
		else {
			return FALSE;
		}
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
			return LDAP::get_generic_bind();
		}
	}
}

?>
