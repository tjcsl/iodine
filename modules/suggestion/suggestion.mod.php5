<?php
/**
* A module that inputs and emails suggestions.
* @package modules
* @subpackage suggestion
*/

class Suggestion implements Module {

	private $template_args = array();
	private $text;

	public function init_box() {
		return FALSE;
	}

	public function display_box($disp) {
		return FALSE;
	}	
	
	public function init_pane() {
		global $I2_USER;
		
		if (!(isset($_REQUEST['submit_form']) && isset($_REQUEST['submit_box']))) {
			return 'Suggestion';
		}
		
		$mesg = $_REQUEST['submit_box'];
		if ($mesg == "" || $mesg == " ") { //may need a whitespace regex
			return 'Suggestion';
		}

		$to = i2config_get('sendto', 'jboning@gmail.com', 'suggestion'); 
		$subj = "Suggestion from {$I2_USER->fullname}";
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$mesg .= "\r\n\r\n $browser";
		$headers = "From: {$I2_USER->get_tjmail()}\r\n";
		$headers .= "Reply-To: $to\r\n";
		$headers .= "Return-Path: $to\r\n";

		if (!mail($to,$subj,$mesg,$headers)) {
			$this->template_args['message'] = 'There was a problem submitting your suggestion. Please contact the Intranet Developers for assistance.';
			//warn("Error sending your suggestion.");
		} else {
			$this->template_args['message'] = 'Your suggestion has been submitted. Thank you for your input.';
			//d("Message sent sucessfully.");
		}
		
		return 'Suggestion';
	}
	
	function display_pane($disp) { 
		if(!(isset($this->template_args['message']))) {
			$disp->disp('suggestion_pane.tpl', $this->template_args);
			return;
		}
		$disp->smarty_assign('message',$this->template_args['message']);
		$disp->disp('suggestion_result.tpl', $this->template_args);	
	}

	function get_name() {
		return 'Suggestion';
	}

	function is_intrabox() {
		return 'Suggestion';
	}
}
?>
