<?php
	/**
	* The error checking module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package error
	*/
	
	class Error {
		/**
		* The Error class constructor.
		* 
		* @access public
		*/
		function Error() {
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
		function default_exception_handler(Exception $exception) {

		}

		/**
		* The generic error function.
		*
		* Use this function to signify an error. Processing will
		* effectively stop from the underlying module's point of view.
		* Even though processing will technically continue so the page
		* renders properly, control is taken away from the module, so
		* you cannot do anything after calling this method, so don't try
		*
		* @param string $msg		The errors message to display.
		* @param boolean $critical	Whether or not to email this error to a
		*							list, if it's absolutely critical.
		*/

		function call_error($msg, $critical = 0) {
		
		}

	}

?>
