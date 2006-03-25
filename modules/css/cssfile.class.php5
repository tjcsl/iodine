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
class CSSFile {

	private $name;

	private $rules = array();

	function __construct($name) {
		$this->name = $name;
	}

	public function add_rule(CSSRule $rule) {
		$this->rules[] = $rule;
	}
	
	public function remove_rule(CSSRule $rule) {
		foreach ($this->rules as $key => $value) {
			if ($value == $rule) {
				unset($this->rules[$key]);
			}
		}
	}

	public function __toString() {
		$str = "/* $this->name */\n";
		foreach ($this->rules as $rule) {
			$str .= $rule->__toString() . "\n";
		}
		return $str;
	}

}

?>
