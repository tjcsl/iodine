<?php
	/**
	* The core module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004-2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package core
	*/
	
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
		if (!is_readable(MODULE_PATH."$class_name.inc.php")) {
			throw new Exception("Cannot load module $class_name."); // PHP4
			die("Cannot load module $class_name.");
		}
		else {
			require_once(MODULE_PATH."$class_name.inc.php");
		}
	}

	/* Load the essential modules; start try block*/
	try {
		/**
		 * The global error-handling mechanism.
		 *
		 * Use this {@link Error} object to handle any errors that might arise.
		 *
		 * @global Error $I2_ERR
		 */
		$I2_ERR = new Error();
		/**
		 * The global logging mechanism.
		 *
		 * Use this {@link Logging} object for logging purposes.
		 *
		 * @global Logging $I2_LOG
		 */
		$I2_LOG = new Logging();
		/**
		 * The global SQL mechanism.
		 *
		 * Use this {@link MySQL} object for connecting to the MySQL database.
		 *
		 * @global MySQL $I2_SQL
		 */
		$I2_SQL = new MySQL();
		/**
		 * The global authentication mechanism.
		 *
		 * Use this {@link Authentication} object for authenticating users.
		 *
		 * @global Authentication $I2_AUTH
		 */
		$I2_AUTH = new Authentication();
		/**
		 * The global user info mechanism.
		 *
		 * Use this {@link User} object for getting information about a user.
		 *
		 * @global User $I2_USER
		 */
		$I2_USER = new User();
		/**
		 * The global display mechanism.
		 *
		 * Use this {@link Display} object for displaying information on a page.
		 *
		 * @global Display $I2_DISP
		 */
		$I2_DISP = new Display('core'); 
		/*
		** Note from braujac:  The Display module's methods are static for most things.
		** The objects are purely for convenience.  In the future this may change, however,
		** so please use only the instance methods for now.
		*/

		 
		
		/* $I2_WHATEVER = new Whatever(); (Hopefully there won't be much more here)*/

		/**
		 * The global array for a module's arguments.
		 *
		 * This array contains argv-style arguments when a module is called to
		 * display a page. $I2_ARGS[0] will always be the name of the module,
		 * and subsequent elements will be the arguments passed to the module
		 * using '/' to delimit arguments in the URL.
		 *
		 * As an example, the URL http://intranet.tjhsst.edu/birthday/10/16/87
		 * will yield an $I2_ARRAY of [0] => birthday, [1] => 10, [2] => 16,
		 * [3] => 87. The 'birthday' module's init() function will automatically
		 * be called on page load, and it can access it's arguments via
		 * accessing the $I2_ARGS array just as a normal global, so it can load
		 * the very special person's info who has that birthday.
		 *
		 * @global mixed $I2_ARGS
		 */
		$I2_ARGS = array();

		/* Eliminates extraneous slashes in the PATH_INFO
		** And splits them into the global I2_ARGS array
		 */
		foreach(explode('/', $_SERVER['PATH_INFO']) as $arg) {
			if($arg) {
				$I2_ARGS[] = $arg;
			}
		}

		/*
		** The order of the following will probably be changed; this is temporary.
		*/
		
		$I2_MODULE_NAME = $I2_ARGS[0];	//TODO: validate module name to make sure the user isn't being nasty.
		eval('$I2_MOD = new ' . $I2_MODULE_NAME . '();'); 	// Instantiate the main module
		
		$I2_DISP->globalHeader(); //Background, CSS link, title bar, etc.
		$I2_DISP->startBoxes(); //Begin ibox column/group/whatever
		foreach ($I2_LOADED_MODULES as $module_name) {
			eval('$module = new ' . $module_name . '();'); //Instantiate the module
			$I2_DISP->openBox($module); //Open an ibox
			$module->display(new Display($module)); //Invoke the module display method
			$I2_DISP->closeBox($module); //Close the ibox
		}
		$I2_DISP->endBoxes(); //Close the box group
		$I2_DISP->openMainBox($I2_MOD); //Open the central box
		$I2_MOD->display(new Display($I2_MODULE_NAME);
		$I2_DISP->closeMainBox($I2_MOD); //Close the central box
		$I2_DISP->globalFooter();
	
	} catch ($exception) {
		if(isset($I2_ERR)) {
			$I2_ERR->unhandled_error($exception);
		} else {
			die("The error module is not loaded and there was an error.");
		}
	}

?>
