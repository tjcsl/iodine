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
class LANFilesystem extends Filesystem {

	//I am not sure if these mapping are correct (esp. "freshmen")
	//TODO: staff
	private static $grade_map = array(
		"12" => "senior",
		"11" => "junior",
		"10" => "sophmore",
		"9" => "freshmen"
	);

	protected $root_dir;
	
	public function __construct($user, $pass) {
		global $I2_USER;
		$this->root_dir = i2config_get("novell_base_dir", "/tmp/novell/", "filecenter") . $user;
		
		if (!(file_exists($this->root_dir) && isset($_SESSION['novell_mounted']))) {
			$volume = self::$grade_map[$I2_USER->grade] . '/students/' . $user;
			self::ncpmount($user, $pass, $volume, $this->root_dir);
			$_SESSION['novell_mounted'] = TRUE;
			$_SESSION['logout_funcs'][] = array(
				array('LANFilesystem', 'ncpumount'),
				array($this->root_dir)
			);
		}
	}

	private static function ncpmount($user, $pass, $volume, $mount_point) {
		$server = i2config_get("novell_server", "TECHNOLOGY", "filecenter");

		d("Mounting $volume@$server to $mount_point as $user");
		
		if (!file_exists($mount_point)) {
			d("Creating mount-point");
			mkdir($mount_point, 0755, TRUE);
		}
		
		self::ncpumount($mount_point);

		$descriptors = array(
			0 => array('pipe', 'r'), 
			1 => array('file', '/dev/null', 'w'),
			2 => array('file', '/dev/null', 'w')
			//1 => array('pipe', 'w'),
			//2 => array('pipe', 'w')
		);

		//TODO: figure out why ncpmount thinks it won't be given a password when run from php. Perhaps its because stdin is not tty?
		//CONFIRMED: by wyang: ncpmount only waits on stdin if tty exists (does same if you do ssh iodine ncpmount -S ......)
		//so maybe we should use ncpmount script that uses expect from original intranet (called stdinmnt or something)
		$process = proc_open("ncpmount -S $server -A $server -V $volume -U $user $mount_point -P $pass", $descriptors, $pipes);
		if(is_resource($process)) {
			/*
			fwrite($pipes[0], $pass);
			fclose($pipes[0]);
			
			d('out: ' . stream_get_contents($pipes[1]));
			fclose($pipes[1]);
			
			d('err: '. stream_get_contents($pipes[2]));
			fclose($pipes[2]);
			*/
			
			$status = proc_close($process);
			d("Mount status: $status");
			
			if ($status != 0) {
				throw new I2Exception("ncpmount exited with status $status");
			}
		
		} else {
			throw new I2Exception("Could not run ncpmount!");
		}

	}
	
	public static function ncpumount($mount_point) {
		d("Unmounting $mount_point");

		$status = exec("ncpumount $mount_point");

		d("Unmount status: $status");
	}

}

?>
