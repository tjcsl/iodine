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
interface I2File {
	
	public function get_absolute_path();
	
	public function get_contents();
	
	public function get_name();
	
	public function get_parent();
	
	public function get_size();

	public function is_directory();
	
	public function is_file();
	
	public function last_modified();
	
}

?>
