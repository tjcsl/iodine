<?php
/**
* Just contains the definition for the class {@link Error}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
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
		if ((ini_get('error_reporting') & $errno) == 0) {
			return;
		}
		switch( $errno ) {

		$this->fatal_error("Error: $errstr\r\n<br />Error number: $errno\r\n<br />Error File: $errfile\r\n<br />Error line: $errline", FALSE);
	}

	/**
	* The default exception handling function.
	*
	* This function handles an exception if nothing else in the I2
	* application does. Modules should handle their own exceptions,
	* though; this is just a failsafe.
	*
	* @param Exception $exception	The exception that was thrown
	*				and not caught
	*/
	function default_exception_handler(Exception $e) {
		$this->fatal_error(''.$e->__toString(), FALSE);
		$this->fatal_error('There has been an unhandled Iodine exception: '.$e->__toString(), TRUE);

	/**
	* The generic fatal error function.
	* The generic error function.
	* Use this function to signify an error. Processing will
	* stop at the end of this method call, so tables/divs/etc will not be
	* effectively stop from the underlying module's point of view.
	* Even though processing will technically continue so the page
	* renders properly, control is taken away from the module, so
	* you cannot do anything after calling this method. Don't try.
	* @param string $msg The error message to display.
	* @param bool $critical Whether or not to email this error to
	* @param boolean $critical Whether or not to email this error to
	*/

	function fatal_error($msg, $critical = 0) {
		global $I2_LOG;

		$out = 'Iodine fatal error: '.$msg;
		
		if (!isSet($I2_LOG)) {
			echo $out.'<BR>';
			die();
		}
		$I2_LOG->log_error($out);
		if ($critical) {
			$I2_LOG->log_mail($out.'\r\nBacktrace: \r\n'.print_r(debug_backtrace(),TRUE));
			$I2_LOG->log_mail($out);
		}
		else {
		$I2_LOG->log_error($out);
		$I2_LOG->log_screen($out);
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
		if (!isset($I2_LOG)) {
			echo("$msg <br />");
	}
		
		$I2_LOG->log_error($msg);

}

?>
