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
		//Trim following /s, stops some breakage
		if ($path[strlen($path)-1]=='/')
			$path=substr($path,0,-1);
		
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

	protected function convert_relative_path($path, $must_exist=TRUE) {
		$basename = basename($path);
		
		if ($must_exist == FALSE && $basename != '..' && $basename != '.') {
			$new_path = $this->convert_path(dirname($path)) . '/' . $basename;
			if (!file_exists($new_path)) {
				return $new_path;
			}
		}
	
		$relative_path = $this->root_dir . '/' . $path;
	
		if ($relative_path === FALSE) {
			throw new I2Exception('File ' . $this->root_dir . '/' . $path . ' does not exist');
		}
	
		if ($relative_path == $this->root_dir || fnmatch($this->root_dir. '/*', $relative_path)) {
			return $relative_path;
		} else {
			throw new I2Exception("File $relative_path is outside of user's homedir");
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
	
	public function remove_file($filename) {
		$path = $this->convert_path($filename);
		
		if (unlink($path) === FALSE) {
			throw new I2Exception("Could not delete file $path");
		}
	}

	public function remove_link($filename) {
		$path = $this->convert_relative_path($filename);//$this->root_dir . "/" . $filename;

		if (unlink($path) == FALSE) {
			throw new I2Exception("Could not delete link $path");
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
			throw new I2Exception("Could not open directory $pathname");
		}
	}
	
	public function move_file($oldpath, $newpath) {
		$oldpath = $this->convert_relative_path($oldpath);
		$newpath = $this->convert_relative_path($newpath, FALSE);

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

	public function exists_file($pathname) {
		$path = $this->convert_path($pathname,FALSE);
		return file_exists($path);
	}
	
	public function echo_contents($filename) {
		$path = $this->convert_path($filename);
		readfile($path);
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
		
		if (rmdir($path) == FALSE) {
			throw new I2Exception("Could not remove directory $path");
		}
	}

	public function remove_dir_recursive($pathname) {
		if(is_link($this->root_dir . $pathname)) {
			//remove_dir_recursive *shouldn't* be called on symlinks,
			//but just in case:
			$this->remove_link($pathname);
		}
		else {
			foreach($this->list_files($pathname) as $file) {
				if($file->is_symlink()) {
					$this->remove_link($pathname . "/" . $file->get_name());
				} else if($file->is_directory()) {
					if(count($this->list_files($pathname . "/" .  $file->get_name())) > 0) { //not empty
						$this->remove_dir_recursive($pathname . "/" .  $file->get_name());
					} else { //empty
						$this->remove_dir($pathname . "/" . $file->get_name());
					}
				} else {
					$this->remove_file($pathname . "/" . $file->get_name());
				}
			}
			$this->remove_dir($pathname);
		}
	}

	public function zip_dir($dirpath, $zippath, $origpath=NULL) {
		$dirpath = $this->convert_path($dirpath);
		
		if ($origpath == NULL) {
			$origpath = $dirpath;
		}

		$dir = opendir($dirpath);
		while (FALSE !== ($file = readdir($dir))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			$i2file = new I2File($dirpath, $file);
			if ($i2file->is_directory()) {
				$subdirpath = substr($i2file->get_absolute_path(), strlen($this->root_dir));
				$this->zip_dir($subdirpath, $zippath, $origpath);
			}
			else {
				$descriptors = array(
					0 => array('file', '/dev/null', 'w'), 
					1 => array('file', '/dev/null', 'w'), 
					2 => array('file', '/dev/null', 'w')
				);

				$filepath = $i2file->get_absolute_path();
				$filepath = substr($filepath, strlen($origpath)+1);
				if ($i2file->get_size() < i2config_get('max_zip_filesize', 104857600, 'filecenter')) {
					$process = proc_open("zip $zippath '$filepath'", $descriptors, $pipes, $origpath);
					$code = proc_close($process);

					if ($code !== 0) {
						throw new I2Exception("Zip exited with error code $code");
					}
				}
			}
		}
	}

	public function zip_file($filepath, $zippath) {
		$filepath = $this->convert_path($filepath);
		
		$file = new I2File(dirname($filepath), basename($filepath));
		$max_filesize = i2config_get('max_zip_filesize', 104857600, 'filecenter');
		if ($file->get_size() < $max_filesize) {
			$filepath = escapeshellarg($filepath);
			exec("zip $zippath -j $filepath", $output, $code);
		
			if ($code !== 0) {
				throw new I2Exception("Zip exited with error code $code");
			}
		}
		else {
			$max_filesize_MB = $max_filesize / pow(2,20);
			throw new I2Exception("File too big to zip: over $max_filesize_MB MB!");
		}
	}

	public function is_valid() {
		return TRUE;
	}

	public function can_do($filepath,$action) { // Override this for permissions in each filesystem.
		return TRUE;
	}
}
?>
