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
	private $cache_file;

	/**
	* The Mail class constructor.
	*/
	function __construct() {
		$cache_dir = i2config_get('cache_dir','/var/cache/iodine/','core') . 'mail/';

		if(!is_dir($cache_dir)) {
			mkdir($cache_dir, 0700, TRUE);
		}

		$this->cache_file = $cache_dir . session_id();

		$timeout = i2config_get('imap_timeout','','mail');
		if($timeout) {
			d('Setting IMAP timeout to '.$timeout,8);
			foreach(array(1,2,3,4) as $i) {
				imap_timeout($i, $timeout);
			}
		}
	}
	
	function init_pane() {
		global $I2_ARGS;
		
		if(!is_array($this->messages)) {
			if(!self::download_msgs()) {
				$this->pane_args['err'] = TRUE;
				return 'TJ Mail: Error in retrieving messages';
			}
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
			if(!self::download_msgs()) {
				$this->box_args['err'] = TRUE;
				return 'Mail -- Error';
			}
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
		global $I2_AUTH;
		if( ! $I2_AUTH->get_user_password()) {
			return FALSE;
		}

		if(($this->messages = self::get_cache()) !== FALSE) {
			d('Using IMAP header cache',7);
			$this->nmsgs = count($this->messages);
			return TRUE;
		}
		
		$path = i2config_get('imap_path','{mail.tjhsst.edu:993/imap/ssl/novalidate-cert}INBOX', 'mail');
		d("Not using IMAP cache, downloading messages from $path",6);
		$this->connection = imap_open($path, $_SESSION['i2_username'], $I2_AUTH->get_user_password());
		if (! $this->connection) {
			return FALSE;
		}

		$this->nmsgs = imap_num_msg($this->connection);

		$sorted = imap_sort($this->connection, SORTDATE, 1);
		$this->messages = imap_fetch_overview($this->connection, implode(',',$sorted));

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

		self::store_cache($this->messages);

		return TRUE;
	}

	private function get_cache() {
		if(!file_exists($this->cache_file)) {
			d('Cache file does not exist',6);
			return FALSE;
		}
		
		if(time() - filemtime($this->cache_file) > i2config_get('imap_cache_time',300,'mail')) {
			d('Cache file is too stale',6);
			return FALSE;
		}

		$ret = unserialize(file_get_contents($this->cache_file));
		return $ret;
	}

	private function store_cache($messages) {
		$data = serialize($messages);
		
		$fh = fopen($this->cache_file, 'w');
		fwrite($fh, $data);
		fclose($fh);
	}
}
?>
