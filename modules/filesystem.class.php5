<?php
/**
* Just contains the definition for the abstract class {@link Filesystem}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Filecenter
* @filesource
*/

/**
* The Filesystem abstract class.
* @package modules
* @subpackage Filecenter
*/
abstract class Filesystem {

	//DEFINE ME!
	protected $root_dir;

	protected function convert_path($path, $must_exist=TRUE) {
		$basename = basename($path);
		
		if ($must_exist == FALSE && $basename != '..' && $basename != '.') {
			$new_path = $this->convert_path(dirname($path)) . '/' . $basename;
			if (!file_exists($new_path)) {
				return $new_path;
			}
		}
	
		$absolute_path = realpath($this->root_dir . '/' . $path);
	
		if ($absolute_path === FALSE) {
			throw new I2Exception('File ' . $this->root_dir . '/' . $path . ' does not exist');
		}
	
		if ($absolute_path == $this->root_dir || fnmatch($this->root_dir. '/*', $absolute_path)) {
			return $absolute_path;
		} else {
			throw new I2Exception("File $absolute_path is outside of user's homedir");
		}
	}

	public function copy_file_into_system($oldfilename, $newfilename) {
		$oldpath = realpath($oldfilename);
		$newpath = $this->convert_path($newfilename, FALSE);
		
		if ($oldpath === FALSE) {
			throw new I2Exception("$oldfilename does not exist.");
		}

		if (copy($oldpath, $newpath) === FALSE) {
			throw new I2Exception("Could not copy $oldpath to $newpath in filesystem.");
		}
	}

	public function create_file($filename, $contents) {
		$path = $this->convert_path($filename, FALSE);
	
		if (file_exists($path)) {
			throw new I2Exception("File $path already exists");
		}
	
		if (file_put_contents($path, $contents) === FALSE) {
			throw new I2Exception("Could not write to $path");
		}
	}
	
	public function delete_file($filename) {
		$path = $this->convert_path($filename);
		
		if (unlink($path) === FALSE) {
			throw new I2Exception("Could not delete file $path");
		}
	}
	
	public function list_files($pathname) {
		$path = $this->convert_path($pathname);
		
		if ($handle = opendir($path)) {
			$files = array();
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..") {
					continue;
				}
				$i2file = new I2File($path, $file);
				
				if ($i2file->get_name() != "") {
					$files[] = $i2file;
				}
			}
			return $files;
	
		} else {
			throw new I2Exception("Could not open directory $path");
		}
	}
	
	public function move_file($oldpath, $newpath) {
		$oldpath = $this->convert_path($oldpath);
		$newpath = $this->convert_path($newpath, FALSE);

		if (file_exists($newpath)) {
			throw new I2Exception("File $newpath already exists");
		}

		if (rename($oldpath, $newpath) === FALSE) {
			throw new I2Exception("Could not rename $oldpath to $newpath");
		}
	}
	
	public function get_file($pathname) {
		$path = $this->convert_path($pathname);
		return new I2File($path);
	}	
	
	public function get_file_contents($filename) {
		$path = $this->convert_path($filename);
		$contents = file_get_contents($path);
		if ($contents === FALSE) {
			throw new I2Exception("Error reading $path");
		}
		return $contents;
	}

	public function is_root($pathname) {
		return ($this->convert_path($pathname) == $this->root_dir);
	}


	public function make_dir($pathname) {
		$path = $this->convert_path($pathname, FALSE);

		if (mkdir($path) === FALSE) {
			throw new I2Exception("Could not make directory $path");
		}
	}

	public function remove_dir($pathname) {
		$path = $this->convert_path($pathname);
		
		if (rmdir($path) === FALSE) {
			throw new I2Exception("Could not remove directory $path");
		}
	}

	//TODO: prevent zipping HUGE files!
	public function zip_dir($dirpath, $zippath) {
		$dirpath = $this->convert_path($dirpath);
		$descriptors = array(
			0 => array('file', '/dev/null', 'w'), 
			1 => array('file', '/dev/null', 'w'), 
			2 => array('file', '/dev/null', 'w')
		);

		$process = proc_open("zip $zippath -r .", $descriptors, $pipes, $dirpath);
		$code = proc_close($process);

		if ($code !== 0) {
			throw new I2Exception("Zip exited with error code $code");
		}
	}

	public function zip_file($filepath, $zippath) {
		$filepath = escapeshellarg($this->convert_path($filepath));
		
		exec("zip $zippath -j $filepath", $output, $code);
	
		if ($code !== 0) {
			throw new I2Exception("Zip exited with error code $code");
		}
	}

	public function is_valid() {
		return TRUE;
	}

}
?>
