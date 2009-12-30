<?php
/**
* Just contains the definition for the class {@link ServReq}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage ServReq
* @filesource
*/

/**
* The module that lets you request services around TJ.
* @package modules
* @subpackage ServReq
*/

class ServReq implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

	private $box_args = array();

	/**
	* Whether the current user is a teacher/admin.
	*/
	private $admin;
	
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
		global $I2_SQL,$I2_ARGS,$I2_USER;

		if( ! isset($I2_ARGS[1]) ) {
			$I2_ARGS[1] = '';
		}

		$archive = false;
		
		//cleanup old stuff:
		//$I2_SQL->query("DELETE FROM servreq WHERE donedate.... want to remove everything marked as done after a certain number of days

		switch($I2_ARGS[1]) {

			case 'add':
				$group = new Group(8); //all staff
				$this->template_args['approvers'] = array();
				$users = $group->members_obj_sorted;
				foreach($users as $person){
					$person_array = array();
					$person_array['name'] = $person->name;
					$person_array['uid'] = $person->uid;
					$this->template_args['approvers'][] = $person_array;
				}
				$this->template = 'req_add.tpl';
				
			case 'edit':
				$this->template = 'req_edit.tpl';
				
			case 'approve':
				$this->template = 'req_app.tpl';
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp($this->template,$this->template_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		global $I2_SQL,$I2_USER;
		
		$this->box_args['myreqs'] = $I2_SQL->query('SELECT * FROM servreq WHERE uid=%d', $I2_USER->uid);
		$this->box_args['admreqs'] = $I2_SQL->query('SELECT * FROM servreq WHERE admid=%d', $I2_USER->uid);
		$this->box_args['appreqs'] = $I2_SQL->query('SELECT * FROM servreq WHERE appid=%d', $I2_USER->uid);
		return 'Service Requests';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('req_box.tpl',$this->box_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'ServReq';
	}
}
?>
