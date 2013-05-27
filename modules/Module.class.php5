<?php
/**
* Just contains the definition for the class {@link Module}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Module
* @filesource
*/

/**
* The API for all Intranet2 modules to extend.
* @package core
* @subpackage Module
*/
abstract class Module {

	/**
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_box($disp) {
		return FALSE;
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		return FALSE;
	}
	
	/**
	* Displays a version of the module designed for small screens.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Returns text to be displayed on a cli.
	*
	* @returns boolean FALSE if the module lacks a cli.
	*/
	function display_cli() {
		return FALSE;
	}

	/**
	* Returns text to be displayed for the api.
	*
	* @returns boolean FALSE if the module lacks a api.
	*/
	function api() {
		return FALSE;
	}

	/**
	* Returns text to be displayed for ajax.
	*
	* @returns boolean FALSE if the module lacks a ajax support.
	*/
	function ajax() {
		return FALSE;
	}


	/**
	* Returns DTD for the api response.
	*
	* @returns boolean TRUE if the module has a dtd, FALSE otherwise.
	*/
	function api_build_dtd() {
		return FALSE;
	}

	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	abstract function get_name();

	/**
	* Performs all initialization necessary for this module to be 
	* displayed in an ibox.
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
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
		return FALSE;
	}
	
	/**
	* Performs all initialization necessary for this module to be 
	* displayed in a small browser.
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module can't be displayed
	*                 on a small screen.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Performs all initialization necessary for cli display
	*
	* @returns string The title of the command, otherwise FALSE if it
	* 		  isn't ready for the cli.
	*/
	function init_cli() {
		return FALSE;
	}
}
?>
