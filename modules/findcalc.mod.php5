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
				if ($_REQUEST['number']==""||strlen($_REQUEST['number'])!=10||!is_numeric($_REQUEST['number']))
				{
					$this->message = "You didn't specify a valid serial number!";
				}
				else{$this->number=$_REQUEST['number'];}
			}
			else if ($_REQUEST['calc_form']=="id")
			{
				if($_REQUEST['number']==""||strlen($_REQUEST['number'])!=14)
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
		$username="";
		if($this->type=="sn")
		{
			$calcs = flatten($I2_SQL->query('SELECT uid,calcsn,calcid FROM calculators WHERE calcsn=%s',$this->number)->fetch_all_arrays(MYSQL_ASSOC));
		}else if($this->type=="id"){
			$calcs = flatten($I2_SQL->query('SELECT uid,calcsn,calcid FROM calculators WHERE calcid=%s',$this->number)->fetch_all_arrays(MYSQL_ASSOC));
		}
		if(count($calcs)==0 && $this->type!="" && $this->message=="")
		{
			$this->message="Calculator not found.";
		}
		else if($this->message=="")
		{
			$username = $I2_SQL->query('SELECT fname,mname,lname FROM user WHERE uid=%s', $calcs["uid"])->fetch_array(MYSQL_ASSOC);
		}
		$display->disp($this->template, array( 'message' => $this->message ,
							'calcs' => $calcs,
							'username' => $username));
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
