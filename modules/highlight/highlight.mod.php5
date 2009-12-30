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
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_FS_ROOT, $I2_ARGS, $I2_USER;

		if (!$I2_USER->is_group_member('admin_source')) {
			throw new I2Exception('You are not authorized to view application source through this module.  Please contact the Intranet 2 Development Team.');
		}

		$filename = '';
		$linenum = intval($I2_ARGS[1]);
		foreach (array_slice($I2_ARGS, 2) as $arg) {
			$filename .= '/' . $arg;
		}
		$filename = realpath($filename);

		if (!$filename) {
				  throw new I2Exception('No such file!');
		}

		if (strpos($filename,$I2_FS_ROOT) != 0) {
				  throw new I2Exception('Highlight cannot be used to read arbitrary files from the server!');
		}
		
		if (strpos($filename,'config.ini')) {
				  throw new I2Exception('Highlight cannot be used to read the server config file!');
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
