<?php
/**
* Just contains the definition for the class {@link Eighth}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that keeps the eighth block office happy.
* @package modules
* @subpackage Eighth
*/
class Eighth implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* The title for the page
	*/
	private $title = '';

	/**
	* The help text for the page
	*/
	private $help_text = '';

	/**
	* Template for the specified action
	*/
	private $template = 'pane.tpl';

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

	/**
	* The user is an 8th-period admin
	*/
	private $admin = FALSE;

	/**
	* The operation a method is to perform
	*/
	private $op = '';

	/**
	* The arguments to a method;
	*/
	private $args = array();

	/**
	* The undo stack for internal use
	*/
	private static $undo;

	/**
	* The redo stack for internal use
	*/
	private static $redo;

	/**
	* Whether to track undo information at all
	*/
	private static $doundo = TRUE;

	/**
	* Whether to force an action
	*/
	private $force = FALSE;

	/**
	* The modules normal users should be able to access.
	*/
	private $safe_modules = array('vcp_schedule');

	/**
	* Array to mark the beginning of an undo or redo transaction.
	*/
	private static $start_undo = array(NULL, NULL, NULL, NULL, 'TRANSACTION_START');
	
	/**
	* Array to mark the end of an undo or redo transaction.
	*/
	private static $end_undo = array(NULL, NULL, NULL, NULL, 'TRANSACTION_END');
	
	/**
	* Maximum length of undo stack
	*/
	private static $max_undo = 10;
	
	/**
	* Value for start_date on login
	*/
	public static $default_start_date;
	
	function __construct() {
		self::$default_start_date = date('Y-m-d'); 
		self::init_undo();
	}

	private function view_undoredo_stack() {
		if (count(self::$undo) + count(self::$redo) == 0) {
			redirect('eighth');
		}
		$this->template = 'view_undoredo.tpl';
		$undo = str_replace("\n","<br/>",print_r(self::$undo,TRUE));
		$redo = str_replace("\n","<br/>",print_r(self::$redo,TRUE));
		$this->template_args['undo'] = $undo;
		$this->template_args['redo'] = $redo;
	}
	private static function fix_undoredo_stack($stack) {	
		if ($stack == NULL || count($stack) == 0) {
			return array();
		}
		$start = TRUE;
		$end = FALSE;
		$action = FALSE;

		$new_stack = array(self::$start_undo);
		foreach ($stack as $k) {
			//No reason for nulls
			if($k == NULL) {
				continue;
			} 
			//Found undo start, but already started
			else if($k == self::$start_undo && $start) {
				continue;
			} 
			//Found undo end, but already ended
			else if($k == self::$end_undo && $end) {
				continue;
			} 
			//Found undo end, but no action yet taken
			else if($k == self::$end_undo && !$action) {
				continue;
			} 
			//Probably should add k to new_stack
			else {
				//Start a new transaction
				if($end && !$start) {
					if ($k != self::$start_undo && $k != self::$end_undo) {
						array_push($new_stack, self::$start_undo);
						array_push($new_stack, $k);
						$start = TRUE;
						$end = FALSE;
						$action = TRUE;
					} else {
						continue;
					}
				}
				//Add an action or predefined ending
				else {
					array_push($new_stack, $k);
					if ($k == self::$end_undo) {
						$start = FALSE;
						$end = TRUE;
						$action = FALSE;
					} else {
						$start = FALSE;
						$end = FALSE;
						$action = TRUE;
					}
				}
			}
		}
		if (!$end) {
			array_push($new_stack, self::$end_undo);
		}
	}
	
	private static function undo($action) {
		return $undoandredo;
	}

	/**
	* Helper for undo() and redo(), which are rather similiar.
	*/
	private static function undoredo_exec($query,$args) {
		global $I2_SQL, $I2_LOG;
		//$I2_LOG->log_file('UNDO/REDO: "'.query.'" -> '.print_r($args,1),6);
		$I2_SQL->query_arr($query,$args);
	}

	public static function undo_transaction() {
		/*
		 * Uncomment if the stack gets dirty and throws errors.
		 * Better yet, figure out why the stack is getting dirty
		 * and fix that instead.
		 */	
		//self::fix_undoredo_stack(self::$undo);
		
		if (count(self::$undo) == 0) {
			return;
		}
		// Drop the last item if it is end_undo
		if(self::$undo[-1] == self::$end_undo)
			array_pop(self::$undo);
		
		$redo_queue = array(self::$end_undo);
		do {
			$action = array_pop(self::$undo);
			$redo_queue[] = $action;
			if($action != self::$start_undo && $action != self::$end_undo ) {
				self::undoredo_exec($action[0],$action[1]);
			}
		} while ($action != self::$start_undo);
		//self::$redo += array_reverse($redo_queue);
		foreach (array_reverse($redo_queue) as $k) {
			self::$redo[] = $k;
		}
	}

	public static function redo_transaction() {
		//self::fix_undoredo_stack(self::$redo);
		
		if (count(self::$redo) == 0) {
			return;
		}
		// Drop the last item if it is start_undo
		if(self::$redo[-1] == self::$start_undo)
			array_pop(self::$redo); 
		
		$undo_queue = array(self::$end_undo);
		do {
			$action = array_pop(self::$redo);
			$undo_queue[] = $action;
			if($action != self::$start_undo && $action != self::$end_undo) {
				self::undoredo_exec($action[2],$action[3]);
			}
		} while ($action != self::$start_undo);
		//self::$undo += array_reverse($undo_queue);
		foreach (array_reverse($undo_queue) as $k) {
			self::$undo[] = $k;
		}
	}

	private static function start_undo_transaction() {
		array_push(self::$undo, self::$start_undo);
	}
	
	private static function start_redo_transaction() {
		array_push(self::$redo, self::$start_undo);
	}
	
	private static function end_undo_transaction() {
		array_push(self::$undo, self::$end_undo);
	}

	private static function end_redo_transaction() {
		array_push(self::$redo, self::$end_undo);
	}

	public static function init_undo() {
		if (isSet($_SESSION['eighth_undo'])) {
			self::$undo = &$_SESSION['eighth_undo'];
		} elseif (!self::$undo) {
			self::$undo = array();
			$_SESSION['eighth_undo'] = array();
		}
		if (isSet($_SESSION['eighth_redo'])) {
			self::$redo = &$_SESSION['eighth_redo'];
		} elseif (!self::$redo) {
			self::$redo = array();
			$_SESSION['eighth_redo'] = array();
		}
		d('8th-period undo stack: '.count(self::$undo).' element(s), topped by '.self::get_undo_name(),7);
		foreach ((self::$undo) as $undo) {
			d('-- Undo Item: --'.print_r($undo,1),8);
		}
		d('8th-period redo stack: '.count(self::$redo).' element(s), topped by '.self::get_redo_name(),7);
		foreach ((self::$redo) as $redo) {
			d('-- Redo Item: --'.print_r($redo,1),8);
		}
	}

	public static function undo_off() {
		self::$doundo = FALSE;
	}
	
	/**
	* Trim the undo stack to $max_undo or less
	*/
	private static function trim_undo_stack() {
		while(count(array_keys(self::$undo, self::$end_undo)) >= self::$max_undo) {
			while(array_shift(self::$undo) != self::$end_undo);
		}	
	}

	/**
	* Register an undoable action with the eighth-period undo system.
	*
	*/
	public static function push_undoable($undoquery, $undoarr, $redoquery, $redoarr, $name='Unknown Action') {
		if (!self::$doundo || !is_array(self::$undo)) {
			return;
		}
		global $I2_LOG;
		$undo = array($redoquery,$redoarr,$undoquery,$undoarr,$name);
		//$I2_LOG->log_file('PUSH UNDO: '.print_r($undo,1));
		array_push(self::$undo,$undo);
		//array_push($_SESSION['eighth_undo'],$undo);
		
		self::trim_undo_stack();
		self::$redo = array();
	}

	private static function get_undoredo_name($stack) {
		foreach(array_reverse($stack) as $k) {
			if ($k != self::$start_undo && $k != self::$end_undo) {
				return $k[4];
			}
		}
		return FALSE;
	}

	public static function get_undo_name() {
		return self::get_undoredo_name(self::$undo);
	}
	
	public static function get_redo_name() {
		return self::get_undoredo_name(self::$redo);
	}

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
	* Initialize variables for CLIodine
	*/
	function init_cli() {
		return "eighth";
	}

	/**
	* Display a text version of the eighth period module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		global $I2_ARGS;
		$valid_commands = array("list","signup","old","archived");
		if(!isset($I2_ARGS[2]) || !in_array(strtolower($I2_ARGS[2]),$valid_commands) ) {
			return "<div>Usage: eighth list [block id]<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eighth signup [activity id] [block id]<br /><br />eighth is a command for accessing and modifying eighth periods.<br /><br />Commands:<br />&nbsp;&nbsp;&nbsp;list - list all activites for today, or whatever block is specified<br />&nbsp;&nbsp;&nbsp;signup - sign up for the selected activity on all available blocks today or on the specified block<br />&nbsp;&nbsp;&nbsp;show - print the details of an activity<br />&nbsp;&nbsp;&nbsp;favorite - change the favorite status of an activity<br /></div>\n";
		}
		switch (strtolower($I2_ARGS[2])) {
			case "list":
				echo "<div>\n";
				if( $this->stories === NULL) {
					$this->stories = Newsitem::get_all_items(false);
				}
				foreach($this->stories as $story) {
					if ((!$story->has_been_read()) && $story->readable()) {
						echo "&nbsp;&nbsp;&nbsp;$story->nid - $story->title<br />\n";
					}
				}
				echo "</div>\n";
				break;
			case "show":
				if( !isset($I2_ARGS[3]) ) {
					echo "<div>ID of article to read not specified.</div>\n";
					break;
				}
				$item = new Newsitem($I2_ARGS[3]);
				echo "<div>\n";
				echo "$item->nid - $item->title<br /><br />\n";
				echo "<div style='width:640px'>$item->text</div><br />\n";
				break;
			case "old":
				echo "<div>\n";
				if( $this->stories === NULL) {
					$this->stories = Newsitem::get_all_items(true);
				}
				foreach($this->stories as $story) {
					if (($archive || !$story->has_been_read()) && $story->readable()) {
						echo "&nbsp;&nbsp;&nbsp;$story->nid - $story->title<br />\n";
					}
				}
				echo "</div>\n";
				break;
			case "archived":
				echo "<div>\n";
				if( $this->stories === NULL) {
					$this->stories = Newsitem::get_all_items(true);
				}
				foreach($this->stories as $story) {
					echo "&nbsp;&nbsp;&nbsp;$story->nid - $story->title<br />\n";
				}
				echo "</div>\n";
				break;
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER, $I2_QUERY;
		$this->args = array();
		$this->admin = self::is_admin();
		$this->template_args['eighth_admin'] = $this->admin;
		
		//Every time a user logs in, set start_date to default
		if(! isSet($_SESSION['eighth']['start_date'])) {
			$_SESSION['eighth']['start_date'] = self::$default_start_date;
		}
		//This may be clobbered later in REQUEST (for vcp_schedule and chg_start)
		$this->args['start_date'] = $_SESSION['eighth']['start_date'];
		
		if(count($I2_ARGS) <= 1) {
			if (!$this->admin) {
				return FALSE;
			}
			$this->template = 'pane.tpl';
			$this->template_args['help'] = '<h2>8th Period Office Online Menu</h2>From here you can choose a number of operations to administrate the eighth period system.';
			return 'Eighth Period Office Online: Home';
		} else {
			$method = $I2_ARGS[1];
			$this->op = (count($I2_ARGS) >= 3 ? $I2_ARGS[2] : '');
			for($i = count($I2_ARGS) - 1; $i > 2; $i -= 2) {
				$this->args[$I2_ARGS[$i - 1]] = $I2_ARGS[$i];
			}
			// Let POST clobber args. This may break things.
			$this->args = array_merge($this->args, $_POST);
			
			// Add GET variables into the array - and let them clobber POST
			foreach($I2_QUERY as $k=>$v) {
				$this->args[$k]=$v;
			}
			if(isset($_SESSION['eighth'])) {
				$this->args += $_SESSION['eighth'];
			}

			//Be careful with this line
			if(method_exists($this, $method) && (in_array($method, $this->safe_modules) || $this->admin) || $I2_USER->is_group_member('grade_staff')) {
				$this->$method();
				$this->template_args['method'] = $method;
				$this->template_args['help'] = $this->help_text;
				if ($this->admin) {
					return "Eighth Period Office Online: {$this->title}";
				} else {
					if($this->template == 'pane.tpl') {
						return FALSE;
					}
					return "Eighth Period Online: {$this->title}";
				}
			} else {
				return array("Eighth Period Online: ERROR - SubModule Doesn't Exist");
			}
		}
		return array('Error', 'Error');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_ARGS, $I2_USER;
		if ($this->template == 'pane.tpl' && !$I2_USER->is_group_member('admin_eighth') && !$I2_USER->is_group_member('grade_staff')) {
			redirect();
		}
		$argstr = implode('/', array_slice($I2_ARGS,1));
		$this->template_args['argstr'] = $argstr;
		$this->template_args['last_undo'] = self::get_undo_name();
		$this->template_args['last_redo'] = self::get_redo_name();

		//For header.tpl and vcp_schedule
		if(! isSet($this->template_args['start_date'])) {
			$this->template_args['start_date'] = $this->args['start_date'];
		}
		
		$this->template_args['defaultaid'] = i2config_get('default_aid','999','eighth');
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		global $I2_USER;
		$date = EighthSchedule::get_next_date();
		$this->template_args['absent'] = count(EighthSchedule::get_absences($I2_USER->uid));
		$this->admin = self::is_admin();
		$this->template_args['eighth_admin'] = $this->admin;
		if($date) {
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($I2_USER->uid, $date, 1), FALSE);
		}
		else {
			$this->template_args['activities'] = array();
		}
		$dates = array($date => date('n/j/Y', @strtotime($date)), date('Y-m-d') => 'Today', date('Y-m-d', time() + 3600 * 24) => 'Tomorrow', '' => 'None Scheduled');
		return "8th Period: {$dates[$date]}";
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('box.tpl', $this->template_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'Eighth';
	}

	/**
	* Comparison function for sorting names
	*
	* @access private
	* @param User $user1 The first user object.
	* @param User $user2 The second user object.
	*/
	private function name_cmp($user1, $user2) {
		return strcasecmp($user1->fullname_comma, $user2->fullname_comma);
	}

	public static function is_admin() {
		global $I2_USER;
		return $I2_USER->is_group_member('admin_eighth');
	}

	public static function is_signup_admin() {
		global $I2_USER;
		return $I2_USER->is_group_member('admin_eighth_signup');
	}
	
	public static function check_admin() {
		if (!self::is_admin()) {
			throw new I2Exception('Attempted to perform an unauthorized 8th-period action!');
		}
	}

	/**
	* Sets up for displaying the block selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the block list.
	* @param date $start_date The date from which to show blocks.
	* @param int $daysf  The number of days forward to show blocks.
	*/
	private function setup_block_selection($add = FALSE, $field = NULL, $title = NULL, $start_date = NULL, $daysf = NULL) {
		if ($field === NULL) {
			$field = 'bid';
		}
		if ($title === NULL) {
			$title = 'Select a block:';
		}
		if ($start_date === NULL) {
			$start_date = $this->args['start_date'];
		}
		if ($daysf === NULL && isSet($this->args['daysforward'])) {
			$daysf = $this->args['daysforward'];
		} else {
			$daysf = 99999;
		}
		$blocks = EighthBlock::get_all_blocks($start_date, $daysf);
		$this->template = 'block_selection.tpl';
		$this->template_args['blocks'] = $blocks;
		if($add) {
			$this->template_args['add'] = TRUE;
		}
		$this->template_args['title'] = $title;
		$this->template_args['field'] = $field;
		$this->title = 'Select a Block';
		$this->help_text = 'Select a Block';
	}

	/**
	* Sets up for displaying the activity selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param int $blockid The block ID to show the activity list for, NULL
	* if you want the full list.
	* @param string $title The title for the activity list.
	*/
	private function setup_activity_selection($add = FALSE, $blockid = NULL, $restricted = FALSE, $field = 'aid', $title = 'Select an activity:', $addtitle = 'Add an activity:') {
		$activities = EighthActivity::get_all_activities($blockid, $restricted);
		$this->template = 'activity_selection.tpl';
		$this->template_args['activities'] = $activities;
		if($add) {
			$this->template_args['add'] = TRUE;
			$this->template_args['add_title'] = $addtitle;
			$this->template_args['add_aids'] = EighthActivity::get_unused_aids();
		}
		$this->template_args['title'] = $title;
		$this->template_args['field'] = $field;
		$this->title = 'Select an Activity';
		$this->help_text = 'Select an Activity';
	}

	/**
	* Sets up for displaying the group selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the group list.
	*/
	private function setup_group_selection($add = FALSE, $title = 'Select a group:', $lastgid = FALSE) {
		$groups = Group::get_all_groups('eighth');
		$this->template = 'group_selection.tpl';
		$this->template_args['groups'] = $groups;
		$this->template_args['lastgid'] = $lastgid;
		if($add) {
			$this->template_args['add'] = TRUE;
		}
		$this->template_args['title'] = $title;
		$this->title = 'Select a Group';
		$this->help_text = 'Select a Group';
	}

	/**
	* Sets up for displaying the room selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the room list.
	*/
	private function setup_room_selection($add = FALSE, $title = 'Select a room:', $rid = FALSE) {
		$rooms = EighthRoom::get_all_rooms();
		$this->template = 'room_selection.tpl';
		$this->template_args['rooms'] = $rooms;
		if ($rid) {
			$this->template_args['rid'] = $rid;
		}
		if($add) {
			$this->template_args['add'] = TRUE;
		}
		$this->template_args['title'] = $title;
		$this->title = 'Select a Room';
		$this->help_text = 'Select a Room';
	}

	/**
	* Sets up for displaying the sponsor selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the sponsor list.
	*/
	private function setup_sponsor_selection($add = FALSE, $title = 'Select a sponsor:') {
		$sponsors = EighthSponsor::get_all_sponsors();
		$this->template = 'sponsor_selection.tpl';
		$this->template_args['sponsors'] = $sponsors;
		if($add) {
			$this->template_args['add'] = TRUE;
		}
		$this->template_args['title'] = $title;
		$this->title = 'Select a Sponsor';
		$this->help_text = 'Select a Sponsor';
	}
	
	/**
	* Sets up for displaying the printing format selection screen.
	*
	* @access private
	* @param string $module The module that we are printing from.
	*/
	private function setup_format_selection($module, $title = '', $args = array(), $user = FALSE) {
		$this->template = 'format_selection.tpl';
		$this->template_args['module'] = $module;
		$this->template_args['title'] = $title;
		$this->template_args['user'] = $user;
		$this->template_args['args'] = "";
		$formats = array("pdf" => "PDF", "ps" => "PostScript", "dvi" => "DVI");
		if(!$user) {
			$formats = array("print" => "Print") + $formats + array("tex" => "LaTeX", "rtf" => "RTF");
		}	
		$this->template_args['formats'] = $formats;
		foreach($args as $key=>$value) {
			$this->template_args['args'] .= "/{$key}/{$value}";
		}
		$this->title = "Choose an Output Format for {$title}";
		if(!$user) {
			$this->help_text = "<span style=\"font-weight: bold; font-size: 125%;\">Choose an output format:</span><br /><br />\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"bold\">Print -</span> Print the data<br />\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"bold\">PDF -</span> Output as a PDF file<br />\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"bold\">PostScript -</span> Output as a PostScript file<br />\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"bold\">DVI -</span> Output as a DVI file<br />\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"bold\">LaTeX -</span> Output the raw LaTeX data";
		}
	}

	/**
	* Register a group of students for an activity
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function reg_group() {
		if($this->op == '') {	
			$this->setup_block_selection();
			$this->template_args['op'] = 'activity';
		}
		else if($this->op == 'activity') {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "group/bid/{$this->args['bid']}";
			return 'Select an activity';
		}
		else if($this->op == 'group') {
			$this->setup_group_selection();
			$this->template_args['op'] = "confirm/bid/{$this->args['bid']}/aid/{$this->args['aid']}";
		}
		else if($this->op == 'confirm') {
			$this->template = 'reg_group.tpl';
			$this->template_args['op'] = "commit/bid/{$this->args['bid']}/aid/{$this->args['aid']}/gid/{$this->args['gid']}";
			$this->template_args['group'] = new Group($this->args['gid']);
			$this->template_args['activity'] = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->title = 'Confirm Registering a Group of Students';
		}
		else if($this->op == 'commit') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$group = new Group($this->args['gid']);
			
			self::start_undo_transaction();
			$activity->add_members($group->members, TRUE);
			self::end_undo_transaction();
			
			redirect('eighth');
		}
	}

	/**
	* Clears the undo and redo stacks
	*
	*/
	public static function clear_undoredo_stack() {
		$_SESSION['eighth_undo'] = array();
		self::$undo = array();
		$_SESSION['eighth_redo'] = array();
		self::$redo = array();
	}

	private function undoit() {
		global $I2_ARGS;
		if ($this->op == 'view') {
			$this->view_undoredo_stack();
		} else {
			if ($this->op == 'undo') {
				self::undo_transaction();
			} elseif ($this->op == 'redo') {
				self::redo_transaction();
			} elseif ($this->op == 'clear') {
				self::clear_undoredo_stack();
			} else {
				redirect('eighth');
			}
			// Circumvent $args because it turns the path into an associative array
			$str = implode('/',array_slice($I2_ARGS,3));
			redirect('eighth/'.$str);
		}
	}

	public static function sort_by_name($one,$two) {
		return strcasecmp($one->name_comma,$two->name_comma);
	}

	/**
	* Add, modify, or remove a special group of students
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function amr_group() {
		if($this->op == '' || $this->op == 'added') {
			if (!isSet($this->args['gid'])) {
				$this->args['gid'] = FALSE;
			}
			$this->setup_group_selection(true,'Select a group',$this->args['gid']);
			$this->template_args['display_modify'] = TRUE;
		}
		else if($this->op == 'add') {
			$gid = Group::add_group('eighth_' . $this->args['name']);
			redirect("eighth/amr_group/added/gid/$gid");
		}
		else if($this->op == 'modify') {
			$grp = new Group($this->args['gid']);
			$grp->set_group_name("eighth_".$_REQUEST['name']);
			redirect("eighth/amr_group/view/gid/{$this->args['gid']}");
		}
		else if($this->op == 'remove') {
				  $group = new Group($this->args['gid']);
				  $group->delete_group();
			redirect('eighth/amr_group');
		}
		else if($this->op == 'view' || $this->op == 'csv') {
			$group = new Group($this->args['gid']);
			$this->template = 'amr_group.tpl';
			$this->template_args['group'] = $group;
			if (isSet($this->args['lastadded'])) {
					$user = new User($this->args['lastadded']);
					$this->template_args['lastaction'] = 'Added user '.$user->fullname_comma;
			}
			if (isSet($this->args['lastremoved'])) {
					$user = new User($this->args['lastremoved']);
					$this->template_args['lastaction'] = 'Removed user '.$user->fullname_comma;
			}
			$membersorted = array();
			$membersorted = $group->members_obj;
			usort($membersorted,array('User','name_cmp'));
			if ($this->op == 'view') {
				$this->template_args['membersorted'] = $membersorted;
				$this->template_args['search_destination'] = 'eighth/amr_group/add_member/gid/'.$this->args['gid'];
				$this->template_args['action_name'] = 'Add';
				if(isset($this->args['error']) && $this->args['error']!='false') {
					$this->template_args['error'] = "There was an error processing your input. Please check to make sure that no entries are missing. Errors were with the ids " . $this->args['error'] . ".";
				}
				$this->title = 'View Group (' . substr($group->name,7) . ')';
			}
			else if ($this->op == 'csv') {
				Display::stop_display();
				header('Pragma: ');
				header('Content-type: text/csv');
				$datestr = date('Y-m-d-His');
				$name = substr($group->name, 7);
				header("Content-Disposition: attachment; filename=\"EighthGroup-$name-$datestr.csv\"");
				print "Last,First,Student ID,Gr,Email\r\n";

				$attrib = array('lname','fname','tjhsstStudentId','grade');
				foreach ($membersorted as $mem) {
					foreach ($attrib as $att) {
						print "{$mem->$att},";
					}
					$mail = $mem->mail;
					if (count($mail)) {
						print ((count($mail) == 1) ? $mail : $mail[0]);
					}
					print "\r\n";
				}
			}
		}
		else if($this->op == 'add_member') {
			$group = new Group($this->args['gid']);
			//TODO: this should be up in 'view', so as to avoid duplicate code
			if (!isSet($this->args['uid']) && Search::get_results()) {
				$this->template_args['info'] = Search::get_results();
				$this->template_args['results_destination'] = 'eighth/amr_group/add_member/gid/'.$this->args['gid'].'/uid/';
				$this->template_args['return_destination'] = 'eighth/amr_group/view/gid/'.$this->args['gid'];
				if(count($this->template_args['info']) == 1) {
					redirect($this->template_args['results_destination'] . $this->template_args['info'][0]->uid);
				}
				$membersorted = array();
				$membersorted = $group->members_obj;
				usort($membersorted,array('User','name_cmp'));
				$this->template_args['membersorted'] = $membersorted;
				$this->template_args['group'] = $group;
				$this->template = 'amr_group.tpl';
			} else {
				$group->add_user(new User($this->args['uid']));
				redirect("eighth/amr_group/view/gid/{$this->args['gid']}/lastadded/".$this->args['uid']);
			}
		}
		else if($this->op == 'remove_member') {
			$group = new Group($this->args['gid']);
			$group->remove_user(new User($this->args['uid']));
			redirect("eighth/amr_group/view/gid/{$this->args['gid']}/lastremoved/".$this->args['uid']);
		}
		else if($this->op == 'remove_all') {
			$group = new Group($this->args['gid']);
			$group->remove_all_members();
			redirect("eighth/amr_group/view/gid/{$this->args['gid']}/");
		}
		else if($this->op == 'add_members') {
			$error="false";
			if (!isset($_FILES['textfile'])) {
				redirect("eighth/amr_group/view/gid/{$this->args['gid']}/");
			}
			$file = $_FILES['textfile'];
			$fname = $file['tmp_name'];
			
			$group = new Group($this->args['gid']);

			$fd = fopen($fname, 'r');
			while (!feof($fd)) {
				$id = trim(fgets($fd));
				if (strlen($id) == 0)
					continue;
				try {
					$thing = User::studentid_to_uid($id);
					if (!$thing) $thing = $id;
					$group->add_user(new User($thing));
				} catch (I2Exception $e) {
					if($error=="false")
						$error=$id;
					else
						$error=$error.",$id";
				}
			}
			fclose($fd);
			redirect("eighth/amr_group/view/error/$error/gid/{$this->args['gid']}/");
		}
	}
	
	/**
	* Add students to a restricted activity
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	* @todo Work on restricted activities and permissions
	*/
	private function alt_permissions() {
		if($this->op == '') {
			$this->setup_activity_selection(FALSE, NULL, TRUE);
		}
		else if($this->op == 'view') {
			$this->template = 'alt_permissions.tpl';
			$this->template_args['activity'] = new EighthActivity($this->args['aid']);
			$this->template_args['groups'] = Group::get_all_groups('eighth');
			if (isSet($this->args['searchdone']) && Search::get_results()) {
				$this->template_args['results_destination'] = 'eighth/alt_permissions/add_member/aid/'.$this->args['aid'].'/uid/';
				$this->template_args['return_destination'] = 'eighth/alt_permissions/view/aid/'.$this->args['aid'];
				$this->template_args['info'] = Search::get_results();
				if(count($this->template_args['info']) == 1) {
					redirect($this->template_args['results_destination'] . $this->template_args['info'][0]->uid);
				}
			} else {
				$this->template_args['search_destination'] = 'eighth/alt_permissions/view/searchdone/1/aid/'.$this->args['aid'];
				$this->template_args['action_name'] = 'Add';
			}
			$this->title = 'Alter Permissions to Restricted Activities';
		}
		else {
			self::start_undo_transaction();
			if($this->op == 'add_group') {
				$activity = new EighthActivity($this->args['aid']);
				$group = new Group($this->args['gid']);
				$activity->add_restricted_members($group->members);
	
				redirect("eighth/alt_permissions/view/aid/{$this->args['aid']}");
			}
			else if($this->op == 'add_member') {
				$activity = new EighthActivity($this->args['aid']);
				$activity->add_restricted_member(new User($this->args['uid']));
			
				redirect("eighth/alt_permissions/view/aid/{$this->args['aid']}");
			}
			else if($this->op == 'remove_member') {
				$activity = new EighthActivity($this->args['aid']);
				$activity->remove_restricted_member(new User($this->args['uid']));
				
				redirect("eighth/alt_permissions/view/aid/{$this->args['aid']}");
			}
			else if($this->op == 'remove_all') {
				EighthActivity::remove_restricted_all_from_activity($this->args['aid']);
				//$activity = new EighthActivity($this->args['aid']);
				//$activity->remove_restricted_all();

				redirect("eighth/alt_permissions/view/aid/{$this->args['aid']}");
			}
			self::end_undo_transaction();
		}
	}

	/**
	* Switch all the students in one activity into another
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function people_switch() {
		if($this->op == '') {
			$this->setup_block_selection(FALSE, 'bid_from');
			$this->template_args['op'] = 'activity_from';
			$this->title = 'Select a Block to Move Students From';
		}
		else if($this->op == 'activity_from') {
			$this->setup_activity_selection(FALSE, $this->args['bid_from'], FALSE, "aid_from", "From this activity:");
			$this->template_args['op'] = "activity_to/bid_from/{$this->args['bid_from']}/bid_to/{$this->args['bid_from']}";
			$this->title = 'Select an Activity to Move Students From';
		}
		else if($this->op == 'block_to') {
			$this->setup_block_selection(FALSE, 'bid_to');
			$this->template_args['op'] = "activity_to/bid_from/{$this->args['bid_from']}/aid_from/{$this->args['aid_from']}";
			$this->title = 'Select a Block into which to move Students';
		}
		else if($this->op == 'activity_to') {
			$this->setup_activity_selection(FALSE, $this->args['bid_to'], FALSE, "aid_to", "To this activity:");
			$this->template_args['op'] = "confirm/bid_from/{$this->args['bid_from']}/aid_from/{$this->args['aid_from']}/bid_to/{$this->args['bid_to']}";
			$this->title = 'Select an Activity into which to move Students';
		}
		else if($this->op == 'confirm') {
			if($this->args['aid_from'] == $this->args['aid_to']) {
				redirect("eighth/people_switch/activity_to/bid_from/{$this->args['bid_from']}/aid_from/{$this->args['aid_from']}/bid_to/{$this->args['bid_to']}");
			}
			$this->template = 'people_switch.tpl';
			try {
				$this->template_args['activity_from'] = new EighthActivity($this->args['aid_from'], $this->args['bid_from']);
			} catch (I2Exception $e) {
				//$this->template_args['activity_from'] = new EighthActivity(i2config_get('default_aid',999,'eighth'), $this->args['bid_from']);
			}
			$this->template_args['activity_to'] = new EighthActivity($this->args['aid_to'], $this->args['bid_to']);
			$this->title = 'Confirm Moving Students';
		}
		else if($this->op == 'commit') {
			//$activity_from = new EighthActivity($this->args['aid_from'], $this->args['bid_from']);
			//$activity_to = new EighthActivity($this->args['aid_to'], $this->args['bid_to']);
			//$activity_to->add_members($activity_from->members, TRUE);
			//$activity_from->remove_all();
			$activity_to = new EighthActivity($this->args['aid_to'], $this->args['bid_to']);
			self::start_undo_transaction();
			$activity_to->transfer_members($this->args['aid_from'], $this->args['bid_to']);
			self::end_undo_transaction();
			redirect('eighth');
		}
	}

	/**
	* Add, modify, or remove an activity
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function amr_activity() {
		if($this->op == '') {
			$this->setup_activity_selection(TRUE);
		}
		else if($this->op == 'view') {
			$this->template = 'amr_activity.tpl';
			$this->template_args['activity'] = new EighthActivity($this->args['aid']);
			$this->title = 'View Activities';
		}
		else if($this->op == 'add') {
			$newid = NULL;
			if ($this->args['aid'] != 'auto' && ! EighthActivity::activity_exists($this->args['aid'])) {
				$newid = $this->args['aid'];
			}
			self::start_undo_transaction();
			$aid = EighthActivity::add_activity($this->args['name'], array(), array(), '', FALSE, FALSE, FALSE, FALSE, $newid);
			self::end_undo_transaction();
			redirect("eighth/amr_activity/view/aid/{$aid}");
		}
		else if($this->op == 'modify') {
			$activity = new EighthActivity($this->args['aid']);
			self::start_undo_transaction();
			$activity->name = $this->args['name'];
			$activity->sponsors = $this->args['sponsors'];
			$activity->rooms = $this->args['rooms'];
			$activity->description = $this->args['description'];
			$activity->restricted = ($this->args['restricted'] == 'on');
			$activity->presign = ($this->args['presign'] == 'on');
			$activity->oneaday = ($this->args['oneaday'] == 'on');
			$activity->bothblocks = ($this->args['bothblocks'] == 'on');
			$activity->sticky = ($this->args['sticky'] == 'on');
			$activity->special = ($this->args['special'] == 'on');
			self::end_undo_transaction();
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == 'remove') {
			if (! empty($this->args['sure'])) {
				self::start_undo_transaction();
				EighthActivity::remove_activity($this->args['aid']);
				self::end_undo_transaction();
				redirect('eighth');
			}
			else {
				$this->template = 'remove_activity_confirm.tpl';
				$this->template_args['activity'] = new EighthActivity($this->args['aid']);
			}
		}
		else if($this->op == 'select_sponsor') {
			$this->setup_sponsor_selection();
			$this->template = 'sponsor_selection.tpl';
			$this->template_args['op'] = "add_sponsor/aid/{$this->args['aid']}";
		}
		else if($this->op == 'add_sponsor') {
			$activity = new EighthActivity($this->args['aid']);
			self::start_undo_transaction();
			$activity->add_sponsor($this->args['sid']);
			self::end_undo_transaction();
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == 'remove_sponsor') {
			$activity = new EighthActivity($this->args['aid']);
			self::start_undo_transaction();
			$activity->remove_sponsor($this->args['sid']);
			self::end_undo_transaction();
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == 'select_room') {
			$this->setup_room_selection();
			$this->template_args['op'] = "add_room/aid/{$this->args['aid']}";
		}
		else if($this->op == 'add_room') {
			$activity = new EighthActivity($this->args['aid']);
			self::start_undo_transaction();
			$activity->add_room($this->args['rid']);
			self::end_undo_transaction();
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == 'remove_room') {
			$activity = new EighthActivity($this->args['aid']);
			self::start_undo_transaction();
			$activity->remove_room($this->args['rid']);
			self::end_undo_transaction();
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		} else {
			redirect('eighth');
		}
	}

	/**
	* Add, modify, or remove a room
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function amr_room() {
		if($this->op == '' || $this->op == 'select') {
			if (!isSet($this->args['rid'])) {
				$this->args['rid'] = FALSE;
			}
			$this->setup_room_selection(true,'Select a room:',$this->args['rid']);
		}
		else if($this->op == 'view') {
			$this->template = 'amr_room.tpl';
			$this->template_args['room'] = new EighthRoom($this->args['rid']);
			$this->title = 'View Rooms';
		}
		else if($this->op == 'add') {
			if (!isSet($this->args['capacity']) || !$this->args['capacity'] || !is_numeric($this->args['capacity'])) {
				$this->args['capacity'] = -1;
			}
			self::start_undo_transaction();
			$rid = EighthRoom::add_room($this->args['name'], $this->args['capacity']);
			self::end_undo_transaction();
			//redirect("eighth/amr_room/view/rid/{$rid}");
			redirect("eighth/amr_room/select/rid/$rid");
		}
		else if($this->op == 'modify') {
			self::start_undo_transaction();
			if ($this->args['modify_or_remove'] == 'modify') {
				$room = new EighthRoom($this->args['rid']);
				$room->name = $this->args['name'];
				$room->capacity = $this->args['capacity'];
				self::end_undo_transaction();
				redirect("eighth/amr_room/view/rid/{$this->args['rid']}");
			} else if ($this->args['modify_or_remove'] == 'remove') {
				EighthRoom::remove_room($this->args['rid']);
				self::end_undo_transaction();
				redirect('eighth/amr_room');
			}
		}
	}

	/**
	* Add, modify, or remove an activity sponsor
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function amr_sponsor() {
		if($this->op == '') {
			$this->setup_sponsor_selection(false);
			$this->template = 'amr_sponsor.tpl';
		}
		else if($this->op == 'submit') {
			$sid = $this->args['sid']; //either FALSE or an sid num
			self::start_undo_transaction();
			if($this->args['is_remove']) {
				EighthSponsor::remove_sponsor($sid);
			} else {
				EighthSponsor::add_sponsor($this->args['fname'], $this->args['lname'], $this->args['pickup'], $sid);
			}
			self::end_undo_transaction();
			redirect('eighth/amr_sponsor');
		}
	}

	/**
	* Schedule an activity for eighth period
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function sch_activity() {
		if($this->op == '') {
			$this->setup_activity_selection();
			$this->template = 'sch_activity_choose.tpl';
		}
		else if($this->op == 'view') {
			$this->template = 'sch_activity.tpl';
			$this->template_args['rooms'] = EighthRoom::get_all_rooms();
			$this->template_args['sponsors'] = EighthSponsor::get_all_sponsors();
			$start_date = $this->args['start_date'];
			list($this->template_args['unscheduled_blocks'], $this->template_args['block_activities']) = EighthSchedule::get_activity_schedule($this->args['aid'], $start_date);
			$this->template_args['unscheduled_blocks'] = "'" . implode("','", $this->template_args['unscheduled_blocks']) . "'";
			$this->template_args['activities'] = EighthActivity::get_all_activities();
			$this->template_args['act'] = new EighthActivity($this->args['aid']);
			$this->title = 'Schedule an Activity (' . $this->template_args['act']->name_r  . ')';
		}
		else if($this->op == 'modify') {
			self::start_undo_transaction();
			foreach($this->args['modify'] as $bid) {
				if($this->args['activity_status'][$bid] == 'UNSCHEDULED') {
					EighthSchedule::unschedule_activity($bid, $this->args['aid']);
				}
				else {
					$sponsorlist = array();
					$roomlist = array();
					$commentslist = NULL;
					$aid = NULL;
					if (isset($this->args['aid'])) {
						$aid = $this->args['aid'];
					}
					if (isset($this->args['sponsor_list']) && isset($this->args['sponsor_list'][$bid])) {
						$sponsorlist = array_filter(explode(',', $this->args['sponsor_list'][$bid]));
					}
					if (isset($this->args['room_list']) && isset($this->args['room_list'][$bid])) {
						$roomlist = array_filter(explode(',', $this->args['room_list'][$bid]));
					}
					if (isset($this->args['comments']) && isset($this->args['comments'][$bid])) {
						$commentslist = $this->args['comments'][$bid];
					}
					EighthSchedule::schedule_activity($bid, $aid, $sponsorlist, $roomlist, $commentslist);
				}
				if($this->args['activity_status'][$bid] == 'CANCELLED') {
					EighthActivity::cancel($bid, $this->args['aid']);
				}
			}
			self::end_undo_transaction();
			redirect("eighth/sch_activity/view/aid/{$this->args['aid']}");
		}
	}

	/**
	* View or print a class roster
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function vp_roster() {
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = 'activity';
		}
		else if($this->op == 'activity') {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "view/bid/{$this->args['bid']}";
		}
		else if($this->op == 'view') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->template = 'vp_roster.tpl';
			$this->template_args['activity'] = $activity;
			$this->template_args['print_url'] = "bid/{$this->args['bid']}/aid/{$this->args['aid']}";
			$this->title = 'View Roster';
		}
		else if($this->op == 'format') {
			$this->setup_format_selection('vp_roster', 'Class Roster', array('aid' => $this->args['aid'], 'bid' => $this->args['bid']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_class_roster($this->args['aid'], $this->args['bid'], $this->args['format']);
		}
	}

	/**
	* View or print the utilization of a room
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function vp_room() {
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = 'search';
		}
		else if($this->op == 'search') {
			$this->template = 'vp_room_search.tpl';
			$this->template_args['bid'] = $this->args['bid'];
			$this->title = 'Search Room Utilization';
		}
		else if($this->op == 'view') {
			$this->template = 'vp_room_view.tpl';
			$this->template_args['block'] = new EighthBlock($this->args['bid']);
			$this->template_args['utilizations'] = EighthRoom::get_utilization($this->args['bid'], $this->args['include'], 
					  !empty($this->args['overbooked']),$this->args['sort']);
			$inc = array();
			foreach ($this->args['include'] as $include) {
					  $inc[$include] = 1;
			}
			$this->template_args['inc'] = $inc;
			$this->title = 'View Room Utilization';
			$this->template_args['print_url'] = "bid/{$this->args['bid']}";
		}
		else if($this->op == 'format') {
			$this->setup_format_selection('vp_room', 'Room Utilization', array('bid' => $this->args['bid']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_room_utilization($this->args['bid'], $this->args['format']);
		}
	}

	/**
	* Cancel/set comments/advertize for an activity
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function cancel_activity() {
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = 'activity';
		}
		else if($this->op == 'activity') {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "view/bid/{$this->args['bid']}";
		}
		else if($this->op == 'view') {
			$this->template = 'cancel_activity.tpl';
			$this->template_args['activity'] = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->title = "Cancel an Activity";
		}
		else if($this->op == 'update') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			self::start_undo_transaction();
			$activity->comment = $this->args['comment'];
			$activity->advertisement = $this->args['advertisement'];
			$activity->cancelled = ($this->args['cancelled'] == "on");
			self::end_undo_transaction();
			//redirect("eighth/cancel_activity/view/bid/{$this->args['bid']}/aid/{$this->args['aid']}");
			redirect("eighth/cancel_activity/activity/bid/{$this->args['bid']}");
		}
	}

	/**
	* Room assignment sanity check
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function room_sanity() {
		if($this->op == '') {
			$this->setup_block_selection();
		}
		else if($this->op == 'view') {
			$this->template = 'room_sanity.tpl';
			$this->template_args['conflicts'] = EighthRoom::get_conflicts($this->args['bid']);
			//$sponsorcon = EighthSponsor::get_conflicts($this->args['bid']);
			$this->template_args['sponsorconflicts'] = EighthSponsor::get_conflicts($this->args['bid']);
			$this->title = 'Room Assignment Sanity Check';
		}
	}

	/**
	* View or print sponsor schedule
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vp_sponsor() {
		if($this->op == '') {
			$this->setup_sponsor_selection();
		}
		else if($this->op == 'view') {
			$sponsor = new EighthSponsor($this->args['sid']);
			$this->template = 'vp_sponsor.tpl';
			$this->template_args['sponsor'] = $sponsor;
			$start_date = $this->args['start_date'];
			$this->template_args['activities'] = EighthSponsor::get_schedule($sponsor->sid,$start_date);
			$this->template_args['print_url'] = "sid/{$this->args['sid']}";
			$this->title = 'View Sponsor Schedule';
		}
		else if($this->op == 'format') {
			$this->setup_format_selection('vp_sponsor', 'Sponsor Schedule', array('sid' => $this->args['sid']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_sponsor_schedule($this->args['sid'], $this->args['format']);
		}
	}

	/**
	* View or print sponsor schedule
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vp_activity() {
		if($this->op == '') {
			$this->setup_activity_selection();
		}
		else if($this->op == 'view') {
			$start_date = $this->args['start_date'];
			$activity = new EighthActivity($this->args['aid']);
			$activities = $activity->get_all_blocks($start_date);
			$this->template_args['activity'] = $activity;
			$this->template_args['activities'] = $activities;
			$this->template_args['print_url'] = "aid/{$this->args['aid']}";

			$this->template = 'vp_activity.tpl';
			$this->title = 'View Activity Schedule';
		}
		else if($this->op == 'format') {
			$this->setup_format_selection('vp_activity', 'Activity Schedule', array('aid' => $this->args['aid']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_activity_schedule($this->args['aid'], $this->args['format']);
		}
	}
	
	/**
	* Reschedule students by student ID for a single activity
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function res_student() {
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = 'activity';
		}
		else if($this->op == 'activity') {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "user/bid/{$this->args['bid']}";
		}
		else if($this->op == 'user') {
			$this->setup_block_selection();
			$this->template = 'res_student.tpl';
			$this->template_args['block'] = new EighthBlock($this->args['bid']);
			$this->template_args['activities'] = EighthActivity::get_all_activities($this->args['bid']);
			$this->template_args['op'] = "user";
			$this->template_args['act'] = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->template_args['bid'] = $this->args['bid'];
			$this->template_args['aid'] = $this->args['aid'];
			if (isSet($this->args['rescheduled'])) {
				$this->template_args['lastuser'] = new User($this->args['rescheduled']);
			}
			if(isSet($this->args['studentId'])) {
				$this->template_args['user'] = new User($this->args['studentId']);
				if (!$this->template_args['user']->is_valid()) {
					redirect('eighth/res_student/user/bid/'.$this->args['bid'].'/aid/'.$this->args['aid']);
				}
			}
			if (isSet($this->args['searchdone']) && Search::get_results()) {
					  $this->template_args['results_destination'] = 'eighth/res_student/reschedule/bid/'.$this->args['bid'].'/aid/'.$this->args['aid'].'/uid/';
					  $this->template_args['return_destination'] = 'eighth/res_student/user/bid/'.$this->args['bid'].'/aid/'.$this->args['aid'];
					  $this->template_args['info'] = Search::get_results();
					  if(count($this->template_args['info']) == 1) {
						  redirect($this->template_args['results_destination'] . $this->template_args['info'][0]->uid);
					  }
			} else {
				$this->template_args['action_name'] = 'Search';
				$this->template_args['search_destination'] = 'eighth/res_student/user/searchdone/1/bid/'.$this->args['bid'].'/aid/'.$this->args['aid'];
			}
			$this->title = 'Reschedule a Student';
		}
		else if($this->op == 'reschedule') {
			$user = new User($this->args['uid']);
			$rescheduled = ($user->objectClass == "tjhsstStudent");
			if ($rescheduled) {
				$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
					
				self::start_undo_transaction();
				EighthSchedule::remove_absentee($this->args['bid'],$this->args['uid']);
				$activity->add_member($user,TRUE);
				self::end_undo_transaction();
			}		
			redirect("eighth/res_student/user/" 
				. ($rescheduled ? "rescheduled/{$this->args['uid']}/" : "") 
				. "bid/{$this->args['bid']}/aid/{$this->args['aid']}");
		}
	}

	/**
	* View, change, or print attendance data
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vcp_attendance() {
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = 'activity';
		}
		else if($this->op == 'activity') {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "view/bid/{$this->args['bid']}";
		}
		else if($this->op == 'view') {
			$this->setup_block_selection();
			$this->template = 'vcp_attendance.tpl';
			$this->template_args['op'] = "view/bid/{$this->args['bid']}";
			$this->template_args['block'] = new EighthBlock($this->args['bid']);
			$this->template_args['aid'] = $this->args['aid'];
			$this->template_args['activities'] = EighthActivity::get_all_activities($this->args['bid']);
			$this->template_args['act'] = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->template_args['absentees'] = EighthSchedule::get_absentees($this->args['bid'], $this->args['aid']);
			$this->template_args['print_url'] = "bid/{$this->args['bid']}/aid/{$this->args['aid']}";
			$this->template_args['is_admin'] = self::is_admin();
			$this->title = 'View Attendance';
		}
		else if($this->op == 'update') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$members = $activity->members;
			self::start_undo_transaction();
			foreach($members as $member) {
				if(isSet($this->args['absentees']) && is_array($this->args['absentees']) && in_array($member, $this->args['absentees'])) {
					EighthSchedule::add_absentee($this->args['bid'], $member);
				}
				else {
					EighthSchedule::remove_absentee($this->args['bid'], $member);
				}
			}
			$activity->attendancetaken = TRUE;
			self::end_undo_transaction();
			redirect("eighth/vcp_attendance/view/bid/{$this->args['bid']}/aid/{$this->args['aid']}");
		}
		else if($this->op == 'format') {
			$this->setup_format_selection('vcp_attendance', 'Attendance Data', array('aid' => $this->args['aid'], 'bid' => $this->args['bid']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_attendance_data($this->args['aid'], $this->args['bid'], $this->args['format']);
		}
	}

	/**
	* Enter TA absences by student ID
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function ent_attendance() {
		return FALSE;
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = "user";
		}
		else if($this->op == 'user') {
			$this->template = 'ent_attendance.tpl';
			$block = new EighthBlock($this->args['bid']);
			$this->template_args['date'] = $block->date;
			$this->template_args['block'] = $block->block;
			$this->template_args['bid'] = $this->args['bid'];
			if (isSet($this->args['lastuid'])) {
				$this->template_args['lastuid'] = $this->args['lastuid'];
				$user = new User($this->args['lastuid']);
				$this->template_args['lastname'] = $user->name;
				$this->template_args['studentid'] = $user->studentid;
				$this->template_args['activity'] = new EighthActivity(EighthSchedule::get_activities_by_block($user->uid, $this->args['bid']), $this->args['bid']);
			}
			$this->title = 'Enter TA Attendance';
		}
		else if($this->op == "mark_absent") {
			$user = new User($this->args['uid']);
			self::start_undo_transaction();
			EighthSchedule::add_absentee($this->args['bid'], $user->uid);
			self::end_undo_transaction();
			redirect('eighth/ent_attendance/user/bid/'.$this->args['bid'].'/lastuid/'.$user->uid);
		} else if ($this->op == 'unmark_absent') {
			self::start_undo_transaction();
			EighthSchedule::remove_absentee($this->args['bid'], $this->args['uid']);
			self::end_undo_transaction();
			redirect('eighth/ent_attendance/user/bid/'.$this->args['bid']);
		}
	}

	/**
	* View or print a list of delinquent students
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vp_delinquent() {
		// TODO: Sorting and exporting for all
		if($this->op == '' || $this->op == 'sort' || $this->op == 'csv') {
			// Print a list of delinquents
			$lower = 0;
			$upper = 1000;
			$start = null;
			$end = null;
			$sort = 'name';
			$grades = array();
			$legal_sorts = array(
				'name' => 'Alphabetically',
				'name_desc' => 'Alphabetically, descending',
				'grade' => 'Grade',
				'grade_desc' => 'Grade, descending',
				'absences' => 'Number of absences',
				'absences_desc' => 'Number of absences, descending'
			);
			if(!empty($this->args['lower']) && ctype_digit($this->args['lower'])) {
				$lower = $this->args['lower'];
			}
			if(isset($this->args['upper']) && $this->args['upper'] != '' && ctype_digit($this->args['upper'])) {
				$upper = $this->args['upper'];
			}
			if(!empty($this->args['start'])) {
				$start = $this->args['start'];
			}
			if(!empty($this->args['end'])) {
				$end = $this->args['end'];
			}
			if(!empty($this->args['sort']) && in_array($this->args['sort'], array_keys($legal_sorts))) {
				$sort = $this->args['sort'];
			}
			if(!isset($this->args['seniors']) && !isset($this->args['juniors']) && !isset($this->args['sophomores']) && !isset($this->args['freshmen'])) {
				$grades = array(9, 10, 11, 12);
				$this->template_args['seniors'] = TRUE;
				$this->template_args['juniors'] = TRUE;
				$this->template_args['sophomores'] = TRUE;
				$this->template_args['freshmen'] = TRUE;
			}
			else {
				if (isset($this->args['seniors'])) {
					$grades[] = 12;
					$this->template_args['seniors'] = TRUE;
				}
				if (isset($this->args['juniors'])) {
					$grades[] = 11;
					$this->template_args['juniors'] = TRUE;
				}
				if (isset($this->args['sophomores'])) {
					$grades[] = 10;
					$this->template_args['sophomores'] = TRUE;
				}
				if (isset($this->args['freshmen'])) {
					$grades[] = 9;
					$this->template_args['freshmen'] = TRUE;
				}
			}

			// We actually want to get the data, in whatever form
			if ($this->op == 'sort' || $this->op == 'csv') {
				// so, get the data
				$alldelinquents = EighthSchedule::get_delinquents($lower, $upper, $start, $end);
				$wanteddelinquents = array();
				foreach ($alldelinquents as $delinquent) {
					$user = new User($delinquent['userid']);
					if (in_array($user->grade, $grades)) {
						$wanteddelinquents[] = array(
							'absences' => $delinquent['absences'],
							'name' => $user->name_comma,
							'uid' => $user->uid,
							'studentid' => $user->tjhsstStudentId,
							'grade' => $user->grade
						);
					}
				}
				// This has issues... apparently, PHP doesn't actually think the methods are static...
				@usort($wanteddelinquents, array("Eighth", "delin_sort_$sort"));

				// move the data to the format of choice
				if ($this->op == 'sort') {
					$this->template_args['show'] = TRUE;
					$this->template_args['delinquents'] = array();
					$this->template_args['delinquents'] = $wanteddelinquents;
				}
				elseif ($this->op == 'csv') {
					Display::stop_display();
					header('Pragma: ');
					header('Content-type: text/csv');
					$datestr = date('Y-m-d-His');
					header("Content-Disposition: attachment; filename=\"EighthAbsentee-$datestr.csv\"");
					print "Abs,Last,First,Student ID,Gr,Counselor,Phone,Address,City,State,ZIP\r\n";
					$attrib = array('lname','fname','tjhsstStudentId','grade','counselor_name','phone_home','street','l','st','postalCode');
					foreach ($wanteddelinquents as $delinquent) {
						$user = new User($delinquent['uid']);
						print "{$delinquent['absences']}";
						foreach($attrib as $i) {
							print ",{$user->$i}";
						}
						print "\r\n";
					}
					return;
				}
			}
			$this->template_args['sorts'] = $legal_sorts;
			$this->template_args['sort'] = $sort;
			$this->template_args['lower'] = $lower;
			$this->template_args['upper'] = $upper;
			$this->template_args['start'] = $start;
			$this->template_args['end'] = $end;
			$this->template = 'vp_delinquent.tpl';
			$this->title = 'View Delinquent Students';
		}
		else if($this->op == 'query') {
			// TODO: Query the delinquents
			$this->template = 'vp_delinquent.tpl';
			$this->title = 'Query Delinquent Students';
		}
	}

	/**
	 * Sort method for vp_delinquents
	 */
	public static function delin_sort_name($a, $b) {
		return strcasecmp($a['name'], $b['name']);
	}

	/**
	 * Sort method for vp_delinquents
	 */
	public static function delin_sort_name_desc($a, $b) {
		return -1 * Eighth::delin_sort_name($a, $b);
	}

	/**
	 * Sort method for vp_delinquents
	 */
	public static function delin_sort_grade($a, $b) {
		return ($a['grade'] < $b['grade']) ? -1 : 1;
	}

	/**
	 * Sort method for vp_delinquents
	 */
	public static function delin_sort_grade_desc($a, $b) {
		return -1 * Eighth::delin_sort_grade($a, $b);
	}

	/**
	 * Sort method for vp_delinquents
	 */
	public static function delin_sort_absences($a, $b) {
		return ($a['absences'] < $b['absences']) ? -1 : 1;
	}

	/**
	 * Sort method for vp_delinquents
	 */
	public static function delin_sort_absences_desc($a, $b) {
		return -1 * Eighth::delin_sort_absences($a, $b);
	}

	/**
	* Finalize student schedules
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function fin_schedules() {
		if($this->op == '') {
			$this->template = 'fin_schedules.tpl';
			$this->template_args['blocks'] = EighthBlock::get_all_blocks();
			$this->title = 'Finalize Schedules';
		}
		else if($this->op == 'lock') {
			$block = new EighthBlock($this->args['bid']);
			self::start_undo_transaction();
			$block->locked = TRUE;
			self::end_undo_transaction();
			redirect('eighth/fin_schedules');
		}
	 	else if($this->op == 'unlock') {
			$block = new EighthBlock($this->args['bid']);
			self::start_undo_transaction();
			$block->locked = FALSE;
			self::end_undo_transaction();
			redirect('eighth/fin_schedules');
		}
	}

	/**
	* Print activity rosters
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function prn_attendance() {
		if($this->op == '') {
			$this->template_args['op'] = 'color';
			$this->setup_block_selection();
		}
		else if ($this->op == 'color') {
			$this->template_args['bid'] = $this->args['bid'];
			$this->template = 'block_color.tpl';
		}
		else if($this->op == 'format') {
			print_r($_POST);
			$this->setup_format_selection('prn_attendance', 'Activity Rosters', array('bid' => $this->args['bid'], 'color' => $_POST['color']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_activity_rosters(explode(',', $this->args['bid']), $this->args['color'], $this->args['format']);
		}
	}

	/**
	* Change starting date
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function chg_start() {
		if($this->op == '') {
			$this->template = 'chg_start.tpl';
			$this->title = 'Change Start Date';
		}
		else if($this->op == 'change') {
			$_SESSION['eighth']['start_date'] = $this->args['start_date'];
			redirect('eighth');
		}
	}

	/**
	* Add or remove 8th period block from system
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function ar_block() {
		if($this->op == '') {
			$this->template = 'ar_block.tpl';
			$start_date = $this->args['start_date'];
			$this->template_args['blocks'] = EighthBlock::get_all_blocks($start_date);
			$this->title = 'Add/Remove Block';
		}
		else if($this->op == 'add') {
			self::start_undo_transaction();
			foreach($this->args['blocks'] as $block) {
				EighthBlock::add_block("{$this->args['Year']}-{$this->args['Month']}-{$this->args['Day']}", $block);
			}
			self::end_undo_transaction();
			redirect('eighth/ar_block#add');
		}
		else if($this->op == 'remove') {
			self::start_undo_transaction();
			EighthBlock::remove_block($this->args['bid']);
			self::end_undo_transaction();
			redirect('eighth/ar_block#add');
		}
	}
	
	/**
	* Repair broken schedules
	*
	* If someone is not signed up for any activities during a given block, he will not be able to even see
	* that block in order to sign up for anything. This signs everyone who is not already signed up for anything
	* into the default activity, so that they can see and change their activity.
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	* @todo Figure out what voodoo this does
	*/
	public function rep_schedules() {
		global $I2_SQL, $I2_LDAP;
		//throw new I2Exception('Don\'t hit that button!');
		$alluids = $I2_LDAP->search('ou=people,dc=tjhsst,dc=edu', 'objectClass=tjhsstStudent', 'iodineUidNumber')->fetch_col('iodineUidNumber');
		$numuids = count($alluids);
		if ($this->op == '') {
			$start_date = $this->args['start_date'];
			$bids = $I2_SQL->query('SELECT bid FROM eighth_blocks WHERE date >= %s', $start_date)->fetch_col('bid');
			$default_aid = i2config_get('default_aid',999,'eighth');
			foreach($bids as $bid) {
				$uidstofix = array();
				$members = $I2_SQL->query('SELECT userid FROM eighth_activity_map WHERE bid=%s', $bid)->fetch_col('userid');
				if (count($members) != $numuids) {
					warn('Found missing members! (blockid ' . $bid . ')');
					$missing = array_diff($alluids, $members);
					warn('Missing members: ' . implode(', ', array_values($missing)));
					//$activity->add_members($missing, true, $bid);
					foreach ($missing as $uid) {
						$I2_SQL->query('INSERT INTO eighth_activity_map SET bid=%d, aid=%d, userid=%d', $bid, $default_aid, $uid);
					}
				}
			}
			//redirect('eighth');
		}
	}

	/**
	* View, change, or print student schedule
	*
	* @access public
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vcp_schedule() {
		global $I2_SQL,$I2_USER;
		if($this->op == '') {
			$this->template = 'vcp_schedule.tpl';
			if(!empty($this->args['uid'])) {
				$this->template_args['users'] = array(new User($this->args['uid']));
			}
			else {
				if (isSet($this->args['fname']) && $this->args['fname']!="")
				{
					$this->template_args['users'] = User::search_info("{$this->args['fname']} {$this->args['name_id']}");
				}
				else {
					$this->template_args['users'] = User::search_info("{$this->args['name_id']}");
				}
			}
			if(count($this->template_args['users']) == 1) {
				redirect("eighth/vcp_schedule/view/uid/{$this->template_args['users'][0]->uid}");
			}
			usort($this->template_args['users'], array('User', 'name_cmp'));
			$this->title = 'Search Students';
		}
		else if($this->op == 'view') {
			$start_date = $this->args['start_date'];
			
			$temp = new DateTime($start_date);
			$temp->modify("+2 weeks");
			$this->template_args['next_date'] = $temp->format("Y-m-d");
			//modify works in-place, so we have to cancel out the +2 in addition to
			//the new -2
			$temp->modify("-4 weeks");
			$this->template_args['prev_date'] = $temp->format("Y-m-d");

			$user = new User($this->args['uid']);
			$this->template_args['user'] = $user;
			$this->template_args['comments'] = $user->comments;
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($this->args['uid'], $start_date), FALSE);
			$this->template_args['absences'] = EighthSchedule::get_absences($this->args['uid']);
			//TODO: Do this in the the template
			$this->template_args['absence_count'] = count($this->template_args['absences']);

			if(strlen($user->counselor_name) == 0) {
				$this->template_args['counselor_name'] = "N/A";
			}
			else {
				$this->template_args['counselor_name'] = $user->counselor_name;
			}
			
			try {
				if($user->schedule()->last() != null) {
					$lastclass = $user->schedule()->last();
					if($lastclass->period != 8) {
						$this->template_args['ta'] = "N/A";
					}
					else {
						$this->template_args['ta'] = $lastclass->teacher->sn;
					}
				}
				else {
					$this->template_args['ta'] = "N/A";
				}
			} catch (I2Exception $e) {
				//There is something wrong with the schedule or teacher.	
			}	
			
			$this->template = 'vcp_schedule_view.tpl';
			$this->title = 'View Schedule';
		}
		else if ($this->op == 'history') {
			$date = getdate();
			$date = ($date['mon'] > 7 ? $date['year'] : $date['year']-1).'-09-01';
			$days = intval((time()-strtotime($date))/86400);
			$this->template_args['start_date'] = strtotime($date);
			
			$user = new User($this->args['uid']);
			$this->template_args['user'] = $user;
			$this->template_args['comments'] = $user->comments;
			$this->template_args['activities'] = EighthActivity::
				id_to_activity(EighthSchedule::get_activities(
				$this->args['uid'], $date, $days), FALSE);
			$this->template_args['absences'] = EighthSchedule::get_absences($this->args['uid']);
			//TODO: Do this in the the template
			$this->template_args['absence_count'] = count($this->template_args['absences']);

			if(strlen($user->counselor_name) == 0) {
				$this->template_args['counselor_name'] = "N/A";
			}
			else {
				$this->template_args['counselor_name'] = $user->counselor_name;
			}

			try {
				if($user->schedule()->last() != null) {
					$lastclass = $user->schedule()->last();
					if($lastclass->period != 8) {
						$this->template_args['ta'] = "N/A";
					}
					else {
						$this->template_args['ta'] = $lastclass->teacher->sn;
					}
				}
				else {
					$this->template_args['ta'] = "N/A";
				}
			} catch (I2Exception $e) {
				//There is something wrong with the schedule or teacher.	
			}	
			$this->title = 'Eighth Periods Attended';
			$this->template = 'vcp_schedule_history.tpl';
		}
		else if($this->op == 'format') {
			$this->setup_format_selection('vcp_schedule', 'Student Schedule', array('uid' => $this->args['uid']), TRUE);
		}
		else if($this->op == 'print') {
			EighthPrint::print_student_schedule($this->args['uid'], $this->args['start_date'], $this->args['format']);
		}
		else if($this->op == 'choose') {
			$valids = array();
			$validdata = array();
			$this->template_args['activities'] = EighthActivity::get_all_activities($this->args['bids'],FALSE);
			$arr = array();
			foreach ($this->template_args['activities'] as $i) {
				if($i->favorite)
					$arr[] = $i;
			}
			$this->template_args['favorites'] = $arr;
			$this->template_args['uid'] = $this->args['uid'];
			$this->template = 'vcp_schedule_choose.tpl';
			if(!is_array($this->args['bids'])) {
				$this->template_args['bids'] = $this->args['bids'];
				$blockdate = ' for ';
				$blockdate = $blockdate.$I2_SQL->query('SELECT DATE_FORMAT((SELECT date FROM eighth_blocks WHERE bid=%d), %s)', $this->args['bids'], '%W, %M %d, %Y')->fetch_single_value();
				$blockdate = $blockdate.', '.$I2_SQL->query('SELECT block FROM eighth_blocks WHERE bid=%d', $this->args['bids'])->fetch_single_value().' Block';
			}
			else {
				$this->template_args['bids'] = implode(',', $this->args['bids']);
				$this->template_args['manybids'] = TRUE; //Tell the template not to offer "Show Rosters."
				if(count($this->args['bids']) > 1) {
					$blockdate = ' for Multiple Blocks';
				}
				else {
					$blockdate = ' for ';
					$blockdate = $blockdate.$I2_SQL->query('SELECT DATE_FORMAT((SELECT date FROM eighth_blocks WHERE bid=%d), %s)', implode($this->args['bids']), '%W, %M %d, %Y')->fetch_single_value();
					$blockdate = $blockdate.', '.$I2_SQL->query('SELECT block FROM eighth_blocks WHERE bid=%d', implode($this->args['bids']))->fetch_single_value().' Block';
				}
			}
			$this->title = 'Choose an Activity'.$blockdate;
		}
		else if($this->op == 'change') {
			if(isset($_POST['submit']) && $_POST['submit'] == "View Roster") {
				//We don't actually want to change activities, we just want to check an activity roster before signing up for the activity.
				redirect("eighth/vcp_schedule/roster/bid/{$this->args['bids']}/aid/{$this->args['aid']}");
			}
			if (isset($this->args['bids']) && isset($this->args['aid'])) {
				$status = array();
				$bids = explode(',', $this->args['bids']);
				foreach($bids as $bid) {
					if(true||EighthSchedule::is_activity_valid($this->args['aid'], $bid)) {
						$activity = new EighthActivity($this->args['aid'], $bid);
						
						self::start_undo_transaction();
						$ret = $activity->add_member(new User($this->args['uid']), isset($this->args['force']));
						self::end_undo_transaction();
						
						$act_status = array();
						if($ret & EighthActivity::CANCELLED) {
							$act_status['cancelled'] = TRUE;
						}
						if($ret & EighthActivity::PERMISSIONS) {
							$act_status['permissions'] = TRUE;
						}
						if($ret & EighthActivity::CAPACITY) {
							$act_status['capacity'] = TRUE;
						}
						if($ret & EighthActivity::STICKY) {
							$act_status['sticky'] = TRUE;
						}
						if($ret & EighthActivity::ONEADAY) {
							$act_status['oneaday'] = TRUE;
						}
						if($ret & EighthActivity::PRESIGN) {
							$act_status['presign'] = TRUE;
						}
						if($ret & EighthActivity::LOCKED) {
							$act_status['locked'] = TRUE;
						}
						if($ret & EighthActivity::PAST) {
							$act_status['past'] = TRUE;
						}
						if(count($act_status) != 0) {
							$act_status['activity'] = $activity;
							$status[$bid] = $act_status;
							$stat = array_keys($act_status);
							$this->template_args['forcereason'] = $stat[0];
						}
						$this->template_args['act_status'] = $act_status;
					}
				}
				if(count($status) == 0) {
					$start_date = $this->args['start_date'];
					if ($start_date !=  self::$default_start_date) {
						$append = "/start_date/{$start_date}";
					} else {
						$append = NULL;
					}
					redirect("eighth/vcp_schedule/view/uid/{$this->args['uid']}$append");
				}
				$this->template = 'vcp_schedule_change.tpl';
				$this->template_args['status'] = $status;
				$this->template_args['uid'] = $this->args['uid'];
				$this->template_args['bids'] = $this->args['bids'];
				$this->template_args['aid'] = $this->args['aid'];
			}
		}
		else if($this->op == 'roster') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->template_args['activity'] = $activity;
			$this->template = 'vcp_schedule_roster.tpl';
			$this->title = 'Activity Roster';
		}
		else if($this->op == 'absences') {
			$absences = EighthActivity::id_to_Activity(EighthSchedule::get_absences($this->args['uid']));
			$this->template_args['absences'] = $absences;
			$user = new User($this->args['uid']);
			$this->template_args['user'] = $user;
			$this->template_args['admin'] = $this->admin;
			$this->template = 'vcp_schedule_absences.tpl';
		}
		else if($this->op == 'remove_absence') {
			self::start_undo_transaction();
			EighthSchedule::remove_absentee($this->args['bid'], $this->args['uid']);
			self::end_undo_transaction();
			redirect('eighth/vcp_schedule/absences/uid/'.$this->args['uid']);
		}
		else if($this->op == 'favorite') {
			// The uid field here is used for the aid instead
			EighthActivity::favorite_change($this->args['uid']);
			$this->template_args['bids'] = $this->args['bids'];
			if(is_numeric($this->args['bids']))
				redirect("eighth/vcp_schedule/choose/uid/{$I2_USER->uid}/bids/{$this->args['bids']}");
			else
				redirect("eighth/vcp_schedule/choose/uid/{$I2_USER->uid}/bids/" . substr($this->args['bids'],0,strpos($this->args['bids'],",")));
		}
	}

	/**
	* Gets 8th-period comments about a user
	*
	*/
	public static function get_user_comments($uid) {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT comments from eighth_comments WHERE uid=%d',$uid)->fetch_single_value();
		if (!$res) {
			return '';
		}
		return $res;
	}
	
	/**
	* Sets 8th-period comments about a user
	*
	*/
	public static function set_user_comments($uid, $comments) {
		global $I2_SQL;
		return $I2_SQL->query('REPLACE INTO eighth_comments (uid,comments) VALUES (%d,%s)',$uid,$comments);
	}
	
	public function view() {
		global $I2_SQL;
		if($this->op == '') {
		}
		else if($this->op == 'comments') {
			/* Editing comments code */
			$this->template = 'edit_comments.tpl';
			$user = new User($this->args['uid']);
			$this->template_args['uid'] = $this->args['uid'];
			$this->template_args['username'] = $user->name;
			$comments = $user->comments;
			$this->template_args['comments'] = $comments;
			$this->title = 'Edit Comments';
		}
		else if($this->op == 'student') {
			/* Editing student code */
			$this->template = 'edit_student.tpl';
			$user = new User($this->args['uid']);
			$this->template_args['user'] = $user;
			$this->title = 'Edit Student Data';
		}
	}

	public function edit() {
		global $I2_SQL;
		if($this->op == '') {
		}
		else if($this->op == 'comments') {
			/* Editing comments code */
			$user = new User($this->args['uid']);
			$user->comments = $this->args['comments'];
			redirect('eighth/vcp_schedule/view/uid/'.$this->args['uid']);
		}
		else if($this->op == 'student') {
			/* Editing student code */
			$user = new User($this->args['uid']);
			foreach($this->args['eighth_user_data'] as $key => $value) {
				$user->$key = $value;
			}
		}
	}

	/**
	 * Exports a list of all students who will be out of the building.
	 */
	public function export_csv() {
		global $I2_SQL;
		if ($this->op == '')
			$this->op = 'select';
		// TODO: Make this export all blocks for a given day, instead of a per-block basis.
		if ($this->op == 'select') {
			$this->setup_block_selection();
		} else if ($this->op == 'view') {
			$bid = $this->args['bid'];
			/* We want all rooms where room name starts with Out of Building (ignore case, trim); */
			$aids = $I2_SQL->query('SELECT activityid FROM eighth_block_map LEFT JOIN eighth_rooms ON rooms=rid' .
				' WHERE bid=%d AND LEFT(LTRIM(LOWER(eighth_rooms.name)),15)="out of building"',$bid)->fetch_all_arrays();
			$activities = array();
			foreach ($aids as $aid) {
				$activity = new EighthActivity($aid[0], $bid);
				// Ignore the excused absence, etc.
				if (strncasecmp($activity->name,'z',1) != 0)
					$activities[] = $activity;
			}
			$this->template = 'export_csv.tpl';
			$this->template_args['bid'] = $bid;
			$this->template_args['activities'] = $activities;
			$this->title = 'Out of Building students';
		} else if ($this->op == 'export') {
			$bid = $this->args['bid'];
			/* We want all rooms where room name starts with Out of Building (ignore case, trim); */
			$aids = $I2_SQL->query('SELECT activityid FROM eighth_block_map LEFT JOIN eighth_rooms ON rooms=rid' .
				' WHERE bid=%d AND LEFT(LTRIM(LOWER(eighth_rooms.name)),15)="out of building"',$bid)->fetch_all_arrays();
			$activities = array();
			foreach ($aids as $aid) {
				$activity = new EighthActivity($aid[0], $bid);
				// Ignore the excused absence, etc.
				if (strncasecmp($activity->name,'z',1) != 0)
					$activities[] = $activity;
			}
			Display::stop_display();
			header('Pragma: ');
			header('Content-type: text/csv');
			$datestr = date('Y-m-d-His');
			header("Content-Disposition: attachment; filename=\"EighthOutOfBuild-$datestr.csv\"");
			print "Name,Activity,Block\r\n";
			foreach ($activities as $activity) {
				foreach ($activity->members_obj as $member)
					print '"' . $member->name_comma . '",' . $activity->name . ',' . $activity->block->block . "\r\n";
			}
			return;
		}
	}

	/**
	 * Edit printing settings.
	 */
	public function edit_printers() {
		global $I2_SQL;
		if ($this->op == '') {
			$this->template = 'edit_printers.tpl';
			$this->template_args['printers'] = $I2_SQL->query('SELECT * FROM eighth_printers ORDER BY name')->fetch_all_arrays(Result::ASSOC);
		}
		if ($this->op == 'choose') {
			$I2_SQL->query('UPDATE eighth_printers SET is_selected=0');
			$I2_SQL->query('UPDATE eighth_printers SET is_selected=1 WHERE id=%d', $this->args['printer']);
			redirect('eighth/edit_printers');
		}
		if ($this->op == 'delete') {
			$I2_SQL->query('DELETE FROM eighth_printers WHERE id=%d', $this->args['printer']);
			redirect('eighth/edit_printers');
		}
		if ($this->op == 'add') {
			$I2_SQL->query('INSERT INTO eighth_printers SET ip=%s, name=%s, is_selected=0', trim($this->args['ip']), $this->args['name']);
			redirect('eighth/edit_printers');
		}
	}

	/**
	 * Get the printer IP.
	 */
	public static function printer_ip() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT ip FROM eighth_printers WHERE is_selected=1')->fetch_single_value();
	}

	/**
	 *
	 */
	public function postsigns() {
		global $I2_SQL;

		if($this->op == '') {
			$dat=$I2_SQL->query('SELECT * FROM eighth_postsigns')->fetch_all_arrays(Result::ASSOC);
			$this->template='postsigns.tpl';
			$cids=array();
			for($i=0;$i<count($dat);$i++) {
				$tmpuser=new User($dat[$i]['uid']);
				$dat[$i]['username']=$tmpuser->name;
				if(!in_array($dat[$i]['cid'],array_keys($cids))) {
					$tmpuser=new User($dat[$i]['cid']);
					$cids[$dat[$i]['cid']]=$tmpuser->name;
				}
			}
			$this->template_args['cids']=$cids;
			$this->template_args['data']=$dat;
		} elseif ($this->op=='view') {
		}
	}
}

?>
