<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage CSS
* @filesource
* Contains helper methods for CSS
*/

/**
* @package modules
* @subpackage CSS
* Contains helper methods for CSS
*/
class StyleSheet {

	private $rulesets = [];
	//private $currentAdd = [];

	/**
	 * Tells the class that a new CSS file is being parsed.
	 * The purpose of knowing this is to avoid overwriting when a selector
	 * is used with two different rules in the same file.
	 */
	public function newFile() {
	//	$this->currentAdd = [];
	}

	/**
	 * Adds a rule (potentially replacing it) to the stylesheet within the
	 * given ruleset.
	 */
	public function replace_rule(CSSRule $rule, CSSBlock $ruleset) {
		$set =& $this->get_ruleset($ruleset);
		foreach ($rule->get_selectors() as $selector) {
		//	if (in_array($selector, $this->currentAdd))
		//		$set[$selector] = array_merge($set[$selector], 
		//			$rule->get_properties());
		//	else
				$set[$selector] = $rule->get_properties();
		//	$this->currentAdd[] = $selector;
		}	
	}

	/**
	 * Adds a rule (appending if necessary) to the stylesheet within the
	 * given ruleset.
	 */
	public function extend_rule(CSSRule $rule, CSSBlock $ruleset) {
		$set =& $this->get_ruleset($ruleset);
		foreach ($rule->get_selectors() as $selector) {
			if (!array_key_exists($selector, $set))
				$set[$selector] = $rule->get_properties();
			else {
				$set[$selector] = array_merge($set[$selector],
					$rule->get_properties());
			}
		//	$this->currentAdd[] = $selector;
		}
	}

	private function &get_ruleset($ruleset) {
		if ($ruleset->get_parent() == NULL) {
			return $this->rulesets;
		} else {
			$arr =& $this->get_ruleset($ruleset->get_parent());
			if (!isset($arr[$ruleset->get_name()]))
				$arr[$ruleset->get_name()] = [];
			return $arr[$ruleset->get_name()];
		}
	}

	/**
	 * Returns a proper CSS file that has all of the rules within the
	 * stylesheet.
	 */
	public function __toString() {
		return $this->print_ruleset($this->rulesets);
	}

	private function print_ruleset($ruleset, $indent='') {
		$str = '';
		foreach ($ruleset as $key => $value) {
			if (substr($key, 0, 1) == '@'  && !(substr($key,0,10) == '@font-face'))
				$str .= $indent . $key . " {\n" .
					$this->print_ruleset($value,$indent."\t") . "$indent}\n\n";
			else {
				$str .= $indent . $key . " {";
				foreach ($value as $property => $datum) {
					if($property[0] == '/') {
						$property = substr(strrchr($property,'/'),1);
					}
					$str .= "\n$indent\t$property: $datum;";
				}
				$str .= "\n$indent}\n\n";
			}
		}
		return $str;
	}

}

?>
