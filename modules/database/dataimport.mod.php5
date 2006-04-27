<?php
class dataimport implements Module {

	private $oldsql;
	private $usertable;
	private $teachertable;
	private $args = array();
	private $admin_pass;
	private $num = 10000;
	private $last_to_people;
	private $last_to_id;
	private $intranet_db;
	private $intranet_pass;
	private $intranet_server;
	private $intranet_user;

	public function __autoconstruct() {
		//TODO: ?
		//$this->oldsql = mysql_connect('intranet');

		/*
		** Set this high to avoid interfering with teachers' random assigned SASI numbers
		*/
		$this->num = 10000;
	}
	
	/**
	* Imports a SASI teacher dump file (teacher.##a)
	*/
	private function import_teacher_data_file_one($filename) {	
		/*
		** First pass through teacher.##a file
		*/
		$file = @fopen($filename,'r');
		
		d("Importing data from teacher data file $filename...",6);
		
		$line = null;
		$this->teachertable = array();

		/*
		** Store a map of last names => people, for later use
		*/
		$this->last_to_people = array();

		$numlines = 0;

		while ($line = fgets($file)) {
			list($id,$lastname,$firstname) = explode('","',$line);
			/*
			** Strip remaining quotes
			*/
			$id = substr($id,1);
			$firstname = ucFirst(strtolower(substr($firstname,0,strlen($firstname)-3)));
			$lastname = ucFirst(strtolower($lastname));
			
			/*
			** Make really sure we're OK before continuing
			*/
			if ($lastname == 'NA' || $firstname === '') {
				continue;
			}
		
			//d("Teacher: $id = $lastname,$firstname",3);

			if (!isset($last_to_people[$lastname])) {
				$this->last_to_people[$lastname] = array(array('fname'=>$firstname,'id'=>$id));
			} else {
				$this->last_to_people[$lastname][] = array('fname'=>$firstname,'id'=>$id);
			}
			
			$this->teachertable[$id] = array(
					'lname' => $lastname,
					'fname' => $firstname,
					'uid' => $id
				);
			$numlines += 1;
		}
		
		d("$numlines teachers imported.",6);

		fclose($file);
	}

	/**
	* Import teacher data from active directory (or any LDAP server).
	* This must be called after import_teacher_file_one - it uses the SASI information.
	*/
	private function import_teacher_data_ldap($server,$user,$pass) {
		$teacherldap = LDAP::get_simple_bind($server,$user,$pass);
		$res = $teacherldap->search('ou=Staff,dc=local,dc=tjhsst,dc=edu','cn=*',array('cn','sn','givenName'));
		$validteachers = array();
		while ($teacher = $res->fetch_array()) {
			$farr = array(
				'lname' => ucFirst(strtolower($teacher['sn'])),
				'fname' => ucFirst(strtolower($teacher['givenName'])),
				'username' => $teacher['cn']
			);
			$lnamematches = $this->last_to_people[$res['sn']];
			$found = FALSE;
			foreach ($lnamematches as $match) {
				if ($farr['fname'] == $match['fname']) {
					$farr['id'] = $match['id'];
					$found = TRUE;
					break;
				}
			}
			if (!$found) {
				throw new I2Exception("Unmatched teacher: \"{$farr['fname']} {$farr['lname']}\"");
			}
			$validteachers[] = $farr;
		}
		$this->finish_teachers($validteachers);
	}

	/**
	* DEPRECATED method of importing teachers from a Novell dump file (staff.#)
	*/
	private function import_teacher_data_file($filename,$teachersfiletwo) {

		$this->import_teacher_data_file_one($filename);

		/*
		** Second pass through staff.# file
		*/

		$file = @fopen($teachersfiletwo,'r');

		$validteachers = array();

		while ($line = fgets($file)) {
			/*
			** There's a ton of extra junk after the comma
			*/
			list($username,$extra) = explode(',',$line);
			$extraarr = explode(' ',$extra);
			$username = strtolower($username);
			/*
			** Username is almost certainly f+m+last, so trim two chars
			*/
			$finit = ucFirst(substr($username,0,1));
			$minit = ucFirst(substr($username,1,1));
			$lastname = ucFirst(substr($username,2));
			/*
			** Now get rid of numbers
			*/
			$numchar = substr($lastname,strlen($lastname)-2,strlen($lastname)-1);
			while (is_int($numchar)) {
				/*
				** Chop a character off the last name b/c it's numeric
				*/
				$lastname = substr($lastname,0,strlen($lastname)-1);
				$numchar = substr($lastname,strlen($lastname)-2,strlen($lastname)-1);
			}

			d("Teacher ($finit. $minit. $lastname): $username",3);
			
			if (!isSet($this->last_to_people[$lastname])) {
				//TODO: how to handle this?  Should we try weird name-guessing games?
				d("Last name \"$lastname\" not recognized",1);
				continue;
			} else {
				$choices = $this->last_to_people[$lastname];
				if (count($choices) == 1) {
					/*
					** We have exactly one match.  We've got our teacher.
					*/
					$newteach = array();
					$newteach['username'] = $username;
					$newteach['id'] = $choices[0]['id'];
					$newteach['lname'] = $lastname;
					$newteach['fname'] = $choices[0]['fname'];
					$validteachers[] = $newteach;
				} else {
					$valid = array();
					d("Multiple choices for last name \"$lastname\"",6);
					foreach ($choices as $choice) {
						/*
						** Attempt to match first initial
						*/
						$myfinit = ucFirst(substr($choice['fname'],0,1));
						if ($myfinit == $finit) {
							$valid[] = $choice;
							d("Found a $myfinit. $lastname",7);
						} else {
							d("\"$myfinit. $lastname\" didn't match",3);
						}
					}
					if (count($valid) > 1) {
						d("Ambiguous last name \"$lastname\"!",1);
						continue;
					}
					if (count($valid) == 0) {
						d("There is no \"$finit. $lastname\"!",1);
						continue;
					}
					$newteach = array();
					$newteach['username'] = $username;
					$newteach['id'] = $valid[0]['id'];
					$newteach['lname'] = $lastname;
					$newteach['fname'] = $valid[0]['fname'];
					$validteachers[] = $newteach;
				}
			}

		}

		$this->finish_teachers($validteachers);
	}

	/**
	* Loops over the passed array and creates each teacher
	*/
	private function finish_teachers($teacherarr) {
		$ldap = LDAP::get_admin_bind($this->admin_pass);

		foreach ($teacherarr as $teacher) {
			$this->create_teacher($teacher,$ldap);
		}
	}

	/** 
	* Import student data from a SASI dump file (intranet.##a) into $datatable;
	*/
	private function import_student_data($filename) {

		$file = @fopen($filename, 'r');

		d("Importing data from user data file $filename...",6);

		$line = null;

		$this->usertable = array();

		$numlines = 0;

		while ($line = fgets($file)) {
			list($username, 
					$StudentID, 
					$Lastname, 
					$Firstname, 
					$Middlename, 
					$Grade, 
					$Sex, 
					$Birthdate, 
					$Homephone, 
					$Address, 
					$City, 
					$State, 
					$Zip, 
					$Couns) = explode('","',$line);
			/*
			** We need to strip the first and last quotation marks
			** and escape the ' symbols where appropriate
			*/
			$this->usertable[] = array(
					'username' => str_replace('\'','\\\'',substr($username,1)),
					'studentid' => $StudentID, 
					'lname' => str_replace('\'','\\\'',$Lastname),
					'fname' => str_replace('\'','\\\'',$Firstname), 
					'mname' => str_replace('\'','\\\'',$Middlename), 
					'grade' => $Grade, 
					'sex' => $Sex, 
					'bdate' => $Birthdate, 
					'phone_home' => $Homephone, 
					'address' => str_replace('\'','\\\'',$Address), 
					'city' => str_replace('\'','\\\'',$City), 
					'state' => str_replace('\'','\\\'',$State), 
					'zip' => $Zip, 
					'counselor' => substr($Couns,-1));
			$numlines++;
		}
		d("$numlines users imported.",6);

		$ldap = LDAP::get_admin_bind($this->admin_pass);
		
		foreach ($this->usertable as $user) {
			$this->create_user($user,$ldap);
		}
	}

	/**
	* Adds a new teacher from the given data
	*/
	private function create_teacher($teacher,$ldap=NULL) {
		global $I2_LDAP;
		if (!$ldap) {
			$ldap = $I2_LDAP;
		}
		$newteach = array();
		$newteach['objectClass'] = 'tjhsstTeacher';
		$newteach['iodineUid'] = $teacher['username'];
		$newteach['iodineUidNumber'] = $teacher['uid'];
		$newteach['cn'] = $teacher['fname'].' '.$teacher['lname'];
		$newteach['sn'] = $teacher['lname'];
		$newteach['givenName'] = $teacher['fname'];
		$newteach['style'] = 'default';
		$newteach['header'] = 'TRUE';
		$newteach['chrome'] = 'TRUE';
		$newteach['startpage'] = 'news';
		$dn = "iodineUid={$newteach['iodineUid']},ou=people";

		//FIXME: check if iodineUidNumber exists and update previous entry if so
		
		d("Creating teacher \"{$newteach['iodineUid']}\"...",5);
		$ldap->add($dn,$newteach);
	}

	/**
	* Adds a new user from the given data
	*/
	private function create_user($user,$ldap=NULL) {
		global $I2_LDAP;
		if (!$ldap) {
			$ldap = $I2_LDAP;
		}
		$usernew = array();
		$usernew['objectClass'] = 'tjhsstStudent';
		$usernew['graduationYear'] = '2006';
		$usernew['cn'] = $user['fname'].' '.$user['lname'];
		$usernew['sn'] = $user['lname'];
		$usernew['tjhsstStudentId'] = $user['studentid'];
		$usernew['iodineUid'] = strtolower($user['username']);
		$usernew['postalCode'] = $user['zip'];
		$usernew['counselor'] = $user['counselor'];
		$usernew['st'] = $user['state'];
		$usernew['l'] = $user['city'];
		$usernew['homePhone'] = $user['phone_home'];
		$usernew['birthday'] = $user['bdate'];
		$usernew['street'] = $user['address'];
		$usernew['givenName'] = $user['fname'];
		if ($user['mname'] != '') {
			$usernew['displayName'] = $user['fname'].' '.$user['mname'].' '.$user['lname'];
		} else {
			$usernew['displayName'] = $user['fname'].' '.$user['lname'];
		}
		$usernew['gender'] = $user['sex'];
		$usernew['title'] = ($user['sex']=='M')?'Mr.':'Ms.';
		$usernew['middlename'] = $user['mname'];
		$usernew['style'] = 'default';
		$usernew['header'] = 'TRUE';
		$usernew['iodineUidNumber'] = $this->num;
		$this->num = $this->num + 1;
		$usernew['startpage'] = 'news';
		$usernew['chrome'] = 'TRUE';
		$dn = "iodineUid={$usernew['iodineUid']},ou=people";

		//FIXME: check if iodineUidNumber or tjhsstStudentId exists
		
		d("Creating user \"{$usernew['iodineUid']}\"...",5);
		$ldap->add($dn,$usernew);
	}

	private function import_eighth_data() {
		global $I2_SQL,$I2_LDAP;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);

		$numactivities = 0;
		$numblocks = 0;
		$numscheduled = 0;
		$numrooms = 0;
		$numactivitiesentered;
		
		/*
		** Create rooms
		*/
		$res = $oldsql->query('SELECT * FROM RoomInfo');
		while ($res->more_rows()) {
			$r = $res->fetch_array(Result::ASSOC);
			list($id,$name,$capacity) = array($r['RoomID'],$r['RoomName'],$r['Capacity']);
			EighthRoom::add_room($name,$capacity,$id);
			$numrooms++;
		}

		/*
		** Create activities
		*/
		$res = $oldsql->query('SELECT * FROM ActivityInfo');
		while ($res->more_rows()) {
			$a = $res->fetch_array(Result::ASSOC);
			
			/*
			** Add activity
			*/
		
			list($aid,$name,$sponsors,$description,$oneaday,$bothblocks,$sticky,$restricted,$presign) =
				array($a['ActivityID'],$a['ActivityName'],$a['Sponsor'],$a['Description'],$a['IsOneADay'],$a['IsBothBlocks'],$a['IsSticky'],
					$a['IsRestricted'],$a['IsPresign']);
			/*
			** Repair bad Intranet data - turn NULL into FALSE
			*/
			if (!$restricted) {
				$restricted = 0;
			}
			if (!$presign) {
				$presign = 0;
			}
			if (!$bothblocks) {
				$bothblocks = 0;
			}
			if (!$sticky) {
				$sticky = 0;
			}
			if (!$sponsors) {
				$sponsors = "";
			}
			if (!$description) {
				$description = "No description available";
			}
			
			// Collect the rooms the activity occurs in
			$validrooms = array();
			
			/*
			** Fetch/process all this activity's blocks
			*/
									
			$blockres = $oldsql->query('SELECT * FROM ActivityScheduleMap WHERE ActivityID=%d',$aid);
			while ($b = $blockres->fetch_array(Result::ASSOC)) {
				list($block,$brooms,$attendance,$cancelled,$bcomment,$advertisement,$date) =
					array($b['ActivityBlock'],$b['Room'],$b['AttendanceTaken'],$b['Cancelled'],$b['Comment'],$b['Advertisement'],$b['ActivityDate']);

				/*
				** Create block if necessary
				*/
				
				$bid = EighthBlock::add_block($date,$block,FALSE);
				
				/*
				** Fix old brokenness again
				*/
				if (!$advertisement) {
					$advertisement = "";
				}
				if (!$bcomment) {
					$bcomment = "";
				}
				
				//FIXME: get the sponsor info properly!
				$sponsors = array();
				
				/*
				** Schedule activity
				*/
				EighthSchedule::schedule_activity($bid,$aid,$sponsors,$brooms,$bcomment,$attendance,$cancelled,$advertisement);
				$validrooms[$brooms] = 1;
				$numscheduled++;
				
			}

			//FIXME: get the sponsor info properly!
			$sponsors = array();

			/*
			** Actually create the activity
			*/
			EighthActivity::add_activity($name,$sponsors,array_keys($validrooms),$description,$restricted,$sticky,$bothblocks,$presign,$aid);
			$numactivities++;
		}

		/*
		** Create groups
		*/
		$res = $oldsql->query('SELECT * FROM GroupInfo');
		while ($res->more_rows()) {
			$g = $res->fetch_array(Result::ASSOC);
			list($id,$name) = array($g['GroupID'],$g['Name']);
			Group::add_group('eighth_'.$name,'Eighth-period activity: '.$description,$id);
			$numgroups++;
		}

		/*
		** Add students to activities
		*/
		$res = $oldsql->query('SELECT * FROM StudentScheduleMap');
		while ($res->more_rows()) {
			$a = $res->fetch_array(Result::ASSOC);
			list($studentid,$aid,$date,$block) = array($a['StudentID'],$a['ActivityID'],$a['ActivityDate'],$a['ActivityBlock']);
			$activity = new EighthActivity($aid);
			$bid = EighthBlock::add_block($date,$block,FALSE);
			$uid = User::get_by_studentid($studentid)->uid;
			if (!$uid) {
				//There's quite a bit of bogus data in the old DB
				continue;
			}
			d("Adding user $uid (StudentID $studentid) to block $bid",6);
			$activity->add_member($uid,TRUE,$bid);
			$numactivitiesentered++;
		}

		d("$numactivities activities created",5);
		d("$numblocks different new blocks created",5);
		d("$numscheduled activity blocks scheduled",5);
		d("$numrooms rooms created",5);
		d("$numgroups 8th-period groups created",5);
		d("$numactivitiesentered student sign-ups processed",5);
	}

	/**
	* Expands a student's Intranet 2 presence by adding their non-critical data from Intranet 1.
	*/
	private function expand_student_info() {
		global $I2_LDAP;
		$ldap = LDAP::get_admin_bind($this->admin_pass);
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		$res = $oldsql->query("SELECT StudentID,Locker,Lastnamesound,Firstnamesound FROM StudentInfo");
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$user = User::get_by_studentid($row['StudentID']);
			$otherres = $oldsql->query("SELECT * FROM StudentMiscInfo WHERE StudentID=%d",$row['StudentID']);
			$user->icq = $otherres['ICQ'];
			$user->aim = $otherres['AIM'];
			$user->msn = $otherres['MSN'];
			$user->jabber = $otherres['Jabber'];
			$user->yahoo = $otherres['Yahoo'];
			$user->showschedule = $otherres['AllowSchedule'];
			$user->showbirthday = $otherres['AllowBirthday'];
			$user->showmap = $otherres['AllowMap'];
			$user->showaddress = $otherres['AllowAddress'];
			$user->showphone = $otherres['AllowPhone'];
			$user->showpicture = $otherres['AllowPicture'];
			$user->locker = $row['Locker'];
			$user->phoneNumber = $otherres['CellPhone'];
			$user->mailAddress = $otherres['Email'];
			$user->soundexlast = $row['Lastnamesound'];
			$user->soundexfirst = $row['Firstnamesound'];
		}
	}

	public function get_name() {
		return 'dataimport';
	}

	public function init_box() {
		return FALSE;
	}

	public function display_box($disp) {
	}

	public function init_pane() {
		global $I2_ARGS;
		if (isSet($_REQUEST['admin_pass'])) {
			$_SESSION['ldap_admin_pass'] = $_REQUEST['admin_pass'];
		}
		if (isSet($_REQUEST['intranet_db']) && isSet($_REQUEST['intranet_pass']) 
				&& isSet($_REQUEST['intranet_server']) && isSet($_REQUEST['intranet_user'])) {
			$_SESSION['intranet_pass'] = $_REQUEST['intranet_pass'];
			$_SESSION['intranet_server'] = $_REQUEST['intranet_server'];
			$_SESSION['intranet_db'] = $_REQUEST['intranet_db'];
			$_SESSION['intranet_user'] = $_REQUEST['intranet_user'];
		}
		if (isSet($_SESSION['ldap_admin_pass'])) {
			$this->admin_pass = $_SESSION['ldap_admin_pass'];
		}
		if (isSet($_SESSION['intranet_pass'])) {
			$this->intranet_pass = $_SESSION['intranet_pass'];
		}
		if (isSet($_SESSION['intranet_db'])) {
			$this->intranet_db = $_SESSION['intranet_db'];
		}
		if (isSet($_SESSION['intranet_server'])) {
			$this->intranet_server = $_SESSION['intranet_server'];
		}
		if (isSet($_SESSION['intranet_user'])) {
			$this->intranet_user = $_SESSION['intranet_user'];
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'unset_pass') {
			unset($_SESSION['ldap_admin_pass']);
			unset($this->admin_pass);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'userdata' && isSet($_REQUEST['userfile'])) {
			$this->import_student_data($_REQUEST['userfile']);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teacherdata' && isSet($_REQUEST['teacherfile']) && isSet($_REQUEST['stafffile'])) {
			$this->import_teacher_data_file($_REQUEST['teacherfile'],$_REQUEST['stafffile']);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teacherdata' && isSet($_REQUEST['teacherfile']) && isSet($_REQUEST['teacherserver'])
		&& isSet($_REQUEST['teacherdn']) && isSet($_REQUEST['teacherpass'])) {
			$this->import_teacher_data_file_one($_REQUEST['teacherfile']);
			$this->import_teacher_data_ldap($_REQUEST['teacherserver'],$_REQUEST['teacherdn'],$_REQUEST['teacherpass']);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'eighthdata' && isSet($_REQUEST['doit'])) {
			$this->import_eighth_data();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'studentinfo' && isSet($_REQUEST['doit'])) {
			$this->expand_student_info();
		}
		return array(TRUE,'Import Legacy Data');
	}

	public function display_pane($disp) {
		$disp->disp('dataimport_pane.tpl',array(
				'userdata' => $this->usertable, 
				'admin_pass' => isSet($this->admin_pass)?TRUE:FALSE,
				'intranet_pass' => isSet($this->intranet_pass)?TRUE:FALSE
				));
	}
}
?>
