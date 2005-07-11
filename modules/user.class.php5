<?php
/**
* Just contains the definition for the class {@link User}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Id: user.class.php5,v 1.20 2005/07/11 15:40:56 adeason Exp $
* @package core
* @subpackage User
* @filesource
*/

/**
* The user information module for Iodine.
* @todo Perhaps make some methods to get information from an array of UIDs
* @package core
* @subpackage User
* @see UserInfo
* @see Schedule
*/
class User {

	/**
	* Information about the user, only stored if this User object
	* represents the current user logged in, since that information will
	* probably be retrieved the most, so we cache it for speed.
	*/
	private $info = NULL;

	/**
	* The uid of the user.
	*/
	private $uid;

	/**
	* The User class constructor.
	* 
	* @access public
	*/
	public function __construct($uid = NULL) {
		global $I2_SQL, $I2_ERR;
		if( $uid === NULL && isset($_SESSION['i2_uid'])) {
			$uid = $_SESSION['i2_uid'];
			$this->info = $I2_SQL->query('SELECT * FROM user where uid=%d', $uid)->fetch_row(MYSQL_ASSOC);
			if( ! $this->info ) {
				$I2_ERR->nonfatal_error('A User object was created with a nonexistent uid');
			}
		}

		$this->uid = $uid;
	}

	/**
	* The php magical __get method.
	*
	* In most cases just use User-><field> to get a field, for example:
	* <code>
	* $person = new User( $id );
	* $first_name = $person->fname;
	* </code>
	* And that will obtain the user's first name. If you want to retrieve
	* multiple items at a time, look at the other methods in this class.
	*/
	public function __get( $name ) {
		global $I2_SQL;

		if( $this->uid === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}
		
		if( $this->info != NULL && in_array($name, array_keys($this->info)) ) {
			return $this->info[$name];
		}
		
		if( $this->info == NULL && $I2_SQL->column_exists( 'user', $name ) ) {
			$table = 'user';
		}
		elseif( ! $I2_SQL->column_exists( 'userinfo', $name) ) {
			throw new I2Exception('Uknown column `'.$name.'` passed to User.');
		}
		else {
			$table = 'userinfo';
		}
		
		// printf input sanitation unneccesary, since we just tested it
		// for vailidity in the lines above
		$res = $I2_SQL->query('SELECT '.$name.' FROM '.$table.' WHERE uid=%d', $this->uid)->fetch_row(MYSQL_NUM);
		return $res[0];
	}

	/**
	* Get all info about a user.
	*
	* Use this function if you're obtaining a lot of information about one
	* person, as it's faster then just going through each column and
	* retrieving each one manually.
	*
	* @return array An associative array for all fields for user info
	*               about this user. Keys are the column names, values are
	*               the values in those columns.
	*/
	public function info() {
		global $I2_SQL;

		if( $this->uid === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}
		
		return $I2_SQL->query('SELECT * FROM user LEFT JOIN userinfo USING (uid) WHERE uid=%d;', $this->uid)->fetch_row(MYSQL_ASSOC);
	}

	/**
	* Get only certain columns of info about the user.
	*
	* Note that the column names are NOT CHECKED, so please do not have
	* user input go directly into this method's parameters. You must check
	* the user input yourself in those cases, but this method was mainly
	* created for you to type in the values yourself, hardcoded. Like
	* "$user->get_cols('bdate', 'email0');" and so forth. (Except for a
	* larger amount of columns than that, if you only want two, then you
	* should probably use the standard way of getting values.)
	*
	* @param mixed $cols,... Either an array containing the names of the
	*                        columns to retrieve, or just pass a string for
	*                        each column you want returned.
	*/
	public function get_cols() {
		global $I2_SQL;

		if( $this->uid === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}
		
		if( func_num_args() < 1 ) {
			throw new I2Exception('Illegal number of arguments passed to User::get_cols()');
		}

		$argv = func_get_args();

		if( is_array($argv[0]) ) {
			$cols = $argv[0];
		}
		else {
			$cols = $argv;
		}

		return $I2_SQL->query('SELECT '.implode(',', $cols). ' FROM user LEFT JOIN userinfo USING (uid) WHERE uid=%d;', $this->uid)->fetch_row(MYSQL_NUM);
	}
}

?>
