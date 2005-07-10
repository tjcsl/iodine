<?php
/**
* Just contains the definition for the class {@link Birthdays}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package modules
* @subpackage Birthday
* @filesource
*/

/**
* A module that displays users with birthdays close to the current date.
* @package modules
* @subpackage Birthday
*/

class Birthdays implements Module{

	private $namearr;

	function init_box(Token $token){
		global $I2_SQL, $I2_USER;
		$this->namearr = $I2_USER->get_users_with_birthday($token,date("Y-m-d"));
		return TRUE;
	}
	
	function display_box($disp){
		$disp->disp('birthdaybox.tpl',array('birthdays' =>$this->namearr));
	}
	
	function init_pane(Token $token){
		return FALSE;
	}
	
	function display_pane($disp){
		return;
	}

	function get_name(){
		return "Birthdays";
	}
}
?>
