<?php
class dataimport implements Module {

	private $oldsql;
	private $usertable;
	private $teachertable;
	private $args = array();
	private $admin_pass;
	private $num;

	public function __autoconstruct() {
		//TODO: ?
		//$this->oldsql = mysql_connect('intranet');
		/*
		** Set this high to avoid interfering with teachers' SASI numbers
		*/
		$this->num = 10000;
	}

	/**
	* @todo FINISH THIS METHOD
	*/
	private function import_teacher_data($filename,$teachersfiletwo) {

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
		$last_to_people = array();

		while ($line = fgets($file)) {
			list($id,$lastname,$firstname) = explode('","',$line);
			if ($lastname == 'NA' || $firstname === '') {
				continue;
			}
			/*
			** Strip remaining quotes
			*/
			$id = substr($id,1);
			$firstname = ucFirst(strtolower(substr($firstname,-1)));
			$lastname = ucFirst(strtolower($lastname));

			if (!isset($last_to_people[$lastname])) {
				$last_to_people[$lastname] = array($id);
			} else {
				$last_to_people[$lastname][] = $fname;
			}
			
			$this->teachertable[$id] = array(
					'lname' => str_replace('\'','\\\'',$lastname),
					'fname' => $firstname,
					'uid' => str_replace('\'','\\\'',$id)
				);
		}
		
		d("$numlines teachers imported.",6);

		fclose($file);

		/*
		** Second pass through staff.# file
		*/

		$file = @fopen($filename,'r');

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

			if (!isSet($last_to_people[$lastname])) {
				//TODO: how to handle this?  Should we try weird name-guessing games?
				throw new I2Exception("Last name \"$lastname\" not recognized");
			} else {
				$choices = $last_to_people[$lastname];
				if (count($choices) == 1) {
					/*
					** We have exactly one match.  We've got our teacher.
					*/
					$this->teachertable[$choices[0]]['username'] = $username;
				} else {
					//TODO: handle
					throw new I2Exception("Multiple choices for last name \"$lastname\"");
				}
			}

		}

		$ldap = LDAP::get_admin_bind($this->admin_pass);

		foreach ($this->teachertable as $id=>$teacher) {
			$this->create_teacher($teacher,$ldap);
		}

	}

	/** 
	* Import student data from a dump file into $datatable;
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
		$newteach['startpage'] = 'news';
		$newteach['header'] = 'TRUE';
		$newteach['chrome'] = 'TRUE';
		$dn = "iodineUid={$newteach['iodineUid']},ou=people";

		//FIXME: check if iodineUidNumber exists and update previous entry if so
		
		d("Creating teacher \"{$newteach['iodineUid']}\"...",5);
		$ldap->add($dn,$newteach);
	}

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
		$usernew['telephoneNumber'] = $user['phone_home'];
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
		$usernew['startpage'] = 'news';
		$usernew['header'] = 'TRUE';
		$usernew['iodineUidNumber'] = $this->num;
		$usernew['chrome'] = 'TRUE';
		$this->num = $this->num + 1;
		$dn = "iodineUid={$usernew['iodineUid']},ou=people";

		//FIXME: check if iodineUidNumber or tjhsstStudentId exists
		
		d("Creating user \"{$usernew['iodineUid']}\"...",5);
		$ldap->add($dn,$usernew);
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
		if (isSet($_SESSION['ldap_admin_pass'])) {
			$this->admin_pass = $_SESSION['ldap_admin_pass'];
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'unset_pass') {
			unset($_SESSION['ldap_admin_pass']);
			unset($this->admin_pass);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'userdata' && isSet($_REQUEST['userfile'])) {
			$this->import_student_data($_REQUEST['userfile']);
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'teacherdata' && isSet($_REQUEST['teacherfile']) && isSet($_REQUEST['stafffile'])) {
			$this->import_teacher_data($_REQUEST['teacherfile'],$_REQUEST['staffile']);
		}
		return array(TRUE,'Import Legacy Data');
	}

	public function display_pane($disp) {
		$disp->disp('dataimport_pane.tpl',array('userdata' => $this->usertable, 'admin_pass' => isSet($this->admin_pass)?TRUE:FALSE));
	}
}
?>
