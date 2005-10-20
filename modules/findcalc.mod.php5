<?php
/**
* Just contains the definition for the class {@link Findcalc}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Findcalc
* @filesource
*/

/**
* The module that keeps TJ students happy.
* @package modules
* @subpackage Findcalc
*/
class Findcalc implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template = "findcalc_pane.tpl";

	/**
	* Template arguments for the specified action
	*/
	private $message;
	private $type;
	private $number;
	private $template_args = array();

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		
		global $I2_USER, $I2_ARGS, $I2_SQL;

		if( isset($_REQUEST['calc_form']) ) {
			//form submitted
			$this->type=$_REQUEST['calc_form'];
			if ($_REQUEST['calc_form']=="sn")
			{
				if ($_REQUEST['number']==""||!is_numeric($_REQUEST['number']))
				{
					$this->message = "You didn't specify a valid serial number!";
				}
				else{$this->number=$_REQUEST['number'];}
			}
			else if ($_REQUEST['calc_form']=="id")
			{
				if($_REQUEST['number']=="")
				{
					$this->message = "You didn't specify a valid calculator ID!";
				}
				else{$this->number=$_REQUEST['number'];}
			}
		}
		return array('Identify Lost Calculator');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_SQL, $I2_USER;
		$this->number="%".$this->number."%";
		$username="";
		$result="";
		$calcs="";
		if($this->type=="sn")
		{
			$calcs = flatten($I2_SQL->query('SELECT uid,calcsn,calcid FROM calculators WHERE calcsn like %s',$this->number)->fetch_all_arrays(MYSQL_ASSOC));
		}else if($this->type=="id"){
			$calcs = flatten($I2_SQL->query('SELECT uid,calcsn,calcid FROM calculators WHERE calcid like %s',$this->number)->fetch_all_arrays(MYSQL_ASSOC));
		}
		if(count($calcs)==0 && $this->type!="" && $this->message=="")
		{
			$this->message="Calculator not found.";
		}
		else if($this->message=="" && $calcs!="")
		{
			$username = flatten($I2_SQL->query('SELECT fname,mname,lname FROM user WHERE uid=%s', $calcs["uid"])->fetch_all_arrays(MYSQL_ASSOC));
		}
		$calcs = array_merge($calcs, $username);
		if($calcs!="")
		{
			$result=$calcs["fname"]." ".$calcs["mname"]." ".$calcs["lname"]." ".$calcs["calcsn"]." (".$calcs["calcid"].")<br />";
		}
		$display->disp($this->template, array( 'message' => $this->message ,
							'result' => $result));
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Identify Lost Calculator";
	}
}

?>
