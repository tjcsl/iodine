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

	private $files = array();

	private $selectors = array();
	
	public function replace_rule(CSSRule $rule) {
		foreach ($rule->get_selectors() as $selector) {
			if (isset($this->selectors[$selector])) {
				$old_rule = $this->selectors[$selector];
				$old_rule->remove_selector($selector);
				if ($old_rule->is_empty()) {
					$this->files[$old_rule->get_filename()]->remove_rule($old_rule);
				}
			}
			$this->selectors[$selector] = $rule;
		}
		$this->add_rule_to_file($rule);
	}

	public function extend_rule(CSSRule $rule) {
		$selectors = $rule->get_selectors();
		$trues = array_fill(0,  sizeof($selectors), TRUE);
		$remaining = array_combine($selectors, $trues);
		
		foreach ($selectors as $selector) {
			if (!$remaining[$selector]) {
				continue;
			}
			
			//Extend previously defined selectors
			if (isset($this->selectors[$selector])) {
				$old_rule = $this->selectors[$selector];
				$matching = array_intersect($selectors, $old_rule->get_selectors());
				$old_rule->remove_selectors($matching);
				if ($old_rule->is_empty()) {
					$this->files[$old_rule->get_filename()]->remove_rule($old_rule);
				}
				$new_rule = new CSSRule($rule->get_filename());
				$new_rule->add_selectors($matching);
				$new_rule->set_properties($old_rule->get_properties());
				$new_rule->set_properties($rule->get_properties());
				$this->add_rule_to_file($new_rule);
				foreach ($matching as $match) {
					$this->selectors[$match] = $new_rule;
					$remaining[$match] = FALSE;
				}
			}
		}

		$remaining = array_filter($remaining);
		if (count($remaining) > 0) {
			$rule->set_selectors(array_keys($remaining));
			$this->add_rule_to_file($rule);
		}
	}

	private function add_rule_to_file($rule) {
		$filename = $rule->get_filename();
		//Create a CSSFile for this rule if one does not already exist
		if (!isset($this->files[$filename])) {
			$this->files[$filename] = new CSSFile($filename);
		}
		//Add this rule to its CSSFile
		$this->files[$filename]->add_rule($rule);
	}
	
	public function __toString() {
		$str = '';
		foreach ($this->files as $file) {
			$str .= $file->__toString();
		}
		return $str;
	}

}

?>
