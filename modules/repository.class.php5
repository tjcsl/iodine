<?php
/**
*
*/

/**
*
*/
interface Repository {
	public function __construct($root);
	public function list_files($path);
	public function summary($file);
	public function is_dir($file);
}
?>
