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
	private $box_messages;
	private $nmsgs;
	private $cache_file;
	private static $msgno_map;

	/**
	* The Mail class constructor.
	*/
	function __construct() {
		global $I2_USER;

		$cache_dir = i2config_get('cache_dir','/var/cache/iodine/','core') . 'mail/';

		if(!is_dir($cache_dir)) {
			mkdir($cache_dir, 0700, TRUE);
		}

		$this->cache_file = $cache_dir . hash('md5', $I2_USER->iodineUid);

		$timeout = i2config_get('imap_timeout','','mail');
		if($timeout) {
			d('Setting IMAP timeout to '.$timeout,8);
			foreach(array(1,2,3,4) as $i) {
				imap_timeout($i, $timeout);
			}
		}
	}
	
	function init_pane() {
		global $I2_ARGS,$I2_USER;

		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clear') {
			// Clear the cache
			$this->clear_cache();
			redirect(implode('/',array_slice($I2_ARGS,2)));
		}
		return FALSE;
/*
		
		$max_msgs = i2config_get('max_pane_msgs', 20, 'mail');

		$offset = (isset($I2_ARGS[1]) && $I2_ARGS[1] > 0) ? $I2_ARGS[1] : 0;

		if(!is_array($this->messages)) {
			if(($this->messages = self::download_msgs($offset, $max_msgs)) === FALSE) {
				$this->pane_args['err'] = TRUE;
				return 'TJ Mail: Error in retrieving messages';
			}
		}

		if($offset >= $this->nmsgs) {
			$offset = 0;
		}

		// If we downloaded the first messages, set the box messages,
		// so the intrabox doesn't redundantly download them
		if ($offset == 0) {
			$this->box_messages = array_slice($this->messages, 0, $I2_USER->mailentries);
		}

		$this->pane_args['messages'] = &$this->messages;
		$this->pane_args['goleft'] = $offset > 0;
		$this->pane_args['goright'] = $offset + $max_msgs < $this->nmsgs;
		$this->pane_args['nmsgs'] = $this->nmsgs;
		$this->pane_args['offset'] = $offset;
		return "TJ Mail: You have {$this->nmsgs} messages";
*/
	}
	
	function display_pane($display) {
//		$display->disp('mail_pane.tpl', $this->pane_args);
	}
	
	function init_box() {
		// TEMPORARY HACK: because mail happens to be dead right now,
		// we don't even want to try loading it.
		return FALSE;

		global $I2_USER;
		// Mailboxes are students only
		if ($I2_USER->is_group_member('grade_staff')) {
			return FALSE;
		}
		$max_msgs = $I2_USER->mailentries;
		if ($max_msgs === FALSE || $max_msgs === NULL) {
			$max_msgs = i2config_get('max_box_msgs', 5, 'mail');
		}
		if ($max_msgs < 0) {
			$max_msgs = 0;
		}

		if (!is_array($this->box_messages)) {
			if(($cache = self::get_cache()) !== FALSE) {
				// Cache exists and is valid
				d('Using IMAP header cache', 7);
				$this->nmsgs = $cache[0];
				$this->box_messages = $cache[1];
				$this->nunseen = $cache[2];
				
			} elseif(($this->box_messages = self::download_msgs(0, $max_msgs)) === FALSE) {
				// Downloading messages failed
				$this->box_args['err'] = TRUE;
				return 'Mail -- Error';
			} else {
				// Downloading messages worked, store them in cache
				self::store_cache($this->nmsgs, $this->box_messages, $this->nunseen);
			}
		}

		$this->box_args['messages'] = &$this->box_messages;
		$this->box_args['readmail_url'] = $GLOBALS['I2_ROOT'] . i2config_get('webmail_module', 'squirrelmail', 'mail');
		return "Mail: {$this->nmsgs} message". ($this->nmsgs != 1 ? 's' : '') . ", {$this->nunseen} unread";
	}

	function display_box($display) {
		$display->disp('mail_box.tpl',$this->box_args);
	}

	function get_name() {
		return 'Mail';
	}

	private function download_msgs($offset, $length) {
		global $I2_AUTH;
		if( ! $I2_AUTH->get_user_password()) {
			d('User password not available! Cannot download mesasges',1);
			return FALSE;
		}
		
		$path = i2config_get('imap_path','{mail.tjhsst.edu:993/imap/ssl/novalidate-cert}INBOX', 'mail');
		d("Not using IMAP cache, downloading messages from $path",6);
		$this->connection = imap_open($path, $_SESSION['i2_username'], $I2_AUTH->get_user_password());
		if (! $this->connection) {
			d('IMAP connection failed: ' . imap_last_error(), 3);
			return FALSE;
		}

		$this->nmsgs = imap_num_msg($this->connection);
		$this->nunseen = imap_status($this->connection, $path, SA_UNSEEN)->unseen;

		if($offset >= $this->nmsgs) {
			$offset = 0;
		}

		//$sorted = array_slice(imap_sort($this->connection, SORTARRIVAL, 1), $offset, $length);
		# We don't use the above because it's slower.
		$sorted = array();
		$endindex = $this->nmsgs-$length < 0 ? 0 : $this->nmsgs-$length;
		for( $i = $this->nmsgs; $i > $endindex; $i--)
			$sorted[] = $i;
		$messages = imap_fetch_overview($this->connection, implode(',',$sorted));

		if (count($sorted) == 0) {
				  self::$msgno_map = array();
		} else {
			// Used for the usort() call below; swaps the keys/values in $sorted
			self::$msgno_map = array_combine(array_values($sorted), array_keys($sorted));
		}

		foreach($messages as $i=>$message) {
			$message->unread = $message->recent || !$message->seen;

			if(!isset($message->subject)) {
				$message->subject = '(no subject)';
			}
			if(!isset($message->from)) {
				$message->from = '(no name)';
			}

			if(strlen($message->subject) > 31) {
				$message->short_subject = substr($message->subject, 0, 30);
				$message->short_subject .= '...';
			}
			else {
				$message->short_subject = $message->subject;
			}

			if(strlen($message->from) > 16) {
				$message->short_from = substr($message->from, 0, 15);
				$message->short_from .= '...';
			}
			else {
				$message->short_from = $message->from;
			}
		}

		usort($messages, array($this, 'cmp_message'));

		// Make sure these get deleted when the user logs out - so you don't get other people's cached mail.

		$_SESSION['logout_funcs'][] = array(array($this,'clear_cache'),array());

		return $messages;
	}

	private function cmp_message($msg1, $msg2) {
		return ( self::$msgno_map[$msg1->msgno] < self::$msgno_map[$msg2->msgno] ) ? -1 : 1;
	}

	private function clear_cache() {
		unlink($this->cache_file);
	}

	private function get_cache() {
		global $I2_USER;
		if(!file_exists($this->cache_file)) {
			d('Cache file does not exist',6);
			return FALSE;
		}
		
		if(time() - filemtime($this->cache_file) > i2config_get('imap_cache_time',300,'mail')) {
			d('Cache file is too stale',6);
			$this->clear_cache();
			return FALSE;
		}

		$ret = unserialize(file_get_contents($this->cache_file));

		// Checks the format of the cache file it must contain
		// serialized data that represents:
		// array(
		//	$nmsgs,
		//	array(
		//		$message1,
		//		$message2,
		//		...
		//	)
		// )
		if( !(	is_array($ret) &&
				count($ret) == 4 &&
				is_int($ret[0]) &&
				is_array($ret[1]) &&
				(count($ret[1]) == 0 || is_object($ret[1][0])) )) {
			d('Invalid mail cache file format', 5);
			unlink($this->cache_file);
			return FALSE;
		}

		if ($ret[3] != $I2_USER->uid) {
			d('Cache is for another user',5);
			unlink($this->cache_file);
			return FALSE;
		}
		
		return $ret;
	}

	private function store_cache($nmsgs, $messages, $nunseen) {
		global $I2_USER;
		$data = serialize(array($nmsgs,$messages,$nunseen,$I2_USER->uid));
		
		$fh = fopen($this->cache_file, 'w');
		fwrite($fh, $data);
		fclose($fh);
	}
}
?>
