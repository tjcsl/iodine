<?php
/**
* Just contains the definition for the {@link Module} Easteregg.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Info
* @filesource
*/

/**
* A module that does virtually nothing.
* @package modules
* @subpackage Easteregg
*/
class Easteregg implements Module {

	/**
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_box($disp) {
		return FALSE;
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_pane($disp) {
		global $I2_ARGS;
		
		if ($I2_ARGS[1] == 't3h_Ub3r1337_h4x') {
			$disp->disp('realegg.tpl');
		}
		else {
			$disp->disp('easteregg_pane.tpl');
		}
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	function get_name() {
		return 'info';
	}
	/**
	* Performs all initialization necessary for this module to be 
	* displayed in an ibox.
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
	* @abstract
	*/
	function init_box() {
		return FALSE;
	}

	/**
	* Performs all initialization necessary for this module to be
	* displayed as the main page.
	*
	* @returns mixed Either a string, which will be the title for both the
	*                main pane and for part of the page title, or an array
	*                of two strings: the first is part of the page title,
	*                and the second is the title of the content pane. To
	*                specify no titles, return an empty array. To specify
	*                that this module has no main content pane (and will
	*                show an error if someone tries to access it as such),
	*                return FALSE.
	* @abstract
	*/
	function init_pane() {
		return 'Easter Egg!';
	}

	/**
	* Returns whether this module functions as an intrabox.
	*
	* @returns boolean True if the module has an intrabox, false if it does not.
	*
	* @abstract
	*/
	function is_intrabox() {
		return FALSE;
	}
	
}
?>
