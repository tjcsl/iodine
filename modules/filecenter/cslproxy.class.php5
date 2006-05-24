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

	public function __construct($user = FALSE, $pass = FALSE, $realm = FALSE) {
		global $I2_SQL,$I2_USER;
		if ($realm==FALSE) { $realm=i2config_get("afs_realm","CSL.TJHSST.EDU","kerberos"); }
//		if (!isset($_SESSION['krb_csl_ticket'])) {
			d("Getting $realm kerberos ticket",6);
			try {
				$kerberos = new Kerberos($user, $pass, $realm);
			} catch (I2Exception $e) {
				//The user's CSL username doesn't match their normal username: we should prompt for a different username/password.
				$this->valid = FALSE;
				return;
			}
			$_SESSION['krb_csl_ticket'] = $kerberos->cache();
//		}
//It is safe to uncomment the if statement once we have unified logins functioning.  This will allow us to properly use both CSL and NetWare migration in one session.
		$this->kerberos_cache = $_SESSION['krb_csl_ticket'];
		$this->valid = TRUE;
	}

	/**
	 * Run the command in pagsh after aklog
	 * return exit status
	 */
	public function __call($function, $args) {
		$temp = tmpfile();

		$descriptors = array(
			0 => array('pipe', 'r'), 
			1 => $temp,
			2 => array('pipe', 'w')
		);

		$env = array(
			'KRB5CCNAME' => $this->kerberos_cache
		);

		$root_path = i2config_get('root_path', NULL, 'core');
		$peer =  $root_path . 'bin/cslhelper.php5';

		$AFS_CELL = i2config_get('cell','csl.tjhsst.edu','afs');
		if (!isSet($this->kerberos_realm)) {
			$this->kerberos_realm = i2config_get('afs_realm','CSL.TJHSST.EDU','kerberos');
		}

		$process = proc_open("pagsh -c \"aklog -c $AFS_CELL -k {$this->kerberos_realm}; $peer {$this->kerberos_realm}\"", $descriptors, $pipes, $root_path, $env);
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
					throw (object)$error;
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
