<?php
/**
* Just contains the definition for the interface {@link Filesystem}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Filesystem
* @filesource
*/

/**
* The Filesystem interface
* @package core
* @subpackage Filesystem
*/
interface Filesystem {

	function create_file($filename, $contents);
	
	function delete_file($filename);
	
	function list_files($pathname);
	
	function move_file($oldpath, $newpath);
	
	function get_file($pathname);

	function is_root($pathname);

	function make_dir($pathname);
	
	function remove_dir($pathname);

}
?>
