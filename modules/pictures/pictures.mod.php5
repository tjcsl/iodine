<?php
/**
* Just contains the definition for the {@link Module} {@link Pictures}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage Studentdirectory
* @filesource
*/

/**
* A {@link Module} to display someone's preferred picture.
* @package core
* @subpackage Module
*/
class Pictures implements Module {

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

	function display_box($disp) {
	}
	
	function display_pane($disp) {
		global $I2_FS_ROOT, $I2_ARGS, $I2_LDAP;
		Display::stop_display();
		$user = new User($I2_ARGS[1]);
		$legal_args = array(
			'freshmanphoto',
			'sophomorephoto', 
			'juniorphoto', 
			'seniorphoto' 
		);
		$photoname='preferredphotoimage';
		if (isset($I2_ARGS[2]) && in_array(strtolower($I2_ARGS[2]), $legal_args)) {
			$photoname = strtolower($I2_ARGS[2]);
		}

		if($photo = $user->$photoname) {
			header("Content-type: image/jpeg");
			echo $photo;
		} else {
			header("Content-type: image/png");
			readfile($I2_FS_ROOT . 'www/pics/bomb.png');
		}
	}
	
	function get_name() {
		return "Pictures";
	}

	function init_box() {
		return FALSE;
	}

	function init_pane() {
		return "";
	}
}
?>
