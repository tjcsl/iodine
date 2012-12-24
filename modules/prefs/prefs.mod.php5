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

	function init_pane() {
		global $I2_USER,$I2_ARGS,$I2_SQL;

		if( isset($_REQUEST['prefs_form']) ) {
			$refreshmail=false;
			if(isset($_REQUEST['pref_mailentries']) && $_REQUEST['pref_mailentries']!=$I2_USER->mailentries) {
				$refreshmail=true;
			}
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
			if($refreshmail) {
				Mail::clear_mail_cache();
			}

			if (is_int($I2_USER->grade)) {
				foreach (array('showaddressself','showphoneself','showbdayself','showscheduleself','showeighthself','showmapself','showpictureself','showfreshmanpictureself','showsophomorepictureself','showjuniorpictureself','showseniorpictureself','showlockerself','newsforwarding','eighthalert','eighthnightalert') as $pref) {
					$I2_USER->$pref = isSet($_REQUEST[$pref]) ? 'TRUE' : 'FALSE';
				}
				if (isSet($_REQUEST['relationship'])) {
					$I2_USER->relationship = $_REQUEST['relationship'];
				}
			} else {
				foreach (array('showaddressself','showphoneself','showbdayself','showpictureself') as $pref) {
					$I2_USER->$pref = isSet($_REQUEST[$pref]) ? 'TRUE' : 'FALSE';
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
		$this->prefs['showaddress'] = $I2_USER->showaddress=='TRUE'?TRUE:FALSE;
		$this->prefs['showscheduleself'] = $I2_USER->showscheduleself=='TRUE'?TRUE:FALSE;
		$this->prefs['showschedule'] = $I2_USER->showschedule=='TRUE'?TRUE:FALSE;
		$this->prefs['showeighthself'] = $I2_USER->showeighthself=='TRUE'?TRUE:FALSE;
		$this->prefs['showeighth'] = $I2_USER->showeighth=='TRUE'?TRUE:FALSE;
		$this->prefs['showpictureself'] = $I2_USER->showpictureself=='TRUE'?TRUE:FALSE;
		$this->prefs['showfreshmanpicture'] = $I2_USER->showfreshmanpicture=='TRUE'?TRUE:FALSE;
		$this->prefs['showsophomorepicture'] = $I2_USER->showsophomorepicture=='TRUE'?TRUE:FALSE;
		$this->prefs['showjuniorpicture'] = $I2_USER->showjuniorpicture=='TRUE'?TRUE:FALSE;
		$this->prefs['showseniorpicture'] = $I2_USER->showseniorpicture=='TRUE'?TRUE:FALSE;
		$this->prefs['showfreshmanpictureself'] = $I2_USER->showfreshmanpictureself=='TRUE'?TRUE:FALSE;
		$this->prefs['showsophomorepictureself'] = $I2_USER->showsophomorepictureself=='TRUE'?TRUE:FALSE;
		$this->prefs['showjuniorpictureself'] = $I2_USER->showjuniorpictureself=='TRUE'?TRUE:FALSE;
		$this->prefs['showseniorpictureself'] = $I2_USER->showseniorpictureself=='TRUE'?TRUE:FALSE;
		$this->prefs['showpicture'] = $I2_USER->showpicture=='TRUE'?TRUE:FALSE;
		$this->prefs['showbdayself'] = $I2_USER->showbdayself=='TRUE'?TRUE:FALSE;
		$this->prefs['showbday'] = $I2_USER->showbday=='TRUE'?TRUE:FALSE;
		$this->prefs['showmapself'] = $I2_USER->showmapself=='TRUE'?TRUE:FALSE;
		$this->prefs['showmap'] = $I2_USER->showmap=='TRUE'?TRUE:FALSE;
		$this->prefs['showphoneself'] = $I2_USER->showphoneself=='TRUE'?TRUE:FALSE;
		$this->prefs['showphone'] = $I2_USER->showphone=='TRUE'?TRUE:FALSE;
		$this->prefs['showlockerself'] = $I2_USER->showlockerself=='TRUE'?TRUE:FALSE;
		$this->prefs['showlocker'] = $I2_USER->showlocker=='TRUE'?TRUE:FALSE;

		$this->user_intraboxen = Intrabox::get_boxes_info(Intrabox::USED);
		$this->nonuser_intraboxen = Intrabox::get_boxes_info(Intrabox::UNUSED);
		
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
