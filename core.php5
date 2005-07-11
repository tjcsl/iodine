<?php
/**
* The core module for Iodine.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @version $Id: core.php5,v 1.25 2005/07/11 05:16:30 adeason Exp $
* @package core
* @filesource
*/

/**
* General functions.
*/
include('functions.inc.php5');

/**
* The current version of Iodine running.
*
* Don't increment this until we have something runnable.
*/
define('I2_VERSION', 0.1);

/**
* The path to the master Iodine configuration file.
*/
define('CONFIG_FILENAME', 'config.ini');

/*
The actual config file in CVS is config.user.ini and config.server.ini
When you check out intranet2 to run it from your personal space, copy
config.user.ini to config.ini and edit the values to work in your own
personal space. Do _NOT_ add config.ini to CVS, as it's different for
everyone. Edit config.server.ini to edit the server (production) config.
*/

/* Load essential modules, parse query string, start session, etc. */
try {

	session_start();

	/**
	* The global associative array for a module's arguments.
	*
	* This contains argv-style arguments
	* to the module specified that were passed on the query string
	* to the Iodine application.
 	*
 	* As an example, the URL
	* http://intranet.tjhsst.edu/birthday/10/16/87 will yield an
	* $I2_ARGS of [0] => birthday, [1] => 10,
	* [2] => 16, [3] => 87. The 'birthday' module's
	* {@link init_pane()} and {@link display_pane()} functions will
	* automatically be called on page load, and it can access it's
	* arguments via accessing the $I2_ARGS array just as a normal
	* global, so it can load the very special person's info who has
	* that birthday.
	*
	* @global array $I2_ARGS
	*/
	$I2_ARGS = array();

	/* Eliminates extraneous slashes in the PATH_INFO
	** And splits them into the global I2_ARGS array
	*/
	if(isset($_SERVER['REDIRECT_QUERY_STRING'])) {
		$query = $_SERVER['REDIRECT_QUERY_STRING'];
	}
	else {
		$query = '';
	}
	foreach(explode('/', $query) as $arg) {
		if($arg) {
			$I2_ARGS[] = $arg;
		}
	}
	
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
	  The global LDAP mechanism.
	 
	  Use this {@link LDAP} object for accessing LDAP-based information.
	 
	  @global LDAP $I2_LDAP
	 */
	//$I2_LDAP = new LDAP();
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
	$I2_DISP = new Display(); 
	/**
	 * The global authentication mechanism.
	 *
	 * Use this {@link Auth} object for authenticating users.
	 *
	 * @global Auth $I2_AUTH
	 */
	$I2_AUTH = new Auth();
	
	/* $I2_WHATEVER = new Whatever(); (Hopefully there won't be much more here) */

	/* gets the user's startpage module if no module has
	been specified */
	$module = isset($I2_ARGS[0]) ?
		$I2_ARGS[0] :
		$I2_USER->get_current_user_info()->get_startpage();

	if (!get_i2module($module)) {
		$I2_ERR->fatal_error('Invalid module name \''.$module.'\'. Either you mistyped a URL or you clicked a broken link. Or Intranet could just be broken.');
	}

	/* Display will instantiate the module, we just pass the name */
	d('Passing module ' . $module . ' to Display module', 9);
	$I2_DISP->display_loop($module);

} catch (Exception $e) {
	if(isset($I2_ERR)) {
		$I2_ERR->default_exception_handler($e);
	} else {
		die('There was an error too early on in the application for anything to handle the error. What you are seeing right now is the fail-safe message. Please inform the intranetmaster of this immediately. Error: '.$e->__toString());
	}
}

?>
