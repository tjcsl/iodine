<?php
	/**
	* The logging module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package logging
	*/
	
	/*
	Logging Team Leader: vmircea
	Logging Team: adeason, mlee1, melthon, jboning
	*/
	
	
	class Logging {
		/**
		* The Logging class constructor.
		* 
		* @access public
		*/
		
		var $my_email = "intranet@tjhsst.edu";

		function Logging() {
			$this->log_access();
		}

		function log_access() {

		}

		function log_error($msg = "") {
			
		}

		/**
		* Log to SysLog
		*
		* @param string $msg The message to log to SysLog
		* @param int $priority The priority of the message to syslog
		* 0 = LOG_CRIT, 1 = LOG_ERR, 2 = LOG_WARNING, 3 = LOG_NOTICE, 4 = LOG_INFO, 5 = LOG_DEBUG
		*/
		function log_syslog($msg = "", $priority = 4) {
			define_syslog_variables();
			stripslashes($msg);
			switch ($priority) {
				case 0:
					$pri = LOG_CRIT;
					break;
				case 1:
					$pri = LOG_ERR;
					break;
				case 2:
					$pri = LOG_WARNING;
					break;
				case 3:
					$pri = LOG_NOTICE;
					break;
				case 4:
					$pri = LOG_INFO;
					break;
				case 5:
					$pri = LOG_DEBUG;
					break;
				default:
					$pri = LOG_INFO;
					break;
			}
			syslog($pri, $msg);
		}
		
		/**
		* Log to an email address (Logcheck equivalent)
		*
		* @param string $msg The messgage to log to the email address.
		*/
		function log_mail($msg = "") {
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "From: Intranet Logs <noreply@intranet.tjhsst.edu>\n";
			mail($my_email, "Intranet Log", $msg, $headers);
		}

		function log_debug($level, $msg = "") {

		}

	}

?>
