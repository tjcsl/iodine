<?php
/**
* Just contains the definition for the class {@link User}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package core
* @subpackage User
* @filesource
*/

/**
* The user information module for Iodine.
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
	protected $info = NULL;

	/**
	* The uid of the user.
	*/
	private $myuid;

	/**
	* Cache for which columns are in the `user` table
	*/
	private static $user_cols = NULL;

	/**
	* The User class constructor.
	*
	* This takes the UID of the user as an argument. If the uid is not
	* or if it is NULL, then the UID of the currently logged in user is
	* used. (If someone isn't logged in yet, the application exits before
	* processing gets here, so we don't have to worry about it.)
	*
	* In that case of a NULL uid, the info is cached in an array in addition
	* to using the current user's information. This is because it is
	* anticipated that the current user's info will be queried a lot, so we
	* cache it so it doesn't need to be looked up all the time.
	* 
	* @access public
	*/
	public function __construct($uid = NULL) {
		global $I2_SQL, $I2_ERR;

		//Construct column cache, if it does not already exist
		if( self::$user_cols === NULL ) {
			self::$user_cols = array();
			foreach( $I2_SQL->query('DESCRIBE `user`;') as $col ) {
				self::$user_cols[] = $col['Field'];
			}
		}
		
		if( $uid === NULL ) {
			if( isset($_SESSION['i2_uid']) ) {
				$uid = $_SESSION['i2_uid'];
				$this->info = $I2_SQL->query('SELECT * FROM user WHERE uid=%d', $uid)->fetch_array(Result::ASSOC);
				if( ! $this->info ) {
					warn('A User object was created with a nonexistent uid');
				}
			}
			else {
				$I2_ERR->fatal_error('Your password and username were correct, but you don\'t appear to exist in our database. If this is a mistake, please contact the intranetmaster about it.');
			}
		}
		//If the user created is the same as the logged in user, use the cache
		elseif( $uid == $GLOBALS['I2_USER']->uid ) {
			$this->info = &$GLOBALS['I2_USER']->info;
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
	*
	* There are a few psuedo fields, which are actually a few fields
	* combined. They are:
	* <ul>
	* <li>name - Returns the name of the student</li>
	* <li>name_comma - Returns the name of the student, with the last
	* name first, with a comma (as in 'Powers, Austin').</li>
	* <li>fullname - Returns the full name of the student</li>
	* <li>fullname_comma - Returns the full name of the student, with the
	* last name first, with a comma (as in 'Powers, Austin Danger').</li>
	* </ul>
	*
	* @param mixed $name The field for which to get data.
	* @return mixed The data you requested.
	*/
	public function __get( $name ) {
		global $I2_SQL,$I2_ERR;

		if( $this->myuid === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}

		/* pseudo-fields
		** must use explicit __get calls here, since recursive implicit
		** __get calls apparently are not allowed.
		*/
		switch( $name ) {
			case 'name':
				$nick = $this->__get('nickname');
				return $this->__get('fname') . ' ' . ($nick ? "($nick) " : '') . $this->__get('lname');
			case 'name_comma':
				$nick = $this->__get('nickname');
				return $this->__get('lname') . ', ' . $this->__get('fname') . ' ' . ($nick ? "($nick)" : '');
			case 'fullname':
				$nick = $this->__get('nickname');
				$mid = $this->__get('mname');
				return $this->__get('fname') . ' ' . ($nick ? "($nick) " : '') . ($mid ? "$mid " : '') . $this->__get('lname');
			case 'fullname_comma':
				$nick = $this->__get('nickname');
				$mid = $this->__get('mname');
				return $this->__get('lname') . ', ' . $this->__get('fname') . ' ' . ($nick ? "($nick) " : '') . ($mid ? "$mid " : '');
			case 'grad_year':
				$grade = $this->__get('grade');
				/**
				 * FIXME Should NOT be hardcoded
				 */
				$grad_years = array(
					9 => 2009,
					10 => 2008,
					11 => 2007,
					12 => 2006 );
				return $grad_years[$grade];
		}
		
		//Check which table the information is in
		if( in_array($name, self::$user_cols) ) {
			if( $this->info != NULL ) {
				//returned cached info if we are caching
				return $this->info[$name];
			}
			$table = 'user';
		}
		elseif( ! $I2_SQL->column_exists('userinfo', $name) ) {
			throw new I2Exception('Tried to get unknown User information `'.$name.'`.');
		}
		else {
			$table = 'userinfo';
		}
		
		$res = $I2_SQL->query('SELECT %c FROM %c WHERE uid=%d;', $name, $table, $this->myuid)->fetch_array(Result::NUM);

		if( $res === FALSE ) {
			$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->myuid.'` was used in obtaining information');
			return FALSE;
			
		}
		
		return $res[0];
	}

	/**
	* The magical php __set method.
	*
	* This is called implicitly by PHP if you try to do
	* <code>$user->val = 'foo';</code>
	* so you don't need to call it directly. The value specified will be
	* treated as a string for purposes of MySQL validation and escaping.
	*
	* @param string $name The name of the field to set.
	* @param mixed $val The data to set the field to.
	*/
	public function __set( $name, $val ) {
	/* Can't use __set easily, because we need another argument for the type
	** technically, we _could_ look up the type in the mysql table, and check
	** for that type, but I'm not sure it's worth the trouble & extra
	** processing time when we can just pass the extra argument
	*/
		global $I2_SQL;

		if( $this->myuid === NULL ) {
			throw new I2Exception('Tried to set information for nonexistant user!');
		}

		//This could really screw up some SQL stuff, so I'm disallowing it
		if( $name == 'uid' ) {
			throw new I2Exception('Something tried to change the uid of a user. This is not permitted.');
		}

		//Check which table the information is in
		if( in_array($name, self::$user_cols) ) {
			$table = 'user';
		}
		elseif( ! $I2_SQL->column_exists('userinfo', $name) ) {
			throw new I2Exception('Tried to set unknown User information `'.$name.'`.');
		}
		else {
			$table = 'userinfo';
		}

		$I2_SQL->query('UPDATE %c SET %c=%s WHERE uid=%d;', $table, $name, $val, $this->myuid);

		if( $this->info != NULL && in_array($name, array_keys($this->info)) ) {
			$this->info[$name] = $val;
		}
	}

	/**
	* Get a user by their username.
	*
	* Returns a new User object that has the username $username.
	*
	* @param string $username The username to get.
	* @return User The user corresponding to that username.
	*/
	public static function get_by_uname($username) {
		global $I2_SQL;
		$uid = $I2_SQL->query('SELECT uid FROM user WHERE username=%s;',$username)->fetch_single_value();
		if(!$uid) {
			return FALSE;
		}
		return new User($uid);
	}

	/**
	* Creates a new user.
	*
	* This will insert the necessary information about a user into the applicable databases,
	* and do whatever is necessary to get that user an account.
	*
	* @return mixed A new User object representing the fresh user.
	*/
	public static function create_user($username,$fname,$lname) {
		global $I2_SQL;
		$res = $I2_SQL->query(
		"INSERT INTO user (	username,	fname,	lname) VALUES(%s,%s,%s,%s,%d)",
									$username,	$fname,	$lname);
		$uid = $res->get_insert_id();
		$res = $I2_SQL->query("INSERT INTO userinfo (uid) VALUES(%d)",$uid);
		return new User($uid);
	}

	/**
	* Get all info about a user.
	*
	* Use this function if you're obtaining a lot of information about one
	* person, as it's faster then just going through each column and
	* retrieving each one manually.
	*
	* @return array A {@link Result} containing all fields for user info
	*               about this user.
	*/
	public function info() {
		global $I2_SQL, $I2_ERR;

		if( $this->myuid === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}
		
		$ret = $I2_SQL->query('SELECT * FROM user LEFT JOIN userinfo USING (uid) WHERE user.uid=%d;', $this->myuid)->fetch_array(Result::ASSOC);

		if( $ret === FALSE ) {
			$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->myuid.'` was used in obtaining information');
		}

		return $ret;
	}

	/**
	* Get a user's groups.
	*
	* Used for finding all a user's groups.
	*
	* @return array An array of {link Group}s of which this user is a member.
	*/
	public function get_groups() {
		return Group::get_user_groups($this);
	}

	/**
	* Adds this user to the given group.
	*
	* Adds the given group to this user's membership list.
	* 
	* @param string $groupname The name of the group to which this user should be added.
	*/
	public function add_to_group($groupname) {
		$group = new Group($groupname);
		return $group->add_user($this);
	}	

	/**
	* Indicates whether this User is a member of the given group. 
	*
	*	Looks up a user's membership status by group name.
	*
	* @param string $groupname The name of the group to check.
	*	@return boolean Whether this User is a member of the passed group.
	*/
	public function is_group_member($groupname) {
		$group = new Group($groupname);
		return $group->has_member($this);
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
	* @return Array The information in the columns you requested.
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

		$ret = $I2_SQL->query('SELECT %c FROM user JOIN userinfo USING (uid) WHERE user.uid=%d;', $cols, $this->myuid)->fetch_array(Result::BOTH);
		
		if( $ret === FALSE ) {
			$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->myuid.'` was used in obtaining information');
		}

		return $ret;
	}

	/**
	* Get information about multiple users at once.
	*
	* @param array $uids An array of UIDs of users to get info about.
	* @param mixed $cols Either pass an array of columns of information you
	*                    want to retrieve, or pass a series of strings as
	*                    additional arguments.
	* @param mixed $cols,...
	* @return array Two-dimensional array of the results, each row being an
	*               associative array with the column as the key.
	*/
	public function get_multi( $uids, $cols ) {
		global $I2_SQL;
	
		if( !is_array($cols)) {
			$cols = func_get_args();
			array_shift($cols);
		}
		
		return $I2_SQL->query('SELECT %c FROM user JOIN userinfo USING (uid) WHERE user.uid IN (%D);', $cols, $uids)->fetch_all_arrays(Result::ASSOC);
	}

	/**
	* Search for users based on their information.
	*
	* @param string The search string.
	* @return array An array of {@link User} objects of the results. An
	* empty array is returned if no match is found.
	*/
	public function search_info($str) {
		global $I2_SQL;
		
		//Change BASH/DOS-style globbing to MySQL-style wildcards
		$str = strtr($str, '*?', '%_');

		$where = '';
		$where_arr = array();

		foreach(explode(' ', $str) as $item) {
			$where .= '(nickname LIKE %s OR username LIKE %s OR fname LIKE %s OR mname LIKE %s OR lname LIKE %s) AND ';
			$arr = array( '%'.$item.'%', '%'.$item.'%', '%'.$item.'%', '%'.$item.'%', '%'.$item.'%' );
			
			$where_arr = array_merge($where_arr, $arr);
		}

		//Cut off last 'AND '
		$where = substr($where, 0, strlen($where)-4);
		$ret = array();

		foreach( $I2_SQL->query_arr('SELECT uid FROM user WHERE '.$where.';', $where_arr) as $row ) {
			$ret[] = new User($row[0]);
		}

		return $ret;
	}

	/**
	* Convert an array of user IDs into an array or {@link User} objects
	*
	* @param array An array of user IDs.
	* @return array An array of {@link User} objects.
	*/
	public static function id_to_user($userids) {
		$ret = array();
		foreach($userids as $userid) {
			$ret[] = new User($userid);
		}
		return $ret;
	}

	/**
	* Sort a list of users when given a list of user IDs.
	*
	* @param array $userids An array of user IDs.
	* @return array An array of sorted {@link User} objects.
	*/
	public static function sort_users($userids) {
		$users = self::id_to_user($userids);
		usort($users, array('self', 'name_cmp'));
		return $users;
	}

	/**
	* The custom sort method for sorting users.
	*
	* @param object $user1 The first user.
	* @param object $user2 The second user.
	* @return int Depending on order, less than 0, 0, or greater than 0.
	*/
	public static function name_cmp($user1, $user2) {
		return strcasecmp($user1->name_comma, $user2->name_comma);
	}
}

?>
