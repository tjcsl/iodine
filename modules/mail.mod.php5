<?php
/**
* Just contains the definition for the class {@link Mail}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Mail
* @filesource
*/

/**
* The module that shows you what email you have on your TJ account.
* @package modules
* @subpackage Mail
*/
class Mail implements Module {
	
	private $box_args = array();

	private $messages;
	private $nmsgs;

	/**
	* The Mail class constructor.
	*/
	function __construct() {
	}
	
	function init_pane() {
		return array("Mail", "Mail");
	}
	
	function display_pane($display) {
		$display->disp('mail_pane.tpl',array());
	}
	
	function init_box() {
		if (!is_array($this->messages)) {
			self::download_msgs();
		}
		$this->box_args['messages'] = array(); 
		$this->box_args['nmsgs'] = $this->nmsgs;
		
		$nmsgs_to_show = ($this->nmsgs < 5 ? $this->nmsgs : 5);
		$this->box_args['nmsgs_to_show'] = $nmsgs_to_show;

		for($n = $this->nmsgs; $n > $this->nmsgs - $nmsgs_to_show; $n--) {
			$message = array();

			$message['from'] = $this->messages[$n - 1]['from'][0]->personal;
			if (! $message['from']) {
				$message['from'] = $this->messages[$n - 1]['from'][0]->mailbox . '@' . $this->messages[$n - 1]['from'][0]->host;
			}
			$message['subject'] = $this->messages[$n - 1]['subject'];
			if (strlen($message['subject']) > 20) {
				$message['subject'] = substr($message['subject'], 0, 20)."...";
			}
			$message['date'] = $this->messages[$n - 1]['date'];

			$this->box_args['messages'][] = $message;
		}
		return "Mail";
	}

	function display_box($display) {
		$display->disp('mail_box.tpl',$this->box_args);
	}

	function get_name() {
		return "Mail";
	}

	function is_intrabox() {
		return true;
	}

	private function download_msgs() {
		$connection = imap_open("{mail.tjhsst.edu:993/imap/ssl/novalidate-cert}INBOX", $_SESSION['i2_username'], $_SESSION['i2_password']);
		if (! $connection) {
			throw new I2Exception("Could not connect to mail server!");
		}

		$this->nmsgs = imap_num_msg($connection);

		$this->messages = array();

		for($n = 1; $n <= $this->nmsgs; $n++) {
			$message = array();

			$headers = imap_headerinfo($connection, $n);
			$message['from'] = $headers->from;
			$message['subject'] = $headers->subject;
			$message['date'] = date("d M Y", strtotime($headers->date));
			$message['full_date'] = $headers->date;
			$message['body'] = imap_body($connection, $n);

			$this->messages[] = $message;
		}
	}

}

?>
