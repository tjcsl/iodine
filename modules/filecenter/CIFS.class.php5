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

		$this->root_dir = i2config_get('cifs_base_dir', '/tmp/cifs/', 'filecenter') . $this->server . "_" . $this->share . "_" . $user;

		if (!(file_exists($this->root_dir) && isset($_SESSION["cifs_{$server}_{$share}_mounted"]))) {
			$this->mount($user, $pass, $this->root_dir);
			$_SESSION["cifs_{$server}_{$share}_mounted"] = TRUE;
			$_SESSION['logout_funcs'][] = array(
				array('CIFS', 'umount'),
				array($this->root_dir)
			);
		}
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
		$pass = escapeshellarg($pass);

		$status = exec("/sbin/mount.cifs //{$this->server}/{$this->share} $mount_point -o username=\"$user\",password=$pass");

		d("Mount status: $status");
		
		if ($status != 0) {
			throw new I2Exception("/sbin/mount.cifs exited with status $status");
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
		exec("/sbin/umount.cifs $mount_point",$out,$status);
		d("Unmount status: $status");

		if($status == "0") {
			d("Removing mount point $mount_point");
			$status = exec("rmdir $mount_point");
			d("Mount point removal status: $status");
		}
	}

}

?>
