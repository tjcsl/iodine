<?php

	/**
	* The module that keeps you hip and cool with the latest happenings
	* the world (or at least the TJ Intranet) over!
	*
	* @author The Intranet 2 Development Team
	*/

	class News implements Module {
		
		private $display;
		private $newsdetails; 
		private $summaries;
		
		/**
		* The News class constructor.
		*/
		function __construct() {
		}
		
		function init_pane($token) {
		}
		
		function display_pane($display) {
			$display->raw_display("This is today's news, in a pane. <BR />");
		}
		
		function init_box($token) {
		}

		function display_box($display) {
			$display->raw_display("This is today's news, in a box. <BR />");
		}

		function get_name() {
			return "News";
		}
	}

?>
