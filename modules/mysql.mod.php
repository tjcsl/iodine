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
		/**
		* Indicates the sort order is descending.
		*/
		const DESC = 'DESC';
		/**
		* Indicates the sort order is ascending.
		*/
		const ASC = 'ASC';
		/**
		* Indicates an AND in a WHERE statement.
		*/
		const AND = 'AND';
		/**
		* Indicates on OR in a WHERE statement.
		*/
		const OR = 'OR';
		/**
		* Indicates a left parenthesis in a WHERE statement.
		*/
		const LPAREN = '(';
		/**
		* Indicates a right parenthesis in a WHERE statement.
		*/
		const RPAREN = ')';
		
		/**
		* The MySQL class constructor.
		* 
		* @access public
		*/
		function MySQL() {
			//TODO: Get config value here//
			$this->connect($blah, $blah2, $blah3);

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
		
		/**
		* Connects to a MySQL database server.
		*
		* @access protected
		* @param string $server The MySQL server location/name.
		* @param string $user The MySQL username.
		* @param string $password The MySQL password.
		*/
		protected function connect($server, $user, $password) {
			return mysql_pconnect($server, $user, $password);
		}
		
		/**
		* Select a MySQL database.
		*
		* @access protected
		* @param string $database The name of the database to select.
		*/
		protected function select_db($database) {
			mysql_select_db($database);
		}

		/**
		* Perform a preformatted MySQL query.  NOT FOR OUTSIDE USE!!!
		*
		* @param string $query The query string.
		*/
		protected function query($query) {
			return mysql_query($query);
		}

		/**
		* Issues a proporly formatted MySQL SELECT query, and returns the results.
		*
		* @param string $table The table to query.
		* @param array $columns The columns to select (all by default).
		* @param array $where The conditions on which to accept a row.  This should
		* be an associative array of two-element arrays consisting of a comparison operator
		* and a valueso that $where[$key] = array($comparitive,$value,$).
		* @param array $conditionals An array indicating how to match the $where argument's requirements.
		* It have appropriate grouping.  For the MySQL query segment
		* "WHERE name='david' AND (id='3' OR foo='bar') AND who='me'", the array would be:
		* array(AND,LPAREN,OR,RPAREN,AND).
		The following lines are from a discussion in an I2 meeting, not actual code.
		Example MySQL query: "SELECT * FROM blah WHERE mycol=`myval123` ORDER BY mykey ASC;"
		<cfquery datasource="blah">
		SELECT * FROM blah WHERE
		mycol=<cfqueryparam type='CFSQLPARAM_INT' value='myval123' />
		ORDER BY mykey ASC;;

		</cfquery>
		$I2_SQL->select("mytable", "*", "mycol=" . $I2_SQL::param('int','myval123'));
		$I2_SQL->select("mytable", "*", "mycol=`%d`", array("myval123"));
		
		* @param array $ordering The desired sort order of the resultset as an array of arrays
		* , with each subarray being array($ordertype,$column).
		*/
		function select($table, $columns = false, $where = false, $conditionals = false, $ordering = false) {
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
						while ($conditionals && $conditionals[0] == LPAREN) {
							$q .= array_shift($conditionals);
						}
					} else {
						if ($conditionals) {
							$poss = array_shift($conditionals);
							while ($poss == LPAREN || $poss = RPAREN) {
								$q .= $poss;
								//TODO: array bounds checking
								$poss = array_shift($conditionals);
							}
							$q .= " $poss ";
						}
					}
					$comptype = $subarray[0];
					$value = addslashes($subarray[1]); //Is addslashes() good enough?
					$q .= "$key $comptype '$value'";
				}
				while ($conditionals && ($poss = array_shift($conditionals))) {
					//if ($poss != RPAREN) { err('Bad parens.') }
					$q .= $poss;
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
		* TODO: document $conditionals.
		*/
		function update($table, $columns, $values = false, $where = false, $conditionals = false) {
			/*
			** If $values isn't present, expand $columns to $columns and $values.
			*/
			
			if (!$values) {
				foreach (array_values($columns) as $val) {
					$values = $val;
				}
				$columns = array_keys($columns);
			}

			/*
			** Build a query.
			*/

			$q = "UPDATE $table SET ";

			//if (array_len($columns) != array_len($values)) { error("uh-oh!"); }
			
			$first = true;
			for ($i = 0; $i < array_len($columns); $i++) {
				if ($first) {
					$first = false;
				} else {
					$q .= ',';
				}
				$val = addslashes($values[$i]);
				$q .= "$columns[$i] = '$val'";
			}

			//TODO: implement $where and $conditionals
			

			return sql_to_result(query($q));
		}
		
		/**
		* Perform a MySQL DELETE statement.  You must provide at least one condition.
		*
		* @param string $table The name of the table to delete from.
		* @param array $where An associative array of names to condition/value pairs,
		* so that $where[$column] = array($matchtype,$value).
		* TODO: document $conditionals.
		*/
		function delete($table, $where = false, $conditionals = false) {
			//TODO: If where is nonexistent, throw error

			$q = "DELETE FROM $table WHERE ";
			
			//TODO: implement 
		}

	}

?>
