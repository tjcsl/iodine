<?php
	/**
	* The error checking module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package core
	* @subpackage logging
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
		switch( $errno ) {
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

	}
			$this->fatal_error("Error: $errstr\r\n<br />Error number: $errno\r\n<br />Error File: $errfile\r\n<br />Error line: $errline", FALSE);
	/**
		}
	/**
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
			$this->fatal_error('There has been an unhandled Iodine error. The file that raised this error was '.$e->getFile().' and the error message was:'."\r\n<br />".$e->getMessage(), TRUE);
		}
	function fatal_error($msg, $critical = 0) {
		/**
		* The generic error function.
		*
		* Use this function to signify an error. Processing will
		* effectively stop from the underlying module's point of view.
		* Even though processing will technically continue so the page
		* renders properly, control is taken away from the module, so
		* you cannot do anything after calling this method. Don't try.
		*
		* @param string $msg The error message to display.
		* @param boolean $critical Whether or not to email this error to
		* a list, if it's absolutely critical.
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
				$I2_LOG->log_mail($out);
				$out .= "\r\n".'<br />This is a critical error, so an email is being sent to the developers, so hopefully this problem will be fixed soon.';
			}
		$out = 'Iodine fatal error: '.$msg;
			$I2_LOG->log_screen($out);
			die();
		}
		if ($critical) {
		
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

			if (!isSet($I2_LOG)) {
				echo("Nonfatal error:  $msg <br />");
			}
			
			$I2_LOG->log_error("Nonfatal error: $msg");
			
		else {
		$I2_LOG->log_error($out);

}
