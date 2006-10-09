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
	
	function init_pane() {
		global $I2_USER,$I2_ARGS,$I2_SQL;

		if( isset($_REQUEST['prefs_form']) ) {
			//form submitted, update info
			foreach($_REQUEST as $key=>$val) {
				if(substr($key, 0, 5) == 'pref_' ) {
					if(is_array($val)) {
						$val = array_filter($val);
					}
					$field = substr($key, 5);
					$I2_USER->$field = $val;
				}
			}

			foreach (array('showaddressself','showphoneself','showbdayself','showscheduleself','showmapself','showpictureself') as $pref) {
				if (isSet($_REQUEST[$pref])) {
					$I2_USER->$pref = 'TRUE';
				} else {
					$I2_USER->$pref = 'FALSE';
				}
			}

			if (isSet($_REQUEST['pref_style']) || isSet($_REQUEST['pref_boxcolor']) || isSet($_REQUEST['pref_boxtitlecolor'])) {
				Display::style_changed();
			}

			if( isset($_REQUEST['add_intrabox']) && isSet($_REQUEST['add_boxid']) ) {
				foreach($_REQUEST['add_boxid'] as $box){
					Intrabox::add_box($box);
				}
			}
			if( isset($_REQUEST['delete_intrabox']) && isSet($_REQUEST['delete_boxid']) ) {
				foreach($_REQUEST['delete_boxid'] as $box){
					Intrabox::delete_box($box);
				}
			}

			//redirect('prefs');
		}

		$this->prefs = $I2_USER->info();

		$photonames = $I2_USER->photoNames;
		$this->photonames = array();
		foreach ($photonames as $photo) {
			$text = ucfirst(strtolower(substr($photo, 0, -5)));
			$this->photonames[$photo] = $text;
		}

		$this->prefs['showaddressself'] = $I2_USER->showaddressself=='TRUE'?TRUE:FALSE;
		$this->prefs['showscheduleself'] = $I2_USER->showscheduleself=='TRUE'?TRUE:FALSE;
		$this->prefs['showpictureself'] = $I2_USER->showpictureself=='TRUE'?TRUE:FALSE;
		$this->prefs['showbdayself'] = $I2_USER->showbdayself=='TRUE'?TRUE:FALSE;
		$this->prefs['showmapself'] = $I2_USER->showmapself=='TRUE'?TRUE:FALSE;
		$this->prefs['showphoneself'] = $I2_USER->showphoneself=='TRUE'?TRUE:FALSE;

		$this->user_intraboxen = Intrabox::get_boxes_info(Intrabox::USED)->fetch_all_arrays(Result::ASSOC);
		$this->nonuser_intraboxen = Intrabox::get_boxes_info(Intrabox::UNUSED)->fetch_all_arrays(Result::ASSOC);
		
		$this->themes = CSS::get_available_styles();

		return array('Your Preferences', 'Preferences');
		
	}
	
	function display_pane($display) {
		$display->disp('prefs_pane.tpl',array(	'prefs' => $this->prefs,
							'user_intraboxen' => $this->user_intraboxen,
							'nonuser_intraboxen' => $this->nonuser_intraboxen,
							'curtheme' => $this->prefs['style'],
							'themes' => $this->themes,
							'photonames' => $this->photonames
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
