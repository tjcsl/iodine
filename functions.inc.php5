<?php
	/**
	* Global utility functions for Iodine. Only general utility functions
	* which do not belong in any other specific module belong here.

	* @author The Intranet2 Development Team <intranet2@lists.tjhsst.edu>
	* @copyright 2004-2005 The Intranet2 Development Team
	* @version 1.0
	* @since 1.0
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
	* Force loading of the passed class.
	*
	* @param string $class The class to load.
	*/
	function i2_force_load($class) {
		require_once($class);
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
		if (class_exists($module_name, FALSE)) {
			/*FIXME: should this return true? It might technically
			not be an I2 module, but it is safe to instantiate...*/
			/*That case is too difficult to detect for our purposes.
			Such a conflict should be extremely rare, so we'll not
			worry about it now.*/
			return TRUE;
		}
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

	function redirect($modulename) {
		global $I2_LOG, $I2_DISP;
		if (!$modulename || !get_i2module($modulename)) {
			//TODO: note the caller and print info about it.
			$I2_LOG->log_debug("An attempt to include a null module was made.");
			return;
		}
		$I2_DISP->halt_display();
		$I2_LOG->log_debug("Redirecting to module $modulename");
		set_i2var('i2_desired_module',$modulename);
	}

	function set_i2var($varname,$value) {
		global $I2_ARGS, $I2_LOG;
		//TODO: permissions check by caller - only core modules should be able to do this
		$I2_LOG->log_debug("Setting i2 variable $varname to $value");
		$_SESSION[$varname] = $value;
		$I2_ARGS[$varname] = $value;
	}

	function unset_i2var($varname) {
		global $I2_ARGS, $I2_LOG;
		$I2_LOG->log_debug("Unsetting i2 variable $varname");
		//TODO: check this...
		$_SESSION[$varname] = null;
		$I2_ARGS[$varname] = null;

	}
?>
