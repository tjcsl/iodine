<?php
/**
* Global utility functions for Iodine. Only general utility functions
* which do not belong in any other specific module belong here.

* @author The Intranet2 Development Team <intranet2@lists.tjhsst.edu>
* @copyright 2004-2005 The Intranet2 Development Team
* @version $Id: functions.inc.php5,v 1.17 2005/07/15 06:34:49 adeason Exp $
* @package core
* @filesource
*/

/**
* A quick function call for debugging.
*
* This function is basically just a wrapper for $I2_LOG->log_debug().
* This function is far more convenient, however, since you don't need
* to declare $I2_LOG as a global, and the function name is only one
* keystroke. What a timesaver!
*
* @param string $text The text to display.
* @param int $level The debugging level at which to log this message.
*                   (The higher the level, the less important, default
*                   is 9, the highest standard level.)
*/
function d($text, $level = 9) {
	global $I2_LOG;
	if (isSet($I2_LOG)) {
		$I2_LOG->log_debug($text, $level);
	}
}

/**
* The __autoload function, used for autoloading modules.
*
* This is the function used by PHP as a last resort for loading 
* noninstantiated classes before it throws an error. It checks for
* readability of the module (if one exists) in the module path.
* Throws an exception if it does not exist.
* 
* @param string $class_name Name of noninstantiated class.
*/
function __autoload($class_name) {
	global $I2_ERR;
	d("Loading $class_name");
	$class_file = '';

	if (!($class_file=get_i2module($class_name))) {
		$I2_ERR->fatal_error('Cannot load module/class '.$class_name.': the file '.$class_file.' is not readable.');
	}
	else {
		require_once($class_file);
	}
}

/**
* Determines whether a certain class name is an instantiatable I2 module.
*
* This checks whether the class name is already instantiated, and if
* not, then checks to see if an I2 module file is available and
* readable. Unlike __autoload, this only returns FALSE if a module does
* not exist, as opposed to raising an exception.
*
* @param String $module_name The name of the module.
* @return string The file name of the module to load, if it exists; false if it doesn't.
*/
function get_i2module($module_name) {
	/* Do not run autoload, since it will throw an exception if the
	class does not exist */
	$prepath = i2config_get('module_path', NULL, 'core');
	
	$file = $prepath.strtolower($module_name).'.mod.php5';
	if (is_readable($file)) {
		return $file;
	}
	$file = $prepath.$module_name.'.mod.php5';
	if (is_readable($file)) {
		return $file;
	}
	$file = $prepath.strtolower($module_name).'.class.php5';
	if (is_readable($file)) {
		return $file;
	}
	$file = $prepath.$module_name.'.class.php5';
	if (is_readable($file)) {
		return $file;
	}
	return FALSE;
}

/**
* Gets a configuration value from the global Iodine configuration
*
* This parses the config file specified by the constant CONFIG_FILENAME.
* The file is parsed only once, and kept in a static variable for the
* rest of the duration of the script. The section argument is optional;
* if you do not supply it, i2config_get will try to guess it for you,
* based on the class you are calling it from.
*
* @param String $field The name of the config value you want to get.
* @param mixed $default The default value to be returned if the specified key is not found.
* @param String $section The name of the config section you want to retrieve the value from.
* @return mixed The config value for the specified field, the default value if one was passed and if the specified key was not found, NULL otherwise.
*/
function i2config_get($field, $default = NULL, $section = NULL) {
	global $I2_ERR;
	static $config = NULL;
	
	if ($config === NULL) { /*Parse the INI file*/
	
		if (!is_readable(CONFIG_FILENAME)) {
			/* This is a critical error, but do not set the
			critical error flag, because the email address
			to send critical errors to is in the config
			file! */
			/* hence, put a hard-coded mail() call here */
			$I2_ERR->fatal_error('The master Iodine configuration file '.CONFIG_FILENAME.' cannot be read.', FALSE);
		}
		
		$config = parse_ini_file(CONFIG_FILENAME, TRUE);
	}

	if ($section != NULL) {
		if (isset($config[$section][$field])) {
			return $config[$section][$field];
		}
		if ($default === NULL) {
			/* Return error, should probably also make a logging call here */
			return NULL;
		}
		return $default;
		
	}
	
	/* If a section was not specified, try to guess it from getting
	the class name of what called us. */
	
	$trace = debug_backtrace();

	/* If we were called by a class, use the config section
	for that class. */
	if (isSet($trace[1]['class'])) {
		return i2config_get($field, $default, strtolower($trace[1]['class']));
	}
	
	/* If we were not called by a class, default to core, since
	if we were called by core, it would not report a class. */

	return i2config_get($field, $default, 'core');
}

/**
* Issues an HTTP redirect to the user.
*
* Redirects a user to the specified URL via an HTTP Location: header.
* Do _not_ use this function if output as already been sent to the
* user. If you attempt to do so, an exception will be thrown.
*
* @param string $url The url to redirect to (relative to Iodine root).
*                    If this is NULL or not passed, then the user will
*                    be redirected to Iodine root.
*/
function redirect($url = NULL) {
	
	if( headers_sent($file, $line) ) {
		throw new I2Exception('A redirect was attempted, but headers have already been sent in file '.$file.' on line '.$line);
	}
	
	$url = i2config_get('www_root', 'https://iodine.tjhsst.edu/', 'core') . $url;
	d('Redirecting to '.$url);

	header('Location: '.$url);
	die();

}

/**
* Flatten an array. (Non-recursive, one level deep.)
*
* @param Array $arr The array to flatten.
* @return Array The flattened array.
*/
function flatten($arr) {
	$ret = array();
	foreach($arr as $item) {
		if( is_array($item) ) {
			$ret += $item;
		}
		else {
			$ret[] = $item;
		}
	}
}

?>
