<?php
/**
* The core module for Iodine.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2006 The Intranet 2 Development Team
* @package core
* @filesource
*/

/**
* General functions.
*/
require('functions.inc.php5');

/**
* The current version of Iodine running.
*
* Don't increment this until we have something runnable.
*/
define('I2_VERSION', 1.00);

/**
* The path to the master Iodine configuration file.
*/
define('CONFIG_FILENAME', 'config.ini.php5');

/**
* The information about the memcache server.
*/
define('MEMCACHE_SERVER', 'localhost');
define('MEMCACHE_PORT', '11211');
define('MEMCACHE_DEFAULT_TIMEOUT', '120');

/**
* A few helpful globals, which need to be generated, so they cannot simply be define()'d.
*/
$I2_SELF = $_SERVER['REDIRECT_URL'];
$I2_DOMAIN = $_SERVER['HTTP_HOST'];

/**
* 'core.php5' is nine letters
*/
$I2_ROOT = (isSet($_SERVER['HTTPS'])?'https://':'http://') . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'],0,-9);
//$I2_ROOT = i2config_get('www_root', 'https://iodine.tjhsst.edu/','core');
$I2_FS_ROOT = substr($_SERVER['SCRIPT_FILENAME'],0,-9);
//$I2_FS_ROOT = i2config_get('root_path', '/var/wwww/iodine/', 'core');

/**
* If this line is not present, it generates a lot of warning messages in recent
* versions of PHP.
*/
if(version_compare(PHP_VERSION, '5.1.0', '>')) {
	date_default_timezone_set(i2config_get('timezone','America/New_York','core'));
}

/*
The actual config file in Git is config.user.ini and config.server.ini
When you check out intranet2 to run it from your personal space, run
setup. Do _NOT_ add config.ini.php5 to Git, as it's different for
everyone. Edit config.server.ini to edit the server (production) config.
*/

/* Load essential modules, parse query string, start session, etc. */
try {
	load_module_map();

	session_set_save_handler(new SessionGC());

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
	$I2_ARGS = [];

	/**
	* The global associative array for a module's query arguments.
	*
	* As an example, the URL
	* https://intranet.tjhsst.edu/module/?a&b=c&d will yield an
	* $I2_QUERY of ['a'] = TRUE, ['b'] = 'c', ['d'] = TRUE
	*
	* @global array $I2_QUERY
	*/
	$I2_QUERY = [];

	/* Eliminates extraneous slashes in the PATH_INFO
	** And splits them into the global I2_ARGS array
	*/
	if(isset($_SERVER['REDIRECT_QUERY_STRING'])) {
		$index = strpos($_SERVER['REDIRECT_QUERY_STRING'], '?');
		$args = substr($_SERVER['REDIRECT_QUERY_STRING'], 0, $index);
		foreach(explode('/', $args) as $arg) {
			if(strlen($arg) != 0) {
				$I2_ARGS[] = $arg;
			}
		}
		$queries = substr($_SERVER['REDIRECT_QUERY_STRING'], $index+1);
		foreach(explode('&', $queries) as $query) {
			if ($query) {
				$element = explode('=', urldecode($query));
				if (sizeof($element) > 1) {
					$I2_QUERY[$element[0]] = $element[1];
				} else {
					$I2_QUERY[$element[0]] = TRUE;
				}
			}
		}
	}

	/**
	* Skip a lot of this computation when you're generating the CSS.
	*
	* Should be a fairly large speedup.
	*/
	if(count($I2_ARGS) > 0 && $I2_ARGS[0]=='css' && CSS::showCSS()) {
		exit();
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
	 * The global memcache access mechanism
	 * 
	 * Use this {@link Cache} object to cache stuff with memcache.
	 *
	 * @global Cache $I2_CACHE
	 */
	$I2_CACHE = new Cache();

	/**
	 * The global SQL mechanism.
	 *
	 * Use this {@link MySQL} object for connecting to the MySQL database.
	 *
	 * @global MySQL $I2_SQL
	 */
	$I2_SQL = new MySQL();

	/**
	 * The Api object.
	 * Used in Auth, so must come first.
	 *
	 * @global Api $I2_API
	 */
	$I2_API  = new Api();

	/**
	 * The control mechanism for all Asynchonous Javascript and XML.
	 * Used in Auth, so must come first.
	 *
	 * @global Ajax $I2_AJAX
	 */
	$I2_AJAX  = new Ajax();

	/**
	 * The global authentication mechanism.
	 *
	 * Use this {@link Auth} object for authenticating users.
	 *
	 * @global Auth $I2_AUTH
	 */
	$I2_AUTH = new Auth();

	/**
	 * The global LDAP mechanism.
	 *
	 * Use this {@link LDAP} object for accessing LDAP-based information.
	 *
	 * @global LDAP $I2_LDAP
	 */
	$ldap_excludes = (isset($I2_ARGS[0]) &&
		($I2_ARGS[0] == 'feeds' ||
		$I2_ARGS[0] == 'calendar' ||
			(isset($I2_ARGS[1]) &&
			($I2_ARGS[0] == 'api' && $I2_ARGS[1] == 'bellschedule') ||
			($I2_ARGS[0] == 'ajax' && $I2_ARGS[1] == 'bellschedule'))));
	if($ldap_excludes && !$I2_AUTH->is_authenticated(TRUE)) {
		//don't try to bind when you're in generic mode.
		$I2_LDAP = LDAP::get_generic_bind();
		$I2_USER = new User(9999);
	} else {
		try {
			$I2_LDAP = LDAP::get_user_bind();
		} catch(I2Exception $e) {
			warn("Full directory access not working...attempting to use generic user because of " . $e);
			$I2_LDAP = LDAP::get_generic_bind();
		}

	/**
	 * The global user info mechanism.
	 *
	 * Use this {@link User} object for getting information about a user.
	 *
	 * @global User $I2_USER
	 */
		$I2_USER = new User();
	}
	/**
	 * The global display mechanism.
	 *
	 * Use this {@link Display} object for nothing, unless you're core.php.
	 *
	 * @global Display $I2_DISP
	 */
	$I2_DISP = new Display();

	/* $I2_WHATEVER = new Whatever(); (Hopefully there won't be much more here) */

	// Starts with whatever module the user specified, otherwise
	// default to 'welcome'
	$module = "";
	if(isSet($I2_ARGS[0])) {
		$module = $I2_ARGS[0];
	} elseif($I2_USER->startpage) {
		$module = $I2_USER->startpage;
	} else {
		$module = i2config_get('startmodule','welcome','core');
	}

	if(strtolower($module) == 'ajax') {
		$I2_AJAX->returnResponse($I2_ARGS[1]);
	} elseif(strtolower($module) == 'api') {
		$I2_API->init();
		// disable backtraces by default
		$I2_API->backtrace=false;
		array_shift($I2_ARGS);
		if(isSet($I2_ARGS[0])) {
			$module = $I2_ARGS[0];
		} else {
			$I2_API->startElement('invalid');
			throw new I2Exception("No module specified. Currently supported modules are news and eighth.");
		}
		d('Passing module' . $module . ' api call', 8);

		if(!get_i2module($module)) {
			$I2_API->startElement($module);
			throw new I2Exception("Not a module");
		} else {
			$mod = new $module();
			$I2_API->startDTD($module);
			$I2_API->writeDTDElement($module,'(body,error,debug)');
			if($mod->api_build_dtd()==false) {
				// no module-specific dtd
				$I2_API->writeDTDElement('body','(#PCDATA)');
			}
			$I2_API->writeDTDElement('error','(#PCDATA)');
			$I2_API->writeDTDElement('debug','(#PCDATA)');
			$I2_API->endDTD();
			$I2_API->startElement($module);
			$mod->api($I2_DISP);
		}
	}
	else {
		/* Display will instantiate the module, we just pass the name */
		d('Passing module ' . $module . ' to Display module', 8);
		$I2_DISP->display_loop($module);
	}

} catch (Exception $e) {
	if(isset($I2_ERR)) {
		$I2_ERR->default_exception_handler($e);
	} else {
		die('There was an error too early on in the application for anything to handle the error. What you are seeing right now is the fail-safe message. Please inform the intranetmaster immediately. Error: '.$e->__toString());
	}
}

?>
