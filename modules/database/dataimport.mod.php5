<?php
class dataimport implements Module {

	private $oldsql;
	private $usertable;
	private $args = array();
	private $admin_pass;
	private $num;

	public function __autoconstruct() {
		//TODO: ?
		//$this->oldsql = mysql_connect('intranet');
		$this->num = 0;
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
		return array(TRUE,'Import Legacy Data');
	}

	public function display_pane($disp) {
		$disp->disp('dataimport_pane.tpl',array('userdata' => $this->usertable, 'admin_pass' => isSet($this->admin_pass)?TRUE:FALSE));
	}
}
?>
