<?php
/**
* The API for all Intranet2 modules to extend.z
*/
interface Module {

	/**
	* Displays all of a module's ibox content.
	*
	* @param object $disp The Display object to use for output.
	* @abstract
	*/
	function displayBox($disp);
	
	/**
	* Displays all of a module's main content.
	*
	* @param object $disp The Display object to use for output.
	* @abstract
	*/
	function display($disp);
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	function getName();

	/**
	* Performs all initialization necessary for this module to be 
	* displayed in an ibox.
	*
	* @abstract
	*/
	function initBox();

	/**
	* Performs all initialization necessary for this module to be
	* displayed as the main page.
	*
	* @abstract
	*/
	function init();
	
}
?>
