<?php
/**
* @package core
* @subpackage Api
* Contains Api support
*/

/**
* @package core
* @subpackage Api
* Contains Api support
*/
class Api extends XMLWriter {

	/**
	 * @private logging is logging on?
	 */
	private $logging = true;

	function disable_logging() {
		$this->logging = false;
	}

	function get_logging() {
		return $this->logging;
	}
	function flush_api() {
		$this->endElement();
		$this->endDocument();
		echo $this->outputMemory();
	}
}
?>
