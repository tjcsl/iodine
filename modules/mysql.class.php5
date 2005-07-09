<?php
/**
* Contains the definition for the class {@link MySQL}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage MySQL
* @filesource
*/

/**
* A string representing all custom printf tags for mysql queries which require an argument. Each character represents a different tag.
*/
define('I2_SQL_TAGS_ARG', 'adsi');
/**
* A string representing all custom printf tags for mysql queries which do not require an argument. Each character represents a different tag.
*/
define('I2_SQL_TAGS_NOARG', 'V%');
		
/**
* The MySQL module for Iodine.
* @package core
* @subpackage MySQL
* @see Result
*/
class MySQL {

	/**
	* Represents a SELECT query.
	*/
	const SQL_SELECT = 1;
	/**
	* Represents an INSERT query.
	*/
	const SQL_INSERT = 2;
	/**
	* Represents an UPDATE query.
	*/
	const SQL_UPDATE = 3;
	/**
	* Represents a DELETE query.
	*/
	const SQL_DELETE = 4;
	
	/**
	* The MySQL class constructor.
	* 
	* @access public
	*/
	function __construct() {
		$this->connect(i2config_get('server','','mysql'), i2config_get('user','','mysql'), i2config_get('pass','','mysql'));
		$this->select_db('iodine');
	}

	/**
	* Converts/wraps a MySQL result object into an Intranet2 type.
	*
	* @access private
	* @param object $sql The MySQL resultset object.
	* @param mixed $querytype A query type, from the MySQL module.
	* @return object An Intranet2-MySQL result object.
	*/
	private function sql_to_result($sql,$querytype) {
		return new Result($sql,$querytype);
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
		global $I2_LOG;
		$I2_LOG->log_debug("Connecting to $server as $user");
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
	protected function raw_query($query) {
		global $I2_ERR;
		$r = mysql_query($query);
		if ($err = mysql_error()) {
			throw new I2Exception('MySQL error: '.$err);
			return false;
		}
		return $r;
	}

	/**
	* Raw-string replacement function for the current select/insert functons.
	* This takes a token and a string. The string is the actual MySQL query
	* with optional printf-style markers to indicate values that should be
	* checked (or formatted in a certain way). Any other arguments after
	* that are the printf-style arguments. For example:
	*
	* <code>
	* query($token, 'SELECT * FROM mytable WHERE id=`%d`', $the_id);
	* </code>
	*
	* Will essentially execute the query
	* 'SELECT * FROM mytable WHERE id=`$the_id`' except it will check that
	* $the_id is a valid integer.
	*
	* The printf-style tags implemented are:
	* <ul>
	* <li>%a - A string which only contains alphanumeric characters</li>
	* <li>%d or %i - An integer, or an integer in a string</li>
	* <li>%V - Outputs the current Iodine version</li>
	* <li>%% - Outputs a literal '%'</li>
	* </ul>
	*
	* @todo check permissions on table access
	* @access public
	* @param string $token The permissions token, ensuring that you have
	*                      access to the table(s) you're trying to access.
	* @param string $query The printf-ifyed query you want to run.
	* @param mixed $args,... Arguments for printf tags.
	*/
	public function query($token, $query) {
		global $I2_ERR;

		$argc = func_num_args()-2;
		$argv = func_get_args();
		array_shift($argv); array_shift($argv);
		
		/* matches Iodine custom printf-style tags */
		if( preg_match_all(
			'/(?<!%)%['.I2_SQL_TAGS_ARG.I2_SQL_TAGS_NOARG.']/',
			$query,
			$tags,
			PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE )
		) {
			foreach ($tags[0] as $tag) {
				/*$tag[0] is the string, $tag[1] is the offset*/
				
				/* tags that require an argument */
				if ( strpos(I2_SQL_TAGS_ARG, $tag[0][1]) ) {
					if($argc < 1) {
						throw new Exception('Insufficient arguments to mysql query string');
					}
					$arg = array_shift($argv);
					$argc--;
				}

				/* Now substitute the tag depending on which tag
				was matched. $arg is the argument, if the tag
				needs one, and $replacement is the string to
				replace the tag with*/
				switch($tag[0][1]) {
					/* 'argument' tags first */
					
					/*alphanumeric string*/
					case 'a':
						if ( !ctype_alnum($arg) ) {
							throw new I2Exception('String `'.$arg.'` contains non-alphanumeric characters, and was passed as an %a string in a mysql query');
							$replacement = '';
						}
						$replacement = $arg;
						break;
					/* integer*/
					case 'd':
					case 'i':
						if (	is_int($arg) ||
							ctype_digit($arg) ||
							(ctype_digit(substr($arg,1)) && $arg[0]=='-') //negatives
						) {
							$replacement = ''.$arg;
						}
						else {
							throw new I2Exception('The string `'.$arg.'` is not an integer, but was passed as %d or %i in a mysql query');
							$replacement = '0';
						}
						break;
					case 's':
						$replacement = '\''.$arg.'\'';
						break;
					
					/* Non-argument tags below here */
					
					/*Iodine version string*/
					case 'V':
						$replacement = 'TJHSST Intranet2 Iodine version '.I2_VERSION;
						break;
					case '%':
						$replacement = '%';
						break;
					
					/* sanity check */
					default:
						$I2_ERR->fatal_error('Internal error, undefined mysql printf tag `%'.$tag[0][1].'`', TRUE);
				}

				$query = substr_replace($query,$replacement,$tag[1],2);
			}
		}


		return new Result($this->raw_query($query),MYSQL::SQL_SELECT);
	}

	/**
	* Joins two result objects into one.
	*
	* @param $left The resultset to appear first in the result.
	* @param $right The resultset to appear last in the result.
	* @return mixed The composite resultset object.
	*/
	function ljoin($left, $right) {
	}
}

?>
