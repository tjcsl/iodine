<?php
/**
* A module that inputs and emails suggestions.
* @package modules
* @subpackage suggestion
*/

class Suggestion implements Module {

	private $template_args = array();

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

		if (!(isset($_REQUEST['submit_form']) && isset($_REQUEST['submit_box']))) {
			return 'Suggestion';
		}
		
		$mesg = $_REQUEST['submit_box'];
		if ($mesg == "" || $mesg == " ") { //may need a whitespace regex
			return 'Suggestion';
		}

		$to = i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion'); 
		$subj = "Suggestion from {$I2_USER->fullname}";
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$mesg .= "\r\n\r\n $browser";
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
