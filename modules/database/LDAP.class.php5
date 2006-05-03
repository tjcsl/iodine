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

	private $dnbase;
	private $bind;
	private $conn;
	private $server;
	private $sizelimit;
	private $timelimit;
	
	private $conns = array();
	
	function __construct($dn=NULL,$pass=NULL,$server=NULL,$simple=TRUE,$proxydn='') {
		global $I2_USER, $I2_ERR, $I2_AUTH;
		if ($server !== NULL) {
			$this->server = $server;
		} else {
			$this->server = i2config_get('server','localhost','ldap');
		}
		$this->dnbase = i2config_get('base_dn','dc=tjhsst,dc=edu','ldap');
		$this->sizelimit = i2config_get('max_rows',500,'ldap');
		$this->timelimit = i2config_get('max_time',0,'ldap');
		
		$this->rebase($dn);
		
		d("Connecting to LDAP server {$this->server}...",8);
		$this->conn = $this->connect();
		if (!$simple) {
			/*
			** GSSAPI bind - ignores $dn and $pass!
			*/
			//$_ENV['KRB5CCNAME'] = $I2_AUTH->cache();
			//putenv("KRB5CCNAME={$_ENV['KRB5CCNAME']}");
			d('KRB5CCNAME for LDAP bind is '.$_ENV['KRB5CCNAME'],8);
			$this->bind = ldap_sasl_bind($this->conn);

			/*
			** This is what stuff would look like for a proxy bind (w/GSSAPI)... But PHP ldap_sasl_bind is badly broken...
			*/
			//$this->bind = ldap_sasl_bind($this->conn,'','','GSSAPI',i2config_get('sasl_realm','CSL.TJHSST.EDU','ldap'),$proxydn);
			
			d("Bound to LDAP via GSSAPI",8);
		} elseif ($dn !== NULL && $pass !== NULL) {
			/*
			** Simple bind
			*/
			$this->bind = ldap_bind($this->conn,$dn,$pass);
			d("Bound to LDAP simply as $dn",8);
		} else {
			/*
			** Anonymous bind
			*/
			$this->bind = ldap_bind($this->conn);
			d('Bound to LDAP anonymously',8);
		}
		/*
		** These errors are nonfatal so they don't bring down the whole application beyond any hope of a fix.
		*/
		if (!$this->conn) {
			$I2_ERR->nonfatal_error('Unable to connect to LDAP server!');
		}
		if (!$this->bind) {
			$I2_ERR->nonfatal_error('Unable to bind to LDAP server!');
		}
	}

	function __destruct() {
		/*
		** Close all LDAP connections made by this module instance
		*/
		foreach ($this->conns as $conn) {
			ldap_close($conn);
		}
	}

	private function connect() {
		$conn = ldap_connect($this->server);
		ldap_set_option($conn,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($conn,LDAP_OPT_DEREF,LDAP_DEREF_ALWAYS);
		$this->conns[] = $conn;
		return $conn;
	}

	public static function get_anonymous_bind() {
		return new LDAP();
	}

	public static function get_user_bind($server = NULL) {
		//All values ignored
		return new LDAP('','pwd',$server,FALSE);
	}

	/**
	* Gets an administrative simple bind.
	*/
	public static function get_admin_bind($pass=NULL) {
		if ($pass === NULL) {
			$pass = i2config_get('admin_pw','ld4pp4ss','ldap');
		}
		return self::get_simple_bind(i2config_get('admin_dn','cn=Manager,dc=tjhsst,dc=edu','ldap'),$pass);
	}

	public static function get_simple_bind($userdn,$pass,$server=NULL) {
		return new LDAP($userdn,$pass,$server);	
	}

	public function search_base($dn=NULL,$attributes='*',$bind=NULL) {
		return $this->search($dn,'objectClass=*',$attributes,LDAP::SCOPE_BASE,$bind);
	}

	public function search_sub($dn=NULL,$query='objectClass=*',$attributes='*',$bind=NULL) {
		return $this->search($dn,$query,$attributes,LDAP::SCOPE_SUB,$bind);
	}

	public function search($dn=NULL,$query='objectClass=*',$attributes='*',$depth=LDAP::SCOPE_SUB,$bind=NULL) {
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		
		$this->rebase($dn);

		if (!$bind) {
			$bind = $this->conn;
		}
			
		if ($query) {
			$query = addslashes($query);
		} else {
			$query = 'objectClass=*';
		}

		$res = null;
	
		try {
	
		//TODO: consider how searching is done
			if ($depth == LDAP::SCOPE_SUB) {
				d("LDAP Searching $dn for ".print_r($attributes,1)." where $query...",7);
				$res = ldap_search($bind,$dn,$query,$attributes,0,$this->sizelimit,$this->timelimit);
			} elseif ($depth == LDAP::SCOPE_ONE) {
				d("LDAP Listing $dn for ".print_r($attributes,1)." where $query...",7);
				$res = ldap_list($bind,$dn,$query,$attributes,0,$this->sizelimit,$this->timelimit);
			} elseif ($depth == LDAP::SCOPE_BASE) {
				d("LDAP Reading $dn's values for ".print_r($attributes,1)." where $query...",7);
				$res = ldap_read($bind,$dn,$query,$attributes,0,$this->sizelimit,$this->timelimit);
			} else {
				throw new I2Exception("Unknown scope number $depth passed to ldap_search!");
			}

		} catch (Exception $e) {
			d("LDAP error: $e",5);
		}

		//d('LDAP got '.ldap_count_entries($bind,$res).' results.',7);
		
		//ldap_free_result($res);
		//return NULL;

		if (!$res) {
			return LDAPResult::get_null();
		}
		
		return new LDAPResult($bind,$res,LDAP::LDAP_SEARCH);
	}

	public function search_one($dn='',$query='objectClass=*',$attributes='*') {
		return $this->search($dn,$query,$attributes,LDAP::SCOPE_ONE);
	}

	/**
	* Adds the base dn if necessary, and escapes special characters
	*
	* @param string $dn The DN to fix
	*/
	private function rebase(&$dn) {
		if (!$dn || $dn === '') {
			$dn = $this->dnbase;
		}
		//FIXME: consider better escaping - this won't always work correctly.
		if (substr($dn,-strlen($this->dnbase)) != $this->dnbase) {
			$dn = addslashes($dn.','.$this->dnbase);
		} else {
			$dn = addslashes($dn);
		}
	}

	public function delete($dn,$bind=NULL) {
		$this->rebase($dn);
		if (!$bind) {
			$bind = $this->conn;
		}
		d("LDAP deleting $dn...",7);
		$res = ldap_delete($bind,$dn);
		return new LDAPResult($bind,$res,LDAP::LDAP_DELETE);
	}

	/**
	* Recursively delete a node and all its children
	*
	* @todo Fix listing of already-deleted objects - this method gets called twice
	*       for each node b/c search_one returns the object itself.
	*/
	public function delete_recursive($dn,$bind=NULL) {
		$this->rebase($dn);
		if (!$bind) {
			$bind = $this->conn;
		}
		/*
		** Find all objects below the given DN and delete each one
		*/
		$res = $this->search_one($dn,FALSE,array('*'),$bind)->fetch_all_arrays(RESULT::ASSOC);
		foreach ($res as $itemdn=>$meh) {
			//d("Deleting dn $itemdn from LDAP recursive delete",6);
			$this->delete_recursive($itemdn,$bind);
		}
		$this->delete($dn,$bind);
	}

	public function modify_val($dn,$attribute_name,$value,$bind=NULL) {
		return $this->modify_object($dn,array($attribute_name=>$value),$bind);
	}

	public function modify_object($dn,$vals,$bind=NULL) {
		if (!$vals) {
			d("Null LDAP modification made to dn $dn",5);
			return TRUE;
		}
		if (!is_array($vals)) {
			throw new I2Exception("Non-array \"$vals\" passed to LDAP modify_object method!");
		}
		$this->rebase($dn);
		if (!$bind) {
			$bind = $this->conn;
		}
		
		d("LDAP modifying $dn: ".print_r($vals,TRUE),7);
		return ldap_modify($bind,$dn,$vals);
	}

	public function compare($dn,$attribute,$value,$bind=NULL) {
		$this->rebase($dn);
		//FIXME: better escaping
		$attribute = addslashes($attribute);
		$value = addslashes($value);
		if (!$bind) {
			$bind = $this->conn;
		}
		//TODO: return LDAPResult
		$res = ldap_compare($bind,$dn,$attribute,$value);
		if ($res === -1) {
			throw new I2Exception(ldap_error($bind));
		}
		return $res;
	}

	public function add($dn,$values,$bind=NULL) {
		$this->rebase($dn);
		if (!$bind) {
			$bind = $this->conn;
		}
		if (!$values) {
			throw new I2Exception("Attempted to create null LDAP object with dn $dn");
		}
		if (!is_array($values)) {
			throw new I2Exception("Cannot create LDAP object $dn with non-array \"$values\"");
		}
		$newvalues = array();
		/*
		** Filter out empty-string and null values
		*/
		foreach ($values as $key=>$value) {
			if ($value && $value != "") {
				$newvalues[$key] = $value;
			}
		}
		d("LDAP adding dn $dn: ".print_r($newvalues,TRUE),7);
		return ldap_add($bind,$dn,$newvalues);
	}
	
}

?>
