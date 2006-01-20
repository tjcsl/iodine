#!/usr/local/bin/php
<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
*/
require_once('modules/i2exception.class.php5');
require_once('modules/i2file.class.php5');
require_once('modules/filesystem.class.php5');
require_once('modules/cslfilesystem.class.php5');

function error($err) {
	fwrite(STDERR, serialize(array('error', $err)));
	exit(1);
}

function exception($err) {
	fwrite(STDERR, serialize(array('exception', $err)));
	exit(1);
}

set_error_handler('error');
set_exception_handler('exception');

list($function, $args) = unserialize(stream_get_contents(STDIN));

$filesystem = new CSLFilesystem();
$ret_val = call_user_func_array(array($filesystem, $function), $args);

fwrite(STDERR, serialize($ret_val));

exit(0);
?>
