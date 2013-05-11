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
	 * @public logging is logging on?
	 */
	public $logging = true;

	/**
	 * @public api is this a api request?
	 */
	public $api = false;

	function flush_api() {
		$this->endElement();
		$this->endDocument();
		echo $this->outputMemory();
	}
	function init() {
		register_shutdown_function(array($this,'flush_api'));
		header('Content-Type: application/xml');
		$this->api = true;
		$this->openMemory();
		$this->setIndent(true);
		$this->startDocument('1.0','ISO-8859-1');
	}
}
?>
