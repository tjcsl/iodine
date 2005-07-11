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
	const SELECT = 1;
	/**
	* Represents an INSERT query.
	*/
	const INSERT = 2;
	/**
	* Represents an UPDATE query.
	*/
	const UPDATE = 3;
	/**
	* Represents a DELETE query.
	*/
	const DELETE = 4;
	
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
		d("Connecting to $server as $user");
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
	* @access public
	* @param Token $token The permissions token, ensuring that you have
	*                      access to the table(s) you're trying to access.
	* @param string $query The printf-ifyed query you want to run.
	* @param mixed $args,... Arguments for printf tags.
	*/
	public function query($token, $query) {
		global $I2_ERR,$I2_LOG;

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

		/* Get query type by examining the query string up to the first
		space */
		switch( strtoupper(substr($query, 0, strpos($query, ' '))) ) {
			case 'SELECT':
				$perm = 'r';
				$query_t = MYSQL::SELECT;
				break;
			case 'UPDATE':
				$perm = 'w';
				$query_t = MYSQL::UPDATE;
				break;
			case 'DELETE':
				$perm = 'd';
				$query_t = MYSQL::DELETE;
				break;
			case 'INSERT':
				$perm = 'i';
				$query_t = MYSQL::INSERT;
				break;
			default:
				throw new I2Exception('Attempted MySQL query of unauthorized command `'.substr($query, 0, strpos($query, ' ')).'`');
		}

		/* token checking disabled, so don't bother determining which
		tables were used */
/*		$tables = self::get_used_tables($query, $query_t);
		d('Tables referenced in SQL query: '.count($tables).': '.implode($tables, ', '));
		foreach( $tables as $table) {
			if( $token->check_rights('mysql/'.$table, $perm) === FALSE) {
				throw new I2Exception('Attempted to perform a MySQL query without proper access. (Table: '.$table.', access needed: '.$perm.')');
			}
		}*/

		return new Result($this->raw_query($query),MYSQL::SELECT);
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

	/**
	* Gets the tables used in a query from the query string.
	*
	* This goes through a raw query string, and partially parses it to
	* determine which tables it uses in the database. This is not a very
	* efficient thing to do, but I'm open to suggestions on any other way
	* of restricting data access in mysql.
	*
	* @param String $str The query string.
	* @param int $type One of the MySQL constants, representing what kind
	*                  of query (SELECT/UPDATE/etc) this is.
	*/
	private static function get_used_tables($str, $type) {
		if( ($offset = stripos($str, ' where ')) ) {
			/* strip off everything after 'where'; not needed */
			$str = substr($str, 0, $offset);
		}

		if( $type == MYSQL::SELECT ) {
			/* strip off column names and stuff */
			$str = substr($str, stripos($str, 'from'));
		}

		if( $str[strlen($str)-1] == ';' ) {
			$str = substr($str, 0, strlen($str)-1);
		}

		$ret = array();
		$words = explode(' ', ltrim($str));
		array_shift($words);
		
		switch($type) {
			case MYSQL::SELECT:
			case MYSQL::DELETE:
				while(isset($words[0])) {
					switch(strtolower($words[0])) {
						case 'from':
							array_shift($words);
							continue;
						case 'where':
						case 'order':
							/* stop processing */
							unset($words);
							break;
						case 'as':
							array_shift($words);
							array_shift($words);
							continue;
						/* joins */
						case 'left':
						case 'right':
						case 'cross':
						case 'natural':
						case 'inner':
						case 'join':
						case 'straight_join':
/* Not implementing JOINs right now because it's too difficult. Perhaps will
implement them at a later date if there is a need. */
							throw new I2Exception('We do not currently support using JOINs in MySQL statements. Try again later, when we do.');
/*							while(strtolower($words[0]) != 'join') {
								array_shift($words);
							}
							array_shift($words);
							continue;*/
						default:
							foreach(explode(',', $words[0]) as $word) {
								if( $word ) {
									$ret[] = $word;
								}
							}
							array_shift($words);

					}
				}
				break;
			case MYSQL::INSERT:
				if(strtolower($words[0]) == 'into') {
					array_shift($words);
				}
				$ret[] = $words[0];
				break;
				
			case MYSQL::UPDATE:
				while(isset($words[0])) {
					if(strtolower($words[0]) == 'set')
						break;
					foreach(explode(',', $words[0]) as $word) {
						if($word)
							$ret[] = $word;
					}
				}
				break;
			default:
				throw new I2Exception('Invalid MySQL query type `'.$type.'` passed to get_used_tables');
		}
		return $ret;
	}
}

?>
