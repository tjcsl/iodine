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
		global $I2_ARGS;
		
		if(!is_array($this->messages)) {
			self::download_msgs();
		}

		if(!isset($I2_ARGS[1])) {
			$I2_ARGS[1] = 0;
		}
		
		$this->pane_args['messages'] = &$this->messages;
		$this->pane_args['nmsgs'] = &$this->nmsgs;
		$this->pane_args['nmsgs_show'] = ($this->nmsgs < 20 ? $this->nmsgs : 20);
		$this->pane_args['offset'] = $I2_ARGS[1];
		return "TJ Mail: You have {$this->nmsgs} messages";
	}
	
	function display_pane($display) {
		$display->disp('mail_pane.tpl', $this->pane_args);
	}
	
	function init_box() {
		if (!is_array($this->messages)) {
			self::download_msgs();
		}
		$this->box_args['messages'] = &$this->messages;
		$this->box_args['nmsgs'] = $this->nmsgs;
		$this->box_args['nmsgs_show'] = ($this->nmsgs < 5 ? $this->nmsgs : 5);
		return 'Mail';
	}

	function display_box($display) {
		$display->disp('mail_box.tpl',$this->box_args);
	}

	function get_name() {
		return 'Mail';
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

		$this->messages = imap_fetch_overview($this->connection, "1:{$this->nmsgs}");

		foreach($this->messages as $message) {
			$message->unread = $message->recent || !$message->seen;

			if(strlen($message->subject) > 30) {
				$message->short_subject = substr($message->subject, 0, 30);
				$message->short_subject .= '...';
			}
			else {
				$message->short_subject = $message->subject;
			}

			if(strlen($message->from) > 15) {
				$message->short_from = substr($message->from, 0, 15);
				$message->short_from .= '...';
			}
			else {
				$message->short_from = $message->from;
			}
		}
	}

}

?>
