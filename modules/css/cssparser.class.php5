<?php
/**
 * Parses a string of CSS to return a nested array of rulesets.
 * This module will work with most legal CSS, except it will not honor
 * misplacement of <tt>!important</tt> rules and might not accept missing
 * semicolons. It does however, accept missing close braces.
 * @author	Joshua Cranmer <jcranmer@tjhsst.edu>
 * @copyright	2007 The Intranet 2 Development Team
 * @package	modules
 * @subpackage	CSS
 */
class CSSParser {
	private $rulesets;
	private $css;
	static $uniqueid = 0; // Unique id for non-repeated elements

	/**
	 * Reads and parses the given CSS string.
	 * @param string $css legal css string.
	 */
	public function __construct($css) {
		// Get rid of comments
		$css = preg_replace("/\/\*.*?\*\//s", '', $css);
		// Get rid of CDO/CDC (read CSS &#167; 4 for more information). 
		$css = preg_replace('/<!--|-->/', '', $css);
		// Collapse all whitespace
		$css = preg_replace("/[ \t\r\n\f]+/", ' ', $css);
		$css = trim($css, " \t\r\n\f");

		$this->css = $css;
		$this->rulesets = $this->readRuleset();
		unset ($this->css);
	}

	/**
	 * Returns an array of the parsed CSS.
	 * Each key represents either a selector or a @-rule that has the same
	 * members through recursive tree hiearchy.
	 * For example:
	 * Array (
	 *    [table#foo] => Array (
	 *         [display] => none,
	 *         [padding] => 1px
	 *    )
	 *    [@media print] => Array (
	 *         [foo] => Array (
	 *             [color] => #abcdef;
	 *         )
	 *    )
	 * )
	 * @return array 
	 */
	public function parsed() {
		return $this->rulesets;
	}

	private function readRuleset() {
		$ruleset = [];
		while (substr($this->css, 0, 1) != '}' && strlen($this->css) > 0) {
			$rules = [];
			$brace = CSSParser::findString($this->css, '{');
			$rule = trim(substr($this->css, 0, $brace), ' ');
			$this->css = trim(substr($this->css, $brace+1), ' ');
			if (substr($rule, 0, 1) == '@' && !(substr($rule,0,10) == '@font-face')) {
				$rules = $this->readRuleset();
			} else {
				$keeprepeats=false;
				while (substr($this->css, 0, 1) != '}') {
					$temp = $this->readRule();
					// This is where the intranet-specific property 'repeat' comes
					// in. This is because while the CSS spec gives absolutely
					// no reason to have this, vendor-specific values for some
					// fields require that several values be stored for that field.
					// Therefore, we prefix each field with a unique number, and 
					// remove it when printing.
					if($temp[0]=='repeat'){
						if($temp[1]=='keep') 
							$keeprepeats=true;
						elseif($temp[1]=='normal')
							$keeprepeats=false;
					}
					else {
						if($keeprepeats) {
							$rules['/*'.(CSSParser::$uniqueid).'*/'.$temp[0]] = $temp[1];
							CSSParser::$uniqueid=CSSParser::$uniqueid+1;
						} else
							$rules[$temp[0]] = $temp[1];
					}
				}
				$this->css = trim(substr($this->css, 1), ' ');
				//We can have multiple custom font faces, but they all have the same
				//rule name normally, so we cheat a little and add a comment
				if(substr($rule,0,10) == '@font-face') {
					$rule=$rule.' /*'.$rules['font-family'].'|'.$rules['src'].'*/';
				}
			}
			// Don't just nuke the old rules, have them overwrite (even if it's in the same file)
			if(isset($ruleset[$rule]))
				$ruleset[$rule] = array_merge($ruleset[$rule],$rules);
			else
				$ruleset[$rule] = $rules;
		}
		$this->css = trim(substr($this->css, 1), ' ');
		return $ruleset;
	}

	/**
	 *  Returns the first instance of the string that is not quoted.
	 *  Examples:<br>
	 *     <tt>findString('test {', '{')</tt> returns 5.<br>
	 *     <tt>findString('not here', '{')</tt> returns -1.<br>
	 *     <tt>findString('" { } " {', '{')</tt> returns 8.<br>
	 *     <tt>findString('"\"{\"{" {', '{')</tt> returns 9.<br>
	 */
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
