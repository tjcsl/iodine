<?php
/**
* @package core
*/

/**
* @package core
*/
class LDAP {

	const LDAP_SEARCH = 100;
	const LDAP_MODIFY = 200;
	const LDAP_DELETE = 300;
	const LDAP_COMPARE = 400;

	private $conn = null;
	private $dnbase = 'dc=tjhsst,dc=edu';
	
	function __construct() {
		global $I2_USER;
		$server = i2config_get('server','localhost','ldap');
		$this->conn = ldap_connect($server);
		ldap_set_option($this->conn,LDAP_OPT_PROTOCOL_VERSION,3);
		// We could use the old krb5 ticket instead of re-authing, but what the hey.
		if (isSet($_SESSION['i2_username'])) {
			$ldapuser = 'uid='.$_SESSION['i2_username'].',ou=people,'.$this->dnbase;
			$bind = ldap_bind($this->conn,$ldapuser,$_SESSION['i2_password']);
			if (!$bind) {
				d("LDAP bind failed!",2);
				ldap_bind($this->conn);
			} else {
				d("Bound to LDAP server $server successfully as $ldapuser.",8);
			}
		} else {
			ldap_bind($this->conn);
		}
	}

	function __destruct() {
		ldap_close($this->conn);
	}

	public function search($dn,$query,$attributes) {

	
		//FIXME: we need careful, considered escaping here.
		if ($dn && $dn != "") {
			$dn = addslashes($dn.','.$this->dnbase);
		} else {
			$dn = $this->dnbase;
		}
		
		if ($query) {
			$query = addslashes($query);
		}

		d("LDAP Searching $dn for ".print_r($attributes,1)." where $query...",7);
		
		//TODO: consider how searching is done
		$res = ldap_search($this->conn,$dn,$query,$attributes);//,0,0,0,LDAP_DEREF_SEARCH);

		d('LDAP got '.ldap_count_entries($this->conn,$res).' results.',7);
		
		//ldap_free_result($res);
		//return NULL;
		
		return new LDAPResult($this->conn,$res,LDAP::LDAP_SEARCH);
	}

	public function delete($dn) {
		$dn = addslashes($dn.','.$this->dnbase);
		$res = ldap_delete($this->conn,$dn);
		return new LDAPResult($this->conn,$res,LDAP::LDAP_DELETE);
	}

	public function modify_val($dn,$attribute_name,$value) {
	}

	public function modify_object($dn,$vals) {
	}

	public function compare($dn,$attribute,$value) {
		$dn = addslashes($dn.','.$this->dnbase);
		$attribute = addslashes($attribute);
		$value = addslashes($value);
		$res = ldap_compare($this->conn,$dn,$attribute,$value);
		if ($res === -1) {
			throw new I2Exception(ldap_error($this->conn));
		}
		return $res;
	}
	
}

?>
