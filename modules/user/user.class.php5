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
	* Keeps track of all cached sets of user information so we don't have to do two lookups.
	*/
	private static $cache = array();
	
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
	* The username of the user.
	*/
	private $username;

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
		global $I2_ERR, $I2_LDAP;

		if( $uid === NULL ) {
			if( isset($_SESSION['i2_uid']) ) {
				$this->username = $_SESSION['i2_uid'];
				$uid = $this->username;
				if (isSet(self::$cache[$uid])) {
					$this->info = &self::$cache[$uid];
				} else {
					$this->info = $I2_LDAP->search('ou=people',"iodineUid=$uid")->fetch_array(RESULT::ASSOC);
				}
				$this->myuid = $this->info['iodineUidNumber'];
			}
			else {
				$I2_ERR->fatal_error('Your password and username were correct, but you don\'t appear to exist in our database. If this is a mistake, please contact the intranetmaster about it.');
			}
		}

		elseif( is_numeric($uid) ) {
			if (isSet(self::$cache[$uid])) {
				$this->info = &self::$cache[$uid];
			} else {
				$this->info = $I2_LDAP->search('ou=people',"iodineUidNumber=$uid")->fetch_array(RESULT::ASSOC);
			}
			$this->username = $this->info['iodineUid'];
			$this->myuid = $uid;
		}

		else {
			if (isSet(self::$cache[$uid])) {
				$this->info = &self::$cache[$uid];
			} else {
				$this->info = $I2_LDAP->search('ou=people',"iodineUid=$uid")->fetch_array(RESULT::ASSOC);
			}
			$this->username = $uid;
			$this->myuid = $this->info['iodineUidNumber'];
		}

		/*
		** Put info in cache
		*/
		self::$cache[$this->myuid] = $this->info;
	}

	public function is_valid() {
		return $this->myuid !== NULL;
	}

	public function recache($field) {
		global $I2_LDAP;
		$this->info[$field] = $I2_LDAP->search_base("iodineUid={$this->iodineUid},ou=people",'style')->fetch_single_value();
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
		global $I2_SQL,$I2_ERR,$I2_LDAP;

		if( $this->username === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user! UID: '.$this->myuid);
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
				return $this->__get('graduationYear');
			case 'lname':
				return $this->__get('sn');
			case 'fname':
				return $this->__get('givenName');
			case 'mname':
				return $this->__get('middlename');
			case 'uid':
				return $this->__get('iodineUidNumber');
			case 'username':
				return $this->__get('iodineUid');
			case 'grade':
				return $this->get_grade($this->__get('graduationYear'));
			case 'phone_home':
				$phone = $this->__get('homePhone');
				return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
			case 'phone_cell':
				$phone = $this->__get('mobile');
				if($phone) {
					return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
				}
				return NULL;
			case 'phone_other':
				$phone = $this->__get('telephoneNumber');
				if($phone) {
					return array('(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6));
				}
				return array();
			case 'bdate':
				return date('M j, Y', strtotime($this->__get('birthday')));
		}
		
		//Check which table the information is in
		if( $this->info != NULL && isSet($this->info[$name])) {
			//returned cached info if we are caching
			return $this->info[$name];
		}
		d("$name not set!",1);
		
		$row = $I2_LDAP->search_base("iodineUid={$this->username},ou=people",$name);
		$res = $row->fetch_single_value();

		if( $res === FALSE ) {
			//$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->myuid.'` was used in obtaining information for '.$name);
			return NULL;
		}
		
		return $res;
	}

	/**
	* Gets the current grade of a student based on their graduation year.
	*
	* @param int $gradyear The year in which the student will graduate.
	* @return int The student's grade, 9-12, or -1 if other.
	*/
	public function get_grade($gradyear) {
		if (!$gradyear) {
			return -1;
		}
		$grade = ((int)i2config_get('senior_gradyear','foobertybroken','user'))-((int)$gradyear)+12;
		if ($grade >= 9 && $grade <= 12) {
			return $grade;
		}
		return -1;
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
		global $I2_LDAP;
		//throw new I2Exception("Changing user info is not yet supported!");
		$ldap = LDAP::get_admin_bind(i2config_get('admin_pw','ld4pp4ss','ldap'));
		$ldap->modify_val("iodineUid={$this->username},ou=people",$name,$val);
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
		global $I2_LDAP;
		$uid = $I2_LDAP->search_base("iodineUid=$username,ou=people",'iodineUidNumber')->fetch_single_value();
		if(!$uid) {
			return FALSE;
		}
		return new User($uid);
	}

	/**
	* Returns whether the user is automagically an admin by birthright.
	*/
	public function is_admin_user() {
		return $this->username == 'admin';
	}

	/**
	* Gets a student by their StudentID
	*
	* Returns a User object that has the StudentID $studentid.
	*
	* @param int $studentid The StudentID to get a User for.
	* @return User The user with the passed StudentID.
	*/
	public static function get_by_studentid($studentid) {
		global $I2_LDAP;
		return new User(self::studentid_to_uid($studentid));
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
		throw new I2Exception("User creation not supported!");
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
		global $I2_LDAP, $I2_ERR;

		if( $this->username === NULL ) {
			throw new I2Exception('Tried to retrieve information for nonexistent user!');
		}
		
		$ret = $I2_LDAP->search('ou=people',"iodineUid={$this->username}")->fetch_array(Result::ASSOC);

		if( $ret === FALSE ) {
			$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->username.'` was used in obtaining information');
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
		global $I2_LDAP;

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

		$ret = $I2_LDAP->query('ou=people',"iodineUid={$this->username}",$cols)->fetch_array(Result::BOTH);
		
		if( $ret === FALSE ) {
			$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->username.'` was used in obtaining information');
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
		throw new I2Exception('get_multi not implemented!');
	}

	/**
	* Search for users based on their information.
	*
	* @param string The search string.
	* @return array An array of {@link User} objects of the results. An
	* empty array is returned if no match is found.
	* @todo Improve drastically
	*/
	public function search_info($str) {
		global $I2_LDAP;
		d("search_info: $str",4);

		//FIXME: improve, close hole?
		$str = addslashes($str);

		//FIXME: improve this
		$res = $I2_LDAP->search('ou=people',
		"(|(iodineUid=$str)(givenName=$str)(sn=$str)(mname=$str))"
										,array('iodineUid'));
		
		if (!$res) {
			return array();
		}
		
		
		$ret = array();
		while ($uid = $res->fetch_single_value()) {
			$ret[] = new User($uid);
		}
		
		return $ret;
	}

	/**
	* Returns a student's schedule.
	*/
	public function schedule() {
		return new ScheduleSQL($this);
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
		usort($users, array('User', 'name_cmp'));
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

	/**
	* Gets the Iodine UID of the student with the given StudentID.
	*
	* @param int A user's Student ID.
	* @return int A user's Iodine UID.
	*/
	public static function studentid_to_uid($studentid) {
		global $I2_LDAP;
		return $I2_LDAP->search('ou=people,dc=tjhsst,dc=edu', "(tjhsstStudentId={$studentid})", 'iodineUidNumber')->fetch_single_value();
	}

}

?>
