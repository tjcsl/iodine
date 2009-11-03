<?php
/**
* Just contains the definition for the class {@link SGD}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Remote
* @filesource
*/

/**
* The module that gives the remote access intrabox.
* @package modules
* @subpackage Remote
*/
class Remote implements Module {
	private $template_args = array();
	function init_pane() {
		return FALSE;
	}
	
	function display_pane($display) {
		return FALSE;
	}
	
	function init_box() {
		return 'Remote Access';
	}

	function display_box($display) {
		$display->disp('list.tpl',$this->template_args);
	}

	function get_name() {
		return 'Remote Access';
	}
}
?>
