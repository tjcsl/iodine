<?php
	/**
	* The MySQL module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package mysql
	*/
	
	class MySQL {
		/**
		* The MySQL class constructor.
		* 
		* @access public
		*/
		function MySQL() {
			//TODO: Get config value here//
			$this->connect($blah, $blah2, $blah3);

		}
		
		function connect($server, $user, $password) {
			mysql_pconnect($server, $user, $password);
		}

		protected function select_db($database) {
			mysql_select_db($database);
		}

		function query($query) {

		}

		function select($table, $columns = "", $where = "") {
			query("SELECT .........");
		}

		function insert($table, $columns, $values) {

		}

		function update($table, $columns, $values, $where = "") {

		}

		function drop($table, $where = "0") {
			//If where is nonexistent, throw error
		}

	}

?>
