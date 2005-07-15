<?php
/**
* Just contains the definition for the class {@link Logging}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Id: logging.class.php5,v 1.18 2005/07/14 20:44:11 adeason Exp $
* @package core
* @subpackage Error
* @filesource
*/

/**
* The logging module for Iodine.
* @todo Make the error/debug boxes X-able, so they don't always block stuff.
* @package core
* @subpackage Error
* @see Error
*/
class Logging {
	
	private $error_buf='';
	private $debug_buf='';
	
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
		register_shutdown_function(array($this, 'flush_debug_output'));
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
			(isset($_SESSION['i2_username'])?$_SESSION['i2_username']:'not_logged_in') . ' - [' .
			date('d/M/Y:H:i:s O') . '] "' .
			$_SERVER['REQUEST_URI'] . '" "' .
			(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'') . '" "' .
			(isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'') . '"' ."\n"
		);

	}

	/**
	* Records an entry in the error log.
	*
	* @todo Actually report the module that triggered this. (Or a backtrace
	*       of modules, or something?)
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


		$this->error_buf .= "\r\n<br />$msg";
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
			$this->log_screen('Level '.$level.' debug: '.$msg);
		}
	}

	/**
	* Logs a message to the screen.
	*
	* This method buffers debug messages so they can be output to the screen
	* at the end of the application's run, in a colorful box.
	*
	* @param String $msg The message to display.
	*/
	public function log_screen($msg) {
		if($this->screen_debug) {
			$this->debug_buf .= "\r\n<br />$msg";
		}
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

	/**
	* Flushes any debug or error messages.
	*
	* Since the way debug/error messages are output now need to be output to
	* the browser all at once, they must be buffered. This method outputs
	* those buffers and properly formats them and all that. To ensure that
	* the messages are displayed, this is registered as a 'shutdown
	* function' in php, so it should be called in almost all cases, even if
	* the application just dies halfway through.
	*/
	public function flush_debug_output() {
		global $I2_DISP;

		if( !( $this->error_buf || $this->debug_buf) ) {
			return;
		}
		
		try {
			if( isset($I2_DISP) ) {
				$I2_DISP->disp('error_debug.tpl', array('errors' => $this->error_buf, 'debug' => $this->debug_buf));
				$this->error_buf = NULL;
				$this->debug_buf = NULL;
				return;
			}
		}
		catch( Exception $e ) {
			// Error in standard output, so just print things, no
			//n eed to actually do anything in this block
		}
		//Will not be reached if all goes well

		if( $this->error_buf ) {
			echo '<div class="error">Intranet has encountered the following error(s):'."\r\n".'<br /><br />'
				.$this->error_buf."\r\n".'<br /><br /></div>';
		}
		if( $this->debug_buf ) {
			echo '<div class="debug">Intranet debug messages:'."\r\n".'<br /><br />'.$this->debug_buf."\r\n".'<br /><br /></div>';
		}
		$this->error_buf = NULL;
		$this->debug_buf = NULL;
	}
}

?>
