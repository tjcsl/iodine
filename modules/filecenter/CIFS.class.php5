<?php
/**
* @author The Intranet 2 Development Team <intranet@tjhsst.edu>
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
* @filesource
* Implements {@link Filesystem} for windows shares
*/

/**
* @package modules
* @subpackage Filecenter
* Implements {@link Filesystem} for windows shares
*/
class CIFS extends Filesystem {

	protected $root_dir;
	private $share;
	private $server;
	private $domain;
	
	/**
	 * The CIFS constructor.
	 *
	 * Mounts any CIFS server.
	 *
	 * @param string $server The CIFS server to connect to.
	 * @param string $user The username to pass to the server for authentication.
	 * @param string $pass The password to pass to the server for authentication.
	 * @param string $domain The authentication domain to use, if using Windows ADS.
	 */
	public function __construct($user, $pass, $share = NULL, $server = NULL, $domain = NULL) {
		global $I2_USER;
		$this->domain = isset($domain) ? $domain : i2config_get('cifs_default_adsdom', NULL, 'filecenter');
		$this->server = isset($server) ? $server : i2config_get('cifs_default_server', '', 'filecenter');
		$this->share = isset($share) ? $share : i2config_get('cifs_default_share', '', 'filecenter');

//		die("<style>.downtime {position: fixed; left: 0; width: 100%; z-index: 999; font-size: 18px; text-align: center; background-color: yellow; border: 1px solid black;}</style><div class='downtime'>LOCAL domain servers, which host the M: and R: drives in addition to other services, are currently down due to renovation.</div>");

		$this->root_dir = i2config_get('cifs_base_dir', '/tmp/cifs/', 'filecenter') . $this->server . "/" . $this->share . "_" . $user;
		d("filecenter using mount point ".$this->root_dir." for CIFS filesystem",5);

		if ( !($this->is_mounted()) ) {
			$this->mount($user, $pass, $this->root_dir);
			$_SESSION["cifs_{$this->server}_{$this->share}_mounted"] = TRUE;
			$_SESSION['logout_funcs'][] = array(
				array('CIFS', 'umount'),
				array($this->root_dir)
			);
		}
	}

	/**
	 * Check if a CIFS share is mounted.
	 *
	 * @access private
	 */
	private function is_mounted() {
		if(file_exists($this->root_dir))
		{
			if(isset($_SESSION["cifs_{$this->server}_{$this->share}_mounted"]))
				return true;
			//return true;
		}
		return false;
	}

	/**
	 * Mount a CIFS share.
	 * 
	 * Uses /sbin/mount.cifs to mount the share.
	 *
	 * @access private
	 * @param string $user The username to pass to the server for authentication.
	 * @param string $pass The password to pass to the server for authentication.
	 * @param string $mount_point The local directory to mount.
	 */
	private function mount($user, $pass, $mount_point) {
		self::umount($mount_point);
		
		d("Mounting //{$this->server}/{$this->share} to $mount_point as $user");

		if (!file_exists($mount_point)) {
			d("Creating mount-point");
			mkdir($mount_point, 0755, TRUE);
		}


		$cifscommand = "sudo /sbin/mount.cifs //{$this->server}/{$this->share} $mount_point";
		$cifscommand .= isset($this->domain) ? " -o domain=\"$this->domain\"" : "";
		$cifscommand .= " -o username=\"$user\"";

		//$status = exec("/sbin/mount.cifs //{$this->server}/{$this->share} $mount_point -o username=\"$user\",password=\"$pass\"");
		$descriptors = array(
			0=>array("pipe","r"),
			1=>array("pipe","w"),
			2=>array("pipe","w"),
		);
		d($cifscommand, 7);

		$pp=proc_open($cifscommand,$descriptors,$pipes);

		if(is_resource($pp)) {
			stream_get_contents($pipes[2], 2);
			fwrite($pipes[0],$pass);
			fclose($pipes[0]);
			$outputstring=stream_get_contents($pipes[1]);
			d("mount.cifs output: ".$outputstring,7);
			fclose($pipes[1]);
			fclose($pipes[2]);
			$retval = proc_close($pp);
			if ($retval != 0) {
				throw new I2Exception("sudo /sbin/mount.cifs exited with status $retval, command was ($cifscommand), Command Output was ($outputstring)");
			}
		} else {
			throw new I2Exception("failed to start /sbin/mount.cifs process");
		}


	}

	/**
	 * Unmount a CIFS share and removes the temporary mount point.
	 * 
	 * Uses /bin/umount to unmount a specified mount point.
	 *
	 * @access public
	 * @param string $mount_point The directory used as a mount point.
	 */
	public static function umount($mount_point) {
		d("Unmounting $mount_point");
		// check if the share is actually mounted
		exec("grep -qs ".$mount_point." /proc/mounts",$out,$status);
		if($status == "0") {
			exec("sudo umount $mount_point",$out,$status);
			d("Unmount status: $status");
		}
		d("Removing mount point $mount_point");
		if(is_dir($mount_point)) {
			$status = exec("rmdir $mount_point");
			d("Mount point removal status: $status");
		}
	}

}

?>
