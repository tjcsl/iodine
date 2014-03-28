<?php
/**
* Just contains the definition for the {@link Module} Info.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Info
* @filesource
*/

/**
* A module used for April Fools 2014
* @package modules
* @subpackage Info
*/
class GC extends Module {
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		
	}
	function get_name() {
		return 'gc';
	}
	function init_pane() {
		global $I2_ARGS;
		if(!isset($I2_ARGS[1])) redirect("/");
		self::check();
		redirect("/");
	}
	static function check() {
		global $I2_ARGS;
		$o = $I2_ARGS[1];
		if($o == "optin") {
			setcookie("gc", true, time()+60*60*24, '/');
			$_COOKIE['gc'] = true;
		} else if($o == "optout") {
			setcookie("gc", false, time()+60*60*24, '/');
			$_COOKIE['gc'] = false;
		}
	}
}
?>
