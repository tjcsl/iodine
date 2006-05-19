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

	private $code;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS;
		return FALSE;

		$filename = "";
		$linenum = intval($I2_ARGS[1]);
		foreach (array_slice($I2_ARGS, 2) as $arg) {
			$filename .= "/" . $arg;
		}
		$array = explode('<br />', highlight_file($filename, TRUE));
		$num = 1;
		foreach ($array as &$line) {
			if ($num == $linenum) {
				$line = "<a name='$linenum'></a><b style='background:#CCCCCC'><font color='black'>" . $num . ": </font>" . $line . "</b>";

			} else {
				$line = "<font color='#666666'>" . $num . ": </font>" . $line;
			}
			$num++;
		}
		$this->code = implode("<br />\n", $array);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		Display::stop_display();
		echo $this->code;
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
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Highlight";
	}
}

?>
