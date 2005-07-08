<?php
/**
* Just contains the definition for the class {@link Logging}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage Error
* @filesource
*/

/**
* The logging module for Iodine.
* @package core
* @subpackage Error
* @see Error
*/
class Logging {
	
	private $my_email;
	private $screen_debug;
	
	/**
	* The Logging class constructor.
	* 
	* @access public
	*/
	function __construct() {
		$this->log_access();
		$this->screen_debug = true;
		$this->my_email = i2config_get('email', 'iodine-errors@tjhsst.edu', 'logging');
	}

	function log_access() {
		global $I2_ERR;
		
		$fname = i2config_get('access_log');
		
		if (!$fname || !($fh = fopen($fname, 'a'))) {
			$I2_ERR->fatal_error('The main iodine access log cannot be accessed.');
		}
		
		/* IP - username - [Apache-style date format] "Request" "Referrer" "User-Agent" */

		fwrite($fh,
			$_SERVER['REMOTE_ADDR'] . ' - ' .
			'blah' /*FIXME*/ . ' - [' .
			date('d/M/Y:H:i:s O') . '] "' .
			$_SERVER['REQUEST_URI'] . '" "' .
			(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'') . '" "' .
			$_SERVER['HTTP_USER_AGENT'] . '"' ."\n"
		);

	}

	function log_error($msg) {
		global $I2_ERR, $I2_DISP;
		
		$fname = i2config_get('error_log');
		
		if (!$fname || !($fh = fopen($fname, 'a'))) {
			$I2_ERR->fatal_error('The main iodine error log cannot be accessed.');
		}
		
		/* IP - [Apache-style date format] [Module] "Request" "Error" */
		fwrite($fh,
			$_SERVER['REMOTE_ADDR'] . ' - [' .
			date('d/M/Y:H:i:s O') . '] [' .
			'mr. module' /*FIXME*/ . '] "' .
			$_SERVER['REQUEST_URI'] . '" "' .
			$msg . '"' ."\n"
		);
		fclose($fh);

		if(isset($I2_DISP)) {
			$I2_DISP->disp('error.tpl', array('error'=>$msg));
		}
	}


	/**
	* Log to Syslog
	*
	* @param string $msg The message to log to SysLog
	* @param int $priority The priority of the message to syslog
	* 0 = LOG_CRIT, 1 = LOG_ERR, 2 = LOG_WARNING, 3 = LOG_NOTICE, 4 = LOG_INFO, 5 = LOG_DEBUG
	*/

	/* I DO NOT THINK THIS IS REALLY NECESSARY
	It will be deleted soon unless I get some objection. -Deason */
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
		$msg = "Iodine error: " . $msg;
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

		if ($this->screen_debug) {
			$trace = debug_backtrace();
			$this->log_screen('Debugging Mesage' . (isset($trace[1]['class'])?' from module '.$trace[1]['class']:' from core') .": $msg, Level $level");
		}
	}

	/**
	* Logs a message to the screen.
	*
	* It may not be obvious at first what the point of this method
	* is, and that's because it's not meant to be called by outside
	* classes. It's main use is to be called from the other Logging
	* and/or Error methods, and this just formats the message for
	* output to the user.
	*
	* @param String $msg The message to display.
	*/
	public function log_screen($msg) {
		global $I2_DISP;
		/* This will get more complicated later, like smarty
		formatting, etc. */
		//FIXME: write a custom template pair for this.
		if (isset($I2_DISP)) {
			echo "<div class='raw'>$msg</div>";
		}
		/* otherwise do nothing, logging to screen should not
		be necessary this early on, before I2_DISP exists */
	}

	/* turns on and off screen debugging, so you can see on the
	screen all debug for temporary amounts of processing if you
	want to */
	public function debug_on() {
		$this->screen_debug = true;
	}

	public function debug_off() {
		$this->screen_debug = false;
	}
}

?>
