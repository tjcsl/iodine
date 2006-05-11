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

	function __construct() {
		self::init_undo();
	}

	private static function undo() {
			  if (count(self::$undo) == 0) {
						 /*
						 ** Nothing to undo
						 */
						 return FALSE;
			  }
			  $undoandredo = array_pop(self::$undo);
			  //array_pop($_SESSION['eighth_redo']);
			  self::undo_exec($undoandredo[0],$undoandredo[1]);
			  array_push(self::$redo,$undoandredo);
			  //array_push($_SESSION['eighth_redo'],$undoandredo);
	}

	/**
	* Helper for undo() and redo(), which are rather similiar.
	*/
	private static function undo_exec($query,$args) {
		global $I2_SQL, $I2_LOG;
		//$I2_LOG->log_file('UNDO/REDO: "'.query.'" -> '.print_r($args,1),6);
		$I2_SQL->query_arr($query,$args);
	}

	public static function redo_transaction() {
		$name = self::get_redo_name();
		if ($name != 'TRANSACTION_END') {
			// Last action was a single-action transaction
			self::redo();
			return;
		}
		self::start_undo_transaction();
		array_pop(self::$redo);
		//array_pop($_SESSION['eighth_redo']);
		while ($name && $name != 'TRANSACTION_START') {
			self::redo();
			$name = self::get_redo_name();
		}
		if (count(self::$redo) > 0) {
			array_pop(self::$redo);
			//array_pop($_SESSION['eighth_redo']);
		}
		self::end_undo_transaction();
	}
	
	public static function undo_transaction() {
		$name = self::get_undo_name();
		if ($name != 'TRANSACTION_END') {
			// Last action was a single-action transaction
			self::undo();
			return;
		}
		self::start_redo_transaction();
		array_pop(self::$undo);
		//array_pop($_SESSION['eighth_undo']);
		while ($name && $name != 'TRANSACTION_START') {
			self::undo();
			$name = self::get_undo_name();
		}
		if (count(self::$undo) > 0) {
			array_pop(self::$undo);
			//array_pop($_SESSION['eighth_undo']);
		}
	}

	public static function start_undo_transaction() {
		array_push(self::$undo,array(NULL,NULL,NULL,NULL,'TRANSACTION_START'));
		//array_push($_SESSION['eighth_undo'],array(NULL,NULL,NULL,NULL,'TRANSACTION_START'));
	}
	
	private static function start_redo_transaction() {
		array_push(self::$redo,array(NULL,NULL,NULL,NULL,'TRANSACTION_START'));
		//array_push($_SESSION['eighth_redo'],array(NULL,NULL,NULL,NULL,'TRANSACTION_START'));
	}
	
	public static function end_undo_transaction() {
		array_push(self::$undo,array(NULL,NULL,NULL,NULL,'TRANSACTION_END'));
		//array_push($_SESSION['eighth_undo'],array(NULL,NULL,NULL,NULL,'TRANSACTION_END'));
	}

	private static function end_redo_transaction() {
		array_push(self::$redo,array(NULL,NULL,NULL,NULL,'TRANSACTION_END'));
		//array_push($_SESSION['eighth_redo'],array(NULL,NULL,NULL,NULL,'TRANSACTION_END'));
	}

	private static function redo() {
			  if (count(self::$redo) == 0) {
						 /*
						 ** Nothing to redo
						 */
						 return FALSE;
			  }
			  $undoandredo = array_pop(self::$redo);
			  //array_pop($_SESSION['eighth_redo']);
			  self::undo_exec($undoandredo[2],$undoandredo[3]);
			  array_push(self::$undo,$undoandredo);
			  //array_push($_SESSION['eighth_undo'],$undoandredo);
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
		d('8th-period redo stack: '.count(self::$redo).' element(s), topped by '.self::get_redo_name(),7);
	}

	public static function undo_off() {
			  self::$doundo = FALSE;
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
	}

	public static function get_undo_name($descend = FALSE) {
		$ct = count(self::$undo);
		if ($ct < 1) {
			return FALSE;
		}
		$name = self::$undo[$ct-1][4];
		while ($descend && $name == 'TRANSACTION_END') {
			$ct--;
			$name = self::$undo[$ct-1][4];
		}
		return $name;
	}

	public static function get_redo_name($descend = FALSE) {
		$ct = count(self::$redo);
		if ($ct < 1) {
			return FALSE;
		}
		$name = self::$redo[$ct-1][4];
		while ($descend && $name == 'TRANSACTION_END') {
			$ct--;
			$name = self::$redo[$ct-1][4];
		}
		return $name;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER;
		$this->args = array();
		$this->admin = self::is_admin();
		$this->template_args['eighth_admin'] = $this->admin;
		if(count($I2_ARGS) <= 1) {
			if (!$this->admin) {
				return FALSE;
			}
			$this->template = 'pane.tpl';
			$this->template_args['help'] = '<h2>8th Period Office Online Menu</h2>From here you can choose a number of operations to administrate the eighth period system.';
			return 'Eighth Period Office Online: Home';
		} else {
			$method = $I2_ARGS[1];
			$this->op = (count($I2_ARGS) % 2 == 1 ? $I2_ARGS[2] : '');
			for($i = count($I2_ARGS) - 1; $i > 2; $i -= 2) {
				$this->args[$I2_ARGS[$i - 1]] = $I2_ARGS[$i];
			}
			$this->args += $_POST;
			if(isset($_SESSION['eighth'])) {
				$this->args += $_SESSION['eighth'];
			}
			if(method_exists($this, $method)) {
				$this->$method();
				$this->template_args['method'] = $method;
				$this->template_args['help'] = $this->help_text;
				if ($this->admin) {
					return "Eighth Period Office Online: {$this->title}";
				} else {
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
		global $I2_ARGS;
		$argstr = implode('/', array_slice($I2_ARGS,1));
		$this->template_args['argstr'] = $argstr;
		$this->template_args['last_undo'] = self::get_undo_name(TRUE);
		$this->template_args['last_redo'] = self::get_redo_name(TRUE);
		$display->disp($this->template, $this->template_args);
	}

	function display_help() {
		redirect($I2_ROOT . 'info/eighth');
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
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($I2_USER->uid, $date, 1));
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
	* @param date $startdate The date from which to show blocks.
	* @param int $daysf  The number of days forward to show blocks.
	*/
	private function setup_block_selection($add = FALSE, $field = NULL, $title = NULL, $startdate = NULL, $daysf = NULL) {
		if ($field === NULL) {
			$field = 'bid';
		}
		if ($title === NULL) {
			$title = 'Select a block:';
		}
		if ($startdate === NULL) {
			if (isSet($this->args['startdate'])) {
				$startdate = $this->args['startdate'];
			} else {
				$startdate = date('Y-m-d');
			}
		}
		if ($daysf === NULL && isSet($this->args['daysforward'])) {
			$daysf = $this->args['daysforward'];
		} else {
			$daysf = 9999;
		}
		$blocks = EighthBlock::get_all_blocks($startdate, $daysf);
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
	private function setup_activity_selection($add = FALSE, $blockid = NULL, $restricted = FALSE, $field = 'aid', $title = 'Select an activity:') {
		$activities = EighthActivity::get_all_activities($blockid, $restricted);
		$this->template = 'activity_selection.tpl';
		$this->template_args['activities'] = $activities;
		if($add) {
			$this->template_args['add'] = TRUE;
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
	private function setup_group_selection($add = FALSE, $title = 'Select a group:') {
		$groups = Group::get_all_groups('eighth');
		$this->template = 'group_selection.tpl';
		$this->template_args['groups'] = $groups;
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
	private function setup_room_selection($add = FALSE, $title = 'Select a room:') {
		$rooms = EighthRoom::get_all_rooms();
		$this->template = 'room_selection.tpl';
		$this->template_args['rooms'] = $rooms;
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
			return "Select an activity";
		}
		else if($this->op == 'group') {
			$this->setup_group_selection();
			$this->template_args['op'] = "commit/bid/{$this->args['bid']}/aid/{$this->args['aid']}";
		}
		else if($this->op == 'commit') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$group = new Group($this->args['gid']);
			$activity->add_members($group->members);
			redirect('eighth');
		}
	}

	/**
	* Clears the undo and redo stacks
	*
	*/
	public static function clear_stack() {
		$_SESSION['eighth_undo'] = array();
		self::$undo = array();
		$_SESSION['eighth_redo'] = array();
		self::$redo = array();
	}

	private function undoit() {
			  global $I2_ARGS;
			  switch ($this->op) {
			  	case 'undo':
					self::undo_transaction();
					break;
			  	case 'redo':
					self::redo_transaction();
					break;
			  	case 'clear':
			  		self::clear_stack();
					break;
				default:
					redirect('eighth');
			  }
			  // Circumvent $args because it turns the path into an associative array
			  $str = implode('/',array_slice($I2_ARGS,3));
			 redirect('eighth/'.$str);
	}

	/**
	* Add, modify, or remove a special group of students
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	private function amr_group() {
		if($this->op == '') {
			$this->setup_group_selection(true);
		}
		else if($this->op == 'add') {
			Group::add_group("" . $this->args['name']);
			redirect("eighth/amr_group");
		}
		else if($this->op == "modify") {
			Group::set_group_name($this->args['gid'],$this->args['name']);
			redirect("eighth/amr_group/view/gid/{$this->args['gid']}");
		}
		else if($this->op == 'remove') {
			Group::delete_group($this->args['gid']);
			redirect('eighth');
		}
		else if($this->op == 'view') {
			$group = new Group($this->args['gid']);
			$this->template = "amr_group.tpl";
			$this->template_args['group'] = $group;
			$this->title = 'View Group (' . substr($group->name,7) . ')';
		}
		else if($this->op == 'add_member') {
			$group = new Group($this->args['gid']);
			$group->add_user($this->args['uid']);
			redirect("eighth/amr_group/view/gid/{$this->args['gid']}");
		}
		else if($this->op == 'remove_member') {
			$group = new Group($this->args['gid']);
			$group->remove_user($this->args['uid']);
			redirect("eighth/amr_group/view/gid/{$this->args['gid']}");
		}
		else if($this->op == 'remove_all') {
			$group = new Group($this->args['gid']);
			$group->remove_all_members();
			redirect("eighth/amr_group/view/gid/{$this->args['gid']}");
		}
		else if($this->op == "add_members") {
			// TODO: Work on adding multiple members
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
			$this->title = 'Alter Permissions to Restricted Activities';
		}
		else if($this->op == 'add_group') {
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
			$activity = new EighthActivity($this->args['aid']);
			$activity->remove_restricted_all();
			redirect("eighth/alt_permissions/view/aid/{$this->args['aid']}");
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
			$this->template_args['activity_from'] = new EighthActivity($this->args['aid_from'], $this->args['bid_from']);
			$this->template_args['activity_to'] = new EighthActivity($this->args['aid_to'], $this->args['bid_to']);
			$this->title = 'Confirm Moving Students';
		}
		else if($this->op == 'commit') {
			$activity_from = new EighthActivity($this->args['aid_from'], $this->args['bid_from']);
			$activity_to = new EighthActivity($this->args['aid_to'], $this->args['bid_to']);
			$activity_to->add_members($activity_from->members);
			$activity_from->remove_all();
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
		if($this->op == "") {
			$this->setup_activity_selection(TRUE);
		}
		else if($this->op == "view") {
			$this->template = "amr_activity.tpl";
			$this->template_args = array("activity" => new EighthActivity($this->args['aid']));
			$this->title = "View Activities";
		}
		else if($this->op == "add") {
			$aid = EighthActivity::add_activity($this->args['name']);
			redirect("eighth/amr_activity/view/aid/{$aid}");
		}
		else if($this->op == 'modify') {
			$activity = new EighthActivity($this->args['aid']);
			$activity->name = $this->args['name'];
			$activity->sponsors = $this->args['sponsors'];
			$activity->rooms = $this->args['rooms'];
			$activity->description = $this->args['description'];
			$activity->restricted = ($this->args['restricted'] == "on");
			$activity->presign = ($this->args['presign'] == "on");
			$activity->oneaday = ($this->args['oneaday'] == "on");
			$activity->bothblocks = ($this->args['bothblocks'] == "on");
			$activity->sticky = ($this->args['sticky'] == "on");
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == "remove") {
			EighthActivity::remove_activity($this->args['aid']);
			redirect("eighth");
		}
		else if($this->op == "select_sponsor") {
			$this->setup_sponsor_selection();
			$this->template_args['op'] = "add_sponsor/aid/{$this->args['aid']}";
		}
		else if($this->op == "add_sponsor") {
			$activity = new EighthActivity($this->args['aid']);
			$activity->add_sponsor($this->args['sid']);
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == "remove_sponsor") {
			$activity = new EighthActivity($this->args['aid']);
			$activity->remove_sponsor($this->args['sid']);
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == "select_room") {
			$this->setup_room_selection();
			$this->template_args['op'] = "add_room/aid/{$this->args['aid']}";
		}
		else if($this->op == "add_room") {
			$activity = new EighthActivity($this->args['aid']);
			$activity->add_room($this->args['rid']);
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
		}
		else if($this->op == "remove_room") {
			$activity = new EighthActivity($this->args['aid']);
			$activity->remove_room($this->args['rid']);
			redirect("eighth/amr_activity/view/aid/{$this->args['aid']}");
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
		if($this->op == '') {
			$this->setup_room_selection(true);
		}
		else if($this->op == 'view') {
			$this->template = "amr_room.tpl";
			$this->template_args['room'] = new EighthRoom($this->args['rid']);
			$this->title = 'View Rooms';
		}
		else if($this->op == 'add') {
			$rid = EighthRoom::add_room($this->args['name'], $this->args['capacity']);
			//redirect("eighth/amr_room/view/rid/{$rid}");
			redirect('eighth/amr_room');
		}
		else if($this->op == 'modify') {
			if ($this->args['modify_or_remove'] == 'modify') {
				$room = new EighthRoom($this->args['rid']);
				$room->name = $this->args['name'];
				$room->capacity = $this->args['capacity'];
				redirect("eighth/amr_room/view/rid/{$this->args['rid']}");
			} else if ($this->args['modify_or_remove'] == 'remove') {
				EighthRoom::remove_room($this->args['rid']);
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
		if($this->op == "") {
			$this->setup_sponsor_selection(true);
		}
		else if($this->op == "view") {
			$this->template = "amr_sponsor.tpl";
			$this->template_args['sponsor'] = new EighthSponsor($this->args['sid']);
			$this->title = "View Sponsors";
		}
		else if($this->op == "add") {
			$sid = EighthSponsor::add_sponsor($this->args['fname'], $this->args['lname']);
			redirect("eighth/amr_sponsor");
		}
		else if($this->op == "modify") {
			$sponsor = new EighthSponsor($this->args['sid']);
			$sponsor->fname = $this->args['fname'];
			$sponsor->lname = $this->args['lname'];
			redirect("eighth/amr_sponsor");
		}
		else if($this->op == "remove") {
			EighthSponsor::remove_sponsor($this->args['sid']);
			redirect("eighth");
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
		if($this->op == "") {
			$this->setup_activity_selection();
			$this->template = 'sch_activity_choose.tpl';
		}
		else if($this->op == "view") {
			$this->template = 'sch_activity.tpl';
			$this->template_args['rooms'] = EighthRoom::get_all_rooms();
			$this->template_args['sponsors'] = EighthSponsor::get_all_sponsors();
			$this->template_args['block_activities'] = EighthSchedule::get_activity_schedule($this->args['aid']);
			$this->template_args['activities'] = EighthActivity::get_all_activities();
			$this->template_args['act'] = new EighthActivity($this->args['aid']);
			$this->title = 'Schedule an Activity (' . $this->template_args['act']->name_r  . ')';
		}
		else if($this->op == 'modify') {
			foreach($this->args['modify'] as $bid) {
				if($this->args['activity_status'][$bid] == 'CANCELLED') {
					EighthActivity::cancel($bid, $this->args['aid']);
				}
				else if($this->args['activity_status'][$bid] == 'UNSCHEDULED') {
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
			}
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
			$this->template_args['op'] = "activity";
		}
		else if($this->op == 'activity') {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "view/bid/{$this->args['bid']}";
		}
		else if($this->op == 'view') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->template = 'vp_roster.tpl';
			$this->template_args['activity'] = $activity;
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
			$this->template_args['utilizations'] = EighthRoom::get_utilization($this->args['bid'], $this->args['include'], !empty($this->args['overbooked']));
			$this->title = "View Room Utilization";
		}
		else if($this->op == 'format') {
			$this->setup_format_selection('vp_room', 'Room Utilization', array("bid" => $this->args['bid']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_room_utilization($this->args['bid'], $this->args['format']);
		}
	}

	/**
	* Cancel/set comments/advertize for an activity
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function cancel_activity() {
		if($this->op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "activity";
		}
		else if($this->op == "activity") {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "view/bid/{$this->args['bid']}";
		}
		else if($this->op == "view") {
			$this->template = "cancel_activity.tpl";
			$this->template_args['activity'] = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->title = "Cancel an Activity";
		}
		else if($this->op == "update") {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$activity->comment = $this->args['comment'];
			$activity->advertisement = $this->args['advertisement'];
			$activity->cancelled = ($this->args['cancelled'] == "on");
			redirect("eighth/cancel_activity/view/bid/{$this->args['bid']}/aid/{$this->args['aid']}");
		}
	}

	/**
	* Room assignment sanity check
	*
	* @access private
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
			$this->template_args['sponsorconflicts'] = EighthSponsor::get_conflicts($this->args['bid']);
			$this->title = 'Room Assignment Sanity Check';
		}
	}

	/**
	* View or print sponsor schedule
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vp_sponsor() {
		if($this->op == "") {
			$this->setup_sponsor_selection();
		}
		else if($this->op == "view") {
			$sponsor = new EighthSponsor($this->args['sid']);
			$this->template = 'vp_sponsor.tpl';
			$this->template_args['sponsor'] = $sponsor;
			$this->template_args['activities'] = $sponsor->schedule;
			$this->title = 'View Sponsor Schedule';
		}
		else if($this->op == "format") {
			$this->setup_format_selection("vp_sponsor", "Sponsor Schedule", array("sid" => $this->args['sid']));
		}
		else if($this->op == "print") {
			EighthPrint::print_sponsor_schedule($this->args['sid'], $this->args['format']);
		}
	}

	/**
	* Reschedule students by student ID for a single activity
	*
	* @access private
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
			$this->template = 'res_student.tpl';
			$this->template_args['block'] = new EighthBlock($this->args['bid']);
			$this->template_args['activities'] = EighthActivity::get_all_activities($this->args['bid']);
			$this->template_args['op'] = "user/bid/{$this->args['bid']}";
			$this->template_args['act'] = new EighthActivity($this->args['aid']);
			if(isset($this->args['studentId'])) {
				$this->template_args['user'] = new User(User::studentid_to_uid($this->args['studentId']));
				if (!$this->template_args['user']->is_valid()) {
					redirect('eighth/res_student/user/bid/'.$this->args['bid'].'/aid/'.$this->args['aid']);
				}
			}
			$this->title = 'Reschedule a Student';
		}
		else if($this->op == 'reschedule') {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$activity->add_member($this->args['uid']);
			redirect("eighth/res_student/user/bid/{$this->args['bid']}/aid/{$this->args['aid']}");
		}
	}

	/**
	* View, change, or print attendance data
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vcp_attendance() {
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = "activity";
		}
		else if($this->op == "activity") {
			$this->setup_activity_selection(FALSE, $this->args['bid']);
			$this->template_args['op'] = "view/bid/{$this->args['bid']}";
		}
		else if($this->op == "view") {
			$this->template = "vcp_attendance.tpl";
			$this->template_args['activity'] = new EighthActivity($this->args['aid'], $this->args['bid']);
			$this->template_args['absentees'] = EighthSchedule::get_absentees($this->args['bid'], $this->args['aid']);
			$this->title = "View Attendance";
		}
		else if($this->op == "update") {
			$activity = new EighthActivity($this->args['aid'], $this->args['bid']);
			$members = $activity->members;
			foreach($members as $member) {
				if(in_array($member, $this->args['absentees'])) {
					EighthSchedule::add_absentee($this->args['bid'], $member);
				}
				else {
					EighthSchedule::remove_absentee($this->args['bid'], $member);
				}
			}
			$activity->attendancetaken = TRUE;
			redirect("eighth/vcp_attendance/view/bid/{$this->args['bid']}/aid/{$this->args['aid']}");
		}
		else if($this->op == "format") {
			$this->setup_format_selection("vcp_attendance", "Attendance Data", array("aid" => $this->args['aid'], "bid" => $this->args['bid']));
		}
		else if($this->op == "print") {
			EighthPrint::print_attendance_data($this->args['aid'], $this->args['bid'], $this->args['format']);
		}
	}

	/**
	* Enter TA absences by student ID
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function ent_attendance() {
		if($this->op == '') {
			$this->setup_block_selection();
			$this->template_args['op'] = "user";
		}
		else if($this->op == 'user') {
			$this->template = 'ent_attendance.tpl';
			$block = new EighthBlock($args['bid']);
			$this->template_args['date'] = $block->date;
			$this->template_args['block'] = $block->block;
			$this->template_args['bid'] = $this->args['bid'];
			if (isSet($this->args['lastuid'])) {
				$this->template_args['lastuid'] = $this->args['lastuid'];
				$user = new User($this->args['lastuid']);
				$this->template_args['lastname'] = $user->name;
				$this->template_args['studentid'] = $user->studentid;
			}
			$this->title = 'Enter TA Attendance';
		}
		else if($this->op == "mark_absent") {
			EighthSchedule::add_absentee($this->args['bid'], $this->args['uid']);
			redirect('eighth/ent_attendance/user/bid/'.$this->args['bid'].'/lastuid/'.$this->args['uid']);
		} else if ($this->op == 'unmark_absent') {
			EighthSchedule::remove_absentee($this->args['bid'], $this->args['uid']);
			redirect('eighth/ent_attendance/user/bid/'.$this->args['bid']);
		}
	}

	/**
	* View or print a list of delinquent students
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vp_delinquent() {
		// TODO: Sorting and exporting for all
		if($this->op == '') {
			// TODO: Print a list of delinquents
			$lower = 1;
			$upper = 1000;
			$start = null;
			$end = null;
			if(!empty($this->args['lower']) && ctype_digit($this->args['lower'])) {
				$lower = $this->args['lower'];
			}
			if(!empty($this->args['upper']) && ctype_digit($this->args['upper'])) {
				$upper = $this->args['upper'];
			}
			if(!empty($this->args['start'])) {
				$start = $this->args['start'];
			}
			if(!empty($this->args['end'])) {
				$end = $this->args['end'];
			}
			$delinquents = EighthSchedule::get_delinquents($lower, $upper, $start, $end);
			$this->template_args['students'] = array();
			$this->template_args['absences'] = array();
			for($i = 0; $i < count($delinquents); $i++) {
				$this->template_args['students'][] = $delinquents[$i]['userid'];
				$this->template_args['absences'][] = $delinquents[$i]['absences'];
			}
			$this->template_args['students'] = User::id_to_user($this->template_args['students']);
			$this->template = "vp_delinquent.tpl";
			$this->title = "View Delinquent Students";
		}
		else if($this->op == "query") {
			// TODO: Query the delinquents
			$this->template = "vp_delinquent.tpl";
			$this->title = "Query Delinquent Students";
		}
		else if($this->op == "sort") {
		}
	}

	/**
	* Finalize student schedules
	*
	* @access private
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
			$block->locked = TRUE;
			redirect('eighth/fin_schedules');
		}
	 	else if($this->op == 'unlock') {
			$block = new EighthBlock($this->args['bid']);
			$block->locked = FALSE;
			redirect('eighth/fin_schedules');
		}
	}

	/**
	* Print activity rosters
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function prn_attendance() {
		if($this->op == '') {
			$this->template_args['op'] = 'format';
			$this->setup_block_selection();
		}
		else if($this->op == 'confirm') {

		}
		else if($this->op == 'format') {
			$this->setup_format_selection('prn_attendance', 'Activity Rosters', array('bid' => $this->args['bid']));
		}
		else if($this->op == 'print') {
			EighthPrint::print_activity_rosters(explode(',', $this->args['bid']), $this->args['format']);
		}
	}

	/**
	* Change starting date
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	* @todo Figure out where to store the starting date, in config.ini for now.
	*/
	public function chg_start() {
		if($this->op == '') {
			$date = '';
			if(isset($_SESSION['eighth']['start_date'])) {
				$date = $_SESSION['eighth']['start_date'];
			}
			$this->template_args['date'] = $date;
			$this->template = 'chg_start.tpl';
			$this->title = 'Change Start Date';
		}
		else if($this->op == 'change') {
			$_SESSION['eighth']['start_date'] = $this->args['date'];
			redirect('eighth');
		}
	}

	/**
	* Add or remove 8th period block from system
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function ar_block() {
		if($this->op == '') {
			$this->template = 'ar_block.tpl';
			$this->template_args['blocks'] = EighthBlock::get_all_blocks(i2config_get('start_date', date('Y-m-d'), 'eighth'));
			$this->title = 'Add/Remove Block';
		}
		else if($this->op == 'add') {
			foreach($this->args['blocks'] as $block) {
				EighthBlock::add_block("{$this->args['Year']}-{$this->args['Month']}-{$this->args['Day']}", $block);
			}
			redirect('eighth/ar_block');
		}
		else if($this->op == 'remove') {
			EighthBlock::remove_block($this->args['bid']);
			redirect('eighth/ar_block');
		}
	}
	
	/**
	* Repair broken schedules
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	* @todo Figure out what voodoo this does
	*/
	public function rep_schedules() {
		global $I2_SQL;
		if($this->op == '') {
			$bids = flatten($I2_SQL->query('SELECT bid FROM blocks')->fetch_all_arrays(MYSQL_NUM));
			foreach($bids as $bid) {
				$activity = new EighthActivity(1);
				EighthSchedule::schedule_activity($bid, $activity->aid, $activity->sponsors, $activity->rooms);
				$uids = flatten($I2_SQL->query('SELECT uid FROM user WHERE uid NOT IN (SELECT userid FROM activity_map WHERE bid=%d)', $bid)->fetch_all_arrays(MYSQL_NUM));
				$activity->add_members($uids, false, $bid);
			}
			redirect("eighth");
		}
	}

	/**
	* View, change, or print student schedule
	*
	* @access private
	* @param string $this->op The operation to do.
	* @param array $this->args The arguments for the operation.
	*/
	public function vcp_schedule() {
		global $I2_SQL;
		if($this->op == '') {
			$this->template = 'vcp_schedule.tpl';
			if(!empty($this->args['uid'])) {
				$this->template_args['users'] = User::id_to_user(flatten($I2_SQL->query('SELECT uid FROM user WHERE uid LIKE %d', $this->args['uid'])->fetch_all_arrays(Result::NUM)));
			}
			else {
				$this->template_args['users'] = User::search_info("{$this->args['fname']} {$this->args['lname']}");
			}
			if(count($this->template_args['users']) == 1) {
				redirect("eighth/vcp_schedule/view/uid/{$this->template_args['users'][0]->uid}");
			}
			usort($this->template_args['users'], array('User', 'name_cmp'));
			$this->title = 'Search Students';
		}
		else if($this->op == 'view') {
			if(!isset($this->args['start_date'])) {
				$this->args['start_date'] = NULL;
			}
			$this->template_args['start_date'] = ($this->args['start_date'] ? strtotime($this->args['start_date']) : time());
			$user = new User($this->args['uid']);
			$this->template_args['user'] = $user;
			$this->template_args['comments'] = $user->comments;
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($this->args['uid'], $this->args['start_date']));
			$this->template_args['absences'] = EighthSchedule::get_absences($this->args['uid']);
			$this->template_args['absence_count'] = count($this->template_args['absences']);
			$this->template = 'vcp_schedule_view.tpl';
			$this->title = 'View Schedule';
		}
		else if($this->op == 'format') {
			if(!isset($this->args['start_date'])) {
				$this->args['start_date'] = NULL;
			}
			$this->setup_format_selection('vcp_schedule', 'Student Schedule', array('uid' => $this->args['uid']) + ($this->args['start_date'] ? array('start_date' => $this->args['start_date']) : array()), TRUE);
		}
		else if($this->op == 'print') {
			EighthPrint::print_student_schedule($this->args['uid'], $this->args['start_date'], $this->args['format']);
		}
		else if($this->op == 'choose') {
			$valids = array();
			$validdata = array();
			$this->template_args['bids'] = (is_array($this->args['bids']) ? implode(',', $this->args['bids']) : $this->args['bids']);
			/*
			** Get only activities common to all blocks.
			*/
			if (is_array($this->args['bids'])) {
				foreach ($this->args['bids'] as $bid) {
					$thisblock = EighthActivity::get_all_activities($bid,FALSE);
					foreach ($thisblock as $activity) {
						if (!isset($valids[$activity->aid])) {
							$valids[$activity->aid] = 1;
							$validdata[] = $activity;
						}
					}
				}
				$this->template_args['activities'] = $validdata;		
			} else {
				$this->template_args['activities'] = EighthActivity::get_all_activities($this->args['bids'],FALSE);
			}
			$this->template_args['uid'] = $this->args['uid'];
			$this->template = 'vcp_schedule_choose.tpl';
			$this->title = 'Choose an Activity';
		}
		else if($this->op == 'change') {
			if ($this->args['bids'] && $this->args['aid']) {
				$status = array();
				$bids = explode(',', $this->args['bids']);
				foreach($bids as $bid) {
					if(EighthSchedule::is_activity_valid($this->args['aid'], $bid)) {
						$activity = new EighthActivity($this->args['aid'], $bid);
						$ret = $activity->add_member(new User($this->args['uid']), isset($args['force']));
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
						if(count($act_status) != 0) {
							$act_status['activity'] = $activity;
							$status[$bid] = $act_status;
						}
					}
				}
				if(count($status) == 0) {
					redirect("eighth/vcp_schedule/view/uid/{$this->args['uid']}");
				}
				$this->template = 'vcp_schedule_change.tpl';
				$this->template_args['status'] = $status;
				$this->template_args['uid'] = $args['uid'];
				$this->template_args['bids'] = $args['bids'];
				$this->template_args['aid'] = $args['aid'];
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
			$this->template_args['uid'] = $this->args['uid'];
			$this->template_args['name'] = $user->fullname_comma;
			$this->template_args['admin'] = $this->admin;
			$this->template = 'vcp_schedule_absences.tpl';
		}
		else if($this->op == 'remove_absence') {
			EighthSchedule::remove_absentee($this->args['bid'], $this->args['uid']);
			redirect('eighth');
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
}

?>
