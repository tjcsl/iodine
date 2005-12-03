<?php
/**
* Just contains the definition for the class {@link StudentDirectory}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
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
* @todo Make the page that displays all of the user's info more pretty.
* @todo Add neato search capabilites as discussed on 11 Nov 2004
*/
class StudentDirectory implements Module {
	
	private $information;
	private $user = NULL;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL,$I2_ARGS,$I2_USER;

		if( ! isset($I2_ARGS[1]) ) {
			$this->information = 'help';
			return array('Student Directory Help', 'Searching Help');
		}
		
		switch($I2_ARGS[1]) {
			//Get info about someone
			case 'info':
				$this->user = isset($I2_ARGS[2]) ? new User($I2_ARGS[2]) : $I2_USER;
				if( ($this->information = $this->user->info()) === FALSE ) {
					return array('Error', 'Error: Student does not exist');
				}
				return array('Student Directory: '.$this->information['fname'].' '.$this->information['lname'], $this->information['fname'].' '.$this->information['lname']);

			case 'search':
				if( $_REQUEST['studentdirectory_query'] == "" ) {
					$this->information = 'help';
					return array('Student Directory Help', 'Searching Help');
				}
				else {
					$this->information = $I2_USER->search_info($_REQUEST['studentdirectory_query']);
					if( count($this->information) == 1 ) {
						redirect('studentdirectory/info/'.$this->information[0]->uid);
					}
					return array('Student Directory search results for "'.$_REQUEST['studentdirectory_query'].'"', 'Search results for "'.$_REQUEST['studentdirectory_query'].'"');
				}
				break;
			default:
				$this->information = FALSE;
				return array('Error', 'Error: Student does not exist');
				
		}
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		if( $this->information == 'help' ) {
			$display->disp('studentdirectory_help.tpl');
		} else {
			if($this->user !== NULL) {
//				try {
					$sched = new Schedule($this->user);
//				} catch( I2Exception $e) {
//					$sched = NULL;
//				}
			} else {
				$sched = NULL;
			}
			$display->disp('studentdirectory_pane.tpl',array('info'=>$this->information,'schedule'=>$sched));
		}
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

	function is_intrabox() {
		return true;
	}
}

?>
