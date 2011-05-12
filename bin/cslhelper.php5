#!/etc/iodine/php_wrapper
<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
*/

define('CONFIG_FILENAME', 'config.ini.php5');

require_once('functions.inc.php5');

load_module_map();

$realm = $argv[0];

function csl_error($err) {
	fwrite(STDERR, serialize(array('error', $err)));
	exit(1);
}

function csl_exception($err) {
	fwrite(STDERR, serialize(array('exception', $err)));
	exit(1);
}

set_error_handler('csl_error');
set_exception_handler('csl_exception');

list($function, $args) = unserialize(stream_get_contents(STDIN));
if ($realm == 'LOCAL.TJHSST.EDU') {
$filesystem = new CSLFilesystem(FALSE,FALSE,$realm);
} else {
$filesystem = new CSLFilesystem();
}
$ret_val = call_user_func_array(array($filesystem, $function), $args);

fwrite(STDERR, serialize($ret_val));

exit(0);
?>
