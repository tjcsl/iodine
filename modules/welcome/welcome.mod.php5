<?php
/**
* A module that provides some introductory information for first time logging in every year.
* Default module should be changed to news after completion of this module.
* @package modules
* @subpackage welcome
*/

class Welcome implements Module {

	private $template_args = array();
	private $data;

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

	function init_box() {
		return FALSE;
	}

	function display_box($disp) {
	}

	function init_pane() {
		global $I2_USER;
		if( isset($_REQUEST['posted']) ) {
			//user finished the intro blurb
			
			//set e-mail
			foreach($_REQUEST as $key=>$val) {
				if(substr($key, 0, 5) == 'pref_' ) {
					if(is_array($val)) {
						$val = array_filter($val);
					}
					$field = substr($key, 5);
					$I2_USER->$field = $val;
				}
			}

			//reset to news
			$I2_USER->startpage = "news";

			//where did they say they wanted to go
			if($_REQUEST['prefs'])
				redirect("prefs");
			redirect();
		} else {
			$I2_USER->chrome = "FALSE";
			return array('Welcome');
		}
	}
	
	function display_pane($disp) {
		global $I2_USER;
		$disp->disp( "welcome.tpl", array( 'user' => $I2_USER) );
		//TODO: Perhaps there is a better way than force flushing...maybe not. --wyang 2007/09/06
		$disp->flush_buffer();
		Display::stop_display();
		$I2_USER->chrome = "TRUE";
	}

	function get_name() {
		return 'welcome';
	}

}
?>
