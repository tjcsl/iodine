<?php
/**
* Just contains the definition for the {@link Module} toggleheader.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Module
* @filesource
*/

/**
* A module which toggles the visibility of the header bar.
* @package core
* @subpackage Utility
*/
class toggleheader implements Module {

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
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	function display_box($disp) {
	}
	
	function display_pane($disp) {
	}
	
	function get_name() {
		return 'toggleheader';
	}

	function init_box() {
		return FALSE;
	}

	function init_pane() {
		global $I2_ROOT,$I2_ARGS,$I2_USER;
		$dest = null;
		d("Args:".print_r($I2_ARGS,1));
		if ($I2_USER->header != 'FALSE') {
			$I2_USER->header = 'FALSE';
		} else {
			$I2_USER->header = 'TRUE';
		}
		if (count($I2_ARGS) == 1) {
			$dest = '';
		} else {
			$dest = implode('/',array_slice($I2_ARGS,1));
		}
		redirect($dest);
		//throw new I2Exception("Failed to redirect to $dest");
		return;
	}
}
?>
