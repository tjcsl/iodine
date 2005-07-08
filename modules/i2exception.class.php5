<?php
/**
* Contains the definition for the I2Exception class for Iodine.
*
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

	protected $critical;

	public function __construct($message, $critical = FALSE) {
		global $I2_LOG;
		
		parent::__construct($message, 0);
		
		$this->critical = $critical;
		if ($critical) {
			$I2_LOG->log_mail('Critical Iodine exception raised: '.$this);
		}
	}
	public function __toString() {
		$str = $this->message;
		$str .= "\r\n\r\nBacktrace:\r\n";
		
		$trace = $this->getTrace();
		
		foreach($trace as $level) {
			$str .= $level['file'].':'.$level['line']."\r\n";
		}
		
		return $str;
	}
}

?>
