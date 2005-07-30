<?php
/**
* Just contains the definition for the class {@link Prefs}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Revision: 1.1 $
* @since 1.0
* @package modules
* @subpackage Prefs
* @filesource
*/

/**
* The module to get/set various user preferences.
* @package modules
* @subpackage Prefs
*/
class Prefs implements Module {

	/**
	* An associative array containing all of the preferences for the user.
	*/
	private $prefs;

	private $user_intraboxen;
	private $nonuser_intraboxen;
	
	/**
	* @todo Error checking of form values and such
	*/
	function init_pane() {
		global $I2_USER,$I2_ARGS;

		$this->prefs = $I2_USER->info();

		if( isset($_REQUEST['prefs_form']) ) {
			//form submitted, update info
			foreach($_REQUEST as $key=>$val) {
				if( substr($key, 0, 5) == 'pref_' ) {
					$field = substr($key, 5);
					$I2_USER->$field = $val;
				}
			}

			if( isset($_REQUEST['add_intrabox']) ) {
				Intrabox::add_box($_REQUEST['add_boxid']);
			}
			if( isset($_REQUEST['delete_intrabox']) ) {
				Intrabox::delete_box($_REQUEST['delete_boxid']);
			}

//			redirect('prefs');
		}

		$this->user_intraboxen = Intrabox::get_boxes_info(Intrabox::USED);
		$this->nonuser_intraboxen = Intrabox::get_boxes_info(Intrabox::UNUSED);
		
		d('nonuser_intraboxen:');
		foreach($this->nonuser_intraboxen as $box) {
			d(print_r($box,TRUE));
		}

		return array('Your Preferences', 'Preferences');
		
	}
	
	function display_pane($display) {
		$display->disp('prefs_pane.tpl',array(	'prefs' => $this->prefs,
							'user_intraboxen' => $this->user_intraboxen,
							'nonuser_intraboxen' => $this->nonuser_intraboxen
		));
	}
	
	function init_box() {
		return FALSE;
	}

	function display_box($display) {
		return FALSE;
	}

	function get_name() {
		return 'Prefs';
	}
}

?>
