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
	* A safe alternative to the echo() or I2_LOG->debug methods.
	*
	* @param string $text The text to display.
	*/
	function echo_handler($text) {
		global $I2_LOG;
		if (isSet($I2_LOG)) {
			$I2_LOG->log_debug($text);
		} else {
			echo($text.'<br />');
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
	* readability of the module (if one exists) in the {@link MODULE_PATH}.
	* Throws an exception if it does not exist.
	* 
	* @param string $class_name Name of noninstantiated class.
	*/
	function __autoload($class_name) {
		global $I2_ERR;
		echo_handler("Loading $class_name");
		$class_file = '';
		if ($class_name == "Error") {
			require_once(MODULE_PATH.'error.class.php5');
			return;
		}

		if (!($class_file=get_i2module($class_name))) {
			$I2_ERR->nonfatal_error('Cannot load module/class '.$class_name.': the file '.$class_file.' is not readable.');
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
	* based on the class you are calling it from. If the specified field
	* or section does not exist in the configuration file, -1 is returned
	* and a warning is logged in the Logging module.
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

	//FIXME:  Isolate these variables
	$master = null;
	$slaves = array();

	function compatible_access($permissions,$request) {
		if ($permissions == 'w') {
			return true;
		}
		if ($permissions == $request) {
			return true;
		}
		return false;
	}

	/**
	* Verifies that the passed token gives rights to access the passed data.
	*
	* @param string $token The authentication token to check.
	* @param string $field The field whose access was attempted.
	* @param string $accesstype 'r' for read, 'w' for write.
	* @return boolean True if access is granted; false if it's denied.
	*/
	function check_token_rights($token, $field, $accesstype) {
		global $slaves;
		if (!isSet($slaves[$token])) {
			/*
			** Invalid token.
			*/
			return false;
		}
		/*
		** Eliminate all of the passed field after the first underscore.
		*/
		$fieldpieces = preg_split('/_/',$field);
		$field = $fieldpieces[0];
		if (isSet($slaves[$token][$field])) {
			return compatible_access($slaves[$token][$field],$accesstype);
		}
		if (isSet($slaves[$token]['*'])) {
			return compatible_access($slaves[$token]['*'],$accesstype);
		}
		/*
		** No mention of the passed field, and no catch-all.
		*/
		return false;
	}

	function generate_token() {
		//FIXME:  make this real.
		return md5(time());
	}

	/**
	* Issue an authentication token for a module to use to access user information.
	*
	* @return string An authentication token with the given rights.
	* @param string $mastertoken The master token obtained from get_master_token.
	* @param mixed $rightsarray An array containing access rights for the new token.
	*/
	function issue_token($mastertoken, $rightsarray) {
		global $slaves;
		global $master;
		global $I2_LOG;
		if ($mastertoken != $master) {
			echo_handler('An invalid master token was used in an attempt to create a new token!');
			return;
		}
		$token = generate_token();
		$slaves[$token] = $rightsarray;
		$ct = count($rightsarray);
		if ($ct > 1) {
			$I2_LOG->log_debug('Authentication token issued, rights: '.print_r($rightsarray,true));
		} else if ($ct%2==1) {
			$I2_LOG->log_debug('A token issue was attempted with an irregular number of access rights...');
		} else {
			$I2_LOG->log_debug('Authentication token issued with no rights!');
		}
		return $token;
	}
	
	function get_master_token() {
		global $master;
		global $slaves;
		if (isSet($master)) {
			echo_handler('An attempt was made to create an extra master token!');
		} else {
			$master = generate_token();
			/*
			** Allow the master token read/write access to everything.
			*/
			$slaves[$master] = array('*'=>'w');
			return $master;
		}
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
		$I2_LOG->log_debug("Setting i2 variable $varname to $value");
		$_SESSION[$varname] = $value;
		$I2_ARGS[$varname] = $value;
	}
?>
