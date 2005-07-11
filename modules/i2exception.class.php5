<?php
/**
* Contains the definition for the I2Exception class for Iodine.
*
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @version $Id: i2exception.class.php5,v 1.3 2005/07/11 05:16:36 adeason Exp $
* @package core
* @subpackage Error
* @filesource
*/

/**
* The standard Exception class for Iodine. Use this for raising errors.
*
* @package core
* @subpackage Error
*/
class I2Exception extends Exception {

	/**
	* Whether or not this exception has been deemed 'critical'
	*/
	protected $critical = FALSE;

	/**
	* Constructor.
	*
	* This constructor just takes a message and a critical flag, which is
	* just true or false. If the critical flag is true, an email is sent
	* out to the developers, whether the Error is handled or not.
	*
	* @param string $message A message describing the exception.
	* @param bool $critical Whether or not this is critical enough to send
	*                       to the developers.
	*/
	public function __construct($message, $critical = FALSE) {
		global $I2_LOG;
		
		parent::__construct($message, 0);
		
		$this->critical = $critical;
		if ($critical) {
			$I2_LOG->log_mail('Critical Iodine exception raised: '.$this);
		}
	}

	/**
	* Converts the exception into a string.
	*
	* This just returns a string describing the exception: if it is
	* 'critical', the message, and a backtrace from where the exception
	* originated (file name and line number).
	*
	* @return string The exception in the form of a string.
	*/
	public function __toString() {
		$str = ($this->critical?'Critical ':'') . 'I2 Exception: '.$this->message;
		$str .= "\r\n\r\nBacktrace:\r\n";
		
		$trace = $this->getTrace();
		
		foreach($trace as $level) {
			$str .= $level['file'].':'.$level['line']."\r\n";
		}
		
		return $str;
	}
}

?>
