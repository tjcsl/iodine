<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Util
* @filesource
*/

/**
* @package modules
* @subpackage Util
*/
class Util implements Module {

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($disp) {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp('util_pane.tpl');
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'util';
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
	function init_pane() {
		global $I2_ARGS;
	
		if (count($I2_ARGS) > 1) {
			switch ($I2_ARGS[1]) {
				case 'config':
					$root_path = i2config_get('root_path', NULL, 'core');
					d('Copying ' . $root_path . 'config.server.ini to ' . $root_path . 'config.ini');
					if (copy($root_path . 'config.server.ini', $root_path . 'config.ini') === FALSE) {
						throw new I2Exception("Could not copy config.server.ini to config.ini");
					}
					break;
			}
		}

		return "Iodine Utilities";
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function is_intrabox() {
		return FALSE;
	}
	
}
?>
