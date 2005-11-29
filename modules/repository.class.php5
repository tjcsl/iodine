<?php
/**
* Code for the {@link Repository} interface.
* @package modules
* @subpackage scm
*/

/**
* An interface for viewing SCM repositories.
* @package modules
* @subpackage scm
*/
interface Repository {
	public function __construct($root);
	public function list_files($path);
	public function summary($file);
	public function is_dir($file);
}
?>
