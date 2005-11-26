#!/usr/bin/php
<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
*/
require_once('modules/i2exception.class.php5');
require_once('modules/i2file.class.php5');
require_once('modules/filesystem.class.php5');
require_once('modules/cslfilesystem.class.php5');

try {
	list($function, $args) = unserialize(stream_get_contents(STDIN));
	$filesystem = new CSLFilesystem();
	$ret_val = call_user_func_array(array($filesystem, $function), $args);
	fwrite(STDOUT, serialize($ret_val));
	exit(0);
} catch (Exception $e) {
	fwrite(STDERR, serialize($e));
	exit(1);
}

?>
