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
	private $numsponsors;

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
		
		// We don't need this overhead, it's slow enough as things stand
		Eighth::undo_off();
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
		
			d("Teacher: $id = $lastname,$firstname",3);

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
		
		$teacherldap = LDAP::get_simple_bind($user,$pass,$server);
		//$teacherldap = LDAP::get_user_bind($server);
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
			d("Working on teacher {$farr['fname']} {$farr['lname']}",6);
			if (!isSet($this->last_to_people[$farr['lname']])) {
				continue;
			}
			$lnamematches = $this->last_to_people[$farr['lname']];
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
				  continue;
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
					$newteach['uid'] = $valid[0]['id'];
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
	* Import student data from a SASI dump file (intranet.##a) into LDAP
	*/
	private function import_student_data($do_old_intranet=TRUE, $studentidonly = FALSE) {
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
			if ($studentidonly && $studentidonly != $StudentID) {
					  $this->num++;
					  continue;
			}
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
					'username' => substr($username,1),
					'studentid' => $StudentID, 
					'lname' => $Lastname,
					'fname' => $Firstname, 
					'mname' => $Middlename, 
					'grade' => $Grade, 
					'sex' => $Sex, 
					'bdate' => $Birthdate, 
					'phone_home' => $Homephone, 
					'address' => $Address, 
					'city' => $City, 
					'state' => $State, 
					'zip' => $Zip, 
					'counselor' => $Couns,
					'nick' => $Nickname
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
					$student['showscheduleself'] = 'TRUE';
				} else {
					$student['showscheduleself'] = 'FALSE';
				}
				if ($otherres['AllowBirthday']) {
					$student['showbdayself'] = 'TRUE';
				} else {
					$student['showbdayself'] = 'FALSE';
				}
				if ($otherres['AllowMap']) {
					$student['showmapself'] = 'TRUE';
				} else {
					$student['showmapself'] = 'FALSE';
				}
				if ($otherres['AllowAddress']) {
					$student['showaddressself'] = 'TRUE';
				} else {
					$student['showaddressself'] = 'FALSE';
				}
				if ($otherres['AllowPhone']) {
					$student['showphoneself'] = 'TRUE';
				} else {
					$student['showphoneself'] = 'FALSE';
				}
				if ($otherres['AllowPicture']) {
					$student['showpictureself'] = 'TRUE';
				} else {
					$student['showpictureself'] = 'FALSE';
				}
				if ($otherres['Locker']) {
					$student['locker'] = $otherres['Locker'];
				}
				$student['mobile'] = $otherres['CellPhone'];
				$student['mail'] = $otherres['Email'];

				/*
				** Get and add privacy info
				*/
				$res = $oldsql->query('SELECT * FROM StudentPrivacyInfo WHERE StudentID=%d',$StudentID)->fetch_array(Result::ASSOC);
				$student['showpictures'] = $res['Picture']==1?'TRUE':'FALSE';
				$student['showschedule'] = $res['Schedule']==1?'TRUE':'FALSE';
				$student['showbirthday'] = $res['Birthday']==1?'TRUE':'FALSE';
				$student['showphone'] = $res['Phone']==1?'TRUE':'FALSE';
				$student['showaddress'] = $res['Address']==1?'TRUE':'FALSE';
				$student['showmap'] = $res['Map']==1?'TRUE':'FALSE';

			}
			if ($student) {
				$this->usertable[] = $student;
				$numlines++;
				if ($numlines % 100 == 0) {
					$I2_LOG->log_file("-$numlines-");
				}
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
			'links'=>'Useful Links',
			'scratchpad'=>'ScratchPad'
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
		global $I2_LDAP,$I2_SQL;
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
		$count = 0;
		foreach ($this->boxids as $boxid=>$name) {
			$I2_SQL->query('INSERT INTO intrabox_map (uid,boxid,box_order,closed) VALUES(%d,%d,%d,%d)',$teacher['uid'],$boxid,$count,0);
			$count++;
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
		$usernew['graduationYear'] = (-1*($user['grade']-12))+i2config_get('senior_gradyear',date('Y'),'user');
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
		if (isSet($user['locker'])) {
			$usernew['locker'] = $user['locker'];
		}
		$usernew['mail'] = $user['mail'];
		$usernew['soundexfirst'] = $user['soundexfirst'];
		$usernew['soundexlast'] = $user['soundexlast'];
		if (isSet($user['aim'])) {
			$usernew['aim'] = $user['aim'];
		}
		if (isSet($user['jabber'])) {
			$usernew['jabber'] = $user['jabber'];
		}
		if (isSet($user['msn'])) {
			$usernew['msn'] = $user['msn'];
		}
		if (isSet($user['icq'])) {
			$usernew['icq'] = $user['icq'];
		}
		$usernew['showpictures'] = $user['showpictures'];
		$usernew['showaddress'] = $user['showaddress'];
		$usernew['showmap'] = $user['showmap'];
		$usernew['showschedule'] = $user['showschedule'];
		$usernew['showphone'] = $user['showphone'];
		$usernew['showbirthday'] = $user['showbirthday'];
		$usernew['showphoneself'] = $user['showphoneself'];
		$usernew['showmapself'] = $user['showmapself'];
		$usernew['showscheduleself'] = $user['showscheduleself'];
		$usernew['showaddressself'] = $user['showaddressself'];
		$usernew['showpictureself'] = $user['showpictureself'];
		$usernew['showbdayself'] = $user['showbdayself'];
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
		$box_order = 1;
		foreach ($this->boxids as $boxid=>$name) {
			$I2_SQL->query('INSERT INTO intrabox_map (uid,boxid,box_order) VALUES(%d,%d,%d)',$usernum,$boxid,$box_order++);
		}
	}

	private function import_eighth_data() {
		global $I2_SQL,$I2_LDAP,$I2_LOG;

		//$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);

		/*
		** NOTE: starting and ending dates are currently ignored!  Any and all data is imported.
		*/

		$numsponsors = $this->import_eighth_sponsors();
					
		list($numactivities,$numrooms) = $this->import_eighth_activities();
		
		$numgroups = $this->import_eighth_groups();
		
		$numactivitiesentered = $this->process_student_signups();		

		$numabsences = $this->import_eighth_absences();

		$numgroupmembers = $this->import_eighth_group_memberships();


		d("$numactivities activities created",5);
		d("$numrooms rooms created",5);
		d("$numsponsors sponsors added",5);
		d("$numgroups 8th-period groups created",5);
		d("$numactivitiesentered student sign-ups processed",5);
		d("$numabsences absences recorded",5);
		d("$numgroupmembers group memberships handled",5);
		$I2_LOG->log_file('Eighth-period import complete!',5);
	}

	private function import_eighth_sponsors() {
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		/*
		** Create sponsors
		*/
		$numsponsors = 0;
		$res = $oldsql->query('Select SponsorID,Firstname,Lastname FROM SponsorInfo');
		while ($row = $res->fetch_array(Result::ASSOC)) {
			EighthSponsor::add_sponsor($row['Firstname'],$row['Lastname'],$row['SponsorID']);
			$numsponsors++;
		}
		return $numsponsors;
	}

	private function import_eighth_activities() {
		global $I2_LOG;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);

		// We're going to dispose of all the bad rooms - this means consolidation.
		// So, we'll reroute all the bad room IDs into the (fewer) good ones.
		$room_mappings = array();

		/*
		** Create rooms
		*/
		$numrooms = 0;
		$res = $oldsql->query('SELECT * FROM RoomInfo');
		while ($res->more_rows()) {
			$r = $res->fetch_array(Result::ASSOC);
			list($id,$name,$capacity) = array($r['RoomID'],$r['RoomName'],$r['Capacity']);
			// Eliminate silly (ROOM CHANGE) rooms
			$ct = 0;
			$name = str_replace('(ROOM CHANGE)','',$name,$ct);
			$name = trim($name);
			if ($ct > 0) {
					  // This WAS a bad room name
					  // Try to find an equivalent good one
					  // This has to use $oldsql because the new database (potentially) isn't fully populated yet
					  $newid = $oldsql->query('SELECT RoomID FROM RoomInfo WHERE RoomName=%s',$name)->fetch_single_value();
					  if ($newid) {
								 $room_mappings[$id] = $newid;
								 $id = $newid;
					  }
			}
			EighthRoom::add_room($name,$capacity,$id);
			$I2_LOG->log_file("Added room \"$name\"",8);
			$numrooms++;
		}

		/*
		** Create activities
		*/
		$numactivities = 0;
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
			$special = FALSE;
			if (strpos($name,'SPECIAL: ')) {
				$special = TRUE;
			}
			$name = str_replace('SPECIAL: ','',$name);
			if (strpos($name,'SPECIAL : ')) {
				$special = TRUE;
			}
			$name = str_replace('SPECIAL : ','',$name);
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

			if ($sponsors && $sponsors != '') {
				$sponsors = $this->create_sponsor($sponsors);
			}
			
			/*
			** Fetch/process all this activity's blocks
			*/
							
			/*$blockres = $oldsql->query('SELECT * FROM ActivityScheduleMap 
												 WHERE ActivityID=%d 
												 AND ActivityDate >= %s 
												 AND ActivityDate <= %s
												 ORDER BY ActivityDate DESC',
												 $aid,$startdate,$enddate);*/
			$blockres = $oldsql->query('SELECT * FROM ActivityScheduleMap 
												 WHERE ActivityID=%d 
												 ORDER BY ActivityDate DESC',
												 	$aid);
			while ($b = $blockres->fetch_array(Result::ASSOC)) {
				list($block,$brooms,$attendance,$cancelled,$bcomment,$advertisement,$date) =
					array($b['ActivityBlock'],$b['Room'],$b['AttendanceTaken'],$b['Cancelled'],
						$b['Comment'],$b['Advertisement'],$b['ActivityDate']);

				/*
				** Create block if necessary
				*/
				
				$bid = EighthBlock::add_block($date,$block,FALSE);
				$I2_LOG->log_file("[Tried to] add block $block on $date",6);
				
				/*
				** Fix old brokenness again
				*/
				if (!$advertisement) {
					$advertisement = '';
				}
				if (!$bcomment) {
					$bcomment = '';
				}

				// If we have a bad (to-be-eliminated) RoomID, use the good one instead
				if (isSet($room_mappings[$brooms])) {
						  $brooms = $room_mappings[$brooms];
				}

				$bsponsors = $oldsql->query('SELECT TeacherID FROM SponsorScheduleMap WHERE ActivityID=%d AND ActivityDate=%t AND ActivityBlock=%s',
						  $aid,$date,$block)->fetch_array(Result::NUM);

				if ($bsponsors && !$sponsors) {
						  $sponsors = $bsponsors;
				}

				/*
				** Schedule activity
				*/
				EighthSchedule::schedule_activity($bid,$aid,$bsponsors,$brooms,$bcomment,$attendance,$cancelled,$advertisement);
				$I2_LOG->log_file("Scheduled activity \"$name\" for $block on $date",6);
				$validrooms[$brooms] = 1;
				
				
			}

			/*
			** Actually create the activity
			*/
			EighthActivity::add_activity($name,$sponsors,array_keys($validrooms),$description,$restricted,$sticky,$bothblocks,$presign,$aid);
			$I2_LOG->log_file("Added activity \"$name\"",5);
			$numactivities++;
			
		}
		return array($numactivities,$numrooms);
	}	

	private function process_student_signups($studentidonly = FALSE) {
	   global $I2_LOG;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		
		$I2_LOG->log_file('Precomputing uid=>studentid mappings');

		$num = 0;

		//Badly named - keys are studentids, vals are uids
		$studentids = array();
		$ldap = LDAP::get_admin_bind($this->admin_pass);
		$res = $ldap->search('ou=people','objectClass=tjhsstStudent',array('tjhsstStudentId','iodineUidNumber'));
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
		$numactivitiesentered = 0;
		$res = $oldsql->query('SELECT * FROM StudentScheduleMap ORDER BY ActivityDate,ActivityBlock DESC');
		while ($res->more_rows()) {
			$a = $res->fetch_array(Result::ASSOC);
			list($studentid,$aid,$date,$block) = array($a['StudentID'],$a['ActivityID'],$a['ActivityDate'],$a['ActivityBlock']);
			if ($studentidonly && $studentid != $studentidonly) {
					  continue;
			}
			$activity = new EighthActivity($aid);
			$bid = EighthBlock::add_block($date,$block,FALSE);
			if (!isSet($studentids[$studentid])) {
				//There's quite a bit of bogus data in the old DB - this filters it
				$uid = User::studentid_to_uid($studentid);
				if (!$uid) {
					// Student isn't at TJ anymore - fin
					continue;
				}
				$studentids[$studentid] = $uid;
			} else {
				$uid = $studentids[$studentid];
			}
			//d("Adding user $uid (StudentID $studentid) to block $bid",6);
			$activity->add_member(new User($uid),TRUE,$bid);
			$I2_LOG->log_file("Switched student with StudentID $studentid into {$activity->name} on $date block $block",7);
			$numactivitiesentered++;
		}
		return $numactivitiesentered;
	}

	private function import_eighth_absences($studentidonly = FALSE) {
		global $I2_LOG;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		/*
		** Import absence information
		*/
		$numabsences = 0;
		$res = $oldsql->query("SELECT * FROM StudentAbsences");
		while ($row = $res->fetch_array(Result::ASSOC)) {
			if ($studentidonly && $studentidonly != $row['StudentID']) {
							 continue;
			}
			$uid = User::to_uidnumber($row['StudentID']);
			//d(print_r($row,1).':'.$uid,6);
			if (!$uid) {
				//Student doesn't go to TJ anymore - their StudentID just dangles here, so we'll discard them
				continue;
			}
			$I2_LOG->log_file("Marking user $uid absent from block {$row['ActivityBlock']} on {$row['ActivityDate']}",5);
			$blockid = EighthBlock::add_block($row['ActivityDate'],$row['ActivityBlock'],FALSE);
			EighthSchedule::add_absentee($blockid,$uid);
			$numabsences++;
		}
		return $numabsences;
	}

	private function import_eighth_groups() {
		global $I2_LOG;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		/*
		** Create groups
		*/
		$numgroups = 0;
		$res = $oldsql->query('SELECT * FROM GroupInfo');
		while ($res->more_rows()) {
			$g = $res->fetch_array(Result::ASSOC);
			list($id,$name) = array($g['GroupID'],$g['Name']);
			$description = 'Automatically created by dataimport';
			Group::add_group('eighth_'.$name,'Eighth-period activity: '.$description,$id);
			$I2_LOG->log_file("Added group for $name",6);
			$numgroups++;
		}
		return $numgroups;
	}


	private function import_eighth_group_memberships($studentidonly = FALSE) {
		global $I2_LOG;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		/*
		** Add students to groups
		*/
		$numgroupmembers = 0;
		$res = $oldsql->query('SELECT StudentID,GroupID FROM StudentGroupMap');
		while ($row = $res->fetch_array(Result::ASSOC)) {
				  $group = new Group($row['GroupID']);
				  // Must be cautious about students no longer at the school, junk data, etc.
				  if (!$row['StudentID'] || ($studentidonly && $studentidonly != $row['StudentID'])) {
							 continue;
				  }
				  $uid = User::to_uidnumber($row['StudentID']);
				  d($uid,1);
				  if (!$uid) {
							 continue;
				  }
				  $group->add_user($uid);
				  $I2_LOG->log_file('Student with StudentID '.$row['StudentID'].' is a member of group number '.$row['GroupID'],5);
				  $numgroupmembers++;
		}
		return $numgroupmembers;
	}

	private function import_eighth_permissions($studentidonly = FALSE) {
		global $I2_LOG;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		$res = $oldsql->query('SELECT * FROM StudentActivityPermissionMap');
		while ($row = $res->fetch_array(Result::ASSOC)) {
				  if ($studentidonly && $studentidonly != $row['StudentID']) {
							 continue;
				  }
				  $user = User::studentid_to_uid($row['StudentID']);
				  // Arr, bad Intranet 1 data!
				  if (!$user) {
							 d('Bad studentid '.$row['StudentID'],3);
							 continue;
				  }
				  //$I2_LOG->log_file('Allowing student with ID '.$row['StudentID'].' to go to activity #'.$row['ActivityID']);
				  $act = new EighthActivity($row['ActivityID']);
				  $act->add_restricted_member(new User($user));
				  $I2_LOG->log_file('Allowing student with ID '.$row['StudentID'].' to go to activity #'.$row['ActivityID']);
				  //EighthActivity::add_restricted_member_to_activity($row['ActivityID'],new User($user));
		}
	}

	private function fix_broken_user($studentid) {
			  $oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
			  $uid = User::studentid_to_uid($studentid);
			  if (!$uid) {
						 // User must be imported first
						 $this->import_student_data(TRUE,$studentid);
			  }
			  $this->process_student_signups($studentid);
			  $this->import_eighth_group_memberships($studentid);
			  $this->import_eighth_permissions($studentid);
			  $this->import_eighth_absences($studentid);
	}

	private function create_sponsor($sponsor) {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT sid FROM eighth_sponsors WHERE lname=%s',$sponsor)->fetch_single_value();
		if ($res) {
			/*
			** Sponsor already exists
			*/
			return $res;
		}
		if (is_numeric($sponsor)) {
			$res = $I2_SQL->query('SELECT sid FROM eighth_sponsors WHERE sid=%d',$sponsor)->fetch_single_value();
			if ($res) {
				return $res;
			}
		}
		$this->numsponsors += 1;
		$res = $I2_SQL->query('INSERT INTO eighth_sponsors (lname) VALUES(%s)',$sponsor);
		return $res->get_insert_id();
	}

	private function import_aphorisms() {
		global $I2_SQL;
		$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
		$res = $oldsql->query('SELECT * FROM StudentAphorismsInfo');
		while ($res->more_rows()) {
				  $row = $res->fetch_array(Result::ASSOC);
				  $I2_SQL->query('REPLACE INTO aphorisms SET uid=%d,college=%s,collegeplans=%s,nationalmeritsemifinalist=%d,nationalmeritfinalist=%d,
							 nationalachievement=%d,hispanicachievement=%d,honor1=%s,honor2=%s,honor3=%s,aphorism=%s',
							 User::to_uidnumber($row['StudentID']),$row['College'],$row['CollegePlans'],$row['NationalMeritSemifinalist'],
							 $row['NationalMeritFinalist'],$row['NationalAchievement'],$row['HispanicAchievement'],$row['Honor1'],$row['Honor2'],$row['Honor3'],
							 $row['Aphorism']
				  );
		}
	}

	/**
	* Expands a student's Intranet 2 presence by adding their non-critical data from Intranet 1.
	* DEPRECATED - dessicated, moved into import_ methods
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
		$I2_SQL->query('DELETE FROM poll_group_map');
		$I2_SQL->query('DELETE FROM groups_perms');
		/*
		** This stuff needs to come last so we retain privs to the bitter end
		** This still depends on a bit of caching being done...
		*/
		$I2_SQL->query('DELETE FROM group_user_map');
		$I2_SQL->query('DELETE FROM groups');
	}

	private function clean_eighth_absences() {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM eighth_absentees');
	}

	private function clean_eighth_groups() {
			  $groups = Group::get_all_groups('eighth');
			  foreach ($groups as $group) {
						 $group->delete_group();
			  }
	}

	private function clean_eighth() {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM eighth_activities');
		$I2_SQL->query('DELETE FROM eighth_activity_map');
		$I2_SQL->query('DELETE FROM eighth_blocks');
		$this->clean_eighth_absences();
		$I2_SQL->query('DELETE FROM eighth_block_map');
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

		if ($this->admin_pass) {
			$ldap = LDAP::get_admin_bind($this->admin_pass);
		} else {
				  $ldap = $I2_LDAP;
		}

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
		$ldap->add('ou=people',$people);

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
		$ldap->delete('iodineUid=admin,ou=people');
		$ldap->add('iodineUid=admin,ou=people',$admin);
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
		$ldap->delete('iodineUid=eighthOffice,ou=people');
		$ldap->add('iodineUid=eighthOffice,ou=people',$admin);
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

	private function clean_polls() {
			  global $I2_SQL;
			  $I2_SQL->query('TRUNCATE TABLE polls');
			  $I2_SQL->query('TRUNCATE TABLE poll_questions');
			  $I2_SQL->query('TRUNCATE TABLE poll_answers');
			  $I2_SQL->query('TRUNCATE TABLE poll_group_map');
			  $I2_SQL->query('TRUNCATE TABLE poll_votes');
	}

	/**
	* Import poll data
	*/
	private function import_polls() {
			global $I2_SQL, $I2_LOG;
			$oldsql = new MySQL($this->intranet_server,$this->intranet_db,$this->intranet_user,$this->intranet_pass);
			$answerstopolls = array();
			$answerstoquestions = array();
			/*
			** Import polls
			*/
			$res = $oldsql->query('SELECT * FROM PollGroupInfo');
			while ($row = $res->fetch_array(Result::ASSOC)) {
					  $questions = explode(',',$row['Polls']);
					  $pollname = $row['GroupName'];
					  $pollid = $row['GroupID'];
					  foreach ($questions as $questionid) {
							$questionstopolls[$questionid] = $pollid;
					  }
					  $pollstart = NULL;
					  $pollend = NULL;
					  $showpoll = TRUE;
					  foreach ($questions as $questionid) {
							$qres = $oldsql->query('SELECT * FROM PollInfo WHERE PollID=%d',$questionid)->fetch_array(Result::ASSOC);
							list($questionid,$questionname,$questionstart,$questionend,$showresults,$type,$classes,$showquestion,$question,$maxvotes) =
									  array(
												 $qres['PollID'],$qres['PollName'],$qres['StartDatetime'],$qres['EndDatetime'],$qres['ShowResults'],
												 $qres['Type'],$qres['Classes'],$qres['ShowPollBox'],$qres['PollQuestion'],$qres['MaxVotes']
									  );
							if ($type == 0) {
									  $type = 'standard';
							} elseif ($type == 1) {
									  $type = 'approval';
							} else {
									  throw new I2Exception('Invalid poll type'.$type.'!');
							}
							if ($pollstart == NULL) {
									  $pollstart = $questionstart;
							}
							if ($pollend == NULL) {
									  $pollend = $questionend;
							}
							if ($questionstart != $pollstart) {
									  d('Inconsistent start date!',1);
							}
							if ($questionend != $pollend) {
									  d('Inconsistent ending date!',1);
							}
							if (!$showquestion) {
									  if ($showpoll) {
										  d('Poll not being shown due to hidden question');
									  }
									  $showpoll = FALSE;
							}
							$ares = $oldsql->query('SELECT * FROM PollOptionInfo WHERE PollID=%d',$questionid);
							while ($arow = $ares->fetch_array(Result::ASSOC)) {
									  list($answerid,$answer) = array($arow['OptionID'],$arow['OptionName']);
									  // Noncolliding unique number (I hope)
									  $answerid = 1000000*$pollid+1000*$questionid+$answerid;
									  $I2_SQL->query('REPLACE INTO poll_answers (aid,answer) VALUES(%d,%s)',
												 	$answerid,$answer
												 );
							}
							$questionid = 1000*$pollid+$questionid;
							if (empty($question)) {
									  $question = $questionname;
							}
							$I2_SQL->query('REPLACE INTO poll_questions (qid,maxvotes,question,answertype) VALUES(%d,%d,%s,%s)',
									  	$questionid,$maxvotes,$question,$type
									  );
					  }
			  		  $I2_SQL->query('REPLACE INTO polls SET pid=%d,name=%s,introduction=%s,visible=%d,startdt=%T,enddt=%T',
							$pollid,$pollname,$pollname,$showpoll?1:0,$pollstart?$pollstart:'1900-01-01 00:00:00',$pollend?$pollend:'3000-01-01 00:00:00');
					  $this->import_poll_permissions($pollid,$classes);
			}
			/*
			** Import user votes
			*/
			$res = $oldsql->query('SELECT * FROM StudentPollMap');
			while ($row = $res->fetch_array(Result::ASSOC)) {
					  list($studentid,$questionid,$answerid) = array($row['StudentID'],$row['PollID'],$row['OptionID']);
					  $user = User::studentid_to_uid($studentid);
					  if (!$user) {
								 d('Invalid studentid '.$studentid,3);
								 continue;
					  }
					  $user = new User($user);
					  if (isSet($questionstopolls[$questionid])) {
						  // We need to remove the bitwise OR encoding used on these answers <sigh>
						  $aidparts = array();
						  // I know this isn't the normal way to decode the data but I feel safer doing it this way than
						  // trusting myself to use bitshifts correctly.  This makes for simpler, safer code.
						  $bin = decbin($answerid);
						  $ct = 0;
						  while ($ct < strlen($bin)) {
									 if (substr($bin,$ct,1) == '1') {
												$aidparts[] = pow(2,strlen($bin)-$ct-1); // 2 ^ (len-ct) = old OptionID de-bitwise-ored
									 }
									 $ct++;
						  }
						  foreach ($aidparts as $aidpart) {
							  $aid = 1000000*$questionstopolls[$questionid]+1000*$questionid+$aidpart;
							  $I2_LOG->log_file('Student '.$studentid.' voted for answer '.$aid.' ('.$aidpart.')');
							  $I2_SQL->query('REPLACE INTO poll_votes SET uid=%d,aid=%d',$user->uid,$aid);
						  }
					  } else {
							$I2_LOG->log_file('Discarding answer '.$answerid.' to question '.$questionid);
					  }
			}
	}

	private function import_poll_permissions($pollid,$classes) {
			  global $I2_SQL;
				     /*
					  ** I have no clue whatsover what's going on with the 'Classes' attribute
					  ** It looks like a bitshifting combination
					  ** I'll just copy some Intranet 1 code and translate to poll_group mappings, hoping it'll work.
					  */
					  $seniorgroup = new Group('grade_12');
					  $juniorgroup = new Group('grade_11');
					  $sophomoregroup = new Group('grade_10');
					  $freshmangroup = new Group('grade_9');
					  if ($classes & (1<<3)) {
								 $I2_SQL->query('REPLACE INTO poll_group_map SET pid=%d,gid=%d',$pollid,$seniorgroup->gid);
					  }
					  if ($classes & (1<<2)) {
								 $I2_SQL->query('REPLACE INTO poll_group_map SET pid=%d,gid=%d',$pollid,$juniorgroup->gid);
					  }
					  if ($classes & (1<<1)) {
								 $I2_SQL->query('REPLACE INTO poll_group_map SET pid=%d,gid=%d',$pollid,$sophomoregroup->gid);
					  }
					  if ($classes & (1<<0)) {
								 $I2_SQL->query('REPLACE INTO poll_group_map SET pid=%d,gid=%d',$pollid,$freshmangroup->gid);
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
		
		$I2_LOG->log_file('Importing teachers...',3);
		$this->import_teacher_data_file_one();
		$this->import_teacher_data_ldap();
		$I2_LOG->log_file('Teachers imported',3);
		
		$I2_LOG->log_file('Importing eighth-period data...',3);
		$this->import_eighth_data();
		$I2_LOG->log_file('Eighth period imported',3);
		
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
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'unset_intranet') {
			unset($_SESSION['intranet_server']);
			unset($this->intranet_server);
			unset($_SESSION['intranet_pass']);
			unset($this->intranet_pass);
			unset($_SESSION['intranet_db']);
			unset($this->intranet_db);
			unset($_SESSION['intranet_user']);
			unset($this->intranet_user);
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
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_eighth_groups' && isSet($_REQUEST['doit'])) {
			$this->clean_eighth_groups();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_teachers' && isSet($_REQUEST['doit'])) {
			$this->clean_teachers();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'eighth_absences' && isSet($_REQUEST['doit'])) {
			$this->clean_eighth_absences();
			$this->import_eighth_absences();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'eighth_groups' && isSet($_REQUEST['doit'])) {
				  $this->clean_eighth_groups();
				  $this->import_eighth_groups();
				  $this->import_eighth_group_memberships();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_eighth' && isSet($_REQUEST['doit'])) {
			$this->clean_eighth();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clean_other' && isSet($_REQUEST['doit'])) {
			$this->clean_other();
			$this->init_db();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'studentinfo' && isSet($_REQUEST['doit'])) {
			$this->expand_student_info();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'eighth_permissions' && isSet($_REQUEST['doit'])) {
			$this->import_eighth_permissions();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teachersponsors' && isSet($_REQUEST['doit'])) {
			$this->make_teachers_sponsors();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'doeverything' && isSet($_REQUEST['doit'])) {
			$this->do_imports();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'polls' && isSet($_REQUEST['doit'])) {
			$this->clean_polls();
			$this->import_polls();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'aphorisms' && isSet($_REQUEST['doit'])) {
			$this->import_aphorisms();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'fixit' && isSet($_REQUEST['doit'])) {
			$this->init_db();
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'fixuser' && isSet($_REQUEST['studentid'])) {
			$this->fix_broken_user($_REQUEST['studentid']);
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
