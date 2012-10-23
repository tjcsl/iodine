<?php
/**
* @author The Intranet 2 Development Team <intranet@tjhsst.edu>
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
* @filesource
*/

/**
* @package modules
* @subpackage Filecenter
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

		$this->root_dir = i2config_get('cifs_base_dir', '/tmp/cifs', 'filecenter') . $this->server . "/" . $this->share;
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

		$user = isset($this->domain) ? $this->domain . "/" . $user : $user;

		//$status = exec("/sbin/mount.cifs //{$this->server}/{$this->share} $mount_point -o username=\"$user\",password=\"$pass\"");
		$descriptors = array(
			0=>array("pipe","r"),
			1=>array("pipe","w"),
			2=>array("file","/tmp/i2cifserr.log","a")
		);
		$pp=proc_open("/sbin/mount.cifs //{$this->server}/{$this->share} $mount_point -o username=\"$user\"", $descriptors,$pipes);
		if(is_resource($pp)) {
			fwrite($pipes[0],$pass);
			fclose($pipes[0]);
			d("mount.ciffs output: ".stream_get_contents($pipes[1]),7);
			fclose($pipes[1]);
			$retval = proc_close($pp);
			if ($retval == -1) {
				throw new I2Exception("/sbin/mount.cifs exited with status $retval, root-dir: $this->root_dir");
			}
		} else {
			throw new I2Exception("falied to start /sbin/mount.cifs process");
		}


	}

	/**
	 * Unmount a CIFS share and removes the temporary mount point.
	 * 
	 * Uses /sbin/umount.cifs to unmount a specified mount point.
	 *
	 * @access public
	 * @param string $mount_point The directory used as a mount point.
	 */
	public static function umount($mount_point) {
		d("Unmounting $mount_point");
		exec("/usr/bin/umount.cifs $mount_point",$out,$status);
		d("Unmount status: $status");

		if($status == "0") {
			d("Removing mount point $mount_point");
			$status = exec("rmdir $mount_point");
			d("Mount point removal status: $status");
		}
	}

}

?>
