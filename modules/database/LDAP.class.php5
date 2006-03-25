<?php
/**
* Just contains the definition for the class {@link LDAP}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @package core
* @subpackage Database
* @filesource
*/

/**
* A class for interfacing with the Lightweight Directory Access Protocol.
* @package core
* @subpackage Database
*/
class LDAP {

	const LDAP_SEARCH = 100;
	const LDAP_MODIFY = 200;
	const LDAP_DELETE = 300;
	const LDAP_COMPARE = 400;

	const SCOPE_SUB = 1;
	const SCOPE_BASE = 2;
	const SCOPE_ONE = 3;

	private $dnbase = 'dc=tjhsst,dc=edu';
	private $bind;
	private $conn;
	private $server;
	
	function __construct($dn=NULL,$pass=NULL,$server=NULL) {
		global $I2_USER;
		if ($server !== NULL) {
			$this->server = $server;
		} else {
			$this->server = i2config_get('server','localhost','ldap');
		}
		d("Connecting to LDAP server {$this->server}...",8);
		$this->conn = $this->connect();
		if ($dn !== NULL && $pass !== NULL) {
			$this->bind = ldap_bind($this->conn,$dn,$pass);
			d("Bound to LDAP as $dn",8);
		} else {
			$this->bind = ldap_bind($this->conn);
			d('Bound to LDAP anonymously',8);
		}
		//$this->bind = ldap_sasl_bind($this->conn,$dn,'','GSSAPI',i2config_get('default_realm','TJHSST.EDU','kerberos'));
		//$this->bind = ldap_sasl_bind($this->conn,'','','GSSAPI');
		/*if (ldap_error($this->conn)) {
			throw new I2Exception(ldap_error($this->conn));
		}*/
		if (!$this->bind) {
			throw new I2Exception('Unable to bind to LDAP server!');
		}
	}

	function __destruct() {
		ldap_close($this->conn);
	}

	private function connect() {
		$conn = ldap_connect($this->server);
		ldap_set_option($conn,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($conn,LDAP_OPT_DEREF,LDAP_DEREF_ALWAYS);
		return $conn;
	}

	public static function get_anonymous_bind() {
		return new LDAP();
	}

	public static function get_user_bind() {
		global $I2_AUTH;
		// We could use the old krb5 ticket instead of re-authing, but what the hey.
		//FIXME: use Kerberos ticket for GSSAPI SASL bind - no password or username should be needed.
		$ldapuser = 'iodineUid='.$_SESSION['i2_username'].',ou=students,ou=people,'.$this->dnbase;
		$pass = $I2_AUTH->get_user_password();
		
		return new LDAP($ldapuser,$pass);
	}

	public function search_base($dn='',$attributes='*') {
		$this->search($dn,'objectClass=*',$attributes,LDAP::SCOPE_BASE);
	}

	public function search($dn='',$query='objectClass=*',$attributes='*',$depth=LDAP::SCOPE_SUB) {
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		
		//FIXME: we need careful, considered escaping here.
		if (substr($dn,-1*strlen($this->dnbase)) == $this->dnbase) {
			//We're OK, the dn ends with the dnbase.
		} else if ($dn && $dn != '') {
			$dn = addslashes($dn.','.$this->dnbase);
		} else {
			$dn = $this->dnbase;
		}
		
		if ($query) {
			$query = addslashes($query);
		} else {
			$query = 'objectClass=*';
		}

		$res = null;

		//TODO: consider how searching is done
		if ($depth == LDAP::SCOPE_SUB) {
			d("LDAP Searching $dn for ".print_r($attributes,1)." where $query...",7);
			$res = ldap_search($this->conn,$dn,$query,$attributes);
		} elseif ($depth == LDAP::SCOPE_ONE) {
			d("LDAP Listing $dn for ".print_r($attributes,1)." where $query...",7);
			$res = ldap_list($this->conn,$dn,$query,$attributes);
		} elseif ($depth == LDAP::SCOPE_BASE) {
			d("LDAP Reading $dn's values for ".print_r($attributes,1)." where $query...",7);
			$res = ldap_read($this->conn,$dn,$query,$attributes);
		} else {
			throw new I2Exception("Unknown scope number $depth passed to ldap_search!");
		}

		d('LDAP got '.ldap_count_entries($this->conn,$res).' results.',7);
		
		//ldap_free_result($res);
		//return NULL;
		
		return new LDAPResult($this->conn,$res,LDAP::LDAP_SEARCH);
	}

	public function search_one($dn='',$query='objectClass=*',$attributes='*') {
		$this->search($dn,$query,$attributes,LDAP::SCOPE_ONE);
	}

	public function delete($dn) {
		if (substr($dn,-strlen($this->dnbase)) != $this->dnbase) {
			$dn = addslashes($dn.','.$this->dnbase);
		}
		$res = ldap_delete($this->bind,$dn);
		return new LDAPResult($this->bind,$res,LDAP::LDAP_DELETE);
	}

	public function modify_val($dn,$attribute_name,$value,$bind=NULL) {
	}

	public function modify_object($dn,$vals,$bind=NULL) {
	}

	public function compare($dn,$attribute,$value) {
		if (substr($dn,-strlen($this->dnbase)) != $this->dnbase) {
			$dn = addslashes($dn.','.$this->dnbase);
		}
		$attribute = addslashes($attribute);
		$value = addslashes($value);
		$res = ldap_compare($this->bind,$dn,$attribute,$value);
		if ($res === -1) {
			throw new I2Exception(ldap_error($this->bind));
		}
		return $res;
	}
	
}

?>
