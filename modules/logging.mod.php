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
		
		private $my_email;
		
		/**
		* The Logging class constructor.
		* 
		* @access public
		*/
		function __construct() {
			$this->log_access();
			$this->my_email = i2config_get('email', 'iodine-errors@tjhsst.edu', 'logging');
		}

		function log_access() {

		}

		function log_error($msg) {
			
		}

		/**
		* Log to Syslog
		*
		* @param string $msg The message to log to SysLog
		* @param int $priority The priority of the message to syslog
		* 0 = LOG_CRIT, 1 = LOG_ERR, 2 = LOG_WARNING, 3 = LOG_NOTICE, 4 = LOG_INFO, 5 = LOG_DEBUG
		*/
		function log_syslog($msg = "", $priority = 4) {
			define_syslog_variables(); //Define the LOG_* variables
			stripslashes($msg); //Get rid of any slashes in $msg
			switch ($priority) { //Convert priority to system constants
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
			syslog($pri, $msg); //Send the message to SysLog
		}
		
		/**
		* Log to an email address (Logcheck equivalent)
		*
		* @param string $msg The messgage to log to the email address.
		*/
		function log_mail($msg) {
			$headers  = "MIME-Version: 1.0\n"; //Setup email headers
			$headers .= "From: Intranet Logs <noreply@intranet.tjhsst.edu>\n";
			mail($this->my_email, 'Intranet Log', $msg, $headers); //Send the email
		}
		
		/**
		* Debug logging.
		*
		* @param string $msg The message to log to debug.
		* @param int $level The debug level.
		*/
		function log_debug($msg, $level = NULL) {

			if ($level === NULL) { /* If not set, get default debug level */
				$level = i2config_get('default_debug_level', 0, 'logging');
			}
			if ($level > i2config_get('debug_loglevel', 9, 'logging')) {
				return;
			}

			/* Maybe put some kind of determination whether to
			print to screen or print to file here? */
			$this->log_screen("Debugging Mesage: $msg, Level $level");
		}

		/**
		* Logs a message to the screen.
		*
		* It may not be obvious at first what the point of this method
		* is, and that's because it's not meant to be called by outside
		Question: should it be private/protected, then?
		* classes. It's main use is to be called from the other Logging
		* and/or Error methods, and this just formats the message for
		* output to the user.
		*
		* @param String $msg The message to display.
		*/
		private function log_screen($msg) {
			/* This will get more complicated later, like smarty
			formatting, etc. */
			//FIXME: write a custom template pair for this.
			echo "<div class='raw'>$msg</div>";
		}

	}

?>
