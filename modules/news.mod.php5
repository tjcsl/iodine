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
			global $I2_SQL;
			$res = $I2_SQL->select($token,'news_stories',array('title','text','authorID','authortype','posted'));
			$this->newsdetails = $res->fetch_all_arrays(MYSQL_NUM);
			return TRUE;
		}
		
		function display_pane($display) {
			//$display->raw_display("This is today's news, in a pane.");
			$display->disp('newspane.tpl',array('news_stories'=>$this->newsdetails));
		}
		
		function init_box($token) {
			global $I2_SQL;
			$res = $I2_SQL->select($token,'news_stories',array('title'));
			$this->summaries = $res->fetch_col('title');
			return TRUE;
		}

		function display_box($display) {
			$display->raw_display("This is today's news, in a box.");
			$display->disp('newsbox.tpl',array('summaries'=>$this->summaries));
		}

		function get_name() {
			return "News";
		}
	}

?>
