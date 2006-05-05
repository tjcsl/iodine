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
	private $boxes;
	private $boxids;

	/**
	* SASI Student dump - intranet.##a
	*/
	private $userfile;

	/**
	* SASI Teacher dump - teacher.##a
	*/
	private $teacherfile;

	/**
	* Novell Teacher dump - staff.#
	*/
	private $stafffile;

	private $school_ldap_server;
	private $school_ldap_user;
	private $school_ldap_pass;

	public function __autoconstruct() {
		global $I2_SQL;
		/*
		** Set this high to avoid interfering with teachers' randomly assigned SASI numbers
		** but not so high that StudentIDs and IodineUidNumbers aren't distinct.
		** So, teacher IodineUidNumbers are < 1000, student IodineUidNumbers are 10000-99999, and StudentIDs are > 99999
		*/
		$this->num = 10000;

	}
	
	/**
	* Imports a SASI teacher dump file (teacher.##a)
	*/
	private function import_teacher_data_file_one() {	
		$filename = $this->teacherfile;
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
	* This must be called after import_teacher_data_file_one - it uses the SASI information.
	*/
	private function import_teacher_data_ldap() {
		global $I2_LOG;
	
		$server = $this->school_ldap_server;
		$user = $this->school_ldap_user;
		$pass = $this->school_ldap_pass;
		
		$count = 0;
		
		//$teacherldap = LDAP::get_simple_bind($user,$pass,$server);
		$teacherldap = LDAP::get_user_bind($server);
		$res = $teacherldap->search('ou=Staff,dc=local,dc=tjhsst,dc=edu','cn=*',array('cn','sn','givenName'));
		$validteachers = array();
		while ($teacher = $res->fetch_array()) {
			if (!isSet($teacher['sn'])) {
				continue;
			}
			$farr = array(
				'lname' => ucFirst(strtolower($teacher['sn'])),
				'fname' => ucFirst(strtolower($teacher['givenName'])),
				'username' => $teacher['cn']
			);
			if (!isSet($this->last_to_people[$teacher['sn']])) {
				continue;
			}
			$lnamematches = $this->last_to_people[$teacher['sn']];
			$found = FALSE;
			foreach ($lnamematches as $match) {
				if ($farr['fname'] == $match['fname']) {
					$farr['uid'] = $match['id'];
					$found = TRUE;
					break;
				}
			}
			if (!$found) {
				d("Unmatched teacher: \"{$farr['fname']} {$farr['lname']}\"",3);
			}
			$validteachers[] = $farr;
			$count++;
			if ($count % 100 == 0) {
				$I2_LOG->log_file("-$count-");
			}
		}
		$this->init_desired_boxes();
		$this->finish_teachers($validteachers);
	}

	/**
	* DEPRECATED method of importing teachers from a Novell dump file (staff.#)
	*/
	private function import_teacher_data_file() {

		$filename = $this->teacherfile;
		$teachersfiletwo = $this->staffile;
		
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
	private function import_student_data($do_old_intranet=TRUE) {
		global $I2_LOG;	
		
		$ldap = LDAP::get_admin_bind($this->admin_pass);
		
		$oldsql = NULL;
		
		if ($do_old_intranet) {
			$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);	
		}

		$filename = $this->userfile;
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
					$Couns,
					$Nickname
					) = explode('","',$line);
			/*
			** We need to strip the first and last quotation marks
			** and escape the ' symbols where appropriate
			** and get rid of the newlines/junk after the last field
			*/
			$Nickname = rtrim($Nickname," \t\r\n\0\x0B'\"");
			if (!$Nickname) {
				$Nickname = '';
			}
			$student = array(
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
					'counselor' => $Couns,
					'nick' => str_replace('\'','\\\'',$Nickname)
					);
			if ($do_old_intranet) {
				$res = $oldsql->query('SELECT Lastnamesound,Firstnamesound FROM StudentInfo WHERE StudentID=%d',$StudentID)->fetch_array(Result::ASSOC);
				$student['soundexlast'] = $res['Lastnamesound'];
				$student['soundexfirst'] = $res['Firstnamesound'];
				$otherres = $oldsql->query('SELECT * FROM StudentMiscInfo WHERE StudentID=%d',$StudentID);
				$otherres = $otherres->fetch_array(Result::ASSOC);
				if ($otherres['ICQ']) {
					$student['icq'] = $otherres['ICQ'];
				}
				if ($otherres['AIM']) {
					$student['aim'] = $otherres['AIM'];
				}
				if ($otherres['MSN']) {
					$student['msn'] = $otherres['MSN'];
				}
				if ($otherres['Jabber']) {
					$student['jabber'] = $otherres['Jabber'];
				}
				if ($otherres['Yahoo']) {
					$student['yahoo'] = $otherres['Yahoo'];
				}
				if ($otherres['AllowSchedule']) {
					$student['showschedule'] = 'TRUE';
				} else {
					$student['showschedule'] = 'FALSE';
				}
				if ($otherres['AllowBirthday']) {
					$student['showbirthday'] = 'TRUE';
				} else {
					$student['showbirthday'] = 'FALSE';
				}
				if ($otherres['AllowMap']) {
					$student['showmap'] = 'TRUE';
				} else {
					$student['showmap'] = 'FALSE';
				}
				if ($otherres['AllowAddress']) {
					$student['showaddress'] = 'TRUE';
				} else {
					$student['showaddress'] = 'FALSE';
				}
				if ($otherres['AllowPhone']) {
					$student['showphone'] = 'TRUE';
				} else {
					$student['showphone'] = 'FALSE';
				}
				if ($otherres['AllowPicture']) {
					$student['showpictures'] = 'TRUE';
				} else {
					$student['showpictures'] = 'FALSE';
				}
				if ($otherres['Locker']) {
					$student['locker'] = $otherres['Locker'];
				}
				$student['mobile'] = $otherres['CellPhone'];
				$student['mail'] = $otherres['Email'];
				
			}
			$this->usertable[] = $student;
			$numlines++;
			if ($numlines % 100 == 0) {
				$I2_LOG->log_file("-$numlines-");
			}
		}
		d("$numlines users imported.",6);
	
		/*
		** This line is needed b/c the create_user method uses $this->boxes and friends
		*/
		$this->init_desired_boxes();
	
		foreach ($this->usertable as $user) {
			$this->create_user($user,$ldap);
		}
	}

	/**
	* Get ready to add default intraboxes for users
	*/
	private function init_desired_boxes() {
		global $I2_SQL;
		/*
		** Set up default intraboxes
		*/
		$this->boxes = array(
			'news'=>'News',
			'eighth'=>'Eighth Period',
			'mail'=>'Your Mail',
			'filecenter'=>'Your Files',
			'birthdays'=>'Birthdays',
			'studentdirectory'=>'Student Directory',
			'links'=>'Useful Links'
		);
		$desiredboxes = array();
		foreach ($this->boxes as $desiredbox=>$name) {
			$boxnum = $I2_SQL->query('SELECT boxid FROM intrabox WHERE name=%s',$desiredbox)->fetch_single_value();
			$desiredboxes[$boxnum] = $name;
		}
		$this->boxids = $desiredboxes;
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

		/*
		** Teachers need intraboxes, too!
		*/
		foreach ($this->boxids as $boxid=>$name) {
			$I2_SQL->query('INSERT INTO intrabox_map (uid,boxid) VALUES(%d,%d)',$teacher['uid'],$boxid);
		}
	}

	/**
	* Adds a new user from the given data
	*/
	private function create_user($user,$ldap=NULL) {
		global $I2_LDAP,$I2_SQL;
		if (!$ldap) {
			$ldap = $I2_LDAP;
		}
		$usernew = array();
		$usernew['objectClass'] = 'tjhsstStudent';
		$usernew['graduationYear'] = (-1*($user['grade']-12))+2006;
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
		$usernew['nickName'] = $user['nick'];
		if ($user['mname'] != '') {
			$usernew['displayName'] = $user['fname'].' '.$user['mname'].' '.$user['lname'];
		} else {
			$usernew['displayName'] = $user['fname'].' '.$user['lname'];
		}
		$usernew['gender'] = $user['sex'];
		$usernew['mobile'] = $user['mobile'];
		$usernew['locker'] = $user['locker'];
		$usernew['mail'] = $user['mail'];
		$usernew['soundexfirst'] = $user['soundexfirst'];
		$usernew['soundexlast'] = $user['soundexlast'];
		$usernew['aim'] = $user['aim'];
		$usernew['jabber'] = $user['jabber'];
		$usernew['msn'] = $user['msn'];
		$usernew['icq'] = $user['icq'];
		$usernew['showpictures'] = $user['showpictures'];
		$usernew['showaddress'] = $user['showaddress'];
		$usernew['showmap'] = $user['showmap'];
		$usernew['showschedule'] = $user['showschedule'];
		$usernew['showphone'] = $user['showphone'];
		$usernew['showbirthday'] = $user['showbirthday'];
		$usernew['title'] = ($user['sex']=='M')?'Mr.':'Ms.';
		$usernew['middlename'] = $user['mname'];
		$usernew['style'] = 'default';
		$usernew['header'] = 'TRUE';
		$usernum = $this->num;
		$this->num = $this->num + 1;
		$usernew['iodineUidNumber'] = $usernum;
		$usernew['startpage'] = 'news';
		$usernew['chrome'] = 'TRUE';
		$dn = "iodineUid={$usernew['iodineUid']},ou=people";

		//FIXME: check if iodineUidNumber or tjhsstStudentId exists
		
		d("Creating user \"{$usernew['iodineUid']}\"...",5);
		$ldap->add($dn,$usernew);
		foreach ($this->boxids as $boxid=>$name) {
			$I2_SQL->query('INSERT INTO intrabox_map (uid,boxid) VALUES(%d,%d)',$usernum,$boxid);
		}
	}

	private function import_eighth_data($startdate=NULL,$enddate=NULL) {
		global $I2_SQL,$I2_LDAP,$I2_LOG;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);

		if ($startdate === NULL) {
			// Go back 1 week by default
			$startdate = date('Y-m-d',time()-7*24*60*60);
		}
		if ($enddate === NULL) {
			// Go 8 weeks forward by default
			$enddate = date('Y-m-d',time()+8*7*24*60*60);
		}

		$numactivities = 0;
		$numblocks = 0;
		$numscheduled = 0;
		$numrooms = 0;
		$numactivitiesentered = 0;
		$numgroups = 0;
		
		/*
		** Create rooms
		*/
		$res = $oldsql->query('SELECT * FROM RoomInfo');
		while ($res->more_rows()) {
			$r = $res->fetch_array(Result::ASSOC);
			list($id,$name,$capacity) = array($r['RoomID'],$r['RoomName'],$r['Capacity']);
			EighthRoom::add_room($name,$capacity,$id);
			$I2_LOG->log_file("Added room \"$name\"",8);
			$numrooms++;
		}
			
		// Collect used sponsors
		$validsponsors = array();

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
			** Eliminate old system of (R) and so forth in the activity name
			*/
			$name = str_replace('(R)','',$name);
			$name = str_replace('(BB)','',$name);
			$name = str_replace('(S)','',$name);
			$name = rtrim($name);
					
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
				$sponsors = '';
			}
			if (!$description) {
				$description = 'No description available';
			}
			
			// Collect the rooms the activity occurs in
			$validrooms = array();

			$validsponsors[$sponsors] = 1;
			
			/*
			** Fetch/process all this activity's blocks
			*/
							
			$blockres = $oldsql->query('SELECT * FROM ActivityScheduleMap 
												 WHERE ActivityID=%d 
												 AND ActivityDate >= %s 
												 AND ActivityDate <= %s
												 ORDER BY ActivityDate DESC',
												 	$aid,$startdate,$enddate);
			while ($b = $blockres->fetch_array(Result::ASSOC)) {
				list($block,$brooms,$attendance,$cancelled,$bcomment,$advertisement,$date) =
					array($b['ActivityBlock'],$b['Room'],$b['AttendanceTaken'],$b['Cancelled'],
						$b['Comment'],$b['Advertisement'],$b['ActivityDate']);

				/*
				** Create block if necessary
				*/
				
				$bid = EighthBlock::add_block($date,$block,FALSE);
				$I2_LOG->log_file("[Tried to] add block $block on $date",8);
				
				/*
				** Fix old brokenness again
				*/
				if (!$advertisement) {
					$advertisement = "";
				}
				if (!$bcomment) {
					$bcomment = "";
				}
				
				/*foreach ($sponsors as $sponsor) {
					//$I2_SQL->query('INSERT INTO ');
				}*/
				//FIXME: get the sponsor info properly!
				//$sponsors = array();
				
				/*
				** Schedule activity
				*/
				EighthSchedule::schedule_activity($bid,$aid,$sponsors,$brooms,$bcomment,$attendance,$cancelled,$advertisement);
				$I2_LOG->log_file("Scheduled activity \"$name\" for $block on $date",7);
				$validrooms[$brooms] = 1;
				$numscheduled++;
				
			}

			/*
			** Actually create the activity
			*/
			EighthActivity::add_activity($name,$sponsors,array_keys($validrooms),$description,$restricted,$sticky,$bothblocks,$presign,$aid);
			$I2_LOG->log_file("Added activity \"$name\"",5);
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
			$I2_LOG->log_file("Added group for $name",6);
			$numgroups++;
		}

		$I2_LOG->log_file('Precomputing uid=>studentid mappings');

		$num = 0;

		//Badly named - keys are studentids, vals are uids
		$studentids = array();
		$ldap = LDAP::get_admin_bind($this->admin_pass);
		$res = $ldap->search('','objectClass=tjhsstStudent',array('tjhsstStudentId','iodineUidNumber'));
		$total = $res->num_rows();
		while ($num < $total) {
			$row = $res->fetch_array(Result::ASSOC);
			if (isSet($row['iodineUidNumber'])) {
				$studentids[$row['tjhsstStudentId']] = $row['iodineUidNumber'];
			}
			$num++;
			if ($num % 100 == 0) {
				$I2_LOG->log_file($num.'/'.$total);
			}
		}

		$I2_LOG->log_file('... Done!');
		
		/*
		** Add students to activities
		*/
		$res = $oldsql->query('SELECT * FROM StudentScheduleMap WHERE ActivityDate >= %s AND ActivityDate <= %s ORDER BY ActivityDate,ActivityBlock DESC',$startdate,$enddate);
		while ($res->more_rows()) {
			$a = $res->fetch_array(Result::ASSOC);
			list($studentid,$aid,$date,$block) = array($a['StudentID'],$a['ActivityID'],$a['ActivityDate'],$a['ActivityBlock']);
			$activity = new EighthActivity($aid);
			$bid = EighthBlock::add_block($date,$block,FALSE);
			if (!isSet($studentids[$studentid])) {
				//There's quite a bit of bogus data in the old DB
				$uid = User::get_by_studentid($studentid)->uid;
				if (!$uid) {
					continue;
				}
				$studentids[$studentid] = $uid;
			} else {
				$uid = $studentids[$studentid];
			}
			d("Adding user $uid (StudentID $studentid) to block $bid",6);
			$activity->add_member($uid,TRUE,$bid);
			$I2_LOG->log_file("Switched student with StudentID $studentid into {$activity->name} on $date block $block",7);
			$numactivitiesentered++;
		}

		/*
		** Create sponsors
		*/
		$numsponsors = 0;
		foreach ($validsponsors as $sponsor) {
			$this->create_sponsor($sponsor);
		}

		d("$numactivities activities created",5);
		d("$numblocks different new blocks created",5);
		d("$numscheduled activity blocks scheduled",5);
		d("$numrooms rooms created",5);
		d("$numsponsors sponsors added",5);
		d("$numgroups 8th-period groups created",5);
		d("$numactivitiesentered student sign-ups processed",5);
	}

	private function create_sponsor($sponsor) {
		global $I2_SQL;
		$I2_SQL->query('REPLACE INTO eighth_sponsors (lname) VALUES(%s)',$sponsor);
	}

	/**
	* Expands a student's Intranet 2 presence by adding their non-critical data from Intranet 1.
	*/
	private function expand_student_info() {
		global $I2_LDAP,$I2_LOG;
		//$I2_LOG->debug_off();
		$ldap = LDAP::get_admin_bind($this->admin_pass);
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		$res = $oldsql->query("SELECT StudentID,Lastnamesound,Firstnamesound FROM StudentInfo");
		
		$count = 0;
		
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$user = User::get_by_studentid($row['StudentID']);
			if (!$user) {
				d('Invalid StudentID '.$row['StudentID'],4);
				continue;
			}
			$count++;
			if ($count % 100 == 0) {
				$I2_LOG->log_file("-$count-");
			}
		}
		//$I2_LOG->debug_on();
	}

	/**
	* Turns all teachers into 8th-period-activity sponsors
	*/
	private function make_teachers_sponsors() {
		global $I2_LDAP;
		$res = $I2_LDAP->search('','objectClass=tjhsstTeacher',array('givenName','sn'));
		while ($res->more_rows()) {
			$row = $res->fetch_array(Result::ASSOC);
			if (isSet($row['givenName']) && isSet($row['sn'])) {
				EighthSponsor::add_sponsor($row['givenName'],$row['sn']);
			}
		}
	}

	private function clean_students($ldap=NULL) {
		global $I2_SQL,$I2_LDAP;
		if (!$ldap) {
			$ldap = LDAP::get_admin_bind($this->admin_pass);
		}
		$ldap->delete_recursive('ou=people','(objectClass=tjhsstStudent)');
		//$ldap->delete_recursive('ou=people');
		//$this->init_db();
	}

	private function clean_teachers($ldap=NULL) {
		global $I2_SQL,$I2_LDAP;
		if (!$ldap) {
			$ldap = LDAP::get_admin_bind($this->admin_pass);
		}
		$ldap->delete_recursive('ou=people','(objectClass=tjhsstTeacher)');
		//$ldap->delete_recursive('ou=people');
		
	}

	private function clean_other($ldap=NULL) {
		global $I2_SQL,$I2_LDAP;
		if (!$ldap) {
			$ldap = LDAP::get_admin_bind($this->admin_pass);
		}
		$I2_SQL->query('DELETE FROM intrabox');
		$I2_SQL->query('DELETE FROM intrabox_map');
		$I2_SQL->query('DELETE FROM news_read_map');
		$I2_SQL->query('DELETE FROM scratchpad');
		$I2_SQL->query('DELETE FROM polls');
		$I2_SQL->query('DELETE FROM poll_votes');
		$I2_SQL->query('DELETE FROM group_poll_map');
		$I2_SQL->query('DELETE FROM groups_perms');
		/*
		** This stuff needs to come last so we retain privs to the bitter end
		** This still depends on a bit of caching being done...
		*/
		$I2_SQL->query('DELETE FROM group_user_map');
		$I2_SQL->query('DELETE FROM groups');
	}

	private function clean_eighth() {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM eighth_activities');
		$I2_SQL->query('DELETE FROM eighth_activity_map');
		$I2_SQL->query('DELETE FROM eighth_blocks');
		$I2_SQL->query('DELETE FROM eighth_block_map');
		$I2_SQL->query('DELETE FROM eighth_absentees');
		$I2_SQL->query('DELETE FROM eighth_sponsors');
		$I2_SQL->query('DELETE FROM eighth_activity_permissions');
		$I2_SQL->query('DELETE FROM eighth_rooms');
	}

	/**
	* Delete student schedules
	*
	* @todo Write this
	*/
	private function clean_schedules($ldap=NULL) {
		global $I2_SQL,$I2_LDAP;
		if (!$ldap) {
			$ldap = LDAP::get_admin_bind($this->admin_pass);
		}
	}

	/**
	* Delete the whole shebang for a fresh import
	*/
	private function clean_up() {
		global $I2_SQL,$I2_LDAP;
		$ldap = NULL;
		if ($this->admin_pass) {
			$ldap = LDAP::get_admin_bind($this->admin_pass);
		} else {
			$ldap = $I2_LDAP;
		}
		$this->clean_students($ldap);
		$this->clean_teachers($ldap);
		$this->clean_schedules($ldap);
		$this->clean_eighth();
		$this->clean_other($ldap);
	}

	/**
	* Do basic database setup
	*/
	private function init_db() {
		global $I2_SQL,$I2_LDAP;

		/*
		** Make the SQL database presentable
		*/
		$essentialgroups = array('admin_all','admin_mysql','admin_ldap','admin_groups','admin_news','admin_eighth');
		foreach ($essentialgroups as $groupname) {
			$I2_SQL->query('INSERT INTO groups (name) VALUES (%s)',$groupname);
		}
		
		/*
		** Get the LDAP database into some sort of shape
		*/
		$people = array(
			'objectClass' => 'organizationalUnit',
			'ou' => 'people',
			'description' => 'People at TJHSST'
		);
		$I2_LDAP->add('ou=people',$people);

		/*
		** Create the admin user account
		*/
		$admin_number = 9998;
		$admin = array(
			'objectClass' => 'fakeUser',
			'cn'	=> 'Admin',
			'givenName' => 'Admin',
			'iodineUid' => 'admin',
			'iodineUidNumber' => "$admin_number",
			'header' => 'TRUE',
			'chrome' => 'TRUE',
			'style' => 'default',
			'startpage' => 'dataimport'
		);
		$I2_LDAP->add('iodineUid=admin,ou=people',$admin);
		$admingid = $I2_SQL->query('SELECT gid FROM groups WHERE name=%s','admin_all')->fetch_single_value();
		$I2_SQL->query('INSERT INTO group_user_map (uid,gid,is_admin) VALUES (%d,%d,%d)',$admin_number,$admingid,1);
		/*
		** Create the 8th-period-office user account
		*/
		$admin_number = 9999;
		$admin = array(
			'objectClass' => 'fakeUser',
			'cn'	=> 'Eighth Period Office',
			'givenName' => 'Eighth Period Office',
			'iodineUid' => 'eighthOffice',
			'iodineUidNumber' => "$admin_number",
			'header' => 'FALSE',
			'chrome' => 'FALSE',
			'style' => 'default',
			'startpage' => 'eighth'
		);
		$I2_LDAP->add('iodineUid=eighthOffice,ou=people',$admin);
		$admingid = $I2_SQL->query('SELECT gid FROM groups WHERE name=%s','admin_eighth')->fetch_single_value();
		$I2_SQL->query('INSERT INTO group_user_map (uid,gid,is_admin) VALUES (%d,%d,%d)',$admin_number,$admingid,1);

		$this->init_desired_boxes();
		/*
		** Create desired intraboxes
		*/
		foreach ($this->boxes as $box=>$name) {
			$I2_SQL->query('INSERT INTO intrabox (name,display_name) VALUES (%s,%s)',$box,$name);
		}
		
	}

	/**
	* Final post-import tasks which need to be run
	*/
	private function make_final() {
		global $I2_SQL,$I2_LDAP;
	}

	/**
	* Does all necessary importing as best it can
	*/
	private function do_imports() {
		global $I2_LOG;
		$I2_LOG->log_file('Beginning cleanup',3);
		$this->clean_up();
		$I2_LOG->log_file('Cleanup complete',3);
		$I2_LOG->log_file('Initializing database(s)',3);
		$this->init_db();
		$I2_LOG->log_file('Database(s) initialized',3);

		$I2_LOG->log_file('Importing students...',3);
		$this->import_student_data();
		$I2_LOG->log_file('Initial import complete, expanding student information...',3);
		$this->expand_student_info();
		$I2_LOG->log_file('Students imported',3);
		
		/*$I2_LOG->log_file('Importing teachers...',3);
		$this->import_teacher_data_file_one();
		$this->import_teacher_data_ldap();
		$I2_LOG->log_file('Teachers imported',3);*/
		
		/*$I2_LOG->log_file('Importing eighth-period data...',3);
		$I2_LOG->log_file('Eighth period imported',3);*/
		
		/*$I2_LOG->log_file('Importing scheduling information...',3);
		$I2_LOG->log_file('Schedules imported',3);*/
		
		$I2_LOG->log_file('Beginning finalization...',3);
		$this->make_final();
		$I2_LOG->log_file('Finalization complete',3);
		
		$I2_LOG->log_file('All tasks completed!',3);
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
		global $I2_ARGS,$I2_USER;
		if (!$I2_USER->is_group_member('admin_all')) {
			return FALSE;
		}
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
		if (isSet($_SESSION['userfile'])) {
			$this->userfile = $_SESSION['userfile'];
		}
		if (isSet($_SESSION['teacherfile'])) {
			$this->teacherfile = $_SESSION['teacherfile'];
		}
		if (isSet($_SESSION['school_ldap_server'])) {
			$this->school_ldap_server = $_SESSION['school_ldap_server'];
		}
		if (isSet($_SESSION['school_ldap_user'])) {
			$this->school_ldap_user = $_SESSION['school_ldap_user'];
		}
		if (isSet($_SESSION['school_ldap_pass'])) {
			$this->school_ldap_pass = $_SESSION['school_ldap_pass'];
		}
		if (isSet($_SESSION['stafffile'])) {
			$this->intranet_user = $_SESSION['stafffile'];
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'unset_pass') {
			unset($_SESSION['ldap_admin_pass']);
			unset($this->admin_pass);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'unset_user') {
			unset($_SESSION['userfile']);
			unset($this->userfile);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'unset_teacher') {
			unset($_SESSION['school_ldap_server']);
			unset($this->school_ldap_server);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'userdata' && isSet($_REQUEST['userfile'])) {
			$this->userfile = $_REQUEST['userfile'];
			$_SESSION['userfile'] = $_REQUEST['userfile'];
			//$this->import_student_data($_REQUEST['userfile']);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teacherdata' && isSet($_REQUEST['teacherfile']) && isSet($_REQUEST['teacherserver'])
		&& isSet($_REQUEST['teacherdn']) && isSet($_REQUEST['teacherpass']) && $_REQUEST['teacherserver'] != '') {
			//$this->import_teacher_data_file_one($_REQUEST['teacherfile']);
			$this->teacherfile = $_REQUEST['teacherfile'];
			$_SESSION['teacherfile'] = $_REQUEST['teacherfile'];
			$this->school_ldap_server = $_REQUEST['teacherserver'];
			$_SESSION['school_ldap_server'] = $_REQUEST['teacherserver'];
			$this->school_ldap_user = $_REQUEST['teacherdn'];
			$_SESSION['school_ldap_user'] = $_REQUEST['teacherdn'];
			$this->school_ldap_pass = $_REQUEST['teacherpass'];
			$_SESSION['school_ldap_pass'] = $_REQUEST['teacherpass'];
			//$this->import_teacher_data_ldap($_REQUEST['teacherserver'],$_REQUEST['teacherdn'],$_REQUEST['teacherpass']);
		} elseif (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teacherdata' && isSet($_REQUEST['teacherfile']) && isSet($_REQUEST['stafffile'])) {
			$this->staffile = $_REQUEST['stafffile'];
			$this->teacherfile = $_REQUEST['teacherfile'];
			//$this->import_teacher_data_file($_REQUEST['teacherfile'],$_REQUEST['stafffile']);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'eighthdata' && isSet($_REQUEST['doit'])) {
			if (isSet($_REQUEST['startdate']) && isSet($_REQUEST['enddate'])) {
				$this->import_eighth_data($_REQUEST['startdate'],$_REQUEST['enddate']);
			} elseif (isSet($_REQUEST['startdate'])) {
				$this->import_eighth_data($_REQUEST['startdate']);
			} else {
				$this->import_eighth_data();
			}
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean' && isSet($_REQUEST['doit'])) {
			$this->clean_up();
			$this->init_db();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_students' && isSet($_REQUEST['doit'])) {
			$this->clean_students();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_teachers' && isSet($_REQUEST['doit'])) {
			$this->clean_teachers();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_eighth' && isSet($_REQUEST['doit'])) {
			$this->clean_eighth();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_other' && isSet($_REQUEST['doit'])) {
			$this->clean_other();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'studentinfo' && isSet($_REQUEST['doit'])) {
			$this->expand_student_info();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teachersponsors' && isSet($_REQUEST['doit'])) {
			$this->make_teachers_sponsors();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'doeverything' && isSet($_REQUEST['doit'])) {
			$this->do_imports();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teachers' && isSet($_REQUEST['doit'])) {
			$this->import_teacher_data_file_one();
			$this->import_teacher_data_ldap();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'students' && isSet($_REQUEST['doit'])) {
			$this->import_student_data();
		}
		return 'Import Legacy Data';
	}

	public function display_pane($disp) {
		$disp->disp('dataimport_pane.tpl',array(
				'userdata' => $this->usertable, 
				'admin_pass' => isSet($this->admin_pass)?TRUE:FALSE,
				'intranet_pass' => isSet($this->intranet_pass)?TRUE:FALSE,
				'userfile' => isSet($this->userfile)?TRUE:FALSE,
				//FIXME: meh, not quite userproof.
				'teacherfile' => (isSet($this->teacherfile)&&(isSet($this->staffile)||isSet($this->school_ldap_server)))?TRUE:FALSE
				));
	}
}
?>
