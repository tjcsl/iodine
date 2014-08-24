<?php
/**
* Just contains the definition for the {@link Module} {@link newimport}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage Admin
* @filesource
*/

/**
* A user-friendly {@link Module} to import data from SASI dumps.
* @package modules
* @subpackage Admin
*/
class Newimport extends Module {

	private $template = 'home.tpl';
	private $template_args = [];

	private $startyear = FALSE;

	private $messages = [];

	private $boxids;

	private $new_iodine_uid;

	public static $sqltables = [
			'alum'				=> 'id',
			'aphorisms'			=> 'uid',
			'calculators'			=> 'uid',
			'eighth_absentees'		=> 'userid',
			'eighth_activity_map'		=> 'userid',
			'eighth_activity_permissions'	=> 'userid',
			'event_admins'			=> 'uid',
			'event_signups'			=> 'uid',
			'event_verifiers'		=> 'uid',
			'groups_static'			=> 'uid',
			'groups_user_perms'		=> 'uid',
			'intrabox_map'			=> 'uid',
			'news_forwarding'		=> 'uid',
			'news_read_map'			=> 'uid',
			'news'				=> 'authorID',
			'parking_apps'			=> 'uid',
			'parking_cars'			=> 'uid',
			//'poll_votes'			=> 'uid', #leave the data in this table, it's historical --wyang 2009/06/22
			'prom'				=> 'uid',
			'scratchpad'			=> 'uid',
			'senior_destinations'		=> 'uid',
			'news_shaded_map'		=> 'uid',
			'homecoming_votes'		=> 'uid',
			'homecoming_votes'		=> 'male',
			'homecoming_votes'		=> 'female',
			'event_signups'			=> 'vid',
			'eighth_activity_permissions'	=> 'userid'
	]; #This is also used by the set_uidnumber function in User.

	/**
	* Required by the {@link Module} interface
	*/
	public function init_pane() {
		global $I2_USER, $I2_ARGS;

		// only people with LDAP admin privs can import stuff
		if (! $I2_USER->is_ldap_admin()) {
			return FALSE;
		}

		if(count($I2_ARGS) > 1) {
			$method = $I2_ARGS[1];
			if(method_exists($this, $method)) {
				return $this->$method();
			}
		}

		return 'Import Data';
	}

	public function teachers() {
		$this->template = 'teachers.tpl';
		return array('Import Data', 'Import Data: Edit Teachers');
	}

	public function teacher_new() {
		global $I2_LDAP;
		if (array_key_exists('id', $_REQUEST)) {
			$uid = $_REQUEST['id'];
		}
		else {
			redirect('newimport');
		}

		$res = $I2_LDAP->search_one(LDAP::get_user_dn(), "(&(objectClass=tjhsstTeacher)(iodineUidNumber=$uid))");

		// RANDOM BUGGY num_rows() RETURNS ONE MORE THAN IT SHOULD
		if ($res->num_rows() > 1) {
			$this->template = 'teacher_new_conflict.tpl';
			$this->template_args['user'] = new User($uid);
			return array('Import Data', 'Import Data: Conflicting ID Number');
		}

		$this->template_args['iodineUidNumber'] = $uid;
		$this->template_args['iodineUid'] = $this->template_args['givenName'] = $this->template_args['sn'] = "";

		$this->template_args['method'] = 'teacher_new_doit';
		$this->template_args['showdelete'] = FALSE;
		$this->template = 'teacher_edit.tpl';
		return array('Import Data', 'Import Data: New Teacher');
	}

	public function teacher_new_doit() {
		if (!array_key_exists('id', $_REQUEST)) {
			redirect('newimport');
		}

		$this->create_teacher($_REQUEST['data']);

		$user = new User($_REQUEST['data']['iodineUid']);

		$this->template = 'teacher_done.tpl';
		$this->template_args['action'] = 'edit';
		$this->template_args['name'] = $user->name;
		return array('Import Data', 'Import Data: Teacher Successfully Created');
	}

	public function teacher_edit() {
		global $I2_ARGS;

		if (array_key_exists('id', $_REQUEST)) {
			$uid = $_REQUEST['id'];
		}
		else if (array_key_exists(2, $I2_ARGS)) {
			$uid = $I2_ARGS[2];
		}
		else {
			redirect('newimport');
		}

		$user = new User($uid);

		$this->template_args['method'] = 'teacher_edit_doit';
		$this->template_args['iodineUid'] = $user->iodineUid;
		$this->template_args['iodineUidNumber'] = $user->iodineUidNumber;
		$this->template_args['givenName'] = $user->givenName;
		$this->template_args['sn'] = $user->sn;

		$this->template_args['showdelete'] = TRUE;

		$this->template = 'teacher_edit.tpl';
		return array('Import Data', 'Import Data: Edit Teacher');
	}

	public function teacher_edit_doit() {
		global $I2_LDAP, $I2_ROOT;

		if (!array_key_exists('id', $_REQUEST)) {
			redirect('newimport');
		}

		$user = new User($_REQUEST['id']);
		$warnings = [];
		
		$data = $_REQUEST['data'];
		if ($data['iodineUid'] != $user->iodineUid) {
			$warnings[] = "You are changing the username. This <em>should</em> work correctly, but may cause problems.";
		}
		if ($data['iodineUidNumber'] != $user->iodineUidNumber) {
			$res = $I2_LDAP->search_one(LDAP::get_user_dn(), "(&(objectClass=tjhsstTeacher)(iodineUidNumber={$data['iodineUidNumber']}))");
			// RANDOM BUGGY num_rows() RETURNS ONE MORE THAN IT SHOULD
			if ($res->num_rows() > 1) {
				$warnings[] = '<span style="color: red;">YOU ARE CHANGING THE UID NUMBER TO A NUMBER THAT IS ALREADY USED.</span> '.
					"It is taken by <a href=\"{$I2_ROOT}newimport/teacher_edit/{$existant->iodineUidNumber}\">{$existant->name}</a>.";
			}
			else {
				$warnings[] = "You are changing the UID number. This <em>should</em> work correctly, but may cause problems.";
			}
		}

		if (!array_key_exists('DOIT', $_REQUEST)) {
			$this->template_args['iodineUid_old'] = $user->iodineUid;
			$this->template_args['iodineUid_new'] = $data['iodineUid'];
			$this->template_args['iodineUidNumber_old'] = $user->iodineUidNumber;
			$this->template_args['iodineUidNumber_new'] = $data['iodineUidNumber'];
			$this->template_args['givenName_old'] = $user->givenName;
			$this->template_args['givenName_new'] = $data['givenName'];
			$this->template_args['sn_old'] = $user->sn;
			$this->template_args['sn_new'] = $data['sn'];
			$this->template_args['warnings'] = $warnings;

			$this->template = 'teacher_edit_doit.tpl';
			return array('Import Data', 'Import Data: Confirm Teacher Data Change');
		}
		else {
			if ($data['iodineUid'] != $user->iodineUid) {
				$user->set_uid($data['iodineUid']);
			}
			if ($data['iodineUidNumber'] != $user->iodineUidNumber) {
				$user->set_uidnumber($data['iodineUidNumber']);
			}
			$user->givenName = $data['givenName'];
			$user->sn = $data['sn'];
			$user->cn = $data['givenName'].' '.$data['sn'];

			$this->template = 'teacher_done.tpl';
			$this->template_args['action'] = 'edit';
			$this->template_args['name'] = $user->name;
			return array('Import Data', 'Import Data: Teacher Successfully Modified');
		}
	}

	public function teacher_delete() {
		if (!array_key_exists('uid', $_REQUEST)) {
			redirect('newimport');
		}

		$user = new User($_REQUEST['uid']);
		$this->template_args['user'] = $user;
		$this->template = 'teacher_del.tpl';
		return array('Import Data', 'Import Data: Confirm Teacher Deletion');
	}

	public function teacher_delete_doit() {
		if (array_key_exists('uid', $_REQUEST)) {
			$uid = $_REQUEST['uid'];
		}
		else {
			redirect('newimport');
		}

		$user = new User($uid);
		$name = $user->name;

		$this->del_user($uid);

		$this->template = 'teacher_done.tpl';
		$this->template_args['action'] = 'delete';
		$this->template_args['name'] = $name;
		return array('Import Data', 'Import Data: Teacher Successfully Deleted');
	}

	public function students() {
		$this->template = 'students.tpl';
		return array('Import Data', 'Import Data: Student Data');
	}

	public function students_doit() {
		global $I2_ERR, $I2_SQL;

		// make sure the user checked the doit box
		if (! isset($_POST['doit'])) {
			$I2_ERR->nonfatal_error("You must check the 'Yes, I really want to do this' box if you really want to import data.");
			return $this->students();
		}

		// make sure all the files went through correctly
		if ($_FILES['studentfile']['error'] != UPLOAD_ERR_OK) {
			$I2_ERR->nonfatal_error('There was an error uploading the student data file: '.$_FILES['studentfile']['error']);
			return $this->students();
		}
		if ($_FILES['schedulefile']['error'] != UPLOAD_ERR_OK) {
			$I2_ERR->nonfatal_error('There was an error uploading the schedule file');
			return $this->students();
		}
		if ($_FILES['coursefile']['error'] != UPLOAD_ERR_OK) {
			$I2_ERR->nonfatal_error('There was an error uploading the course data file');
			return $this->students();
		}

		$studentfile = $_FILES['studentfile']['tmp_name'];
		$schedulefile = $_FILES['schedulefile']['tmp_name'];
		$coursefile = $_FILES['coursefile']['tmp_name'];

		// start-of-the year stuff
		if ($_POST['startyear']) {
			$this->startyear = TRUE;
			$this->start_year();
		}

		// do the import!
		$this->import_student_data($studentfile);
		$this->import_schedules($coursefile, $schedulefile);

		// get rid of the files
		unlink($studentfile);
		unlink($schedulefile);
		unlink($coursefile);

		/*
		** by this time the MySQL connection is pretty much guaranteed
		** to have gone away; we'll recreate it, so everything else
		** works like it's supposed to
		*/
		$I2_SQL = new MySQL();

		$this->template = 'done.tpl';
		$this->template_args['action'] = 'Student Import';
		$this->template_args['messages'] = $this->messages;
		return array('Data Import', 'Data Import: Finished Student Data');
	}

	/**
	* Required by the {@link Module} interface
	*/
	public function display_pane($display) {
		$display->disp($this->template,$this->template_args);
	}

	/**
	* Required by the {@link Module} interface
	*/
	public function get_name() {
		return 'Newimport';
	}

	/**
	* Create a new teacher in the database
	*/
	private function create_teacher($info, $ldap=NULL) {
		global $I2_SQL, $I2_LDAP;
		
		if ($ldap === NULL) {
			$ldap = $I2_LDAP;
		}

		$newteach = [];
		$newteach['objectClass'] = 'tjhsstTeacher';
		$newteach['iodineUid'] = $info['iodineUid'];
		$newteach['iodineUidNumber'] = $info['iodineUidNumber'];
		$newteach['cn'] = $info['givenName'].' '.$info['sn'];
		$newteach['sn'] = $info['sn'];
		$newteach['givenName'] = $info['givenName'];
		$newteach['style'] = 'default';
		$newteach['header'] = 'TRUE';
		$newteach['chrome'] = 'TRUE';
		$newteach['startpage'] = 'welcome';
		$dn = "iodineUid={$newteach['iodineUid']},ou=people,dc=tjhsst,dc=edu";

		$ldap->add($dn,$newteach);

		$this->init_desired_boxes();

		$count = 0;
		//$I2_SQL->query('DELETE FROM intrabox_map WHERE uid=%d', $info['id']);
		foreach ($this->boxids as $boxid=>$name) {
			if ($name == 'eighth' || $name == 'mail') {
				continue; // teachers don't need (or want) eighth period or mail
			}
			$I2_SQL->query('INSERT INTO intrabox_map (uid,boxid,box_order,closed) VALUES(%d,%d,%d,%d)',$info['iodineUidNumber'],$boxid,$count,0);
			$count++;
		}
	}
	
	/**
	* Do start-of-the-year cleanup stuff
	*
	* Basically, wipes eighth period absences
	* and now also homecoming court votes
	*/
	private function start_year() {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM eighth_absentees');
		$I2_SQL->query('DELETE FROM homecoming_votes');
	}

	/**
	* Update student data from a SASI dump file (INTRANET.***) into LDAP
	*/
	private function import_student_data($filename) {
		global $I2_LDAP;

		$ldap = $I2_LDAP;

		$oldusers = $ldap->search(LDAP::get_user_dn(), 'objectClass=tjhsstStudent', 'iodineUid')->fetch_col('iodineUid');

		$newusers = [];
		$newuserdata = [];

		$file = @fopen($filename, 'r');

		d("Importing data from user data file $filename...",6);
		// fgetcsv reads a line and parses as CSV
		// args past are length=0 (autodetect max line length)
		// and delimiter="\t" for tab-delimiters instead of commas
		while (list($username, 
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
					$Nickname,
				) = fgetcsv($file, 0, "\t")) {
			$username = strtolower($username);
			$newusers[] = $username;
			$newuserdata[] = array(
				'username' => $username,
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
				'nick' => $Nickname,
			);
		}
		$this->messages[] = count($newusers).' users read from SASI dump file';

		// This has to be done before deleting old users, just in case
		$res = $ldap->search('ou=people,dc=tjhsst,dc=edu', 'objectClass=tjhsstStudent', array('iodineUidNumber'));
		$this->new_iodine_uid = max($res->fetch_col('iodineUidNumber'))+1;

		$toremove = array_diff($oldusers, $newusers);
		foreach ($toremove as $olduser) {
			/*
			** Don't clobber "fake" students with uids between
			** 1000 and 9999 (all real student iodineUidNumbers
			** are above 10000).
			**
			** FIXME: This number should probably not be hardcoded.
			*/
			$fakecount=0;
			$res = $ldap->search(LDAP::get_user_dn(), 'iodineUid='.$olduser, 'iodineUidNumber');
			d("deleting old user $olduser",7);
			if ($res->fetch_single_value() >= 10000) {
				$this->del_user($olduser,$ldap);
				d("this user is real, goodby",7);
			} else {
				$fakecount++;
				d("this user is fake",7);
			}
		}
		$this->messages[] = 'Removed '.(count($toremove)-$fakecount).' old users';

		$this->init_desired_boxes();

		$numcreated = 0;

		/*
		** FIXME: LDAPResult num_rows() is badly broken and returns one
		** greater than the correct result. Figure out what in the world
		** that's all about.
		*/
		foreach ($newuserdata as $user) {
			$username = $user['username'];
			$res = $ldap->search('ou=people,dc=tjhsst,dc=edu', "iodineUid=$username");

			if ($res->num_rows() - 1 > 1) {
				warn("PROBLEM! More than one user found with iodineUid $username...");
			}
			else if ($res->num_rows() - 1 == 0) {
				$numcreated++;
				$this->create_user($user,$ldap);
			}

			$this->update_user($user,$ldap);
		}
		$this->messages[] = "$numcreated new users created";
	}

	/**
	* Adds a new user from the given data
	*/
	private function create_user($user,$ldap=NULL) {
		global $I2_LDAP,$I2_SQL;
		if (!$ldap) {
			$ldap = $I2_LDAP;
		}
		$usernew = [];
		$usernew['objectClass'] = 'tjhsstStudent';
		$usernew['graduationYear'] = User::get_gradyear($user['grade']);
		$usernew['iodineUid'] = strtolower($user['username']);
		$usernew['givenName'] = $user['fname'];
		$usernew['cn'] = $user['fname'].' '.$user['lname'];
		$usernew['sn'] = $user['lname'];
		$usernew['tjhsstStudentId'] = $user['studentid'];
		$usernew['iodineUidNumber'] = $this->new_iodine_uid++;
		$usernew['style'] = 'default';
		$usernew['header'] = 'TRUE';
		$usernew['chrome'] = 'TRUE';
		$usernew['startpage'] = 'welcome';

		$usernew['showpictures'] = 'FALSE';
		$usernew['showaddress'] = 'FALSE';
		$usernew['showmap'] = 'FALSE';
		$usernew['showschedule'] = 'FALSE';
		$usernew['showphone'] = 'FALSE';
		$usernew['showbirthday'] = 'FALSE';
		$usernew['showeighth'] = 'FALSE';
		$usernew['showlocker'] = 'FALSE';
		$usernew['showphoneself'] = 'TRUE';
		$usernew['showmapself'] = 'TRUE';
		$usernew['showscheduleself'] = 'TRUE';
		$usernew['showaddressself'] = 'TRUE';
		$usernew['showpictureself'] = 'TRUE';
		$usernew['showbdayself'] = 'TRUE';
		$usernew['showeighthself'] = 'TRUE';
		$usernew['showlockerself'] = 'TRUE';

		$usernew['preferredPhoto'] = 'AUTO';

		$dn = "iodineUid={$usernew['iodineUid']},ou=people,dc=tjhsst,dc=edu";

		d("Creating user \"{$usernew['iodineUid']}\"...",7);
		//warn("Creating user \"{$usernew['iodineUid']}\"...");
		$ldap->add($dn,$usernew);
		$box_order = 1;
		foreach (array_keys($this->boxids) as $boxid) {
			$I2_SQL->query('INSERT INTO intrabox_map (uid,boxid,box_order) VALUES(%d,%d,%d)',$usernew['iodineUidNumber'],$boxid,$box_order++);
		}
	}

	/**
	* Updates a user's info from the given data
	*/
	private function update_user($user,$ldap=NULL) {
		global $I2_LDAP;
		if (!$ldap) {
			$ldap = $I2_LDAP;
		}
		$usernew = [];
		$usernew['tjhsstStudentId'] = $user['studentid'];
		$usernew['cn'] = $user['fname'].' '.$user['lname'];
		$usernew['sn'] = $user['lname'];
		$usernew['postalCode'] = $user['zip'];
		if ($user['counselor']) {
			$usernew['counselor'] = $user['counselor'];
		}
		$usernew['st'] = $user['state'];
		$usernew['l'] = $user['city'];
		if ($user['phone_home']) {
			$usernew['homePhone'] = $user['phone_home'];
		}
		$usernew['birthday'] = $user['bdate'];
		$usernew['street'] = $user['address'];
		$usernew['givenName'] = $user['fname'];
		$usernew['graduationYear'] = User::get_gradyear($user['grade']);
		if ($user['nick']) {
			$usernew['nickName'] = $user['nick'];
		}
		if ($user['mname'] != '') {
			$usernew['displayName'] = $user['fname'].' '.$user['mname'].' '.$user['lname'];
		} else {
			$usernew['displayName'] = $user['fname'].' '.$user['lname'];
		}
		$usernew['gender'] = $user['sex'];
		if ($this->startyear) {
			$usernew['showpictures'] = 'FALSE';
			$usernew['showaddress'] = 'FALSE';
			$usernew['showmap'] = 'FALSE';
			$usernew['showschedule'] = 'FALSE';
			$usernew['showphone'] = 'FALSE';
			$usernew['showbirthday'] = 'FALSE';
			$usernew['showeighth'] = 'FALSE';
			$usernew['showlocker'] = 'FALSE';
			$usernew['showphoneself'] = 'TRUE';
			$usernew['showmapself'] = 'TRUE';
			$usernew['showscheduleself'] = 'TRUE';
			$usernew['showaddressself'] = 'TRUE';
			$usernew['showpictureself'] = 'TRUE';
			$usernew['showbdayself'] = 'TRUE';
			$usernew['showeighthself'] = 'TRUE';
			$usernew['showlockerself'] = 'TRUE';
			$usernew['eighthoffice-comments'] = ' ';
		}
		$usernew['title'] = ($user['sex']=='M')?'Mr.':'Ms.';
		if ($user['mname']) {
			$usernew['middlename'] = $user['mname'];
		}
		$dn = "iodineUid={$user['username']},ou=people,dc=tjhsst,dc=edu";

		d("Updating user \"{$user['username']}\"...",7);
		//warn("Updating user \"{$user['username']}\"...");
		$ldap->modify_object($dn,$usernew);
	}

	/**
	* Delete a user from the databases
	*/
	private function del_user($user, $ldap=NULL) {
		global $I2_SQL, $I2_LDAP;

		if ($ldap == NULL) {
			$ldap = $I2_LDAP;
		}

		d("deleting user $user", 7);
		$uid = $ldap->search(LDAP::get_user_dn(), "iodineUid=$user", 'iodineUidNumber')->fetch_single_value();
		d("(uidnumber $uid)", 7);
		if ($uid) {
			foreach (self::$sqltables as $table => $col) {
				$I2_SQL->query('DELETE FROM %c WHERE %c=%d', $table, $col, $uid);
			}
		}
		d("deleting_recursive: ".LDAP::get_user_dn($user).", 'objectClass=*'", 7);
		$ldap->delete_recursive(LDAP::get_user_dn($user), 'objectClass=*');
	}

	private function import_schedules($classfile, $schedulefile) {
		global $I2_LDAP,$I2_LOG;
		
		$ldap = $I2_LDAP;

		/*
		** Before everything, get rid of the old classes
		*/
		$oldclasses = $ldap->search(LDAP::get_schedule_dn(), 'objectClass=tjhsstClass', 'tjhsstSectionId')->fetch_col('tjhsstSectionId');
		foreach ($oldclasses as $oldsectionid) {
			$ldap->delete(LDAP::get_schedule_dn($oldsectionid));
		}

		$numclasses = 0;
				
		/*
		** First create classes
		*/
		d("Reading from class file: $classfile...", 6);
		$file = @fopen($classfile,'r');
		// fgetcsv reads a line and parses as CSV
		// args past are length=0 (autodetect max line length)
		// and delimiter="\t" for tab-delimiters instead of commas
		while (list($sectionid,$periodstart,$periodend,$courselen,$othercourselen,$otherothercourselen,$teacherid,$room,$class) = fgetcsv($file, 0, "\t")) {
			list($classid,) = explode('-',$sectionid);
			$numclasses++;

			$semesterno = $courselen[1];

			// Hunt down the sponsor - and kill them!
			$sponsordn = $ldap->search(LDAP::get_user_dn(),"iodineUidNumber=$teacherid",array('iodineUid'))->fetch_single_value();

			if (!$sponsordn) {
				$I2_LOG->log_file("Unable to find teacher number $teacherid for class \"$class\" ($sectionid)");
				continue;
			}

			$sponsor = new User($sponsordn);
			
			$sponsordn = LDAP::get_user_dn($sponsor->username);

			$newclass = array(
				'objectClass' => 'tjhsstClass',
				'tjhsstClassId' => $classid,
				'tjhsstSectionId' => $sectionid,
				'courselength' => $courselen=='YR'?4:($courselen[0]=='S'?2:1),
				'quarternumber' => $courselen=='YR'?array(1,2,3,4):($courselen[0]=='S'?($semesterno==1?array(1,2):array(3,4)):$semesterno),
				'roomNumber' => $room,
				'year' => User::get_gradyear(12),
				'cn' => $class,
				'sponsorDn' => $sponsordn,
				'classPeriod' => range((int)$periodstart,(int)$periodend),
			);
			$ldap->add(LDAP::get_schedule_dn($sectionid),$newclass);
		}
		fclose($file);

		$this->messages[] = "$numclasses classes read from SASI dump file";

		/*
		** Set up student <=> course mappings
		*/
		$students = [];
		d("Reading from schedule file: $schedulefile...", 6);
		$studentcoursefile = @fopen($schedulefile,'r');
		// fgetcsv reads a line and parses as CSV
		// args past are length=0 (autodetect max line length)
		// and delimiter="\t" for tab-delimiters instead of commas
		while (list($studentid, $last, $first, $middle, $period, $sectionone, $courseid, $coursename, $teacherid, $teachername, $term, $room) = fgetcsv($studentcoursefile, 0, "\t")) {
			$class = $ldap->search(LDAP::get_schedule_dn(),"tjhsstSectionId=$sectionone",array('tjhsstSectionId'))->fetch_single_value();
			if (!$class) {
				$I2_LOG->log_file('Invalid SectionID '.$sectionone.' for studentid '.$studentid);
				continue;
			}

			$classdn = LDAP::get_schedule_dn($class);

			if (!isset($students[$studentid])) {
				$students[$studentid] = [];
			}
			$students[$studentid][] = $classdn;
		}
		fclose($studentcoursefile);

		foreach ($students as $studentid=>$classdns) {
			$studentdn = LDAP::get_user_dn($studentid);
			//warn("studentid: $studentid; dn: $studentdn");
			$ldap->modify_val($studentdn,'enrolledClass',$classdns);
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
		$boxes = array(
			'news'=>'News',
			'eighth'=>'Eighth Period',
			'mail'=>'Your Mail',
			'filecenter'=>'Your Files',
			'birthdays'=>'Birthdays',
			'studentdirectory'=>'Student Directory',
			'links'=>'Useful Links',
			'scratchpad'=>'ScratchPad'
		);
		$desiredboxes = [];
		foreach ($boxes as $desiredbox=>$name) {
			$boxnum = $I2_SQL->query('SELECT boxid FROM intrabox WHERE name=%s',$desiredbox)->fetch_single_value();
			$desiredboxes[$boxnum] = $name;
		}
		$this->boxids = $desiredboxes;
	}

}

?>
