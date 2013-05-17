<?php
/**
* A module that inputs and emails suggestions.
* @package modules
* @subpackage suggestion
*/

class Suggestion implements Module {

	private $template_args = [];

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	public function init_box() {
		return FALSE;
	}

	public function display_box($disp) {
		return FALSE;
	}	
	
	public function init_pane() {
		global $I2_USER;
		
		$usermail = $I2_USER->mail;
		if (is_array($usermail)) {
			$usermail = $usermail[0];
		}
		$this->template_args['usermail'] = $usermail;
		$this->template_args['sendchoices'] = array(array('name'=>'intranet','address'=>i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion')),array('name'=>'SGA','address'=>'sga.tjhsst@gmail.com'));

		if (!(isset($_REQUEST['submit_form']) && isset($_REQUEST['submit_box']))) {
			return 'Suggestion';
		}
		
		$mesg = $_REQUEST['submit_box'];
		if ($mesg == "" || $mesg == " ") { //may need a whitespace regex
			return 'Suggestion';
		}

		$to = i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion');
		switch ($_REQUEST['sendchoice']) {
			case 'SGA':
				$to='sga.tjhsst@gmail.com';
				$mesg .= "\r\n\r\nSent through Intranet.";
				break;
			case 'intranet':
			default:
				$to=i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion');
				// Intranet dev gets more info for debugging.
				$browser = $_SERVER['HTTP_USER_AGENT'];
				$mesg .= "\r\n\r\n Username: $I2_USER->username UID: $I2_USER->uid \r\n $browser";
				break;
		}
		$subj = "Suggestion from {$I2_USER->fullname}";
		$headers = "From: $usermail\r\n";
		$headers .= "Reply-To: $usermail\r\n";
		$headers .= "Return-Path: $to\r\n";

		$this->template_args['mailed'] = mail($to,$subj,$mesg,$headers);
		return 'Suggestion';
	}
	
	function display_pane($disp) { 
		$disp->disp('suggestion_pane.tpl', $this->template_args);
	}

	function get_name() {
		return 'Suggestion';
	}

	function is_intrabox() {
		return 'Suggestion';
	}
}
?>
