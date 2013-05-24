<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage CSS
* @filesource
* Handles css rules
*/

/**
* @package modules
* @subpackage CSS
* Handles css rules
*/
class CSSRule {
	
	private $selectors = [];
	
	private $properties = [];

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
		global $I2_ROOT;
		$value = preg_replace('/url\("?([^")]*)"?\)/', "url(\"{$I2_ROOT}\$1\")", $value);
		$this->properties[$key] = $value;
	}

	public function get_property($key) {
		return $this->properties[$key];
	}
}

?>
