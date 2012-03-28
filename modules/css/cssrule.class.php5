<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage CSS
* @filesource
*/

/**
* @package modules
* @subpackage CSS
*/
class CSSRule {
	
	private $selectors = array();
	
	private $properties = array();

	public function add_selector($selector) {
		$this->selectors[] = $selector;
	}
	
	public function get_selectors() {
		return $this->selectors;
	}

	public function get_properties() {
		return $this->properties;
	}

	public function set_property($key, $value) {
		$value = preg_replace('/url\("?([^"]*)"?\)/', "url(\"{$GLOBALS['I2_ROOT']}\$1\")", $value);
		$this->properties[$key] = $value;
	}

	public function get_property($key) {
		return $this->properties[$key];
	}
}

?>
