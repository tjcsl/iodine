<?php
/**
* @package core
* @subpackage Database
*/

/**
* @package core
* @subpackage Database
*/
class LDAP {

	const LDAP_SEARCH = 100;
	const LDAP_MODIFY = 200;
	const LDAP_DELETE = 300;
	const LDAP_COMPARE = 400;

	private $dnbase = 'dc=tjhsst,dc=edu';
	private $binds;
	private $conns;
	private $server;
	
	function __construct() {
		global $I2_USER;
		$this->server = i2config_get('server','localhost','ldap');
		$this->binds = array();
		$this->conns = array();
		
	}

	function __destruct() {
		// It doesn't hurt to try to close already closed connections
		foreach ($this->conns as $conn) {
			ldap_close($conn);
		}
	}

	private function connect() {
		$conn = ldap_connect($this->server);
		$this->conns[] = $conn;
		ldap_set_option($conn,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($conn,LDAP_OPT_DEREF,LDAP_DEREF_ALWAYS);
		return $conn;
	}

	public function get_anonymous_bind() {
		if (!isSet($this->binds['__anonymous'])) {
			$this->binds['__anonymous'] = ldap_bind($this->connect());
			d('Connected anonymously to the LDAP server',8);
		} else {
			d('Re-using anonymous LDAP bind',7);
		}
		return $this->binds['__anonymous'];
	}

	public function get_user_bind() {
		global $I2_AUTH;
		if (isSet($this->binds['__user'])) {
			d('Re-using old LDAP user bind',7);
			return $this->binds['__user'];
		}
		// We could use the old krb5 ticket instead of re-authing, but what the hey.
		$ldapuser = 'iodineUid='.$_SESSION['i2_username'].',year=2006,ou=students,ou=people,'.$this->dnbase;
		$uname = $_SESSION['i2_username'];
		$pass = $I2_AUTH->get_user_password();
		$realm = i2config_get('default_realm','LOCAL.TJHSST.EDU','kerberos');
		//$ldapuser = "iodineUid=$uname,cn=$realm,cn=gssapi,cn=auth";
		
		$conn = $this->connect();
		
		//$bind = ldap_bind($this->connect(),$ldapuser,Auth::get_user_password());
		//$bind = ldap_sasl_bind($conn,$ldapuser,$pass,'gssapi',$realm);
		$bind = ldap_bind($conn,$ldapuser,$pass);
		
		if (!$bind) {
			throw new I2Exception("LDAP user bind as $ldapuser failed!");
		}
		d("Bound to LDAP server $this->server successfully as $ldapuser.",8);
		$this->binds['__user'] = $bind;
		return $conn;
	}

	public function search($dn='',$query='objectClass=*',$attributes='*',$conn=NULL) {

	
		if (!$conn) {
			$conn = $this->get_user_bind();
		}
		
		
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		
		//FIXME: we need careful, considered escaping here.
		if (substr($dn,-strlen($this->dnbase)) == $this->dnbase) {
			//We're OK, the dn ends with the dnbase.
		} else if ($dn && $dn != "") {
			$dn = addslashes($dn.','.$this->dnbase);
		} else {
			$dn = $this->dnbase;
		}
		
		if ($query) {
			$query = addslashes($query);
		}

		d("LDAP Searching $dn for ".print_r($attributes,1)." where $query...",7);
		
		//TODO: consider how searching is done
		$res = ldap_search($conn,$dn,$query,$attributes);

		d('LDAP got '.ldap_count_entries($conn,$res).' results.',7);
		
		//ldap_free_result($res);
		//return NULL;
		
		return new LDAPResult($conn,$res,LDAP::LDAP_SEARCH);
	}

	public function search_one($dn='',$query='objectClass=*',$attributes='*',$conn=NULL) {
		if (!$conn) {
			$conn = $this->get_user_bind();
		}

		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}

		if (substr($dn,-strlen($this->dnbase)) == $this->dnbase) {
			//We're OK, the dn ends with the dnbase.
		} else if ($dn && $dn != "") {
			$dn = addslashes($dn.','.$this->dnbase);
		} else {
			$dn = $this->dnbase;
		}

		if ($query) {
			$query = addslashes($query);
		}
		
		d("LDAP Listing $dn for ".print_r($attributes,1)." where $query...",7);
		
		$res = ldap_list($conn,$dn,$query,$attributes);

		d('LDAP got '.ldap_count_entries($conn,$res).' results.',7);
		
		//ldap_free_result($res);
		//return NULL;
		
		return new LDAPResult($conn,$res,LDAP::LDAP_SEARCH);

	}

	public function delete($dn,$bind=NULL) {
		if (substr($dn,-strlen($this->dnbase)) != $this->dnbase) {
			$dn = addslashes($dn.','.$this->dnbase);
		}
		$res = ldap_delete($bind,$dn);
		return new LDAPResult($bind,$res,LDAP::LDAP_DELETE);
	}

	public function modify_val($dn,$attribute_name,$value,$bind=NULL) {
	}

	public function modify_object($dn,$vals,$bind=NULL) {
	}

	public function compare($dn,$attribute,$value,$bind=NULL) {
		if (!$bind) {
			$bind = $this->get_user_bind();
		}
		if (substr($dn,-strlen($this->dnbase)) != $this->dnbase) {
			$dn = addslashes($dn.','.$this->dnbase);
		}
		$attribute = addslashes($attribute);
		$value = addslashes($value);
		$res = ldap_compare($bind,$dn,$attribute,$value);
		if ($res === -1) {
			throw new I2Exception(ldap_error($bind));
		}
		return $res;
	}
	
}

?>
