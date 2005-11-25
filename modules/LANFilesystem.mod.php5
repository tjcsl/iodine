<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage LANFilesystem
* @filesource
*/

/**
* @package modules
* @subpackage LANFilesystem
*/
class LANFilesystem implements Filesystem {

	//I am not sure if these mapping are correct (esp. "freshmen")
	//TODO: staff
	private static $grade_map = array(
		"12" => "senior",
		"11" => "junior",
		"10" => "sophmore",
		"9" => "freshmen"
	);

	private $homedir;
	
	public function __construct($user, $pass) {
		$this->homedir = i2config_get("novell_base_dir", "/tmp/novell", "filecenter") . $user;
		
		if (!(file_exists($this->homedir) && isset($_SESSION['novell_mounted']))) {
			$this->mount($user, $pass);
			$_SESSION['novell_mounted'] = TRUE;
			$_SESSION['logout_funcs'][] = array($this, 'unmount');
		}
	}

	private function mount($user, $pass) {
		global $I2_USER;

		$server = i2config_get("novell_server", "TECHNOLOGY", "filecenter");
		$volume = self::$grade_map[$I2_USER->grade] . '/students/' . $user;

		d("Mounting $volume@$server to $this->homedir as $user");
		
		if (!file_exists($this->homedir)) {
			d("Creating mount-point");
			mkdir($this->homedir, 0755);
		}
		
		$this->unmount();

		$descriptors = array(
			0 => array('pipe', 'r'), 
			1 => array('file', '/dev/null', 'w'),
			2 => array('file', '/dev/null', 'w')
			//1 => array('pipe', 'w'),
			//2 => array('pipe', 'w')
		);

		//TODO: figure out why ncpmount thinks it won't be given a password when run from php. Perhaps its because stdin is not tty?
		$process = proc_open("ncpmount -S $server -A $server -V $volume -U $user $this->homedir -P $pass", $descriptors, $pipes);
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
	
	public function unmount() {
		d("Unmounting $this->homedir");

		$status = exec("ncpumount $this->homedir");

		d("Unmount status: $status");
	}

	//TODO: don't display names files outside of user's homedir (even in exceptions)
	private function convert_path($path, $must_exist=TRUE) {
		$basename = basename($path);
		
		if ($must_exist == FALSE && $basename != '..' && $basename != '.') {
			return $this->convert_path(dirname($path)) . '/' . $basename;
		}

		$absolute_path = realpath($this->homedir . '/' . $path);

		if ($absolute_path === FALSE) {
			throw new I2Exception('File ' . $this->homedir . '/' . $path . 'does not exist');
		}

		if ($absolute_path == $this->homedir || fnmatch($this->homedir. '/*', $absolute_path)) {
			return $absolute_path;
		} else {
			throw new I2Exception("File $absolute_path is outside of user's homedir");
		}
	}

	/**
	* Required by the {@link Filesystem} interface.
	*/
	function create_file($filename, $contents) {
		$path = $this->convert_path($filename, FALSE);

		if (file_exists($path)) {
			throw new I2Exception("File $path already exists");
		}

		if (file_put_contents($path, $contents) === FALSE) {
			throw new I2Exception("Could not write to $path");
		}
	}
	
	/**
	* Required by the {@link Filesystem} interface.
	*/
	function delete_file($filename) {
		$path = $this->convert_path($filename);
		
		if (unlink($path) === FALSE) {
			throw new I2Exception("Could not delete file $path");
		}
	}
	
	/**
	* Required by the {@link Filesystem} interface.
	*/
	function list_files($pathname) {
		$path = $this->convert_path($pathname);
		
		if ($handle = opendir($path)) {
			$files = array();
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..") {
					continue;
				}

				$files[] = new LANFile($path, $file);
			}
			return $files;

		} else {
			throw new I2Exception("Could not open directory $path");
		}
	}
	
	/**
	* Required by the {@link Filesystem} interface.
	*/
	function move_file($oldpath, $newpath) {
		$oldpath = $this->convert_path($oldpath);
		$newpath = $this->convert_path($newpath, FALSE);
		
		if (rename($oldpath, $newpath) === FALSE) {
			throw new I2Exception("Could not rename $oldpath to $newpath");
		}
	}
	
	/**
	* Required by the {@link Filesystem} interface.
	*/
	function get_file($pathname) {
		$path = $this->convert_path($pathname);
		return new LANFile($path);
	}

	/**
	* Required by the {@link Filesystem} interface.
	*/
	function is_root($pathname) {
		return ($this->convert_path($pathname) == $this->homedir);
	}


	/**
	* Required by the {@link Filesystem} interface.
	*/
	function make_dir($pathname) {
		$path = $this->convert_path($pathname, FALSE);

		if (mkdir($path) === FALSE) {
			throw new I2Exception("Could not make directory $path");
		}
	}

	/**
	* Required by the {@link Filesystem} interface.
	*/
	function remove_dir($pathname) {
		$path = $this->convert_path($pathname);
		
		if (rmdir($path) === FALSE) {
			throw new I2Exception("Could not remove directory $path");
		}
	}

}

?>
