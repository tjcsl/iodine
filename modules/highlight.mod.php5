<?php
/**
* Just contains the definition for the class {@link Highlight}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Highlight
* @filesource
*/

/**
* The module that keeps the eighth block office happy.
* @package modules
* @subpackage Highlight
*/
class Highlight implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template = "highlight.tpl";

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS;

		$args = array();
		//if(count($I2_ARGS) == 1) {
		$filename = "";
		$linenum = intval($I2_ARGS[1]);
		foreach (array_slice($I2_ARGS, 2) as $arg) {
			$filename .= "/" . $arg;
			echo $arg . "\n";
		}
		$code = highlight_file($filename, TRUE);
		$code = explode('<br />', $code);
		$num = 1;
		foreach ($code as &$line) {
			if ($num == $linenum) {
				$line = "<a name='$linenum'></a><b style='background:#CCCCCC'><font color='black'>" . $num . ": </font>" . $line . "</b>";

			} else {
				$line = "<font color='#666666'>" . $num . ": </font>" . $line;
			}
			$num++;
		}
		$code = implode("<br />\n", $code);
		//	$I2_ARGS[1] = urlencode("/home/sgross/cvs/intranet2/modules/mysql.class.php5");
			$this->template_args = array("code" => $code);
		//}
		return array("Source Code Highlighter", "Highlight");
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Highlight";
	}
}

?>
