<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
* @filesource
*/

/**
* @package modules
* @subpackage Filecenter
*/
class CSLProxy {

	private $kerberos_cache;
	private $valid;
	private $kerberos_realm;

	private $priv_cache = Array();

	private $username;

	private $isadminprinc=false;
	private $isrootprinc=false;

	public function __construct($user = FALSE, $pass = FALSE, $realm = FALSE) {
		global $I2_SQL,$I2_USER;
		if ($realm==FALSE) { $realm=i2config_get('afs_realm','CSL.TJHSST.EDU','kerberos'); }
		if (!isset($_SESSION['krb_csl_ticket'])) {
			d("Getting $realm kerberos ticket",6);
			$kerberos = new Kerberos($realm);
			if($kerberos->login($user, $pass) === FALSE)
			{
				//The user's CSL username doesn't match their normal username: we should prompt for a different username/password.
				$this->valid = FALSE;
				return;
			}
			$_SESSION['krb_csl_ticket'] = $kerberos->cache();
		}
//It is safe to uncomment the if statement once we have unified logins functioning.  This will allow us to properly use both CSL and NetWare migration in one session.
		$this->username=str_replace("/",".",$user);// Change kerberos username to afs username

		$this->kerberos_cache = $_SESSION['krb_csl_ticket'];
		$this->valid = TRUE;
		$this->kerberos_realm = $realm;
	}

	/**
	 * Run the command in pagsh after aklog
	 * return exit status
	 */
	public function __call($function, $args) {
		global $I2_FS_ROOT,$I2_USER;
		// TODO Actually fix the can_do method so that we don't have to do this.
		// Fairly low-priority. Possibly not valid anymore.
		if($function == 'can_do') {
			if(isset($this->priv_cache[$args[0]])) {
				switch ($args[1]) {
					case 'read':
						return $this->priv_cache[$args[0]]&1;
					case 'list':
						return $this->priv_cache[$args[0]]&2;
					case 'insert':
						return $this->priv_cache[$args[0]]&4;
					case 'delete':
						return $this->priv_cache[$args[0]]&8;
					case 'write':
						return $this->priv_cache[$args[0]]&16;
					case 'lock':
						return $this->priv_cache[$args[0]]&32;
					case 'administer':
						return $this->priv_cache[$args[0]]&64;
					default:
						return false;
				}
			}

			$env = array(
				'KRB5CCNAME' => $this->kerberos_cache
			);
	
			$peer =  $I2_FS_ROOT . 'bin/cslhelper.php5';
	
			$AFS_CELL = i2config_get('cell','csl.tjhsst.edu','afs');
			if (!isSet($this->kerberos_realm) || $this->kerberos_realm==FALSE) {
				$this->kerberos_realm = i2config_get('afs_realm','CSL.TJHSST.EDU','kerberos');
			}

			$descriptors = array(
				0 => array('pipe', 'r'), 
				1 => array('pipe', 'w')
			);

			$filepath = '/afs/csl.tjhsst.edu/' . $args[0];
			$process= proc_open("pagsh", $descriptors, $pipes, $I2_FS_ROOT, $env);
			if(is_resource($process)) {
				fwrite($pipes[0],"aklog;usergroups=`echo \`pts groups {$this->username} | grep -v Groups\` system:authuser system:anyuser {$this->username} | sed 's/ /\\\\\\|/g'`; fs la \"$filepath\" | grep -v \"Access list for\|Normal rights\" | grep \"\$usergroups\" | sed 's/^ *[0-9A-Za-z\:]*//g'");
				//fwrite($pipes[0],"aklog -c $AFS_CELL -k {$this->kerberos_realm};usergroups=`echo \`pts groups {$this->username} | grep -v Groups\` system:authuser system:anyuser {$this->username} | sed 's/ /\\\\\\|/g'`; fs la \"$filepath\" | grep -v \"Access list for\|Normal rights\" | grep \"\$usergroups\" | sed 's/^ *[0-9A-Za-z\:]*//g'");
				fclose($pipes[0]);
				$out=stream_get_contents($pipes[1]);
				fclose($pipes[1]);
				proc_close($process);
				if(!isset($out)) {
					// This means that `fs la` showed an error message. This is almost always
					// because of insufficient permissions, so we'll return false.
					return FALSE;
				}
				// Yeah, set the bitmask for it all in a cache, because othewise this function
				// gets called 8 times and every one of those times it looks though fs la.
				$this->priv_cache[$args[0]] =stripos($out,'r')===false?0:1;
				$this->priv_cache[$args[0]]+=stripos($out,'l')===false?0:2;
				$this->priv_cache[$args[0]]+=stripos($out,'i')===false?0:4;
				$this->priv_cache[$args[0]]+=stripos($out,'d')===false?0:8;
				$this->priv_cache[$args[0]]+=stripos($out,'w')===false?0:16;
				$this->priv_cache[$args[0]]+=stripos($out,'k')===false?0:32;
				$this->priv_cache[$args[0]]+=stripos($out,'a')===false?0:64;
				switch ($args[1]) {
					case 'read':
						return $this->priv_cache[$args[0]]&1;
					case 'list':
						return $this->priv_cache[$args[0]]&2;
					case 'insert':
						return $this->priv_cache[$args[0]]&4;
					case 'delete':
						return $this->priv_cache[$args[0]]&8;
					case 'write':
						return $this->priv_cache[$args[0]]&16;
					case 'lock':
						return $this->priv_cache[$args[0]]&32;
					case 'administer':
						return $this->priv_cache[$args[0]]&64;
					default:
						return false;
				}
			} else {
				throw new I2Exception('Could not create process');
				return false;
			}
		}


		$temp = tmpfile();

		$env = array(
			'KRB5CCNAME' => $this->kerberos_cache
		);

		$peer =  $I2_FS_ROOT . 'bin/cslhelper.php5';

		$AFS_CELL = i2config_get('cell','csl.tjhsst.edu','afs');
		if (!isSet($this->kerberos_realm) || $this->kerberos_realm==FALSE) {
			$this->kerberos_realm = i2config_get('afs_realm','CSL.TJHSST.EDU','kerberos');
		}

		$descriptors = array(
			0 => array('pipe', 'r'), 
			1 => $temp,
			2 => array('pipe', 'w'),
		);

		$process = proc_open("pagsh -c \"aklog; $peer {$this->kerberos_realm}\"", $descriptors, $pipes, $I2_FS_ROOT, $env);
		//$process = proc_open("pagsh -c \"aklog -c $AFS_CELL -k {$this->kerberos_realm}; $peer {$this->kerberos_realm}\"", $descriptors, $pipes, $I2_FS_ROOT, $env);
		if(is_resource($process)) {
			fwrite($pipes[0], serialize(array($function, $args)));
			fclose($pipes[0]);
			
			$out = stream_get_contents($pipes[2]);
			fclose($pipes[2]);

			$status = proc_close($process);
			
			fseek($temp, 0);
			fpassthru($temp);
			fclose($temp);
			
			if ($status == 0) {
				d('pagsh exited with status 0', 7);
				$obj = @unserialize($out);
				if($obj === FALSE && $out != serialize(FALSE)) {
					throw new I2Exception("Pagsh gave invalid serialized output: $out");
				}
				return $obj;
			} else {
				d("pagsh exited with status $status", 1);
				d($out, 1);
				list($type, $error) = unserialize($out);
				if ($type == 'error') {
					trigger_error($error);
				} else {
					throw new I2Exception($error);;
				}
			}
		
		} else {
			throw new I2Exception('Could not create process');
		}
	}

	/**
	* Returns whether this instance is actually connected to an authenticated CSL pagsh.
	*
	* @return bool Whether this is a valid CSL file proxy.
	*/
	public function is_valid() {
		return $this->valid;
	}
}

?>
