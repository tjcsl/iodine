<?php
/**
* Just contains the definition for the class {@link News}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Id: studentdirectory.mod.php5,v 1.1 2005/07/14 13:08:11 vmircea Exp $
* @package modules
* @subpackage StudentDirectory
* @filesource
*/

/**
* This module helps you find info on your fellow classmates, addresses, classes,
* etc.
* @package modules
* @subpackage StudentDirectory
* @todo Actually make this work
* @todo Decide on a system for parental permission (on student directory info) and then make the mysql structure for it
*/
class StudentDirectory implements Module {
	
	/**
	* The display object to use
	*/
	private $display;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL;
		return true;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp('studentdirectorypane.tpl',$this->information));
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return true; // right now we don't need to get any initial values, the box will just contain a form like the old intranet for queries
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('studentdirectorybox.tpl');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "StudentDirectory";
	}
}

?>
