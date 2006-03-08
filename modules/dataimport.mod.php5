<?php
class dataimport implements Module {

	private $oldsql;
	private $datatable;
	private $args = array();

	public function __autoconstruct() {
		//TODO: ?
		//$this->oldsql = mysql_connect('intranet');
	}


	/** 
	* Import data from a dump file into $datatable;
	*/
	private function import_file_data($filename) {
	
		$file = @fopen($filename, "r");

		$line = null;

		$this->datatable = array();

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
			$this->datatable[] = array(
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
		}
	}

	/**
	* Update Iodine with the new data
	*/
	private function update_db() {
	}

	public function get_name() {
		return "dataimport";
	}

	public function init_box() {
		return FALSE;
	}

	public function display_box($disp) {
	}

	public function init_pane() {
		global $I2_ARGS;
		if (isSet($I2_ARGS['datafile'])) {
			$this->import_file_data($I2_ARGS['datafile']);
		}
		return array(TRUE,"Import Legacy Data");
	}

	public function display_pane($disp) {
		$disp->disp('dataimport_pane.tpl',array('data' => $this->datatable));
	}
}
?>
