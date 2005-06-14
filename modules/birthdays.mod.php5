<?php
	/**
	* Module displays birthdays for today or a few days past and
	* future.
	*
	* @author The Intranet 2 Development Team
	*/
	class Birthdays implements Module{
	
		private $namearr;
	
		function init_box($token){
			global $I2_SQL, $I2_USER;
			//FIXME: get_users_with_birthday needs to be commited
			//and modified
			$namearr = $I2_USER->get_users_with_birthday($token,date());
		}
		
		function display_box($disp){
			$disp->disp('birthdays.tpl',array());
		}
		
		function init_pane($token){
		}
		
		function display_pane($disp){
		}
	}
?>
