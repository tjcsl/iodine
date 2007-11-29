<?php
/**
* Just contains the definition for the interface {@link AuthType}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Auth
* @filesource
*/

/**
* The API for all Intranet2 authentication mechanisms to implement
* @package core
* @subpackage Auth
*/
interface AuthType {

	/**
	* Do auth-mechanism-specific login stuff.
	*
	* This will involve some form of checking the username/password pair,
	* but may include more; for instance, Kerberos auth will need to store
	* the name of the credentials cache.
	*
	* @param string $user The username
	* @param string $pass The password
	* @return bool Whether authentication succeeded or not
	*/
	public function login($user, $pass);

	/**
	* Do auth-mechanism-specific stuff that needs to happen on a page load.
	*
	* This may or may not actually involve doing anything. This is here so
	* that Kerberos has the opportunity to tell the environment where the
	* user's ticket is.
	*/
	public function reload();

	/**
	* Get an LDAP bind appropraite to the user.
	*
	* This is because not all ways of authenticating a user will bind to
	* LDAP the same way; Kerberos needs to use GSSAPI, while other stuff
	* needs to do a simple bind.
	*
	* @return LDAP An LDAP object representing the appropriate LDAP bind
	*/
	public function get_ldap_bind();
}
?>
