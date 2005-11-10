<?php
/**
* Just contains the definition for the class {@link Prefs}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
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

	protected $user_intraboxen;
	protected $nonuser_intraboxen;
	private $themes;
	
	/**
	* @todo Error checking of form values and such
	* @TODO Don't allow users to set random stuff with faked POST values!
	*/
	function init_pane() {
		global $I2_USER,$I2_ARGS,$I2_SQL;

		if( isset($_REQUEST['prefs_form']) ) {
			//form submitted, update info
			foreach($_REQUEST as $key=>$val) {
				if( substr($key, 0, 5) == 'pref_' ) {
					$field = substr($key, 5);
					$I2_USER->$field = $val;
				}
			}

			if (isSet($_REQUEST['pref_style'])) {
				Display::style_changed();
			}

			if( isset($_REQUEST['add_intrabox']) && isSet($_REQUEST['add_boxid']) ) {
				d("Boxes: ".print_r($_REQUEST['add_boxid'],TRUE));
				foreach ($_REQUEST['add_boxid'] as $val) {
					Intrabox::add_box($val);
				}
			}
			if( isset($_REQUEST['delete_intrabox']) && isSet($_REQUEST['delete_boxid']) ) {
				foreach($_REQUEST['delete_boxid'] as $val) {
					Intrabox::delete_box($val);
				}
			}

			//redirect('prefs');
		}

		$this->prefs = $I2_USER->info();

		$this->user_intraboxen = Intrabox::get_boxes_info(Intrabox::USED)->fetch_all_arrays(RESULT_ASSOC);
		$this->nonuser_intraboxen = Intrabox::get_boxes_info(Intrabox::UNUSED)->fetch_all_arrays(RESULT_ASSOC);
		
		d('nonuser_intraboxen:');
		foreach($this->nonuser_intraboxen as $box) {
			d(print_r($box,TRUE));
		}

		$this->themes = $this->get_available_styles();

		return array('Your Preferences', 'Preferences');
		
	}

	public function get_available_styles() {
		$styles = explode(',',i2config_get('styles','default','css'));
		d('Available styles: '.print_r($styles,true));
		return $styles;
	}
	
	function display_pane($display) {
		$display->disp('prefs_pane.tpl',array(	'prefs' => $this->prefs,
							'user_intraboxen' => $this->user_intraboxen,
							'nonuser_intraboxen' => $this->nonuser_intraboxen,
							'curtheme' => $this->prefs['style'],
							'themes' => $this->themes
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
