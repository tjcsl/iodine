<?php

class Data {

	private $ldap;

	function __construct() {
		global $I2_ERR;
		$this->ldap = ldap_connect("localhost");
		if (!ldap_sasl_bind($this->ldap)) {
			$I2_ERR->call_error("LDAP binding failed!");
		}
	}

	function get_user_info($token,$uid) {
		global $I2_ERR;
		if (!check_token_rights($token,'ldap/iodine','r')) {
			$I2_ERR->call_error("Bad authentication token accessing LDAP information for user $uid");
			return;
		}
		$ret = array();
		
		$res = ldap_search($this->ldap,'ou=users,dc=iodine,dc=tjhsst,dc=edu',"(objectClass=iodineUser,uid=$uid)");
		if (!$res) {
			return false;
		}
		
		$ret = ldap_get_attributes($ldap,$res);
		
		return $ret;
	}


}

?>
