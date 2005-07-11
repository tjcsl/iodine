<?php
/**
* Just contains the definition for the class {@link Logging}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Id: logging.class.php5,v 1.11 2005/07/11 05:16:36 adeason Exp $
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
	
	/**
	* Email address to send critical messages to.
	*/
	private $my_email;
	
	/**
	* Whether to debug information to the screen or not.
	*/
	private $screen_debug = FALSE;
	
	/**
	* The Logging class constructor.
	* 
	* @access public
	*/
	public function __construct() {
		$this->log_access();
		$this->screen_debug = true;
		$this->my_email = i2config_get('email', 'iodine-errors@tjhsst.edu', 'logging');
	}

	/**
	* Records an entry in the access log. This is called on every page load
	* that does not crash before the Logging object is instantiated.
	*/
	public function log_access() {
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

	/**
	* Records an entry in the error log.
	*
	* @param string $msg The error message to record.
	*/
	public function log_error($msg) {
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
	* @todo Write a smarty template for the output.
	*/
	public function log_screen($msg) {
		global $I2_DISP;
		if (isset($I2_DISP)) {
			echo "<div class='raw'>$msg</div>";
		}
		/* otherwise do nothing, logging to screen should not
		be necessary this early on, before I2_DISP exists */
	}

	/**
	* Turns on screen debugging, so debug messages are output to the screen
	*/
	public function debug_on() {
		$this->screen_debug = true;
	}

	/**
	* Turns off screen debugging
	*/
	public function debug_off() {
		$this->screen_debug = false;
	}
}

?>
