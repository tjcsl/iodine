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
		$class_file = MODULE_PATH.strtolower($class_name).'.mod.php';

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
	* @return Boolean True if the I2 module file is available, or if the class has already been instantiated. False otherwise.
	*/
	function is_i2module($module_name) {
		/* Do not run autoload, since it will throw an exception if the
		class does not exist */
		if (class_exists($module_name, FALSE)) {
			/*FIXME: should this return true? It might technically
			not be an I2 module, but it is safe to instantiate...*/
			return TRUE;
		}
		if (is_readable(MODULE_PATH.strtolower($module_name).'.mod.php')) {
			return TRUE;
		}
		return FALSE;
	}
?>
