<?php
/**
* Just contains the definition for the class {@link Feeds}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage RSS
* @filesource
*/

/**
* The module that handles unauthenticated feeds.
* @package modules
* @subpackage Feeds
*/
class Feeds implements Module {
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
		return FALSE;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		return FALSE;
	}

	public static function update() {
		RSS::update();
		ATOM::update();
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
		return "Feeds";
	}
}

?>
