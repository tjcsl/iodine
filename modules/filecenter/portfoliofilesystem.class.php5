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
class PortfolioFilesystem extends Filesystem {

	protected $root_dir;
	
	/**
	 * The PortfolioFilesystem constructor.
	 *
	 * Mounts the samba server for portfolios.
	 *
	 * @param string $user The username to pass to the server for authentication.
	 * @param string $pass The password to pass to the server for authentication.
	 */
	public function __construct($user, $pass) {
		global $I2_USER;
		$this->root_dir = i2config_get("samba_base_dir", "/tmp/samba/", "filecenter") . $user;

		if (!(file_exists($this->root_dir) && isset($_SESSION['samba_mounted']))) {
			self::smbmount($user, $pass, $this->root_dir);
			$_SESSION['samba_mounted'] = TRUE;
			$_SESSION['logout_funcs'][] = array(
				array('PortfolioFilesystem', 'smbumount'),
				array($this->root_dir)
			);
		}
	}

	/**
	 * Mount a samba filesystem.
	 * 
	 * Uses smbmount to mount the portfolio directory.
	 *
	 * @access private
	 * @param string $user The username to pass to the server for authentication.
	 * @param string $pass The password to pass to the server for authentication.
	 * @param string $mount_point The local directory to mount.
	 */
	private static function smbmount($user, $pass, $mount_point) {
		$server = i2config_get("samba_server", "tj03.local", "filecenter");
		$sharename = i2config_get("samba_sharename", "portfolio", "filecenter");

		d("Mounting //$server/$sharename to $mount_point as $user");
		
		if (!file_exists($mount_point)) {
			d("Creating mount-point");
			mkdir($mount_point, 0755);
		}
		
		self::smbumount($mount_point);

		$status = exec("smbmount //$server/$sharename $mount_point -o username=$user,password=$pass");

		d("Mount status: $status");
		
		if ($status != 0) {
			throw new I2Exception("smbmount exited with status $status");
		}

	}

	/**
	 * Unmount a samba server
	 * 
	 * Uses smbumount to unmount a specified mount point.
	 *
	 * @access public
	 * @param string $mount_point The directory used as a mount point.
	 */
	public static function smbumount($mount_point) {
		d("Unmounting $mount_point");

		$status = exec("smbumount $mount_point");

		d("Unmount status: $status");
	}

}

?>
