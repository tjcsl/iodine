<?php
/**
* Contains the definition for the I2Exception class for Iodine.
*
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @version $Id: i2exception.class.php5,v 1.7 2005/09/27 02:23:58 sgross Exp $
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

	//protected $I2_ROOT = i2config_get('www_root', 'https://iodine.tjhsst.edu/','core');
	
	/**
	* Whether or not this exception has been deemed 'critical'.
	* @var bool
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
	$I2_ROOT = i2config_get('www_root', 'https://iodine.tjhsst.edu/','core');
		$str = ($this->critical?'Critical ':'') . 'I2 Exception: '.$this->message;
		$str .= "<br />\r\n<br />\r\nBacktrace:<br />\r\n";
		
		$trace = $this->getTrace();
		
		foreach($trace as $level) {
			$file = $level['file'];
			$line = $level['line'];
			$str .= "<a href='" . $I2_ROOT . "highlight/$line$file#$line'>" . $file .':'.$line ."</a><br />\r\n";
		}
		
		return $str;
	}
}

?>
