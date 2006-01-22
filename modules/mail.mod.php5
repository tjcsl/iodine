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

	private $pane_args = array();
	
	private $pane_tpl;

	private $connection;
	private $messages;
	private $nmsgs;

	/**
	* The Mail class constructor.
	*/
	function __construct() {
	}
	
	function init_pane() {
		if (!is_array($this->messages)) {
			self::download_msgs();
		}
		
		global $I2_ARGS;

		if (!isset($I2_ARGS[1])) {
			$I2_ARGS[1] = NULL;
		}
		switch($I2_ARGS[1]) {
			case 'message':
				$this->pane_tpl = 'view_message.tpl';

				$msgnum = $I2_ARGS[2];

				$this->pane_args['date'] = $this->messages[$msgnum - 1]['date_time'];
				$this->pane_args['from'] = $this->messages[$msgnum - 1]['long_from'];
				$this->pane_args['to_array'] = $this->messages[$msgnum - 1]['to_array'];
				$this->pane_args['subject'] = $this->messages[$msgnum - 1]['subject'];

				$structure = imap_fetchstructure($this->connection, $msgnum);
				if ($structure->type == 1) { // Multi-part message
					$this->pane_args['body'] = imap_fetchbody($this->connection, $msgnum, "1.2"); // Get text/html
					if (! $this->pane_args['body']) {
						$this->pane_args['body'] = imap_fetchbody($this->connection, $msgnum, "1"); // if text/html didn't work, get text
					}
				}
				else { // Single-part message
					$this->pane_args['body'] = imap_body($this->connection, $msgnum);
				}
				return;
			default:
				$this->pane_tpl = 'mail_pane.tpl';

				$this->pane_args['messages'] = array(); 
				$this->pane_args['nmsgs'] = $this->nmsgs;

				for($n = $this->nmsgs; $n > 0; $n--) {
					$message = array();

					$message['number'] = $this->messages[$n - 1]['number'];
					$message['unread'] = $this->messages[$n - 1]['unread'];
					$message['from'] = $this->messages[$n - 1]['from'];
					$message['subject'] = $this->messages[$n - 1]['subject'];
					$message['date'] = $this->messages[$n - 1]['date'];

					$this->pane_args['messages'][] = $message;
				}
				return;
		}
		return array("Mail", "Mail");
	}
	
	function display_pane($display) {
		$display->disp($this->pane_tpl,$this->pane_args);
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

			$message['unread'] = $this->messages[$n - 1]['unread'];
			$message['from'] = $this->messages[$n - 1]['from'];

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
		$this->connection = imap_open("{mail.tjhsst.edu:993/imap/ssl/novalidate-cert}INBOX", $_SESSION['i2_username'], Auth::get_user_password());
		if (! $this->connection) {
			throw new I2Exception("Could not connect to mail server!");
		}

		$this->nmsgs = imap_num_msg($this->connection);

		$this->messages = array();

		for($n = 1; $n <= $this->nmsgs; $n++) {
			$message = array();

			$message['number'] = $n;

			$headers = imap_headerinfo($this->connection, $n);

			if (! is_object($headers)) {
				$message['from'] = $message['long_from'] = $message['to'] = $message['subject'] = $message['date'] = $message['date_time'] = '[unaccessible]';
				$message['unread'] = false;
				$this->messages[] = $message;
				continue;
			}
				
			if (isset($headers->from[0]->personal)) {
				$message['from'] = $headers->from[0]->personal;
				$message['long_from'] = $headers->from[0]->personal . ' &lt;' . $headers->from[0]->mailbox . '@' . $headers->from[0]->host . '&gt;';
			}
			else {
				$message['from'] = $message['long_from'] = $headers->from[0]->mailbox . "@" . $headers->from[0]->host;
			}

			$message['to'] = array();
			foreach($headers->to as $recipient) {
				if (isset($recipient->personal)) {
					$message['to_array'][] = $recipient->personal . '&lt;' . $recipient->mailbox . '@' . $recipient->host . '&gt;';
				}
				else {
					$message['to_array'][] = $recipient->mailbox . '@' . $recipient->host;
				}
			}

			if (isset($headers->subject)) {
				$message['subject'] = $headers->subject;
			}
			else {
				$message['subject'] = '[no subject]';
			}

			if (isset($headers->date)) {
				$message['date'] = date("d M Y", strtotime($headers->date));
				$message['date_time'] = date("l, F dS, Y @ g:i A", strtotime($headers->date));
			}
			else {
				$message['date'] = $message['date_time'] = '[no date]';
			}

			$message['unread'] = $headers->Unseen == 'U' || $headers->Recent == 'N';

			$this->messages[] = $message;
		}
	}

}

?>
