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
*/
class StudentDirectory implements Module {
	
	private $information;
	private $user = NULL;

	public function get_classes($studentId) {
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL,$I2_ARGS,$I2_USER;

		$this->user = NULL;
		if( ! isset($I2_ARGS[1]) ) {
			$this->information = 'help';
			return array('Student Directory Help', 'Searching Help');
		}
		
		switch($I2_ARGS[1]) {
			//Get info about someone
			case 'info':
				try {
					$this->user = isset($I2_ARGS[2]) ? new User($I2_ARGS[2]) : $I2_USER;
				} catch(I2Exception $e) {
					return array('Error', 'Error: Student does not exist');
				}
				return array('Student Directory: '.$this->user->fname.' '.$this->user->lname, $this->user->fname.' '.$this->user->lname);

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
			case 'class':
				if(!isset($I2_ARGS[2])) {
					redirect();
				}
				$sec = new Section($I2_ARGS[2]);
				$this->information = array('class'=>$sec,'students'=>$sec->get_students());
				return "Students in {$sec->name}, Period {$sec->period}";
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
				try {
					$sched = $this->user->schedule();
				} catch( I2Exception $e) {
					$sched = NULL;
				}
			} else {
				$sched = NULL;
			}
			$display->disp('studentdirectory_pane.tpl',array('info' => $this->information, 'schedule' => $sched, 'user' => $this->user, 'eighth' => EighthActivity::id_to_activity(EighthSchedule::get_activities($this->user->uid))));
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
}

?>
