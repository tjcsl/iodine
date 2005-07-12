<?php
/**
* Just contains the definition for the class {@link User}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Id: user.class.php5,v 1.25 2005/07/12 06:53:09 adeason Exp $
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
	private $myuid;

	/**
	* The User class constructor.
	* 
	* @access public
	*/
	public function __construct($uid = NULL) {
		global $I2_SQL, $I2_ERR;
		if( $uid === NULL ) {
			if( isset($_SESSION['i2_uid']) ) {
				$uid = $_SESSION['i2_uid'];
				$this->info = $I2_SQL->query('SELECT * FROM user where uid=%d', $uid)->fetch_array(MYSQL_ASSOC);
				if( ! $this->info ) {
					$I2_ERR->nonfatal_error('A User object was created with a nonexistent uid');
				}
			}
			else {
				$I2_ERR->fatal_error('Your password and username were correct, but you don\'t appear to exist in our database. If this is a mistake, please contact the intranetmaster about it.');
			}
		}

		$this->myuid = $uid;
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

		if( $this->myuid === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}
		
		if( $this->info != NULL && in_array($name, array_keys($this->info)) ) {
			return $this->info[$name];
		}
		
		if( $this->info == NULL && $I2_SQL->column_exists( 'user', $name ) ) {
			$table = 'user';
		}
		elseif( ! $I2_SQL->column_exists('userinfo', $name) ) {
			throw new I2Exception('Tried to get unknown User information `'.$name.'`.');
		}
		else {
			$table = 'userinfo';
		}
		
		$res = $I2_SQL->query('SELECT %c FROM %c WHERE uid=%d;', $name, $table, $this->myuid)->fetch_array(MYSQL_NUM);
		return $res[0];
	}

	/**
	* Set information about a user.
	*
	* This sets a certain piece of information about a user to something.
	* You need to specify the name of the attribute, and the type of
	* information it is, along with the value to set it to, so it can be
	* validated by {@link MySQL}.
	*
	* @param string $name The name of the field to set.
	* @param int $type A constant from {@link MySQL} that represents what
	*                  type of data this is.
	* @param mixed $val The data to set the field to.
	*/
	public function set( $name, $type, $val ) {
	// Can't use __set easily, because we need another argument for the type
	//technically, we _could_ look up the type in the mysql table, and check
	//for that type, but I'm not sure it's worth the trouble & extra
	//processing time when we can just pass the extra argument
		global $I2_SQL;

		if( $this->myuid === NULL ) {
			throw new I2Exception('Tried to set information for nonexistant user!');
		}

		if( $I2_SQL->column_exists( 'user', $name ) ) {
			$table = 'user';
		}
		elseif( ! $I2_SQL->column_exists('userinfo', $name) ) {
			throw new I2Exception('Tried to get unknown User information `'.$name.'`.');
		}
		else {
			$table = 'userinfo';
		}

		switch( $type ) {
			case MYSQL::STRING:	$tag = '%s'; break;
			case MYSQL::INT:	$tag = '%d'; break;
			case MYSQL::FLOAT:
			case MYSQL::DATE:
			default:
				$GLOBALS['I2_ERR']->nonfatal_error('Tried to set the User field `'.$name.'` to an unknown/unsupported type: `'.$type.'`');
				return;
		}

		$I2_SQL->query('UPDATE %c SET %c='.$tag.' WHERE uid=%d;', $table, $name, $val, $this->myuid);

		if( $this->info != NULL && in_array($name, array_keys($this->info)) ) {
			$this->info[$name] = $val;
		}
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

		if( $this->myuid === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}
		
		return $I2_SQL->query('SELECT * FROM user LEFT JOIN userinfo USING (uid) WHERE uid=%d;', $this->myuid)->fetch_array(MYSQL_ASSOC);
	}

	/**
	* Get only certain columns of info about the user.
	*
	* Even though the column values are checked, it's not adviseable to have
	* user input go into this method, at least, not directly (just for
	* general security reasons). It was created mainly so you could do
	* <code>
	* $user = new User($id);
	* $arr = $user->get_cols('username', 'fname', 'lname', 'bdate');
	* </code>
	* or something like that.
	*
	* @param mixed $cols,... Either an array containing the names of the
	*                        columns to retrieve, or just pass a string for
	*                        each column you want returned.
	*/
	public function get_cols() {
		global $I2_SQL;

		if( $this->myuid === NULL ) {
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

		return $I2_SQL->query('SELECT %c FROM user LEFT JOIN userinfo USING (uid) WHERE uid=%d;', $cols, $this->myuid)->fetch_array(MYSQL_NUM);
	}
}

?>
