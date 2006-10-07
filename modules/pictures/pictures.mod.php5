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

	function display_box($disp) {
	}
	
	function display_pane($disp) {
		global $I2_ARGS, $I2_LDAP;
		Display::stop_display();
		$user = new User($I2_ARGS[1]);
		if(!isset($I2_ARGS[2]))
		{
			if($photo = $user->preferredPhoto) {
				header("Content-type: image/jpeg");
				echo $photo;
			} else {
				header("Content-type: image/png");
				readfile(i2config_get('root_path', '/var/www/iodine/', 'core') . 'www/pics/bomb.png');
			}
		} else {
			switch($I2_ARGS[2])
			{
				case 'freshman':
					if($photo = $user->freshmanPhoto) {
						header("Content-type: image/jpeg");
						echo $photo;
					} else {
						header("Content-type: image/png");
						readfile(i2config_get('root_path', '/var/www/iodine/', 'core') . 'www/pics/bomb.png');
					}
					break;
				case 'sophomore':
					if($photo = $user->sophomorePhoto) {
						header("Content-type: image/jpeg");
						echo $photo;
					} else {
						header("Content-type: image/png");
						readfile(i2config_get('root_path', '/var/www/iodine/', 'core') . 'www/pics/bomb.png');
					}
					break;
				case 'junior':
					if($photo = $user->juniorPhoto) {
						header("Content-type: image/jpeg");
						echo $photo;
					} else {
						header("Content-type: image/png");
						readfile(i2config_get('root_path', '/var/www/iodine/', 'core') . 'www/pics/bomb.png');
					}
					break;
				case 'senior':
					if($photo = $user->seniorPhoto) {
						header("Content-type: image/jpeg");
						echo $photo;
					} else {
						header("Content-type: image/png");
						readfile(i2config_get('root_path', '/var/www/iodine/', 'core') . 'www/pics/bomb.png');
					}
					break;
				default:
					header("Content-type: image/png");
					readfile(i2config_get('root_path', '/var/www/iodine/', 'core') . 'www/pics/bomb.png');
					break;
			}
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
