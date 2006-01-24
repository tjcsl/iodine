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

	private $filename;

	function __construct($filename) {
		$this->filename = $filename;
	}

	public function add_selector($selector) {
		$this->selectors[] = $selector;
	}
	
	public function add_selectors($selectors) {
		$this->selectors = array_merge($this->selectors, $selectors);
	}

	public function get_filename() {
		return $this->filename;
	}

	public function get_selectors() {
		return $this->selectors;
	}

	public function get_properties() {
		return $this->properties;
	}

	public function is_empty() {
		return sizeof($this->selectors) == 0;
	}

	public function remove_selector($selector) {
		$key = array_search($selector, $this->selectors);
		unset($this->selectors[$key]);
	}

	public function remove_selectors($selectors) {
		$this->selectors = array_diff($this->selectors, $selectors);
	}
	
	public function set_property($key, $value) {
		$value = preg_replace('/url\((.*)\)/', "url({$GLOBALS['I2_ROOT']}\$1)", $value);
		$this->properties[$key] = $value;
	}
	
	public function set_properties($properties) {
		$this->properties = array_merge($this->properties, $properties);
	}

	public function set_selectors($selectors) {
		$this->selectors = $selectors;
	}

	public function __toString() {
		$str = implode(', ', $this->selectors);
		$str .= " {\n";
		foreach ($this->properties as $key => $value) {

			$str .= "\t$key: $value;\n";
		}
		$str .= "}\n";
		return $str;
	}

}

?>
