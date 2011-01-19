<?php
/**
* Just contains the definition for the class {@link SGD}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage SGD
* @filesource
*/

/**
* The module that shows you the Sun Global Desktop interface.
* @package modules
* @subpackage SGD
*/
class SGD implements Module {
	private $template;
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

	function init_pane() {
		return 'Sun Global Desktop';
	}
	
	function display_pane($display) {
		global $I2_ARGS,$I2_USER,$I2_AUTH;
		if(count($I2_ARGS)<=1) {
			$display->disp('pane.tpl',$this->template_args);
		} else {
			// We do it in here so that smarty doesn't cache pages with the user's password in them.
			echo "<body onload='document.sunform.submit();'>";
			echo "<form name='sunform' action='https://sun.tjhsst.edu/sgd/authentication/ttaAuthentication.jsp' method=POST>";
			echo "<input type='hidden' name='Username' value='".$I2_USER->username."'/>";
			//echo "<input type='hidden' name='Password' value='".$I2_AUTH->get_user_password()."'/>";
			echo "<input type='hidden' name='Password' value=''/>";
			echo "</form></body>";
			Display::stop_display();
			exit;
		}
	}
	
	function init_box() {
		return FALSE;
	}

	function display_box($display) {
		return FALSE;
	}

	function get_name() {
		return 'Sun Global Desktop';
	}
}
?>
