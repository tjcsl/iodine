<?php
	/**
	* The core module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004-2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package core
	*/

	include('../functions.inc.php5');

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

		session_start();
		
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
		//$I2_LDAP = new LDAP();
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

		//FIXME: PROTECT THIS TOKEN!
		$mastertoken = get_master_token();	
		if (!isSet($_SESSION)) {
			$_SESSION = array();
		}
		foreach($_SESSION as $key=>$value) {
			//TODO: filter out bad stuff.
			//if (strpos($key,'i2') !== 0) {
				$I2_ARGS[$key] = $value;			
				$I2_LOG->log_debug("Mapped key $key to $value from session variables.");
			//}
		}

		foreach ($_REQUEST as $key=>$value) {
			//TODO: filter.
			//if (strpos($key,'i2') !== 0) {
				$I2_ARGS[$key] = $value;
				/*
				** Hide passwords.
				*/
				if ($key == 'login_password') {
					$value = '**HIDDEN**';
				}
				$I2_LOG->log_debug("Mapped key $key to value $value in the request string.");
			//}
		}

		$I2_ARGS['i2_query'] = array();
		$I2_ARGS['i2_boxes'] = array();

		/* Eliminates extraneous slashes in the PATH_INFO
		** And splits them into the global I2_ARGS array
		*/
		foreach(explode('/', $_SERVER['QUERY_STRING']) as $arg) {
			if($arg) {
				if (!isSet($I2_ARGS['i2_desired_module'])) {
					$I2_ARGS['i2_desired_module'] = $arg;
				} else {
					$I2_LOG->log_debug("Added $arg to the query string variable.");
					$I2_ARGS['i2_query'][] = $arg;
				}
			}
		}

		$authed = $I2_AUTH->check_authenticated();

		if (!$authed) {
			$I2_DISP->show_login($mastertoken);
			if (!isSet($I2_ARGS['i2_desired_module'])) {
				$I2_ARGS['i2_desired_module'] = $I2_USER->get_current_user_info($mastertoken)->get_startpage($mastertoken);
			}
			$authed = $I2_AUTH->check_authenticated();
		}
		
		if (!get_i2module($I2_ARGS['i2_desired_module'])) {
			$I2_ERR->fatal_error('Invalid module name \''.$I2_ARGS['i2_desired_module'].'\'. Either you mistyped a URL or you clicked a broken link. Or Intranet could just be broken.');
		}

		$I2_LOG->log_debug('Desired module is '.$I2_ARGS['i2_desired_module']);

		//FIXME: put stuff in $I2_ARGS['i2_boxes'] here.

		foreach ($I2_ARGS['i2_boxes'] as $module) {
			$I2_LOG->log_debug("Box: $module");
		}

		/* Display will instantiate the module, we just pass the name */
		if ($authed) {
			$I2_DISP->display_loop($I2_ARGS['i2_desired_module'],$mastertoken);
		}
	
	} catch (Exception $e) {
		if(isSet($I2_ERR)) {
			$I2_ERR->default_exception_handler($e);
		} else {
			die('The error module is not loaded and there was an error: '.$e->getMessage());
		}
	}

?>
