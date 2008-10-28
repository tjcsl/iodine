<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
*/

/**
* @package modules
* @subpackage Filecenter
*/
class I2File {

	const FILE      = 1;
	const DIRECTORY = 2;
	const LINKFILE  = 3;
	const LINKDIR   = 4;

	private $absolute_path;
	private $relative_path;

	private $filesize;

	private $type;
	
	private $last_modified;
	
	public function __construct($path, $file=NULL) {
		if ($file != NULL) {
			$path = $path . '/' . $file;
		}
		
		$this->absolute_path = realpath($path);
		$this->relative_path = $path;
		
		if ($this->absolute_path !== FALSE) {
			//TODO even though these use the relative path, they return the
			//filesize and modify_date of the linked file. Why?
			$this->filesize = filesize($this->relative_path);
			$this->last_modified = filemtime($this->relative_path);

			if (is_link($this->relative_path)) {
				if (is_dir($this->absolute_path)) {
					$this->type = self::LINKDIR;
				} else {
					$this->type = self::LINKFILE;
				}
			} else if (is_dir($this->absolute_path)) {
				$this->type = self::DIRECTORY;
			} else {
				$this->type = self::FILE;
			}
		}
	}

	public function get_absolute_path() {
		return $this->absolute_path;
	}
	
	public function get_name() {
		return basename($this->relative_path);
	}
	
	public function get_parent() {
		return dirname($this->absolute_path);
	}
	
	public function get_size() {
		return $this->filesize;
	}
	
	public function is_directory() {
		return $this->type == self::DIRECTORY or $this->type == self::LINKDIR;
	}

	public function is_symlink() {
		return $this->type == self::LINKFILE or $this->type == self::LINKDIR;
	}
	
	public function is_file() {
		return $this->type == self::FILE;
	}

	public function is_hidden() {
		return strpos($this->get_name(), '.') === 0;
	}
	
	public function last_modified() {
		return $this->last_modified;
	}

}

?>
