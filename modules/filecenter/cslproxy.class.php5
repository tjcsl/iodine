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

	public function __construct($user = FALSE, $pass = FALSE) {
		global $I2_SQL,$I2_USER;
		if (!$user && !$pass) {
			d('Using existing kerberos ticket for AFS',6);
			$this->kerberos_cache = $_ENV['KRB5CCNAME'];
			$this->valid = TRUE;
		}
		if (!isset($_SESSION['krb_csl_ticket'])) {
			d('Getting CSL kerberos ticket',6);
			/*$res = $I2_SQL->query("SELECT user,pass FROM cslfiles WHERE uid=%d",$I2_USER->uid);
			if ($res->more_rows) {
				$row = $res->fetch_row(RESULT_ASSOC);
				$user = $row['user'];
				$pass = $row['pass'];
			}*/
			try {
				$kerberos = new Kerberos($user, $pass, i2config_get('afs_realm','CSL.TJHSST.EDU','kerberos'));
			} catch (I2Exception $e) {
				//The user's CSL username doesn't match their normal username: we should prompt for a different username/password.
				$this->valid = FALSE;
				return;
			}
			$_SESSION['krb_csl_ticket'] = $kerberos->cache();
		}
		$this->kerberos_cache = $_SESSION['krb_csl_ticket'];
		$this->valid = TRUE;
	}

	/**
	 * Sets the Kerberos realm for which to run aklog
	 */
	public function set_realm($realm) {
		$this->kerberos_realm = $realm;
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
		if (!isSet($this->kerberos_realm) {
			$this->kerberos_realm = i2config_get('afs_realm','CSL.TJHSST.EDU','kerberos');
		}

		$process = proc_open("pagsh -c \"aklog -c $AFS_CELL -k {$this->kerberos_realm}; $peer\"", $descriptors, $pipes, $root_path, $env);
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
