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
		* Indicates that results should be greater than a value.
		*/
		const $GREATER_THAN = '>';
		/**
		* Indicates that results should be less than a value.
		*/
		const $LESS_THAN = '<';
		/**
		* Indicates that results should be equal to a value.
		*/
		const $EQUAL_TO = '=';
		
		private var $db;
		
		/**
		* The MySQL class constructor.
		* 
		* @access public
		*/
		function MySQL() {
			//TODO: Get config value here//
			$db = $this->connect($blah, $blah2, $blah3);

		}

		/**
		* Converts/wraps a MySQL result object into an Intranet2 type.
		*
		* @access private
		* @param object $sql The MySQL resultset object.
		* @return object An Intranet2-MySQL result object.
		*/
		private function sql_to_result($sql) {
			//TODO:  Implement for real.  Decide on what results should be.
			return $sql;
		}
		
		protected function connect($server, $user, $password) {
			return mysql_pconnect($server, $user, $password);
		}

		protected function select_db($database) {
			mysql_select_db($database);
		}

		protected function query($query) {
			return mysql_query($db,$query); //Is this the right ordering?
		}

		/**
		* Issues a proporly formatted MySQL SELECT query, and returns the results.
		*
		* @param string $table The table to query.
		* @param array $columns The columns to select (all by default).
		* @param assoc_array $where The conditions on which to accept a row.  This should
		* be an associative array of two-element arrays consisting of a comparison operator
		* and a value, so that $where[$key] = array($comparitive,$value).
		* @param array $ordering The desired sort order of the resultset.
		*/
		function select($table, $columns = false, $where = false, $ordering = false) {
			/*
			** Build a (hopefully valid) MySQL query from the arguments.
			*/
			$q = "SELECT ";
			if (!$columns) {
				$q .= '*';
			} else {
				$first = true;
				foreach ($columns as $col) {
					if ($first) {
						$first = false;
					} else {
						$q .= ',';
					}
					$q .= $col;
				}
			}
			
			$q .= " FROM $table";
			
			if ($where) {
				$q .= " WHERE ";
				$first = true;
				foreach ($where as $key=>$subarray) {
					if ($first) {
						$first = false;
					} else {
						$q .= ',';
					}
					$comptype = $subarray[0];
					$value = addslashes($subarray[1]); //Is addslashes() good enough?
					$q .= "$key $comptype '$value'";
				}
			}
			
			if ($ordering) {
				$q .= " ORDER BY ";
				$first = true;
				foreach ($ordering as $item) {
					if ($first) {
						$first = false;
					} else {
						$q .= ',';
					}
					$q .= $item;
				}
			}

			//Glad that's over with.  Now, we query the database.
			
			return sql_to_result(query($q));
		}

		/**
		* Perform a MySQL INSERT query.
		*
		* @param string $table The table to insert into.
		* @param mixed $cols_to_vals An associative array of columns to values (or just columns if $values is provided).
		* @param array $values An array of values.
		* @return A result object.
		*/
		function insert($table, $cols, $values = false) {
			
			/*
			** If $values wasn't passed, break up $cols into $cols and $values. 
			*/
			if (!$values) {
				$values = array_values($cols);
				$cols = array_keys($cols);
			}

			/*
			** Build the query.
			*/
			
			$q = "INSERT INTO $table(";
			$first = true;
			foreach ($cols as $col) {
				if ($first) {
					$first = false;
				} else {
					$q .= ',';
				}
				$q .= $col;
			}
			$q .= ') VALUES(';
			$first = true;
			foreach ($values as $val) {
				if ($first) {
					$first = false;
				} else {
					$q .= ',';
				}
				$var = addslashes($val);
				$q .= "'$val'";
			}
			$q .= ')';

			return sql_to_result(query($q));
			
		}

		/**
		* Performs a MySQL UPDATE statement.
		*
		* @param string $table The table to update.
		* @param mixed $columns An associative array of columns to values, or an array of columns if $values is provided.
		* @param array $values An array of values.
		* @param array $where An associative array of arrays containing condition-match pairs, so that
		* $where[$key] = array($comparetype,$value).
		*/
		function update($table, $columns, $values = false, $where = false) {
			/*
			** If $values isn't present, expand $columns to $columns and $values.
			*/
			//FIXME fix it!!
			if ($values) {
				$a = 0;
				while ($val = array_pop($values)) {
					$columns[$columns[$a]] = $val;
				}
			}

			/*
			** Build a query.
			*/

			$q = "UPDATE $table SET ";

			foreach ($columns as $col) {
			}
		}

		function drop($table, $where = false) {
			//If where is nonexistent, throw error
		}

	}

?>
