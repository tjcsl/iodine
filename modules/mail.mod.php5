<?php
	/**
	* The module that shows you what email you have on your TJ account.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package modules
	* @subpackage mail
	*/

	class Mail implements Module {
		
		/**
		* The Mail class constructor.
		*/
		function __construct() {
		}
		
		function init_pane($token) {
		}
		
		function display_pane($display) {
			$display->disp('mailpane.tpl',array());
		}
		
		function init_box($token) {
		}

		function display_box($display) {
			$display->raw_display("This is your mail, in a box.");
			$display->disp('mailbox.tpl',array());
		}

		function get_name() {
			return "Mail";
		}
	}

?>
