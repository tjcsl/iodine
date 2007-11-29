<?php
/**
* Just contains the definition for the class {@link Simple}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @package core
* @subpackage Auth
* @filesource
*/

/**
* The module that handles simple LDAP auth.
* @package core
* @subpackage Auth
*/
class Simple implements AuthType {

	private $dn;

	/**
	* The login method required by the {@link AuthType} interface
	*/
	public function login($user, $pass) {
		/*
		** LDAP will through an error if binding fails... 
		** if it doesn't, the password was correct!
		*/
		try {
			$authdn = 'iodineUid='.$user.','.LDAP::get_user_dn();
			LDAP::get_simple_bind($authdn,$pass);
			$this->dn = $authdn;
			return TRUE;
		}
		catch (I2Exception $e) {
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
		global $I2_AUTH;

		$pw = $I2_AUTH->get_user_password();
		return LDAP::get_simple_bind($this->dn, $pw);
	}
}

?>
