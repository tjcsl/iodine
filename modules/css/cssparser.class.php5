<?php
/**
 * Parses a string of CSS to return proper stuff.
 */
class CSSParser {
	public $rulesets;
	private $css;
	public function __construct($css) {
		// Get rid of comments
		$css = preg_replace("/\/\*.*?\*\//s", '', $css);
		// Get rid of CDO/CDC
		$css = preg_replace('/<!--|-->/', '', $css);
		// Collapse all whitespace
		$css = preg_replace("/[ \t\r\n\f]+/", ' ', $css);
		$css = trim($css, " \t\r\n\f");

		$this->css = $css;
		$this->rulesets = $this->readRuleset();
		unset ($this->css);
	}

	private function readRuleset() {
		$ruleset = array();
		while (substr($this->css, 0, 1) != '}' && strlen($this->css) > 0) {
			$rules = array();
			$brace = CSSParser::findString($this->css, '{');
			$rule = trim(substr($this->css, 0, $brace), ' ');
			$this->css = trim(substr($this->css, $brace+1), ' ');
			if (substr($rule, 0, 1) == '@') {
				$rules = $this->readRuleset();
			} else {
				while (substr($this->css, 0, 1) != '}') {
					$temp = $this->readRule();
					$rules[$temp[0]] = $temp[1];
				}
				$this->css = trim(substr($this->css, 1), ' ');
			}
			$ruleset[$rule] = $rules;
		}
		$this->css = trim(substr($this->css, 1), ' ');
		return $ruleset;
	}

	static function findString($css, $s) {
		$pos = 0;
		$inString = false;
		$prevSlash = false;
		while ($pos < strlen($css)) {
			$char = substr($css, $pos++, 1);
			switch ($char) {
			case '\\':
				$prevSlash = true;
				continue;
			case '"':
				if ($prevSlash) {
					$prevSlash = false;
					break;
				}
				$inString = !$inString;
				break;
			case $s:
				if (!$inString)
					return $pos-1;
			default:
				$prevSlash = false;
			}
		}
		return -1;
	}
	private function readRule() {
		$colon = CSSParser::findString($this->css, ':');
		$rule = trim(substr($this->css, 0, $colon), ' ');
		$this->css = trim(substr($this->css, $colon+1), ' ');
		$semicolon = CSSParser::findString($this->css, ';');
		$property = trim(substr($this->css, 0, $semicolon));
		$this->css = trim(substr($this->css, $semicolon+1));
		return array($rule, $property);
	}
}
?>
