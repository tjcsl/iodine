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
class Pictures extends Module {
	
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

	function init_pane() {
		return "Pictures";
	}
}
?>
