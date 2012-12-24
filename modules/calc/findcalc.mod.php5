<?php
/**
* Just contains the definition for the class {@link Findcalc}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Calc
* @filesource
*/

/**
* The module that keeps TJ students happy.
* @package modules
* @subpackage Calc
*/
class Findcalc implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template = "findcalc_pane.tpl";

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

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
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS, $I2_SQL;
		if ($I2_USER->objectClass != 'tjhsstTeacher' && !$I2_USER->is_group_member('admin_calc')) {
				  return FALSE;
		}

		if( isset($_REQUEST['calc_form']) ) {
			//form submitted
			$type = $_REQUEST['calc_form'];
			if ($type == 'sn') {
				if ($_REQUEST['number']==""||!is_numeric($_REQUEST['number'])) {
					$this->template_args['message'] = "You didn't specify a valid serial number!";
				}
				else {
					$number= '%'.$_REQUEST['number'].'%';
					$calcs = $I2_SQL->query('SELECT uid,calcsn,calcid FROM calculators WHERE calcsn like %s',$number)->fetch_all_arrays(Result::ASSOC);
				}
			}
			else if ($type == 'id') {
				if($_REQUEST['number']=="") {
					$this->template_args['message'] = "You didn't specify a valid calculator ID!";
				}
				else {
					$number= '%'.$_REQUEST['number'].'%';
					$calcs = $I2_SQL->query('SELECT uid,calcsn,calcid FROM calculators WHERE calcid like %s',$number)->fetch_all_arrays(Result::ASSOC);
				}
			}
		}

		if(isset($calcs)) {
			if(count($calcs)==0 && $type!="" && !isset($this->template_args['message'])) {
				$this->template_args['message']="Calculator not found.";
			}
			else if(!isset($this->template_args['message']) && $calcs!="") {
				$this->template_args['results'] = array();

				foreach($calcs as $calc) {
					$output_array = array();

					$output_array['uid'] = $uid = $calc['uid'];
					$possible_owner = new User($uid);
					$output_array['name'] = $possible_owner->name;

					$output_array['sn'] = $calc['calcsn'];
					$output_array['id'] = $calc['calcid'];

					$this->template_args['results'][] = $output_array;
				}
			}
		}
		return array('Identify Lost Calculator');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Identify Lost Calculator";
	}
}

?>
