<?php
/**
* Just contains the definition for the class {@link Error}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @package core
* @subpackage Error
* @filesource
*/

/**
* The error checking module for Iodine.
* @package core
* @subpackage Error
* @see logging
*/
class Error {
	/**
	* The Error class constructor.
	* 
	* @access public
	*/
	function __construct() {
		set_error_handler(array(&$this,'default_error_handler'));
		error_reporting(E_ALL | E_STRICT);
		set_exception_handler(array(&$this,'default_exception_handler'));
	}

	/**
	* The default error handling function.
	*
	* This is the function that is triggered if someone happens
	* to use PHP's trigger_error() function (which you should not
	* be using), just in case. All parameters are as per the
	* specification on php.net.
	*/
	function default_error_handler($errno, $errstr, $errfile, $errline) {
		global $I2_ROOT;
		/* 
		** Ignore messages if error_reporting() is zero, i.e. error
		** suppression is on.
		*/

		if(error_reporting() == 0 ) {
			return;
		}

		/*
		** Drop LDAP sizelimit errors into debug messages
		*/
		if (strpos($errstr,'Partial search results returned: Sizelimit exceeded.')) {
			d('LDAP sizelimit exceeded',3);
			return;
		}
		
		/*
		** Ditto LDAP object-not-found errors
		*/
		if (strpos($errstr,'Search: No such object')) {
			d('LDAP search performed for invalid object',5);
			return;
		}
		
		/*
		** Ditto LDAP delete-non-leaf errors
		*/
		if (strpos($errstr,'Delete: Operation not allowed on non-leaf')) {
			d('LDAP object deletion attempted on non-leaf!',3);
			return;
		}

		/*
		** ... AND invalid DN syntax errors, generally caused by having a null in the IodineUID
		*/
		if (strpos($errstr,'Search: Invalid DN syntax')) {
			d('LDAP search performed with invalid DN syntax',4);
			return;
		}
		
		/*
		** ... AND add-failed-already-exists errors
		*/
		if (strpos($errstr,'Add: Already exists')) {
			d('LDAP add operation failed because node already exists',4);
			return;
		}

		/*
		** ...AND those annoying variable deprecation warnings, usually from Smarty.
		*/
		if (strpos($errstr,'Deprecated. Please use the public/private/protected modifiers')) {
			return;
		}
		
		$fileurl = str_replace('%2F', '/', urlencode($errfile));
		switch( $errno ) {
			case E_WARNING:
			case E_NOTICE:
			case E_STRICT:
				$this->nonfatal_error("Warning: $errstr\r\n<br />Error number: $errno\r\n<br />File: <a href='" . $I2_ROOT . "highlight/$errline$fileurl#$errline'>$errfile:$errline</a>");
				break;
			default:
				$this->fatal_error("Error: $errstr\r\n<br />Error number: $errno\r\n<br />File: $errfile\r\n<br />Line: $errline");
		}

	}

	/**
	* The default exception handling function.
	*
	* This function handles an exception if nothing else in the I2
	* application does. Modules should handle their own exceptions,
	* though; this is just a failsafe.
	*
	* @param Exception $e The exception that was thrown
	*                     and not caught
	*/
	function default_exception_handler(Exception $e) {
		$this->fatal_error(''.$e->__toString(), FALSE);
	}

	/**
	* The generic fatal error function.
	*
	* Use this function to signify an error. Processing will
	* stop at the end of this method call, so tables/divs/etc will not be
	* closed. If you want that to happen, then throw an exception. This
	* method is just for errors which must cause Iodine to stop immediately
	*
	* @param string $msg The error message to display.
	* @param bool $critical Whether or not to email this error to
	* a list, if it's absolutely critical.
	*/

	function fatal_error($msg, $critical = FALSE) {
		global $I2_LOG;

		$out = 'Iodine fatal error: '.$msg;
		
		if ($critical) {
			$I2_LOG->log_mail($out.'\r\nBacktrace: \r\n'.print_r(debug_backtrace(),TRUE));
			$out .= "\r\n".'<br />This is a critical error, so an email is being sent to the developers, hopefully this problem will be fixed soon.';
		}
		else {
			$out .= "<br />\r\n".'If this problem persists, please contact the intranetmaster.';
		}
		
		if (!isset($I2_LOG)) {
			echo $out.'<BR>';
			die();
		}

		$I2_LOG->log_error($out);

		//echo 'Backtrace: <br/><pre>'.print_r(debug_backtrace(),TRUE).'</pre>';
		
		die();
	}
	
	
	/**
	* The non-fatal error function.
	*
	* Use this function to signify an error that, while possibly fatal
	* for the module that raised it, should NOT be fatal for the entire
	* application run.
	*
	* @param string $msg The message associated with the error.
	*/
	function nonfatal_error($msg) {
		global $I2_LOG;

		if (isset($I2_LOG)) {
			$I2_LOG->log_error($msg);
		}
	}

}

?>
