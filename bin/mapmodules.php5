#!/etc/iodine/php_wrapper
<?php
/**
* A script to generate the map mapping modules to filenames.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Module
* @filesource
*/
	
define('CONFIG_FILENAME', 'config.ini');

require_once('functions.inc.php5');
require_once('modules/admin/modulesmapper.class.php5');

ModulesMapper::generate();

?>
