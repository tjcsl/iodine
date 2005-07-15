<?php
/**
* Just contains the definition for the class {@link StudentDirectory}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Id: studentdirectory.mod.php5,v 1.4 2005/07/14 20:50:21 adeason Exp $
* @package modules
* @subpackage StudentDirectory
* @filesource
*/

/**
* This module helps you find info on your fellow classmates, addresses, classes,
* etc.
* @package modules
* @subpackage StudentDirectory
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
		global $I2_SQL,$I2_ARGS,$I2_USER;
		$user = isset($I2_ARGS[1]) ? new User($I2_ARGS[1]) : $I2_USER;
		if( ($this->information = $user->info()) === FALSE ) {
			return array('Error', 'Error: Student does not exist');
		}
		return array('Student Directory: '.$this->information['fname'].' '.$this->information['lname'], $this->information['fname'].' '.$this->information['lname']);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp('studentdirectory_pane.tpl',array('info'=>$this->information));
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return 'Search the Student Directory'; // right now we don't need to get any initial values, the box will just contain a form like the old intranet for queries
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('studentdirectory_box.tpl');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'StudentDirectory';
	}
}

?>
