<?php
	/**
	* The logging module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package logging
	*/
	
	class Logging {
		/**
		* The Logging class constructor.
		* 
		* @access public
		*/
		function Logging() {
			$this->log_access();
		}

		function log_access() {

		}

		function log_error($msg = "") {

		}

		function log_syslog($msg = "") {

		}

		function log_mail($msg = "") {

		}

		function log_debug($level, $msg = "") {

		}

	}

?>
