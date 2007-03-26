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
class StyleSheet {

	private $rulesets = array();
	public function replace_rule(CSSRule $rule, CSSBlock $ruleset) {
		$set =& $this->get_ruleset($ruleset);
		foreach ($rule->get_selectors() as $selector) {
			$set[$selector] = $rule->get_properties();
		}	
	}

	public function extend_rule(CSSRule $rule, CSSBlock $ruleset) {
		$set =& $this->get_ruleset($ruleset);
		foreach ($rule->get_selectors() as $selector) {
			if (!array_key_exists($selector, $set))
				$set[$selector] = $rule->get_properties();
			else {
				$set[$selector] = array_merge($set[$selector], $rule->get_properties());
			}
		}
	}

	private function &get_ruleset($ruleset) {
		if ($ruleset->get_parent() == NULL) {
			return $this->rulesets;
		} else {
			$arr =& $this->get_ruleset($ruleset->get_parent());
			if (!isset($arr[$ruleset->get_name()]))
				$arr[$ruleset->get_name()] = array();
			return $arr[$ruleset->get_name()];
		}
	}
	public function __toString() {
		return $this->print_ruleset($this->rulesets);
	}

	private function print_ruleset($ruleset) {
		$str = '';
		foreach ($ruleset as $key => $value) {
			if (substr($key, 0, 1) == '@')
				$str .= $key . " {\n" . $this->print_ruleset($value) . "}\n\n";
			else {
				$str .= $key . " {";
				foreach ($value as $property => $datum) {
					$str .= "\n\t$property: $datum;";
				}
				$str .= "\n}\n\n";
			}
		}
		return $str;
	}

}

?>
