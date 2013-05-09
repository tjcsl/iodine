<?php
/**
* Just contains the definition for the class {@link Logging}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package core
* @subpackage Error
* @filesource
*/
class UnregisterableCallback{

    // Store the Callback for Later
    private $callback;

    // Check if the argument is callable, if so store it
    public function __construct($callback)
    {
        if(is_callable($callback))
        {
            $this->callback = $callback;
        }
        else
        {
            throw new InvalidArgumentException("Not a Callback");
        }
    }

    // Check if the argument has been unregistered, if not call it
    public function call()
    {
        if($this->callback == false)
            return false;

        $callback = $this->callback;
        $callback(); // weird PHP bug
    }

    // Unregister the callback
    public function unregister()
    {
        $this->callback = false;
    }
}
/**
* The logging module for Iodine.
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
	private $screen_debug;

	/**
	* The file to use for debug logging
	*/
	private $debug_log;
	
	/**
	* The file to use for access logging
	*/
	private $access_log;
	
	/**
	* The file to use for error logging
	*/
	private $error_log;
	
	/**
	* The file to use for auth logging
	*/
	private $auth_log;
	
	/**
	* The default debug level for log_debug calls without a debug level
	*/
	private $default_debug_level;
	
	/**
	* The debug level below which messages should be logged
	*/
	private $debug_loglevel;
	
	/**
	* Wheter or now profiling output should be shown
	*/
	private $debug_profile;

	/**
	* The Logging class constructor.
	* 
	* @access public
	*/
	public function __construct() {
		global $I2_ERR, $I2_LOG_SHUTDOWN;

		$this->my_email = i2config_get('email', 'iodine-errors@tjhsst.edu', 'logging');
	
		/* If not defined in config.ini, the log paths are absolute. */
		$log_dir = i2config_get('log_dir', NULL, 'logging');
		if($log_dir === NULL) {
			$this->debug_log = i2config_get('debug_log');
			$this->access_log = i2config_get('access_log');
			$this->error_log = i2config_get('error_log');
			$this->auth_log = i2config_get('auth_log');
		} else { 
			$this->debug_log = $log_dir . i2config_get('debug_log','iodine-debug.log','logging');
			$this->access_log = $log_dir . i2config_get('access_log','iodine-access.log','logging');
			$this->error_log = $log_dir . i2config_get('error_log','iodine-error.log','logging');
			$this->auth_log = $log_dir . i2config_get('auth_log','iodine-auth.log','logging');
		}
		

		if (!file_exists($this->access_log) && !file_put_contents($this->access_log,"Iodine Access Log created at: ".date('d/M/Y:H:i:s O')."\n")) {
			$I2_ERR->fatal_error('The main iodine access log cannot be accessed.');
		}
		$this->log_access();
		if (!file_exists($this->debug_log) && !file_put_contents($this->debug_log,"Iodine Debug Log created at: ".date('d/M/Y:H:i:s O')."\n")) {
			$I2_ERR->fatal_error('The main iodine debug log cannot be accessed.');
		}
		if (!file_exists($this->error_log) && !file_put_contents($this->error_log,"Iodine Error Log created at: ".date('d/M/Y:H:i:s O')."\n")) {
			$I2_ERR->fatal_error('The main iodine error log cannot be accessed.');
		}
		if (!file_exists($this->auth_log) && !file_put_contents($this->auth_log,"Iodine Auth Log created at: ".date('d/M/Y:H:i:s O')."\n")) {
			$I2_ERR->fatal_error('The main iodine authentication log cannot be accessed.');
		}
		$this->default_debug_level = i2config_get('default_debug_level', 0, 'logging');
		$this->debug_loglevel = i2config_get('debug_loglevel', 9, 'logging');
		$this->debug_profile = i2config_get('debug_profile', false, 'logging');
		$this->screen_debug = i2config_get('screen_debug', 1, 'logging');
		

		$I2_LOG_SHUTDOWN = new UnregisterableCallback(array($this, 'flush_debug_output'));

		register_shutdown_function(array($I2_LOG_SHUTDOWN, "call"));
		
	}

	/**
	* Records an entry in the access log. This is called on every page load
	* that does not crash before the Logging object is instantiated.
	*
	* The format for the access log is
	* 'IP - username - [Apache-style date format] "Request" "Referrer" "User-Agent"'.
	*/
	public function log_access() {
		file_put_contents($this->access_log,
			$_SERVER['REMOTE_ADDR'] . ' - ' .
			(isset($_SESSION['i2_username'])?$_SESSION['i2_username']:'not_logged_in') . ' - [' .
			date('d/M/Y:H:i:s O') . '] "' .
			$_SERVER['REQUEST_URI'] . '" "' .
			(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'') . '" "' .
			(isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'') . '"' ."\n",
			FILE_APPEND
		);
	}

	/**
	* Records an entry in the error log.
	*
	* Records an error message in the error log, and outputs the error to
	* the screen. The format in the error log file is
	* 'IP - [Apache-style date format] [Mini-backtrace] "Request" "Error"'.
	*
	* @param string $msg The error message to record.
	*/
	public function log_error($msg) {
		$trace_arr = array();
		foreach(array_slice(debug_backtrace(),1) as $trace) {
			if (isSet($trace['file']) && isSet($trace['line'])) {
				$trace_arr[] = basename($trace['file'],'.php5') .':'. $trace['line'];
			} else if (isSet($trace['line'])) {
				$trace_arr[] = 'Unknown file:'. $trace['line'];	
			} else {
				$trace_arr[] = 'Unknown file: Unknown line';
			}
		}
		
		file_put_contents($this->error_log,
			$_SERVER['REMOTE_ADDR'] . ' - [' .
			@date('d/M/Y:H:i:s O') . '] [' .
			implode($trace_arr, ',') . '] "' .
			$_SERVER['REQUEST_URI'] . '" "' .
			$msg . '"' ."\n",
			FILE_APPEND
		);

		$this->error_buf .= "\r\n<p>$msg</p>";
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
			$level = $this->default_debug_level;
		}
		if(is_int($level)) {
			if ($level > $this->debug_loglevel) {
				return;
			}
			if ($this->screen_debug) {
				$this->log_screen('Level '.$level.' debug: '.$msg);
			} else {
				$this->log_file($msg,$level);
			}
		} else {
			if ($level == 'P' && $this->debug_profile)
				if ($this->screen_debug) {
					$this->log_screen('Level '.$level.' debug: '.$msg);
				} else {
					$this->log_file($msg,$level);
				}
		}
	}

	/**
	 * Auth logging.
	 *
	 * Logs messages to the "log_auth" file.
	 *
	 * @param string $msg The message to log.
	 */
	function log_auth($msg) {
		file_put_contents($this->auth_log,
			$msg."\n",
			FILE_APPEND
		);
	}

	/**
	* Logs directly to a file
	*/
	public function log_file($msg,$level=NULL) {
		if ($level === NULL) { /* If not set, get default debug level */
			$level = $this->default_debug_level;
		}
		if ($level > $this->debug_loglevel) {
			return;
		}
		file_put_contents($this->error_log,
			$msg."\n",
			FILE_APPEND
		);
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
			$msg = htmlspecialchars($msg);
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
	* the applicatioan just dies halfway through.
	*/
	public function flush_debug_output() {
		global $I2_DISP,$I2_API,$I2_ARGS,$module;
		
		if(isset($I2_API)) {
			$I2_API->startElement('error');
			$I2_API->writeRaw($this->error_buf);
			$I2_API->endElement();
			$I2_API->startElement('debug');
			$I2_API->writeRaw($this->debug_buf);
			$I2_API->endElement();
			$I2_API->endElement();
			$I2_API->endDocument();
			echo $I2_API->outputMemory();
			$this->error_buf = NULL;
			$this->debug_buf = NULL;
			return;
		}
		// don't bother setting up Display if we're logging out
		if(isset($I2_ARGS[0]) && $I2_ARGS[0] == 'logout')
			return;
		try {
			if( ! isset($I2_DISP) ) {
				$I2_DISP = new Display();
			}
			//if(date("Mj")=="Apr1")
			//	$this->error_buf="<br />Error: ".exec('/usr/games/fortune bofh-excuses')."\r\n".$this->error_buf;
			$I2_DISP->disp('error_debug.tpl', array('errors' => $this->error_buf, 'debug' => $this->debug_buf));
			$I2_DISP->flush_buffer();
			$this->error_buf = NULL;
			$this->debug_buf = NULL;
			return;
		}
		catch( Exception $e ) {
			/* Error in standard output, so just print things, no
			** need to actually do anything in this block
			*/
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
