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
	* The admin_all group, cached for convenience
	*/
	private static $admin_all_group;

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
					$this->info = array();
					$blah = $I2_LDAP->search('ou=people',"iodineUid=$uid",array('iodineUidNumber'))->fetch_array(RESULT::ASSOC);
					foreach ($blah as $key=>$val) {
						$this->info[strtolower($key)] = $val;
					}
				}
				$this->myuid = $this->info['iodineuidnumber'];
			}
			else {
				$I2_ERR->fatal_error('Your password and username were correct, but you don\'t appear to exist in our database. If this is a mistake, please contact the intranetmaster about it.');
			}
		}

		else if ($uid instanceof User) {
			/*
			** Someone tried new User(user object), so we'll just let them be stupid.
			*/
			$this->myuid = $uid->uid;
			$this->username = $uid->username;
			return $uid;
		} else {
			$uid = self::to_uidnumber($uid);
			if (!$uid) {
				throw new I2Exception('Blank uidnumber used in User construction');
			}
			$this->info = array();
			if (isSet(self::$cache[$uid]) && isSet(self::$cache[$uid]['iodineuid'])) {
				$this->info['iodineuid'] = self::$cache[$uid]['iodineuid'];
			} else {
				$blah = $I2_LDAP->search('ou=people',"iodineUidNumber=$uid",array('iodineUid'))->fetch_array(Result::ASSOC);
				if ($blah) {
					foreach ($blah as $key=>$val) {
						$this->info[strtolower($key)] = $val;
					}
				} else {
					throw new I2Exception('Invalid iodineUidNumber '.$uid);
				}
			}
			$this->username = $this->info['iodineuid'];
			$this->myuid = $uid;
		}

		/*
		** Put info in cache
		*/
		self::$cache[$this->myuid] = &$this->info;
	}

	public function is_valid() {
		return $this->myuid !== NULL;
	}

	public function recache($field) {
		global $I2_LDAP;
		$this->info[$field] = $I2_LDAP->search_base("iodineUid={$this->iodineUid},ou=people",'style')->fetch_single_value();
	}

	/**
	* Returns the uidnumber represented by the passed value.
	* You may pass in a StudentID or a username.
	*
	* @return int The IodineUidNumber
	*/
	public static function to_uidnumber($thing) {
		global $I2_LDAP;
		//d('Attempting to resolve '.print_r($thing,1).' to a uidNumber',6);
		if (is_numeric($thing)) {
			if ($thing > 99999) {
				/*
				** Number is a StudentID
				*/
				$res = $I2_LDAP->search('ou=people',"(&(objectClass=tjhsstStudent)(tjhsstStudentId=$thing))",array('iodineUidNumber'));
				$uid = $res->fetch_single_value();
				//self::$cache[$uid] = array('tjhsstStudentId' => $thing);
				if (!$uid) {
					d('StudentID lookup failed for StudentID '.$thing,6);
					return FALSE;
				}
				return $uid;
			} else {
				/*
				** Number is already a UidNumber
				*/
				return $thing;
			}
		} else {
			/*
			** Passed value is a username
			*/
			$uid = $I2_LDAP->search_base("iodineUid=$thing,ou=people",array('iodineUidNumber'))->fetch_single_value();
			if (!$uid) {
				d('Username lookup failed for username '.$thing,6);
				return FALSE;
			}
			self::$cache[$thing] = array('iodineUidNumber' => $uid);
			return $uid;
		}
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

		if(!$this->username) {
			throw new I2Exception('Tried to retrieve information for nonexistant user! UID: '.$this->myuid);
		}

		$name = strtolower($name);

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
			/*case 'graduationyear':
				if (in_array('tjhsstTeacher',$this->__get('objectClass'))) {
					return -1;
				}
				break;*/
		   case 'uid':
		   case 'uidnumber':
				return $this->__get('iodineUidNumber');
			case 'username':
				return $this->__get('iodineUid');
			case 'grade':
				$grade = self::get_grade($this->__get('graduationYear'));
				if ($grade < 0) {
					return 'staff';
				}
				return $grade;
			case 'phone_home':
				$phone = $this->__get('homePhone');
				$phone = preg_replace('/[^0-9]/', '', $phone);
				$international = strlen($phone) - 10;
				return ($international ? '+' . substr($phone, 0, $international) . ' ' : '') . '(' . substr($phone, $international, 3) . ') ' . substr($phone, $international + 3, 3) . '-' . substr($phone, $international + 6);
			case 'phone_cell':
				$phone = $this->__get('mobile');
				if($phone) {
					$phone = preg_replace('/[^0-9]/', '', $phone);
					$international = strlen($phone) - 10;
					return ($international ? '+' . substr($phone, 0, $international) . ' ' : '') . '(' . substr($phone, $international, 3) . ') ' . substr($phone, $international + 3, 3) . '-' . substr($phone, $international + 6);
				}
				return NULL;
			case 'phone_other':
				$phone = $this->__get('telephoneNumber');
				if($phone) {
					if(is_array($phone)) {
						$numbers = array();
						foreach($phone as $key => $value) {
							$value = preg_replace('/[^0-9]/', '', $value);
							$international = strlen($value) - 10;
							$numbers[] = ($international ? '+' . substr($value, 0, $international) . ' ' : '') . '(' . substr($value, $international, 3) . ') ' . substr($value, $international + 3, 3) . '-' . substr($value, $international + 6);
						}
					} else {
						$phone = preg_replace('/[^0-9]/', '', $phone);
						$international = strlen($phone) - 10;
						return ($international ? '+' . substr($phone, 0, $international) . ' ' : '') . '(' . substr($phone, $international, 3) . ') ' . substr($phone, $international + 3, 3) . '-' . substr($phone, $international + 6);
					}
				}
				return NULL;
			case 'bdate':
				$born = $this->__get('birthday');
				if (!$born) { // heh
					return FALSE;
				}
				return date('M j, Y', strtotime($born));
			case 'counselor_obj':
				$couns = $this->__get('counselor');
				if (!$couns) {
					return FALSE;
				}
				$user = new User($couns);
				return $user;
			case 'counselor_name':
				$couns = $this->__get('counselor_obj');
				if ($couns) {
					return $couns->sn;
				}
				return FALSE;
			case 'preferredPhoto':
			case 'preferredphoto':
			case 'preferred_photo':
				$row = $I2_LDAP->search_base(LDAP::get_user_dn($this), 'preferredPhoto')->fetch_array(Result::NUM);
				if(!$row) {
					return NULL;
				}
				$row = $I2_LDAP->search($row['preferredPhoto'])->fetch_binary_value('jpegPhoto');
				return $row[0];
			case 'show_map':
					  return ($this->__get('perm-showmap')!='FALSE')&&($this->__get('perm-showmap-self')!='FALSE');
			case 'showpictureself':
			case 'showaddressself':
			case 'showphoneself':
			case 'showmapself':
			case 'showscheduleself':
		 	case 'hideeighthself':
			case 'showbdayself':
				$row = $I2_LDAP->search_base(LDAP::get_user_dn($this->username),$name);
				if (!$row) {
					return NULL;
				}
				return $row->fetch_single_value()=='TRUE'?TRUE:FALSE;
		}
		
		//Check which table the information is in
		if( $this->info != NULL && isSet($this->info[$name])) {
			//returned cached info if we are caching
			return $this->info[$name];
		}
		
		$row = $I2_LDAP->search_base("iodineUid={$this->username},ou=people",$name);
		
		if (!$row) {
			//$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->myuid.'` was used in obtaining information for '.$name);
			return NULL;
		}
		
		$res = $row->fetch_single_value();

		/*
		** Emails are special - they can be autogenerated
		*/
		if (!$res && $name == 'mail') {
			if ($this->is_group_member('grade_staff')) {
				return $this->__get('givenName').'.'.$this->__get('sn').'@fcps.edu';
			} else {
				return $this->username . '@tjhsst.edu';
			}
		}

		if (!$res) {
			d("$name not set!",6);
			return;
		}

		$this->info[$name] = $res;
		
		return $res;
	}	

	/**
	* Gets the current grade of a student based on their graduation year.
	*
	* @param int $gradyear The year in which the student will graduate.
	* @return int The student's grade, 9-12, or -1 if other.
	*/
	public static function get_grade($gradyear) {
	   if (!$gradyear) {
			d('False gradyear passed to get_grade',6);
			return -1;
		}
		$grade = ((int)i2config_get('senior_gradyear','foobertybroken','user'))-((int)$gradyear)+12;
		if ($grade >= 9 && $grade <= 12) {
			return $grade;
		}
		d('Gradyear out-of-bounds passed to get_grade',5);
		return -1;
	}

	/**
	* Gets the graduation year of a student based on their current grade.
	*
	* @param int $grade The student's current grade
	* @return int The graduation year of the student
	*/
	public static function get_gradyear($grade) {
			  if (!$grade || $grade < 9 || $grade > 12) {
						 d('Grade out-of-bounds passed to get_gradyear',5);
			  }
			  $gradyear = ((int)i2config_get('senior_gradyear','ntohurchouorchu','user'))-((int)$grade)+12;
			  return $gradyear;
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
		$this->set($name,$val);
	}

	public function set($name,$val,$ldap=NULL) {
		global $I2_LDAP,$I2_USER;
		if ($ldap === NULL) {
			$ldap = $I2_LDAP;
		}
		$name = strtolower($name);
		if ($name == 'username' || $name == 'iodineuid') {
			$this->username = $val;
		}
		/*if (!$this->username) {
			throw new I2Exception('User entries without usernames cannot be modified!');
		}*/
		if ($val == '') {
			$val = array();
		}
			
		if($name == 'mobile' || $name == 'homephone' || $name == 'telephoneNumber') {
			if(is_array($val)) {
				foreach($val as $key=>$value) {
					$val[$key] = preg_replace('/[^0-9]/', '', $value);
				}
			} else {
				$val = preg_replace('/[^0-9]/', '', $val);
			}
		}

		switch ($name) {
			case 'phone_cell':
				$this->set('mobile',$val,$ldap);
				return;
			case 'phone_home':
				$this->set('homePhone',$val,$ldap);
				return;
			case 'phone_other':
				$this->set('phoneNumber',$val,$ldap);
				return;
			case 'showmapself':
			case 'showbdayself':
			case 'showscheduleself':
			case 'hideeighthself':
			case 'showaddressself':
			case 'showphoneself':
				$val = ($val=='on'||$val=='TRUE')?'TRUE':'FALSE';
				break;
			case 'showpictureself':
				// Set all the pictures' attributes to match the parent user's
				$val = ($val=='on'||$val=='TRUE')?'TRUE':'FALSE';
				$res = $ldap->search_one(LDAP::get_user_dn($this->__get('username')),'objectClass=iodinePhoto',array('cn'));
				while ($row = $res->fetch_array()) {
					$ldap->modify_val(LDAP::get_pic_dn($row['cn'],$this),'showpictureself',$val);
				}
		}
		
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

		/*
		** admin_all is admin_*
		*/
	
		if (!self::$admin_all_group) {
			self::$admin_all_group = new Group('admin_all');
		}
		if (substr($groupname,0,7) == 'admin_' && self::$admin_all_group->has_member($this)) {
			return TRUE;
		}
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
	* Provides legacy Intranet 1 powersearching.
	* I firmly believe that the transparent modern searching style performed by search_info is superior.
	* However, to keep the yearbook happy, this familiar style has been implemented.
	* @param string $str The Intranet-1 style search string (fieldname:value), with wildcards etc.
	* @param arary $grades The grades to search (overrides grade:)
	* @return array An array of {@link User} objects representing the results, or an empty array if no matches.
	*/
	public static function search_info_legacy($str,$grades=FALSE) {
			  global $I2_LDAP;
			  /*
			  ** Map things users would type into LDAP attributes
			  */
			  $maptable = array(
						 'firstname' => 'givenName',
						 'first' => 'givenName',
						 'lastname' => 'sn',
						 'last' => 'sn',
						 'firstnamesound' => 'soundexfirst',
						 'lastnamesound' => 'soundexlast',
						 'city' => 'l',
						 'town' => 'l',
						 'middle' => 'mname',
						 'phone' => 'homePhone',
						 'cell' => 'mobile',
						 'telephone' => 'homePhone',
						 'address' => 'street'
			  );

			  $soundexed = array(
						 'soundexfirst' => 1,
						 'soundexlast' => 1
			  );

			  /*
			  ** Construct query in three parts: prefix + infix + postfix
			  */

			  $prefix = '';
			  $postfix = '';
			  $infix = '';
			  $ormode = FALSE;
	   	  if ($grades) {
				  $prefix = '(&(|';
				  foreach ($grades as $grade) {
							 $prefix .= "(graduationYear=$grade)";
				  }
				  $prefix .= ')';
				  $postfix = ')';
			  }
			  
			  $separator = " \t";

			  $tok = strtok($str,$separator);

			  while ($tok !== FALSE) {
					$colonpos = strpos($str,':');
					$key = FALSE;
					$eq = '=';
					if ($colonpos == 0) {
							  //Invalid: leading colon
							  $colonpos = -1;
							  $tok = str_replace(':','',$tok);
					}
				   if	($colonpos == strlen($tok)-1) {
							  // Invalid: trailing colon
							  // Assume blah:*
							  $tok .= '*';
					}
					if ($colonpos) {
							  $boom = explode(':',$str);
							  if (count($boom) > 2) {
								  // Invalid: more than one colon
								  $tok = str_replace(':','',$tok);
							  } else {
										 $key = $boom[0];
										 //Apply attributename translation
										 if (isSet($maptable[$key])) {
													$key = $maptable[$key];
										 }
										 $tok = $boom[1];
										 $poteq = substr($tok,0,1);
										 // Check if gt or lt was specified instead of equals
										 switch ($poteq) {
										 	 case '>':
													$eq = '>=';
										 	 case '<':
													$eq = '<=';
											 case '=':
											   // Strip character - this is run for gt,lt,and specified eq
											   $tok = substr($tok,1);
												break;
											 default:
												break;								
										 }
							  }
					}
					if (isSet($key)) {
							  // We know what we're trying to search for
							  if (isSet($soundexed[strtolower($key)])) {
										 $tok = soundex($tok);
							  }
							  $prefix = '(&'.$prefix;
							  $infix .= '('.$key.$eq.$tok.')';
							  $postfix .= ')';
					} else {
							  if (strcasecmp($tok,'OR') == 0) {
										 $ormode = TRUE;
										 // User wants an OR for these search terms
										 continue;
							  }
							  if ($ormode) {
								  $prefix = '(&'.$prefix;
								  $postfix .= ')';
								  $infix .= "(|(iodineUid=$tok)(sn=$tok)(mname=$tok)(givenName=$tok))";
								  $ormode = FALSE;
							  }
					}
			  		$tok = strtok($str,$separator);
			  }
			  $res = $I2_LDAP->search(LDAP::get_user_dn(),$prefix.$infix.$postfix,array('iodineUid'))->fetch_all_single_values();
			  return self::sort_users($res);
	}

	/**
	* Search for users based on their information.
	*
	* @param string $str The search string.
	* @param array $grades An array of graduation years to find results for
	* @param boolean $old_style Whether to perform Intranet-1 style power searches
	* 									 (or the best possible approximation thereof)
	* @return array An array of {@link User} objects of the results. An
	* empty array is returned if no match is found.
	* @todo Improve drastically
	*/
	public static function search_info($str,$grades=NULL,$old_style=FALSE) {
		global $I2_LDAP;
		d("search_info: $str".($old_style?' (legacy)':''),6);

		if ($grades && !is_array($grades)) {
				  $grades = explode(',',$grades);
		}

		if ($old_style) {
			return self::search_info_legacy($str,$grades);
		}

		if ($grades) {
			$newgrades = '(graduationYear=*)';
			$newgrades = '(|';
		  	foreach ($grades as $grade) {
				$newgrades .= "(graduationYear=$grade)";
			}
			$newgrades .= ')';
		} else {
			$newgrades = '(objectClass=*)';
		}

		//FIXME: improve, close hole?
		// Note from BRJ: Because we do server-side access control, the negative effects of LDAP code injection are minimal.
		// They can create custom search strings, but they can't get any info they shouldn't have access to anyhow, and
		// generating invalid search queries (thus causing errors) does no harm to anybody.
		//$str = addslashes($str);

		$str = trim($str);


		$results = array();
		$firstres = TRUE;
		
		if (!$str || $str == '') {
			$res = $I2_LDAP->search('ou=people',"$newgrades",array('iodineUid'));
			while ($uid = $res->fetch_single_value()) {
				$results[] = $uid;
			}
		} elseif (is_numeric($str)) {
				  $res = $I2_LDAP->search('ou=people',"(&(|(tjhsstStudentId=$str)(iodineUidNumber=$str))$newgrades)",array('iodineUid'));
				  while ($uid = $res->fetch_single_value()) {
							 $results[] = $uid;
				  }
		} else {

			/*
			** Complicated code: finds results which match ALL space-delimited terms in the search string
			*/

			$numtokens = 0;
			$separator = " \t";
			$tok = strtok($str,$separator);
			$preres = array();


			while ($tok !== FALSE) {
				//$soundex = soundex($tok);

				$res = $I2_LDAP->search(LDAP::get_user_dn(),
			//	"(&(|(soundexFirst=$soundex)(soundexLast=$soundex)(givenName=*$tok*)(sn=*$tok*)(iodineUid=*$tok*)(mname=*$tok*))$newgrades)"
				"(&(|(givenName=*$tok*)(sn=*$tok*)(iodineUid=*$tok*)(mname=*$tok*))$newgrades)"
				,array('iodineUid'));

				while ($uid = $res->fetch_single_value()) {
					if (!$firstres && !isSet($preres[$uid])) {
							  // Results which weren't previously found should be discarded
							  continue;
					} elseif (!$firstres) {
							  //Increment the value so we know which results were only in the first query (they'll have a value of 1)
							  $preres[$uid]++;
					} else {
							  $preres[$uid] = 1;
					}
				}

				$firstres = FALSE;

				$tok = strtok($separator);
				$numtokens++;
			}

			if ($numtokens == 0) {
					  $results = array();
			} elseif ($numtokens == 1) {
					  $results = array_keys($preres);
			} else {
				foreach ($preres as $key=>$value) {
						  if ($value == 1) {
									 // The query matched our first token but not later ones - break
									 continue;
						  }
						  $results[] = $key;
				}
			}
		}
		$ret = self::sort_users($results);

		return $ret;
	}

	/**
	* Returns a student's schedule.
	*/
	public function schedule() {
		return new Schedule($this);
	}

	/**
	* Convert an array of user IDs into an array or {@link User} objects
	*
	* @param array An array of user IDs.
	* @return array An array of {@link User} objects.
	*/
	public static function id_to_user($userids) {
		$ret = array();
		if (!is_array($userids)) {
				  $userids = array($userids);
		}
		foreach($userids as $userid) {
			if (!$userid) {
				continue;
			}
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
		return $I2_LDAP->search('ou=people,dc=tjhsst,dc=edu', "(&(objectClass=tjhsstStudent)(tjhsstStudentId={$studentid}))", 'iodineUidNumber')->fetch_single_value();
	}

}

?>
