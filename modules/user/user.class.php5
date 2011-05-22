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
	*
	*/
	private static $usernametouid = array();

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
	
	private static $senior_gradyear;

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
			//if( isset($_SESSION['i2_uid']) ) {
			//	$this->username = $_SESSION['i2_uid'];
			if( isset($_SESSION['i2_username']) ) {
				$this->username = $_SESSION['i2_username'];
				$uid = $this->username;
				if (isset(self::$usernametouid[$uid])&&isSet(self::$cache[self::$usernametouid[$uid]])) {
					$this->info = &self::$cache[self::$usernametouid[$uid]];
				} else {
					$this->info = array();
					$blah = $I2_LDAP->search(LDAP::get_user_dn(),"iodineUid=$uid")->fetch_array(RESULT::ASSOC);
					foreach ($blah as $key=>$val) {
						$this->info[strtolower($key)] = $val;
					}
					$this->info['allset']=True;
				}
				$this->myuid = $this->info['iodineuidnumber'];
				$_SESSION['i2_userid']=$this->myuid;
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
			$this->info = $uid->info;
			return $uid;
		} else {
			$uid = self::to_uidnumber($uid);
			if (!$uid) {
				throw new I2Exception('Blank uidnumber used in User construction'.$this->username);
			}
			$this->info = array();
			if (isSet(self::$cache[$uid]) && isSet(self::$cache[$uid]['iodineuid'])) {
				$this->info = self::$cache[$uid];
			} else {
				$blah = $I2_LDAP->search(LDAP::get_user_dn(),"iodineUidNumber=$uid")->fetch_array(Result::ASSOC);
				if ($blah) {
					foreach ($blah as $key=>$val) {
						$this->info[strtolower($key)] = $val;
					}
					$this->info['allset']=True;
				} else {
					throw new I2Exception('Invalid iodineUidNumber '.$uid);
					//warn('Invalid iodineUidNumber '.$uid);
					//Below two lines are 9996 replacement hack; while this can be safely removed once we are sure we properly clean out all the users from all the tables upon deletion using dataimport, I recommend leaving it in place in case other coding errors cause the same effect, as occurred 9/2009. --wyang, comment modified 2009/09/12
					$this->info['iodineuid']="nostudent";
					$this->info['iodineuidnumber']=$uid;
				}
			}
			$this->username = $this->info['iodineuid'];
			$this->myuid = $uid;
		}

		/*
		** Set the null array
		*/
		if(!isset($this->info['__nulls']))
			$this->info['__nulls']=array();
		/*
		** Put info in cache
		*/
		self::$cache[$this->myuid] = &$this->info;
		/*
		** Cache the username->uid connection
		*/
		self::$usernametouid[$this->username]=$this->myuid;
	}

	public function is_valid() {
		return $this->myuid !== NULL;
	}

	public function recache($field) {
		global $I2_LDAP;
		$this->info[$field] = $I2_LDAP->search_base(LDAP::get_user_dn_username($this->username),array($field))->fetch_single_value();
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
				$res = $I2_LDAP->search(LDAP::get_user_dn(),"(&(objectClass=tjhsstStudent)(tjhsstStudentId=$thing))",array('iodineUidNumber'));
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

		if(isset($this->info[$name])){
			return $this->info[$name];
		}
		if(isset($this->info['__nulls']) && is_array($this->info['__nulls']) && in_array($name,$this->info['__nulls']))
			return;
		/* pseudo-fields
		** must use explicit __get calls here, since recursive implicit
		** __get calls apparently are not allowed.
		*/
		switch( $name ) {
			case 'info':
				return $this->info;
			case 'allowed_modules':
				$this->info['allowed_modules']=$I2_SQL->query("SELECT module FROM allowed_modules WHERE userclass=%s",$this->__get("objectclass"))->fetch_all_single_values();
				return $this->info['allowed_modules'];
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
			case 'gradyear':
			case 'year':
				return $this->__get('graduationYear');
			case 'lname':
				return $this->__get('sn');
			case 'fname':
				return $this->__get('givenName');
			case 'firstornick':
				$nick = $this->__get('nickname');
				if ($nick) {
					return $nick;
				}
				return $this->__get('fname');
			case 'mname':
				return $this->__get('middlename');
			case 'tjmail':
				return $this->get_tjmail();
		   	case 'uid':
		   	case 'uidnumber':
			case 'iodineuidnumber':
				return $this->myuid;
				// Cut down on the LDAP queries. myuid is set in the constructor already.
				//return $this->__get('iodineUidNumber');
			case 'username':
				return $this->username;
			case 'sex':
				return $this->gender;
			case 'grade':
				$grade = self::get_grade($this->__get('graduationYear'));
				if ($grade < 0) {
					if($this->__get('objectclass')=='tjStarPresenter')
						return 'TJStar';
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
			case 'college':
				$row = $I2_SQL->query("SELECT name, CollegeName, college_certain FROM senior_destinations LEFT JOIN CEEBMap USING (CEEB) WHERE uid=%d", $this->myuid)->fetch_array(Result::ASSOC);
				if(count($row) == 1) {
					return "";
				}
				$ret = $row["CollegeName"];
				if(!$row["college_certain"]) {
					$ret .= " (unsure)";
				}
				return $ret;
			case 'major':
				$row = $I2_SQL->query("SELECT name, MajorMap.major, major_certain FROM senior_destinations LEFT JOIN MajorMap ON senior_destinations.major=MajorMap.MajorID WHERE uid=%d", $this->myuid)->fetch_array(Result::ASSOC);
				if(count($row) == 1) {
					return "";
				}
				$ret = $row["major"];
				if(!$row["major_certain"]) {
					$ret .= " (unsure)";
				}
				return $ret;
			
			case 'photonames':
				$cns = $I2_LDAP->search(LDAP::get_user_dn_username($this->username), 'objectClass=iodinePhoto', array('cn'))->fetch_col('cn');
				@usort($cns, array("User", "sort_photos"));
				return $cns;
			case 'preferredphotoname':
				$preferredPhoto = $this->__get('preferredPhoto');
				if ($preferredPhoto == 'AUTO') {
					$photos = $this->__get('photoNames');
					$preferredPhoto = array_pop($photos);
				}
				return $preferredPhoto;
			case 'preferredphotoimage':
			case 'preferred_photo_image':
				return $this->__get($this->__get('preferredPhotoName'));
			case 'freshmanphoto':
			case 'sophomorephoto':
			case 'juniorphoto':
			case 'seniorphoto':
				$userdn = LDAP::get_user_dn_username($this->username);
				@$pic = $I2_LDAP->search("cn=$name,$userdn")->fetch_binary_value('jpegPhoto');
				return $pic[0];
			case 'show_map':
					  return ($this->__get('perm-showmap')!='FALSE')&&($this->__get('perm-showmap-self')!='FALSE');
			case 'newsforwarding':
				return $this->get_news_forwarding();
			case 'eighthalert':
				return $this->get_eighth_alert_status();
			case 'eighthnightalert':
				return $this->get_eighth_night_alert_status();
			case 'relationship':
				return $this->get_relationship_status();
			case 'gradename'://Used for the windows file mounts, hence the capitolization
				$convert=array(9=>'Freshman',10=>'Sophomore',11=>'Junior',12=>'Senior','staff'=>'Staff');
				return $convert[$this->__get('grade')];
			case 'comments':
				return $this->__get("eighthoffice-comments");
			case 'showpictureself':
			case 'showpicture':
			case 'showpictures':
			case 'showaddressself':
			case 'showaddress':
			case 'showphoneself':
			case 'showphone':
			case 'showmapself':
			case 'showmap':
			case 'showscheduleself':
			case 'showschedule':
		 	case 'showeighthself':
		 	case 'showeighth':
			case 'showbdayself':
			case 'showbday':
			case 'showbdateself':
			case 'showbdate':
			case 'showlocker':
			case 'showlockerself':
				if(!isset($this->info[$name])) {
					$row = $I2_LDAP->search_base(LDAP::get_user_dn_username($this->username),$name);
					if (!$row) {
						return NULL;
					}
					$this->info[$name]=$row->fetch_single_value();
					self::$cache[$this->myuid][$name]=$this->info[$name];
				}
				return $this->info[$name]=='TRUE'?TRUE:FALSE;
				break;
			case 'showfreshmanpicture':
				return $this->get_photo_perm("freshmanPhoto","showpicture");
				break;
			case 'showsophomorepicture':
				return $this->get_photo_perm("sophomorePhoto","showpicture");
				break;
			case 'showjuniorpicture':
				return $this->get_photo_perm("juniorPhoto","showpicture");
				break;
			case 'showseniorpicture':
				return $this->get_photo_perm("seniorPhoto","showpicture");
				break;
			case 'showfreshmanpictureself':
				return $this->get_photo_perm("freshmanPhoto","showpictureself");
				break;
			case 'showsophomorepictureself':
				return $this->get_photo_perm("sophomorePhoto","showpictureself");
				break;
			case 'showjuniorpictureself':
				return $this->get_photo_perm("juniorPhoto","showpictureself");
				break;
			case 'showseniorpictureself':
				return $this->get_photo_perm("seniorPhoto","showpictureself");
				break;
			// Lots of aliases for different stuff in ldap
			case 'studentid':
			case 'fcpsstudentid':
				return $this->__get('tjhsststudentid');
		}
		
		//Check which table the information is in
		if( $this->info != NULL && isSet($this->info[$name])) {
			//returned cached info if we are caching
			return $this->info[$name];
		}
		
		d("Missed cache, name was ".$name,6);
		if($this->info['allset']) { //If all the information is initiallized, short the checks.
			$this->info['__nulls'][]=$name;
			self::$cache[$this->myuid]['__nulls'][]=$name;
			return NULL;
		}

		$row = $I2_LDAP->search_base(LDAP::get_user_dn_username($this->username),$name);
		
		if (!$row) {
			//$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->myuid.'` was used in obtaining information for '.$name);
			$this->info['__nulls'][]=$name;
			self::$cache[$this->myuid]['__nulls'][]=$name;
			return NULL;
		}
		
		$res = $row->fetch_single_value();
		if(is_array($res)&&count($res)==0) {
			//If it's just an array, treat it as a null value. This fixes a few fields that don't handle it correctly.
			$this->info['__nulls'][]=$name;
			self::$cache[$this->myuid]['__nulls'][]=$name;
			return NULL;
		}

		/*
		** Emails are special - they can be autogenerated
		** But lets not do that all the time...causes more confusion than not sometimes. --wyang 2007/08/31
		*/
		/*if (!$res && $name == 'mail') {
			return $this->get_tjmail();
		}*/

		if (!$res) {
			d("$name not set!",6);
			$this->info['__nulls'][]=$name;
			self::$cache[$this->myuid]['__nulls'][]=$name;
			return;
		}

		$this->info[$name] = $res;
		self::$cache[$this->myuid][$name]=$res;
		d("VALID CACHE MISS: ".$name,3);
		return $res;
	}	

	public function get_tjmail() {
		if ($this->is_group_member('grade_staff')) {
			return $this->__get('givenName').'.'.$this->__get('sn').i2config_get('staff_suffix','@fcps.edu','mail');
		} else {
			return $this->username . i2config_get('student_suffix','@tjhsst.edu','mail');
		}
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

		$grade = self::get_gradyear(12) - ((int)$gradyear) + 12;
		if ($grade >= 9 && $grade <= 12) {
			return $grade;
		}
		d('Gradyear out-of-bounds passed to get_grade',5);
		return -1;
	}

	/**
	* Get the list of a user's friends.
	*
	* @param int $uid The integer user id of the user whose friends are being queried.
	* @return array The list of all of a student's intranet friends.
	*/
	public static function get_friends($uid) {
		global $I2_SQL;
		$list = $I2_SQL->query('SELECT distinct fid FROM friends WHERE uid=%d',$uid)->fetch_all_single_values();
		return $list;
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
		if (! isSet(self::$senior_gradyear)) {
			$date = getdate();
			self::$senior_gradyear = $date['year'] + ($date['mon'] >= 9 ? 1 : 0);
		}
		$gradyear = self::$senior_gradyear - ((int)$grade) + 12;
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
			throw new I2Exception('User iodineUids cannot be modified via the __set method!');
		}
		if ($name == 'iodineuidnumber') {
			throw new I2Exception('User iodineUidNumbers cannot be modified via the __set method!');
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

		//Put the new value into the cache.
		if(!isset($val) || (is_array($val) && count($val)==0)) {
			$this->info['__nulls'][]=$name;
			self::$cache[$this->myuid]['__nulls'][]=$name;
			$this->info[$name]=NULL;
			self::$cache[$this->myuid][$name]=NULL;
		} else {
			$this->info[$name]=$val;
			self::$cache[$this->myuid][$name]=$val;
		}
		switch ($name) {
			case 'counselor_name':
				$couns = $ldap->search_one('ou=people,dc=tjhsst,dc=edu',
					"(&(objectclass=tjhsstTeacher)(sn=$val))",
					array('iodineuidnumber'))->fetch_single_value();
				if($couns) {
					$this->set('counselor',$couns,$ldap);
				}
				return;
			case 'fname':
				$this->set('givenName',$val,$ldap);
				return;
			case 'mname':
				$this->set('middlename',$val,$ldap);
				return;
			case 'lname':
				$this->set('sn',$val,$ldap);
				return;
			case 'grade':
				$this->set('graduationYear',self::get_gradyear($val),$ldap);
				return;
			case 'sex':
				$this->set('gender',$val,$ldap);
				return;
			case 'bdate':
				$this->set('birthday', date('Ymd', strtotime($val)), $ldap);
				return;
			case 'phone_cell':
				$this->set('mobile',$val,$ldap);
				return;
			case 'phone_home':
				$this->set('homePhone',$val,$ldap);
				return;
			case 'phone_other':
				$this->set('phoneNumber',$val,$ldap);
				return;
			case 'newsforwarding':
				$this->set_news_forwarding($val);
				$this->info[$name]=($val=='TRUE');
				return;
			case 'eighthalert':
				$this->set_eighth_alert_status($val);
				$this->info[$name]=($val=='TRUE');
				return;
			case 'eighthnightalert':
				$this->set_eighth_night_alert_status($val);
				$this->info[$name]=($val=='TRUE');
				return;
			case 'relationship':
				$this->set_relationship_status($val);
				$this->info[$name]=$val;
				return;
			case 'showmapself':
			case 'showmap':
			case 'showbdayself':
			case 'showbdateself':
			case 'showbdate':
			case 'showscheduleself':
			case 'showschedule':
			case 'showeighthself':
			case 'showeighth':
			case 'showaddressself':
			case 'showaddress':
			case 'showphoneself':
			case 'showphone':
			case 'showlocker':
			case 'showlockerself':
				$val = ($val=='on'||$val=='TRUE')?'TRUE':'FALSE';
				break;
			case 'showpicture':
			case 'showpictures':
			case 'perm_showpicture':
			case 'perm_showpictures':
				// Set all the pictures' attributes to match the parent user's
				$val = ($val=='on'||$val=='TRUE')?'TRUE':'FALSE';
				$this->set("showfreshmanpicture",$val);
				$this->set("showsophomorepicture",$val);
				$this->set("showjuniorpicture",$val);
				$this->set("showseniorpicture",$val);
				break;
			case 'showpictureself':
			case 'perm_showpictureself':
				$val = ($val=='on'||$val=='TRUE')?'TRUE':'FALSE';
				break;
			case 'showfreshmanpicture':
			case 'perm_showfreshmanpicture':
				$this->set_photo_perm("freshmanPhoto","showpicture",$val);
				return;
			case 'showsophomorepicture':
			case 'perm_showsophomorepicture':
				$this->set_photo_perm("sophomorePhoto","showpicture",$val);
				return;
			case 'showjuniorpicture':
			case 'perm_showjuniorpicture':
				$this->set_photo_perm("juniorPhoto","showpicture",$val);
				return;
			case 'showseniorpicture':
			case 'perm_showseniorpicture':
				$this->set_photo_perm("seniorPhoto","showpicture",$val);
				return;
			case 'showfreshmanpictureself':
			case 'perm_showfreshmanpictureself':
				$this->set_photo_perm("freshmanPhoto","showpictureself",$val);
				return;
			case 'showsophomorepictureself':
			case 'perm_showsophomorepictureself':
				$this->set_photo_perm("sophomorePhoto","showpictureself",$val);
				return;
			case 'showjuniorpictureself':
			case 'perm_showjuniorpictureself':
				$this->set_photo_perm("juniorPhoto","showpictureself",$val);
				return;
			case 'showseniorpictureself':
			case 'perm_showseniorpictureself':
				$this->set_photo_perm("seniorPhoto","showpictureself",$val);
				return;
			case 'webpage':
				//TODO: less hacky... someone might theoretically want to use ftp or something
				if (is_array($val)) {
					foreach ($val as $key=>$thisval) {
						if (substr($thisval, 0, 4) != 'http') {
							$val[$key] = 'http://'.$thisval;
						}
					}
				}
				else {
					if (substr($val, 0, 4) != 'http') {
						$val = 'http://'.$val;
					}
				}
				break;
		}
		
		if($val==NULL) {
			$val=array(); //LDAP will not like you if you give it null entries (i.e. the modify fails)
		}
		$ldap->modify_val(LDAP::get_user_dn_username($this->username),$name,$val);
	}

	/**
	* Change a user's iodineUid.
	*/
	public function set_uid($username) {
		global $I2_LDAP;

		$olddn = LDAP::get_user_dn_username($this->username);
		$I2_LDAP->rename($olddn, $username);
		$this->username = $username;
		$this->info['iodineuid'] = $username;

		$newdn = LDAP::get_user_dn_username($this->username);

		/*
		** Maybe we want to be fancy and do something flexible? For now
		** we'll just look at classes, the only things that point to
		** users...
		*/
		//$refs = $I2_LDAP->search_sub("dc=tjhsst,dc=edu", ":dn:distinguishedNameMatch:=$olddn", array('dn', 'sponsorDn'))->fetch_all_arrays(Result::ASSOC);
		$refs = $I2_LDAP->search_sub(LDAP::get_schedule_dn(), "(&(objectClass=tjhsstClass)(sponsorDn=$olddn))", array('dn', 'sponsorDn'))->fetch_all_arrays(Result::ASSOC);
		foreach ($refs as $dn => $result) {
			$sponsorDn = $result['sponsorDn'];
			if (is_array($sponsorDn)) {
				$index = array_search($olddn, $sponsorDn);
				$sponsorDn[$index] = $newdn;
			}
			else {
				$sponsorDn = $newdn;
			}
			$I2_LDAP->modify_val($dn, 'sponsorDn', $sponsorDn);
		}
	}

	/**
	* Change a user's iodineUidNumber.
	*/
	public function set_uidnumber($uidnumber) {
		global $I2_LDAP, $I2_SQL;

		$olduid = $this->myuid;
		$I2_LDAP->modify_val(LDAP::get_user_dn_username($this->username), 'iodineUidNumber', $uidnumber);
		$this->myuid = $uidnumber;
		$this->info['iodineuidnumber'] = $uidnumber;

		foreach (Newimport::$sqltables as $table => $field) {
			$I2_SQL->query('UPDATE %c SET %c=%d WHERE %c=%d', $table, $field, $uidnumber, $field, $olduid);
		}
	}

	/**
	* Set the permission bits on a photo.
	*/
	public function set_photo_perm($photoname,$perm,$val,$ldap=NULL) {
		global $I2_LDAP;
		if ($ldap === NULL) {
			$ldap = $I2_LDAP;
		}
		$val = ($val=='on'||$val=='TRUE')?'TRUE':'FALSE';
		if (in_array($photoname, $this->__get("photonames"))) {
			$ldap->modify_val(LDAP::get_pic_dn($photoname,$this),$perm,$val);
		}
		else {
			d("$photoname is not currently imported", 8);
		}
	}

	/**
	* Get the permission bits on a photo.
	*/
	public function get_photo_perm($photoname,$perm,$ldap=NULL) {
		global $I2_LDAP;
		if ($ldap === NULL) {
			$ldap = $I2_LDAP;
		}
		if (in_array($photoname, $this->__get("photonames"))) {
			d("searching for permissions on $photoname",8);
			$row = $ldap->search_base(LDAP::get_pic_dn($photoname,$this),array($perm));
			if(!$row)
				return NULL;
			$result = $row->fetch_single_value();
			return $result=='TRUE'?TRUE:FALSE;
		}
		return NULL;
	}

	/**
	* Change a user's news forwarding status.
	*/
	public function set_news_forwarding($val) {
		global $I2_SQL;
		if ($val == 'TRUE' || $val == 'on') {
			if (!$this->get_news_forwarding())
				$I2_SQL->query('INSERT INTO news_forwarding VALUES (%d)',$this->myuid);
		} else {
			$I2_SQL->query('DELETE FROM news_forwarding WHERE uid=%d',$this->myuid);
		}
	}

	/**
	* Get a user's news forwarding status.
	*/
	public function get_news_forwarding() {
		global $I2_SQL;
		return count($I2_SQL->query('SELECT * FROM news_forwarding WHERE uid=%d',$this->myuid)->fetch_all_single_values())>0;
	}


	/**
	* Change a user's eighth alert status.
	*/
	public function set_eighth_alert_status($val) {
		global $I2_SQL;
		if ($val == 'TRUE' || $val == 'on') {
			if (!$this->get_eighth_alert_status())
				$I2_SQL->query('INSERT INTO eighth_alerts VALUES (%d)',$this->myuid);
		} else {
			$I2_SQL->query('DELETE FROM eighth_alerts WHERE userid=%d',$this->myuid);
		}
	}

	/**
	* Get a user's eighth alert status.
	*/
	public function get_eighth_alert_status() {
		global $I2_SQL;
		return count($I2_SQL->query('SELECT * FROM eighth_alerts WHERE userid=%d',$this->myuid)->fetch_all_single_values())>0;
	}
	/**
	* Change a user's night-before eighth alert status.
	*/
	public function set_eighth_night_alert_status($val) {
		global $I2_SQL;
		if ($val == 'TRUE' || $val == 'on') {
			if (!$this->get_eighth_night_alert_status())
				$I2_SQL->query('INSERT INTO eighth_night_alerts VALUES (%d)',$this->myuid);
		} else {
			$I2_SQL->query('DELETE FROM eighth_night_alerts WHERE userid=%d',$this->myuid);
		}
	}

	/**
	* Get a user's night-before eighth alert status.
	*/
	public function get_eighth_night_alert_status() {
		global $I2_SQL;
		return count($I2_SQL->query('SELECT * FROM eighth_night_alerts WHERE userid=%d',$this->myuid)->fetch_all_single_values())>0;
	}

	/**
	* Set a user's "relationship" status
	*/
	public function set_relationship_status($val) {
		global $I2_SQL;
		
		if ($I2_SQL->query('SELECT COUNT(*) FROM relationships WHERE uid=%d', $this->myuid)->fetch_single_value() > 0) {
			$I2_SQL->query('UPDATE relationships SET device=%s WHERE uid=%d', $val, $this->myuid);
		} else {
			$I2_SQL->query('INSERT INTO relationships (device, uid) VALUES (%s, %d)', $val, $this->myuid);
		}
	}
	/**
	* Get a user's "relationship" status
	*/
	public function get_relationship_status() {
		global $I2_SQL;
		if ($I2_SQL->query('SELECT COUNT(*) FROM relationships WHERE uid=%d', $this->myuid)->fetch_single_value() > 0) {
			return $I2_SQL->query('SELECT device FROM relationships WHERE uid=%d', $this->myuid)->fetch_single_value();
		} else {
			return "no one";
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
		global $I2_LDAP;
		$uid = $I2_LDAP->search_base(LDAP::get_user_dn($username),array('iodineUidNumber'))->fetch_single_value();
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
		
		$ret = $I2_LDAP->search(LDAP::get_user_dn(),"iodineUid={$this->username}")->fetch_array(Result::ASSOC);

		if( $ret === FALSE ) {
			$I2_ERR->nonfatal_error('Warning: Invalid userid `'.$this->username.'` was used in obtaining information');
		}

		return $ret;
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
	* Checks whether a user is an LDAP admin.
	*
	* Works by checking for the is-admin attribute in LDAP, since that's
	* how the LDAP dynamic admin group works.
	*
	* @return bool Whether the user has admin priviledges in LDAP or not
	*/
	public function is_ldap_admin() {
		return $this->__get('is-admin')=='TRUE';
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
			  ** Supports mapping to one of an array of values, in which case it ORs the match
			  */
			  $maptable = array(
						 'firstname' => array('givenname','nickname'),
						 'first' => array('givenname','nickname'),
						 'lastname' => 'sn',
						 'last' => 'sn',
						 'nick' => 'nickname',
						 'name' => array('sn','mname','givenname', 'nickname'),
						 'firstnamesound' => 'soundexfirst',
						 'firstsound' => 'soundexfirst',
						 'lastnamesound' => 'soundexlast',
						 'lastsound' => 'soundexlast',
						 'namesound' => array('soundexfirst','soundexlast'),
						 'city' => 'l',
						 'town' => 'l',
						 'middle' => 'mname',
						 'middlename' => 'mname',
						 'phone' => array('homephone', 'mobile'),
						 'homephone' => 'homephone',
						 'cell' => 'mobile',
						 'address' => 'street',
						 'zip' => 'postalcode',
						 'grade' => 'graduationYear',
			  			 'email' => 'mail'
			  );

			  $soundexed = array(
						 'soundexfirst' => 1,
						 'soundexlast' => 1,
						 'namesound' => 1,
						 'lastsound' => 1,
						 'firstsound' => 1
			  );

			  /*
			  ** Construct query in three parts: prefix + infix + postfix
			  */

			  $prefix = '(&';
			  $postfix = ')';
			  $infix = '';
			  $ormode = FALSE;
	 		  if ($grades) {
				  $prefix .= '(|';
				  foreach ($grades as $grade) {
							 $prefix .= "(graduationYear=$grade)";
				  }
				  $prefix .= ')';
			  }
			  
			  $separator = " \n\t";

			  $rawtok = strtok($str,$separator);

			  while ($rawtok !== FALSE) {
					$colonpos = strpos($rawtok,':');
					$tok = $rawtok;
					$key = FALSE;
					$eq = '=';
					if ($colonpos === 0) {
							  //Ignore leading colons to allow people to trigger this search function for anything
							  $colonpos = FALSE;
							  $tok = str_replace(':','',$tok);
							  d('Leading colon',8);
					}
					if ($colonpos == strlen($tok)-1) {
							  // Invalid: trailing colon
							  // Assume blah:*
							  $tok .= '*';
							  d('Trailing colon',8);
					}
					if ($colonpos) {
						$boom = explode(':',$tok);
						if (count($boom) > 2) {
							// Invalid: more than one colon
							$tok = str_replace(':','',$tok);
							d('Multiple colons',8);
						} else {
							$key = strtolower($boom[0]);
							//Apply attributename translation
							/*if (isSet($maptable[$key])) {
								d($key.' remapped to '.print_r($maptable[$key],1),8);
								$key = $maptable[$key];
							}*/
							$tok = $boom[1];
							$poteq = substr($tok,0,1);
							// Check if gt or lt was specified instead of equals
							switch ($poteq) {
								case '>':
									$eq = '>=';
									$tok = substr($tok,1);
									break;
								case '<':
									$eq = '<=';
									$tok = substr($tok,1);
									break;
								case '=':
									$tok = substr($tok,1);
									break;
								default:
									break;								
							}
						}
					}
					if ($key) {
						// We know what we're trying to search for
						$key = strtolower($key);
						if ($key == 'grade') {
							$tok = self::get_gradyear($tok);
							$key = 'graduationYear';
						}
						if (isSet($soundexed[$key])) {
							$tok = soundex($tok);
						}
						if (isSet($maptable[$key])) {
							$key = $maptable[$key];
						}
						if (!is_array($key)) {
							$key = array($key);
						}
						$infix .= '(|';
						foreach ($key as $keypart) {
							$infix .= '('.$keypart.$eq.$tok.')';
						}
						$infix .= ')';
					} else {
						if (strcasecmp($tok,'OR') == 0) {
							$ormode = TRUE;
							// User wants an OR for these search terms
			  				$rawtok = strtok($separator);
							continue;
						}
						$infix .= "(|(iodineUid=$tok)(sn=$tok)(mname=$tok)(givenName=$tok)(nickname=$tok))";
					}
					if ($ormode) {
						$prefix .= '(|';
						$postfix = ')'.$postfix;
						$ormode = FALSE;
					}
			  		$rawtok = strtok($separator);
			  }

			  $res = $I2_LDAP->search(LDAP::get_user_dn(),$prefix.$infix.$postfix,array('iodineUid'));
			  $ret = array();
			  while ($row = $res->fetch_array(Result::ASSOC)) {
			  	$ret[] = $row['iodineUid'];
			  }
			  return self::sort_users($ret);
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


		if (strpos($str,':') !== FALSE) {
			// If a user puts a colon in the string, assume they want old-style searching
			$old_style = TRUE;
		}

		d("search_info: $str".($old_style?' (legacy)':''),6);
		
		// User is trying an LDAP query
		if (strpos($str,'&') !== FALSE || strpos($str,'|') !== FALSE) {
			$res = $I2_LDAP->search(LDAP::get_user_dn(),"$str",array('iodineUid'));
			$results = array();
			while ($row = $res->fetch_array(Result::ASSOC)) {
				$results[] = $row['iodineUid'];
			}
		} else {

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
				$res = $I2_LDAP->search(LDAP::get_user_dn(),"$newgrades",array('iodineUid'));
				while ($row = $res->fetch_array(Result::ASSOC)) {
					$results[] = $row['iodineUid'];
				}
			} elseif (is_numeric($str)) {
				$res = $I2_LDAP->search(LDAP::get_user_dn(),"(&(|(tjhsstStudentId=$str)(iodineUidNumber=$str))$newgrades)",array('iodineUid'));
				while ($row = $res->fetch_array(Result::ASSOC)) {
					$results[] = $row['iodineUid'];
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
					$res = $I2_LDAP->search(LDAP::get_user_dn(),
					"(&(|(givenName=*$tok)(givenName=$tok*)(sn=*$tok)(sn=$tok*)(iodineUid=*$tok)(iodineUid=$tok*)(mname=*$tok)(mname=$tok*)(nickname=*$tok)(nickname=$tok*))$newgrades)"
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
		//Sort by last name, then first name.
		if($user1->lname == $user2->lname)
			return strcasecmp($user1->fname, $user2->fname);
		return strcasecmp($user1->lname, $user2->lname);
	}

	/**
	* Another custom sort function.
	* This one also works with FakeUsers
	*
	* @param object $user1 The first user.
	* @param object $user2 The second user.
	* @return int Depending on order, less than 0, 0, or greater than 0.
	*/
	public static function commaname_cmp($user1,$user2) {
		return strcasecmp($user1->name_comma, $user2-> name_comma);
	}

	/**
	* Gets the Iodine UID of the student with the given StudentID.
	*
	* @param int A user's Student ID.
	* @return int A user's Iodine UID.
	*/
	public static function studentid_to_uid($studentid) {
		global $I2_LDAP;
		return $I2_LDAP->search(LDAP::get_user_dn(), "(&(objectClass=tjhsstStudent)(tjhsstStudentId={$studentid}))", 'iodineUidNumber')->fetch_single_value();
	}

	/**
	 * The helper function for sorting pictures by grade for AUTO preferredPhoto
	 */
	public static function sort_photos($photo1, $photo2) {
		$priority = array(	'seniorPhoto' => 4,
					'juniorPhoto' => 3,
					'sophomorePhoto' => 2,
					'freshmanPhoto' => 1);
		return ($priority[$photo1] < $priority[$photo2]) ? -1 : 1;
	}

	/**
	* Determine if today is the user's birthday.
	* 
	* @return boolean Whether or not it is the user's birthday.
	*/
	public function borntoday() {
		return date('md', time()) == date('md',strtotime($this->__get('bdate')));
	}

	/**
	* Cache all users whose uids are in the specified array or int.
	*/
	public static function cache_users($arr,$vars=NULL) {
		global $I2_LDAP;
		if(!is_array($arr)) {
			throw new I2Exception('Non-array passed to User::cache_users!');
			return false;
		}
		if(empty($arr)) {
			return true;
		}
		$attributes='*';
		$allset=True;
		if($vars && is_array($vars)) {
			for($i=0;$i<count($vars);$i++)
				$vars[$i]=strtolower($vars[$i]);
			if(!in_array('iodineuidnumber',$vars))
				$vars[]='iodineuidnumber';
			if(!in_array('iodineuid',$vars))
				$vars[]='iodineuid';
			$attributes=$vars;//implode(',',$vars);
			$allset=False;
		}
		$query= "(|";
		foreach($arr as $id) {
			$query .= "(iodineUidNumber=$id)";
		}
		$data = $I2_LDAP->search(LDAP::get_user_dn(),$query.")",$attributes);
		while($row=$data->fetch_array(Result::ASSOC)) {
			$index=$row['iodineUidNumber'];
			$info=array();
			foreach($row as $key=>$val) {
				$info[strtolower($key)]=$val;
			}
			if($vars && is_array($vars)) {
				foreach(array_diff($vars,array_keys($info)) as $var)
					$info['__nulls'][]=$var;
						
			}
			$info['allset']=$allset;
			if(!isset($info['nickname']))//Reduce some spam a little
				$info['__nulls'][]='nickname';
			self::$cache[$index] = $info;
			self::$usernametouid[$row['iodineUid']]=&$index;
		}
		//print_r(self::$cache);
		return true;
	}
}

?>
