<?php
/**
* Just contains the definition for the class {@link Mail}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Mail
* @filesource
*/

/**
* The module that shows you what email you have on your TJ account.
* @package modules
* @subpackage Mail
*/
class Mail implements Module {
	
	/**
	* The Mail class constructor.
	*/
	function __construct() {
	}
	
	function init_pane() {
		return array("Mail", "Mail");
	}
	
	function display_pane($display) {
		$display->disp('mail_pane.tpl',array());
	}
	
	function init_box() {
		return "Mail";
	}

	function display_box($display) {
		$display->disp('mail_box.tpl',array());
	}

	function get_name() {
		return "Mail";
	}

	function is_intrabox() {
		return true;
	}
}

?>
