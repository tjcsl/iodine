<?php
	/**
	* Global utility functions for Iodine. Only general utility functions
	* which do not belong in any other specific module belong here.

	* @author The Intranet2 Development Team <intranet2@lists.tjhsst.edu>
	* @copyright 2004-2005 The Intranet2 Development Team
	* @version 1.0
	* @since 1.0
	* @package core
	*/

	/**
	* This is only temporary
	*/
	define('MODULE_PATH', './');
	
	/**
	* The __autoload function, used for autoloading modules.
	*
	* This is the function used by PHP as a last resort for loading 
	* noninstantiated classes before it throws an error. It checks for
	* readability of the module (if one exists) in the {@link MODULE_PATH}.
	* Throws an exception if it does not exist.
	* 
	* @param string $class_name Name of noninstantiated class.
	*/
	function __autoload($class_name) {
		$class_file = i2config_get('module_path', 'core').strtolower($class_name).'.mod.php';

		if (!is_i2module($class_name)) {
	//		throw new Exception("Cannot load module $class_name."); // PHP4
			die('Cannot load module '.$class_name.': the file '.$class_file.' is not readable.');
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
	* @return bool True if the I2 module file is available, or if the class has already been instantiated. False otherwise.
	*/
	function is_i2module($module_name) {
		/* Do not run autoload, since it will throw an exception if the
		class does not exist */
		if (class_exists($module_name, FALSE)) {
			/*FIXME: should this return true? It might technically
			not be an I2 module, but it is safe to instantiate...*/
			return TRUE;
		}
		if (is_readable(i2config_get('module_path', 'core').strtolower($module_name).'.mod.php')) {
			return TRUE;
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
	* based on the class you are calling it from. If the specified field
	* or section does not exist in the configuration file, -1 is returned
	* and a warning is logged in the Logging module.
	*
	* @param String $field The name of the config value you want to get.
	* @param String $section The name of the config section you want to retrieve the value from.
	* @param mixed $default The default value to be returned if the specified key is not found.
	* @return mixed The config value for the specified field, the default value if one was passed and if the specified key was not found, NULL otherwise.
	*/
	function i2config_get($field, $section = NULL, $default) {
		global $I2_ERR;
		static $config = NULL;
		
		if ($config === NULL) { /*Parse the INI file*/
		
			if (!is_readable(CONFIG_FILENAME)) {
				/* This is a critical error, but do not set the
				critical error flag, because the email address
				to send critical errors to is in the config
				file! */
				/* hence, put a hard-coded mail() call here */
				$I2_ERR->call_error('The master Iodine configuration file '.CONFIG_FILENAME.' cannot be read.', FALSE);
			}
			
			$config = parse_ini_file(CONFIG_FILENAME, TRUE);
		}

		if ($section != NULL) {
			if (isset($config[$section][$field])) {
				return $config[$section][$field];
			}
			/* Return error, should probably also make a logging call here */
			return NULL;
		}
		
		/* If a section was not specified, try to guess it from getting
		the class name of what called us. */
		
		$trace = debug_backtrace();

		/* If we were called by a class, use the config section
		for that class. */
		if (isset($trace[1]['class'])) {
			return i2config_get($field, strtolower($trace[1]['class']));
		}
		
		/* If we were not called by a class, default to core, since
		if we were called by core, it would not report a class. */

		return i2config_get($field, 'core');
	}
?>
