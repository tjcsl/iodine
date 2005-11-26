<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage CSLProxy
* @filesource
*/

/**
* @package modules
* @subpackage CSLProxy
*/
class CSLProxy {

	private $kerberos_cache;

	public function __construct($user, $pass) {
		if (!isset($_SESSION['krb_csl_ticket'])) {
			d("Getting kerberos ticket");
			$kerberos = new Kerberos($user, $pass, 'CSL.TJHSST.EDU');
			$_SESSION['krb_csl_ticket'] = $kerberos->cache();
		}
		$this->kerberos_cache = $_SESSION['krb_csl_ticket'];
	}

	/**
	 * Run the command in pagsh after aklog
	 * return exit status
	 */
	public function __call($function, $args) {
		$descriptors = array(
			0 => array('pipe', 'r'), 
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$env = array(
			'KRB5CCNAME' => $this->kerberos_cache
		);

		$root_path = i2config_get('root_path', NULL, 'core');
		$peer =  $root_path . 'bin/cslhelper.php5';
		
		$process = proc_open('pagsh -c "aklog; ' . $peer . '"', $descriptors, $pipes, $root_path, $env);
		if(is_resource($process)) {
			fwrite($pipes[0], serialize(array($function, $args)));
			fclose($pipes[0]);
			
			$out = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			
			$err = stream_get_contents($pipes[2]);
			fclose($pipes[2]);
			
			$status = proc_close($process);
			
			if ($status == 0) {
				d("pagsh xited with status 0");
				$obj = unserialize($out);
				return $obj;
			} else {
				d("pagsh exited with status $status");
				throw (object)unserialize($err);
			}
		
		} else {
			throw new I2Exception("Could not create process");
		}
	}
}

?>
