<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package core
* @subpackage LANFile
*/

/**
* @package core
* @subpackage LANFile
*/
class LANFile implements I2File {

	private $absolute_path;
	
	public function __construct($path, $file=NULL) {
		if ($file != NULL) {
			$path = $path . '/' . $file;
		}
		
		$this->absolute_path = realpath($path);
		
		if ($this->absolute_path === FALSE) {
			throw new I2Exception("File $path does not exist!");
		}
	}

	/**
	* Required by the {@link I2File} interface.
	*/
	public function get_absolute_path() {
		return $this->absolute_path;
	}
	
	/**
	* Required by the {@link I2File} interface.
	*/
	public function get_contents() {
		$contents = file_get_contents($this->absolute_path);
		if ($contents === FALSE) {
			throw new I2Exception("Error reading $this->absolute_path");
		}
		return $contents;
	}
	
	/**
	* Required by the {@link I2File} interface.
	*/
	public function get_name() {
		return basename($this->absolute_path);
	}
	
	/**
	* Required by the {@link I2File} interface.
	*/
	public function get_parent() {
		return dirname($this->absolute_path);
	}
	
	/**
	* Required by the {@link I2File} interface.
	*/
	public function get_size() {
		return filesize($this->absolute_path);
	}
	
	/**
	* Required by the {@link I2File} interface.
	*/
	public function is_directory() {
		return is_dir($this->absolute_path);
	}
	
	/**
	* Required by the {@link I2File} interface.
	*/
	public function is_file() {
		return is_file($this->absolute_path);
	}
	
	/**
	* Required by the {@link I2File} interface.
	*/
	public function last_modified() {
		return filemtime($this->absolute_path);
	}

}

?>
