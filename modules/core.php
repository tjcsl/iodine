<?php
	/**
	* The core module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004-2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package core
	*/

	include('../functions.inc.php');

	/**
	* The path to the master Iodine configuration file.
	*/
	define('CONFIG_FILENAME', '../config.user.ini');
	/* The actual config file in CVS is ../config.ini, but since the config
	file contents should be different for different people when I2 is
	checked out from CVS. So, copy config.ini to config.user.ini in your
	personal intranet2 CVS dir, and change the necessary values. But do
	_NOT_ add config.user.ini to cvs, so it stays different across the
	different instances of the I2 application. */

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
		 * The global LDAP mechanism.
		 *
		 * Use this {@link LDAP} object for accessing LDAP-based information.
		 *
		 * @global LDAP $I2_LDAP
		 */
		$I2_LDAP = new LDAP();
		/**
		 * The global authentication mechanism.
		 *
		 * Use this {@link Auth} object for authenticating users.
		 *
		 * @global Auth $I2_AUTH
		 */
		$I2_AUTH = new Auth();
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
		 * Use this {@link Display} object for nothing, unless you're core.php.
		 *
		 * @global Display $I2_DISP
		 */
		$I2_DISP = new Display('core'); 
		
		/* $I2_WHATEVER = new Whatever(); (Hopefully there won't be much more here) */

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
		foreach(explode('/', $_SERVER['QUERY_STRING']) as $arg) {
			if($arg) {
				$I2_ARGS[] = $arg;
			}
		}
		
		if (count($I2_ARGS) == 0) {
			//FIXME: no modules?!  Whatever will we do?!
			return;
		}
		
		if (!get_i2module($I2_ARGS[0])) {
			$I2_ERR->fatal_error('Invalid module name \''.$I2_ARGS[0].'\'. Either you mistyped a URL or you clicked a broken link. Or Intranet could just be broken.');
		}

		/* Display will instantiate the module, we just pass the name */
		$I2_DISP->display_loop($I2_ARGS[0]);
	
	} catch (Exception $e) {
		if(isset($I2_ERR)) {
			$I2_ERR->default_exception_handler($e);
		} else {
			die('The error module is not loaded and there was an error: '.$e->getMessage());
		}
	}

?>
