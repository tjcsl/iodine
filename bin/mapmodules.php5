#!/usr/bin/php5
<?php
	
define('CONFIG_FILENAME', 'config.ini');

require_once('functions.inc.php5');
require_once('modules/admin/modulesmapper.class.php5');

ModulesMapper::generate();

?>
