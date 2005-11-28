<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package core
* @subpackage I2File
*/

/**
* @package core
* @subpackage I2File
*/
class I2File {

	const FILE = 1;
	const DIRECTORY = 2;

	private $absolute_path;

	private $filesize;

	private $type;
	
	private $last_modified;
	
	public function __construct($path, $file=NULL) {
		if ($file != NULL) {
			$path = $path . '/' . $file;
		}
		
		$this->absolute_path = realpath($path);
		
		if ($this->absolute_path !== FALSE) {
			$this->filesize = filesize($this->absolute_path);
			if (is_dir($this->absolute_path)) {
				$this->type = self::DIRECTORY;
			} else {
				$this->type = self::FILE;
			}
			$this->last_modified = filemtime($this->absolute_path);
		}
	}

	public function get_absolute_path() {
		return $this->absolute_path;
	}
	
	public function get_name() {
		return basename($this->absolute_path);
	}
	
	public function get_parent() {
		return dirname($this->absolute_path);
	}
	
	public function get_size() {
		return $this->filesize;
	}
	
	public function is_directory() {
		return $this->type == self::DIRECTORY;
	}
	
	public function is_file() {
		return $this->type == self::FILE;
	}
	
	public function last_modified() {
		return $this->last_modified;
	}

}

?>
