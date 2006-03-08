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
	private $title = "";

	/**
	* The help text for the page
	*/
	private $help_text = "";

	/**
	* Template for the specified action
	*/
	private $template = "eighth_pane.tpl";

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS;
		$args = array();
		if(count($I2_ARGS) <= 1) {
			$this->template = "eighth_pane.tpl";
			$this->template_args['help'] = "<h2>8th Period Office Online Menu</h2>From here you can choose a number of operations to administrate the eighth period system.";
			return "Eighth Period Office Online: Home";
		}
		else if(count($I2_ARGS) == 2 || (count($I2_ARGS) % 2) != 0) {
			$method = $I2_ARGS[1];
			$op = (count($I2_ARGS) > 2 ? $I2_ARGS[2] : "");
			for($i = 3; $i < count($I2_ARGS); $i += 2) {
				$args[$I2_ARGS[$i]] = $I2_ARGS[$i + 1];
			}
			$args += $_POST;
			if(method_exists($this, $method)) {
				$this->$method($op, $args);
				$this->template_args['method'] = $method;
				$this->template_args['help'] = $this->help_text;
				return "Eighth Period Office Online: {$this->title}";
			}
			else {
				$this->template = "eighth_test_pane.tpl";
				$this->template_args = array("method" => $method, "args" => $args);
			}
		}
		return array("Error", "Error");
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		global $I2_USER;
		$date = EighthSchedule::get_next_date();
		$this->template_args['absent'] = count(EighthSchedule::get_absences($I2_USER->uid));
		if($date) {
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($I2_USER->uid, $date, 1));
		}
		else {
			$this->template_args['activities'] = array();
		}
		$dates = array($date => date("n/j/Y", @strtotime($date)), date("Y-m-d") => "Today", date("Y-m-d", time() + 3600 * 24) => "Tomorrow", "" => "None Scheduled");
		return "8th Period: {$dates[$date]}";
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp("eighth_box.tpl", $this->template_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Eighth";
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

	/**
	* Sets up for displaying the block selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the block list.
	*/
	private function setup_block_selection($add = FALSE, $field = "bid", $title = "Select a block:") {
		$blocks = EighthBlock::get_all_blocks(i2config_get("start_date", date("Y-m-d"), "eighth"));
		$this->template = "eighth_block_selection.tpl";
		$this->template_args += array("blocks" => $blocks, "add" => $add);
		$this->template_args['title'] = $title;
		$this->template_args['field'] = $field;
		$this->title = "Select a Block";
		$this->help_text = "Select a Block";
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
	private function setup_activity_selection($add = FALSE, $blockid = NULL, $restricted = FALSE, $field = "aid", $title = "Select an activity:") {
		$activities = EighthActivity::get_all_activities($blockid, $restricted);
		$this->template = "eighth_activity_selection.tpl";
		$this->template_args += array("activities" => $activities, "add" => $add);
		$this->template_args['title'] = $title;
		$this->template_args['field'] = $field;
		$this->title = "Select an Activity";
		$this->help_text = "Select an Activity";
	}

	/**
	* Sets up for displaying the group selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the group list.
	*/
	private function setup_group_selection($add = FALSE, $title = "Select a group:") {
		$groups = Group::get_all_groups("eighth");
		$this->template = "eighth_group_selection.tpl";
		$this->template_args += array("groups" => $groups, "add" => $add);
		$this->template_args['title'] = $title;
		$this->title = "Select a Group";
		$this->help_text = "Select a Group";
	}

	/**
	* Sets up for displaying the room selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the room list.
	*/
	private function setup_room_selection($add = FALSE, $title = "Select a room:") {
		$rooms = EighthRoom::get_all_rooms();
		$this->template = "eighth_room_selection.tpl";
		$this->template_args += array("rooms" => $rooms, "add" => $add);
		$this->template_args['title'] = $title;
		$this->title = "Select a Room";
		$this->help_text = "Select a Room";
	}

	/**
	* Sets up for displaying the sponsor selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the sponsor list.
	*/
	private function setup_sponsor_selection($add = FALSE, $title = "Select a sponsor:") {
		$sponsors = EighthSponsor::get_all_sponsors();
		$this->template = "eighth_sponsor_selection.tpl";
		$this->template_args += array("sponsors" => $sponsors, "add" => $add);
		$this->template_args['title'] = $title;
		$this->title = "Select a Sponsor";
		$this->help_text = "Select a Sponsor";
	}
	
	/**
	* Sets up for displaying the printing format selection screen.
	*
	* @access private
	* @param string $module The module that we are printing from.
	*/
	private function setup_format_selection($module, $title = "", $args = array(), $user = FALSE) {
		$this->template = "eighth_format_selection.tpl";
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
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function reg_group($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "activity";
		}
		else if($op == "activity") {
			$this->setup_activity_selection(FALSE, $args['bid']);
			$this->template_args['op'] = "group/bid/{$args['bid']}";
			return "Select an activity";
		}
		else if($op == "group") {
			$this->setup_group_selection();
			$this->template_args['op'] = "commit/bid/{$args['bid']}/aid/{$args['aid']}";
		}
		else if($op == "commit") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$group = new Group($args['gid']);
			$activity->add_members($group->members);
			redirect("eighth");
		}
	}

	/**
	* Add, modify, or remove a special group of students
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function amr_group($op, $args) {
		if($op == "") {
			$this->setup_group_selection(true);
		}
		else if($op == "add") {
			Group::add_group("eighth_" . $args['name']);
			redirect("eighth");
		}
		else if($op == "modify") {
			Group::set_group_name($args['gid'],$args['name']);
			redirect("eighth/amr_group/view/gid/{$args['gid']}");
		}
		else if($op == 'remove') {
			Group::delete_group($args['gid']);
			redirect("eighth");
		}
		else if($op == 'view') {
			$group = new Group($args['gid']);
			$this->template = "eighth_amr_group.tpl";
			$this->template_args['group'] = $group;
			$this->title = 'View Group (' . substr($group->name,7) . ')';
		}
		else if($op == 'add_member') {
			$group = new Group($args['gid']);
			$group->add_user($args['uid']);
			redirect("eighth/amr_group/view/gid/{$args['gid']}");
		}
		else if($op == "remove_member") {
			$group = new Group($args['gid']);
			$group->remove_user($args['uid']);
			redirect("eighth/amr_group/view/gid/{$args['gid']}");
		}
		else if($op == "remove_all") {
			$group = new Group($args['gid']);
			$group->remove_all_members();
			redirect("eighth/amr_group/view/gid/{$args['gid']}");
		}
		else if($op == "add_members") {
			// TODO: Work on adding multiple members
		}
	}
	
	/**
	* Add students to a restricted activity
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	* @todo Work on restricted activities and permissions
	*/
	private function alt_permissions($op, $args) {
		if($op == "") {
			$this->setup_activity_selection(FALSE, NULL, TRUE);
		}
		else if($op == "view") {
			$this->template = "eighth_alt_permissions.tpl";
			$this->template_args['activity'] = new EighthActivity($args['aid']);
			$this->template_args['groups'] = Group::get_all_groups("eighth");
			$this->title = "Alter Permissions to Restricted Activities";
		}
		else if($op == "add_group") {
			$activity = new EighthActivity($args['aid']);
			$group = new Group($args['gid']);
			$activity->add_restricted_members($group->members);
			redirect("eighth/alt_permissions/view/aid/{$args['aid']}");
		}
		else if($op == "add_member") {
			$activity = new EighthActivity($args['aid']);
			$activity->add_restricted_member($args['uid']);
			redirect("eighth/alt_permissions/view/aid/{$args['aid']}");
		}
		else if($op == "remove_member") {
			$activity = new EighthActivity($args['aid']);
			$activity->remove_restricted_member($args['uid']);
			redirect("eighth/alt_permissions/view/aid/{$args['aid']}");
		}
		else if($op == "remove_all") {
			$activity = new EighthActivity($args['aid']);
			$activity->remove_restricted_all();
			redirect("eighth/alt_permissions/view/aid/{$args['aid']}");
		}
	}

	/**
	* Switch all the students in one activity into another
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function people_switch($op, $args) {
		if($op == "") {
			$this->setup_block_selection(FALSE, "bid_from");
			$this->template_args['op'] = "activity_from";
			$this->title = "Select a Block to Move From";
		}
		else if($op == "activity_from") {
			$this->setup_activity_selection(FALSE, $args['bid_from'], FALSE, "aid_from", "From this activity:");
			$this->template_args['op'] = "activity_to/bid_from/{$args['bid_from']}/bid_to/{$args['bid_from']}";
			$this->title = "Select an Activity to Move From";
		}
		else if($op == "block_to") {
			$this->setup_block_selection(FALSE, "bid_to");
			$this->template_args['op'] = "activity_to/bid_from/{$args['bid_from']}/aid_from/{$args['aid_from']}";
			$this->title = "Select a Block to Move to";
		}
		else if($op == "activity_to") {
			$this->setup_activity_selection(FALSE, $args['bid_to'], FALSE, "aid_to", "To this activity:");
			$this->template_args['op'] = "confirm/bid_from/{$args['bid_from']}/aid_from/{$args['aid_from']}/bid_to/{$args['bid_to']}";
			$this->title = "Select an Activity to Move to";
		}
		else if($op == "confirm") {
			if($args['aid_from'] == $args['aid_to']) {
				redirect("eighth/people_switch/activity_to/bid_from/{$args['bid_from']}/aid_from/{$args['aid_from']}/bid_to/{$args['bid_to']}");
			}
			$this->template = "eighth_people_switch.tpl";
			$this->template_args['activity_from'] = new EighthActivity($args['aid_from'], $args['bid_from']);
			$this->template_args['activity_to'] = new EighthActivity($args['aid_to'], $args['bid_to']);
			$this->title = "Confirm Moving Students";
		}
		else if($op == "commit") {
			$activity_from = new EighthActivity($args['aid_from'], $args['bid_from']);
			$activity_to = new EighthActivity($args['aid_to'], $args['bid_to']);
			$activity_to->add_members($activity_from->members);
			$activity_from->remove_all();
			redirect("eighth");
		}
	}

	/**
	* Add, modify, or remove an activity
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function amr_activity($op, $args) {
		if($op == "") {
			$this->setup_activity_selection(TRUE);
		}
		else if($op == "view") {
			$this->template = "eighth_amr_activity.tpl";
			$this->template_args = array("activity" => new EighthActivity($args['aid']));
			$this->title = "View Activities";
		}
		else if($op == "add") {
			$aid = EighthActivity::add_activity($args['name']);
			redirect("eighth/amr_activity/view/aid/{$aid}");
		}
		else if($op == 'modify') {
			$activity = new EighthActivity($args['aid']);
			$activity->name = $args['name'];
			$activity->sponsors = $args['sponsors'];
			$activity->rooms = $args['rooms'];
			$activity->description = $args['description'];
			$activity->restricted = ($args['restricted'] == "on");
			$activity->presign = ($args['presign'] == "on");
			$activity->oneaday = ($args['oneaday'] == "on");
			$activity->bothblocks = ($args['bothblocks'] == "on");
			$activity->sticky = ($args['sticky'] == "on");
			redirect("eighth/amr_activity/view/aid/{$args['aid']}");
		}
		else if($op == "remove") {
			EighthActivity::remove_activity($args['aid']);
			redirect("eighth");
		}
		else if($op == "select_sponsor") {
			$this->setup_sponsor_selection();
			$this->template_args['op'] = "add_sponsor/aid/{$args['aid']}";
		}
		else if($op == "add_sponsor") {
			$activity = new EighthActivity($args['aid']);
			$activity->add_sponsor($args['sid']);
			redirect("eighth/amr_activity/view/aid/{$args['aid']}");
		}
		else if($op == "remove_sponsor") {
			$activity = new EighthActivity($args['aid']);
			$activity->remove_sponsor($args['sid']);
			redirect("eighth/amr_activity/view/aid/{$args['aid']}");
		}
		else if($op == "select_room") {
			$this->setup_room_selection();
			$this->template_args['op'] = "add_room/aid/{$args['aid']}";
		}
		else if($op == "add_room") {
			$activity = new EighthActivity($args['aid']);
			$activity->add_room($args['rid']);
			redirect("eighth/amr_activity/view/aid/{$args['aid']}");
		}
		else if($op == "remove_room") {
			$activity = new EighthActivity($args['aid']);
			$activity->remove_room($args['rid']);
			redirect("eighth/amr_activity/view/aid/{$args['aid']}");
		}
	}

	/**
	* Add, modify, or remove a room
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function amr_room($op, $args) {
		if($op == "") {
			$this->setup_room_selection(true);
		}
		else if($op == "view") {
			$this->template = "eighth_amr_room.tpl";
			$this->template_args['room'] = new EighthRoom($args['rid']);
			$this->title = "View Rooms";
		}
		else if($op == "add") {
			EighthRoom::add_room($args['name'], $args['capacity']);
		}
		else if($op == "modify") {
			if ($args['modify_or_remove'] == 'modify') {
				$room = new EighthRoom($args['rid']);
				$room->name = $args['name'];
				$room->capacity = $args['capacity'];
				redirect("eighth/amr_room/view/rid/{$args['rid']}");
			} else if ($args['modify_or_remove'] == 'remove') {
				EighthRoom::remove_room($args['rid']);
				redirect("eighth");
			}
		}
	}

	/**
	* Add, modify, or remove an activity sponsor
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function amr_sponsor($op, $args) {
		if($op == "") {
			$this->setup_sponsor_selection(true);
		}
		else if($op == "view") {
			$this->template = "eighth_amr_sponsor.tpl";
			$this->template_args['sponsor'] = new EighthSponsor($args['sid']);
			$this->title = "View Sponsors";
		}
		else if($op == "add") {
			$sid = EighthSponsor::add_sponsor($args['fname'], $args['lname']);
			redirect("eighth/amr_sponsor/view/sid/{$sid}");
		}
		else if($op == "modify") {
			$sponsor = new EighthSponsor($args['sid']);
			$sponsor->fname = $args['fname'];
			$sponsor->lname = $args['lname'];
			redirect("eighth/amr_sponsor/view/sid/{$args['sid']}");
		}
		else if($op == "remove") {
			EighthSponsor::remove_sponsor($args['sid']);
			redirect("eighth");
		}
	}

	/**
	* Schedule an activity for eighth period
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function sch_activity($op, $args) {
		if($op == "") {
			$this->setup_activity_selection();
		}
		else if($op == "view") {
			$this->template = "eighth_sch_activity.tpl";
			$this->template_args['rooms'] = EighthRoom::get_all_rooms();
			$this->template_args['sponsors'] = EighthSponsor::get_all_sponsors();
			$this->template_args['activities'] = EighthSchedule::get_activity_schedule($args['aid']);
			$this->template_args['aid'] = $args['aid'];
			$this->title = "Schedule an Activity";
		}
		else if($op == "modify") {
			foreach($args['modify'] as $bid) {
				if($args['activity_status'][$bid] == "CANCELLED") {
					EighthActivity::cancel($bid, $args['aid']);
				}
				else if($args['activity_status'][$bid] == "UNSCHEDULED") {
					EighthSchedule::unschedule_activity($bid, $args['aid']);
				}
				else {
					EighthSchedule::schedule_activity($bid, $args['aid'], $args['sponsor_list'][$bid], $args['room_list'][$bid], $args['comments'][$bid]);
				}
			}
			redirect("eighth/sch_activity/view/aid/{$args['aid']}");
		}
	}

	/**
	* View or print a class roster
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function vp_roster($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "activity";
		}
		else if($op == "activity") {
			$this->setup_activity_selection(FALSE, $args['bid']);
			$this->template_args['op'] = "view/bid/{$args['bid']}";
		}
		else if($op == "view") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$this->template = "eighth_vp_roster.tpl";
			$this->template_args['activity'] = $activity;
			$this->title = "View Roster";
		}
		else if($op == "format") {
			$this->setup_format_selection("vp_roster", "Class Roster", array("aid" => $args['aid'], "bid" => $args['bid']));
		}
		else if($op == "print") {
			EighthPrint::print_class_roster($args['aid'], $args['bid'], $args['format']);
		}
	}

	/**
	* View or print the utilization of a room
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	private function vp_room($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "search";
		}
		else if($op == "search") {
			$this->template = "eighth_vp_room_search.tpl";
			$this->template_args['bid'] = $args['bid'];
			$this->title = "Search Room Utilization";
		}
		else if($op == "view") {
			$this->template = "eighth_vp_room_view.tpl";
			$this->template_args['block'] = new EighthBlock($args['bid']);
			$this->template_args['utilizations'] = EighthRoom::get_utilization($args['bid'], $args['include'], !empty($args['overbooked']));
			$this->title = "View Room Utilization";
		}
	}

	/**
	* Cancel/set comments/advertize for an activity
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function cancel_activity($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "activity";
		}
		else if($op == "activity") {
			$this->setup_activity_selection(FALSE, $args['bid']);
			$this->template_args['op'] = "view/bid/{$args['bid']}";
		}
		else if($op == "view") {
			$this->template = "eighth_cancel_activity.tpl";
			$this->template_args['activity'] = new EighthActivity($args['aid'], $args['bid']);
			$this->title = "Cancel an Activity";
		}
		else if($op == "update") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$activity->comment = $args['comment'];
			$activity->advertisement = $args['advertisement'];
			$activity->cancelled = ($args['cancelled'] == "on");
			redirect("eighth/cancel_activity/view/bid/{$args['bid']}/aid/{$args['aid']}");
		}
	}

	/**
	* Room assignment sanity check
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function room_sanity($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
		}
		else if($op == "view") {
			$this->template = "eighth_room_sanity.tpl";
			$this->template_args['conflicts'] = EighthRoom::get_conflicts($args['bid']);
			$this->title = "Room Assignment Sanity Check";
		}
	}

	/**
	* View or print sponsor schedule
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function vp_sponsor($op, $args) {
		if($op == "") {
			$this->setup_sponsor_selection();
		}
		else if($op == "view") {
			$sponsor = new EighthSponsor($args['sid']);
			$this->template = "eighth_vp_sponsor.tpl";
			$this->template_args['sponsor'] = $sponsor;
			$this->template_args['activities'] = $sponsor->schedule;
			$this->title = "View Sponsor Schedule";
		}
		else if($op == "format") {
			$this->setup_format_selection("vp_sponsor", "Sponsor Schedule", array("sid" => $args['sid']));
		}
		else if($op == "print") {
			EighthPrint::print_sponsor_schedule($args['sid'], $args['format']);
		}
	}

	/**
	* Reschedule students by student ID for a single activity
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function res_student($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "activity";
		}
		else if($op == "activity") {
			$this->setup_activity_selection(FALSE, $args['bid']);
			$this->template_args['op'] = "user/bid/{$args['bid']}";
		}
		else if($op == "user") {
			$this->template = "eighth_res_student.tpl";
			$this->template_args['block'] = new EighthBlock($args['bid']);
			$this->template_args['activity'] = new EighthActivity($args['aid']);
			if(isset($args['uid'])) {
				$this->template_args['user'] = new User($args['uid']);
			}
			$this->title = "Reschedule a Student";
		}
		else if($op == "reschedule") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$activity->add_member($args['uid']);
			redirect("eighth/res_student/user/bid/{$args['bid']}/aid/{$args['aid']}");
		}
	}

	/**
	* View, change, or print attendance data
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function vcp_attendance($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "activity";
		}
		else if($op == "activity") {
			$this->setup_activity_selection(FALSE, $args['bid']);
			$this->template_args['op'] = "view/bid/{$args['bid']}";
		}
		else if($op == "view") {
			$this->template = "eighth_vcp_attendance.tpl";
			$this->template_args['activity'] = new EighthActivity($args['aid'], $args['bid']);
			$this->template_args['absentees'] = EighthSchedule::get_absentees($args['bid'], $args['aid']);
			$this->title = "View Attendance";
		}
		else if($op == "update") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$members = $activity->members;
			foreach($members as $member) {
				if(in_array($member, $args['absentees'])) {
					EighthSchedule::add_absentee($args['bid'], $member);
				}
				else {
					EighthSchedule::remove_absentee($args['bid'], $member);
				}
			}
			$activity->attendancetaken = TRUE;
			redirect("eighth/vcp_attendance/view/bid/{$args['bid']}/aid/{$args['aid']}");
		}
		else if($op == "format") {
			$this->setup_format_selection("vcp_attendance", "Attendance Data", array("aid" => $args['aid'], "bid" => $args['bid']));
		}
		else if($op == "print") {
			EighthPrint::print_attendance_data($args['aid'], $args['bid'], $args['format']);
		}
	}

	/**
	* Enter TA absences by student ID
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function ent_attendance($op, $args) {
		if($op == "") {
			$this->setup_block_selection();
			$this->template_args['op'] = "user";
		}
		else if($op == "user") {
			$this->template = "eighth_ent_attendance.tpl";
			$this->template_args['bid'] = $args['bid'];
			$this->title = "Enter TA Attendance";
		}
		else if($op == "mark_absent") {
			EighthSchedule::add_absentee($args['bid'], $args['uid']);
			redirect("eighth");
		}
	}

	/**
	* View or print a list of delinquent students
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function vp_delinquent($op, $args) {
		// TODO: Sorting and exporting for all
		if($op == "") {
			// TODO: Print a list of delinquents
			$lower = 1;
			$upper = 1000;
			$start = null;
			$end = null;
			if(!empty($args['lower']) && ctype_digit($args['lower'])) {
				$lower = $args['lower'];
			}
			if(!empty($args['upper']) && ctype_digit($args['upper'])) {
				$upper = $args['upper'];
			}
			if(!empty($args['start'])) {
				$start = $args['start'];
			}
			if(!empty($args['end'])) {
				$end = $args['end'];
			}
			$delinquents = EighthSchedule::get_delinquents($lower, $upper, $start, $end);
			$this->template_args['students'] = array();
			$this->template_args['absences'] = array();
			for($i = 0; $i < count($delinquents); $i++) {
				$this->template_args['students'][] = $delinquents[$i]['userid'];
				$this->template_args['absences'][] = $delinquents[$i]['absences'];
			}
			$this->template_args['students'] = User::id_to_user($this->template_args['students']);
			$this->template = "eighth_vp_delinquent.tpl";
			$this->title = "View Delinquent Students";
		}
		else if($op == "query") {
			// TODO: Query the delinquents
			$this->template = "eighth_vp_delinquent.tpl";
			$this->title = "Query Delinquent Students";
		}
		else if($op == "sort") {
		}
	}

	/**
	* Finalize student schedules
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function fin_schedules($op, $args) {
		if($op == "") {
			$this->template = "eighth_fin_schedules.tpl";
			$this->template_args['blocks'] = EighthBlock::get_all_blocks();
			$this->title = "Finalize Schedules";
		}
		else if($op == "lock") {
			$block = new EighthBlock($args['bid']);
			$block->locked = TRUE;
			redirect("eighth/fin_schedules");
		}
	 	else if($op == "unlock") {
			$block = new EighthBlock($args['bid']);
			$block->locked = FALSE;
			redirect("eighth/fin_schedules");
		}
	}

	/**
	* Print activity rosters
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function prn_attendance($op, $args) {
		if($op == "") {
			$this->template_args['op'] = "format";
			$this->setup_block_selection();
		}
		else if($op == "confirm") {

		}
		else if($op == "format") {
			$this->setup_format_selection("prn_attendance", "Activity Rosters", array("bid" => $args['bid']));
		}
		else if($op == "print") {
			EighthPrint::print_activity_rosters(explode(",", $args['bid']), $args['format']);
		}
	}

	/**
	* Change starting date
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	* @todo Figure out where to store the starting date, in config.ini for now.
	*/
	public function chg_start($op, $args) {
		if($op == "") {
			$this->template = "eighth_chg_start.tpl";
			$this->title = "Change Start Date";
		}
		else if($op == "change") {
			// TODO: Change starting date
		}
	}

	/**
	* Add or remove 8th period block from system
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function ar_block($op, $args) {
		if($op == "") {
			$this->template = "eighth_ar_block.tpl";
			$this->template_args['blocks'] = EighthBlock::get_all_blocks(i2config_get("start_date", date("Y-m-d"), "eighth"));
			$this->title = "Add/Remove Block";
		}
		else if($op == "add") {
			foreach($args['blocks'] as $block) {
				EighthBlock::add_block("{$args['Year']}-{$args['Month']}-{$args['Day']}", $block);
			}
			redirect("eighth/ar_block");
		}
		else if($op == "remove") {
			EighthBlock::remove_block($args['bid']);
			redirect("eighth/ar_block");
		}
	}
	
	/**
	* Repair broken schedules
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	* @todo Figure out what voodoo this does
	*/
	public function rep_schedules($op, $args) {
		global $I2_SQL;
		if($op == "") {
			$bids = flatten($I2_SQL->query("SELECT bid FROM eighth_blocks")->fetch_all_arrays(MYSQL_NUM));
			foreach($bids as $bid) {
				$activity = new EighthActivity(1);
				EighthSchedule::schedule_activity($bid, $activity->aid, $activity->sponsors, $activity->rooms);
				$uids = flatten($I2_SQL->query("SELECT uid FROM user WHERE uid NOT IN (SELECT userid FROM eighth_activity_map WHERE bid=%d)", $bid)->fetch_all_arrays(MYSQL_NUM));
				$activity->add_members($uids, false, $bid);
			}
			redirect("eighth");
		}
	}

	/**
	* View, change, or print student schedule
	*
	* @access private
	* @param string $op The operation to do.
	* @param array $args The arguments for the operation.
	*/
	public function vcp_schedule($op, $args) {
		global $I2_SQL;
		if($op == "") {
			$this->template = "eighth_vcp_schedule.tpl";
			if(!empty($args['uid'])) {
				$this->template_args['users'] = User::id_to_user(flatten($I2_SQL->query("SELECT uid FROM user WHERE uid LIKE %d", $args['uid'])->fetch_all_arrays(Result::NUM)));
			}
			else {
				$this->template_args['users'] = User::search_info("{$args['fname']} {$args['lname']}");
			}
			if(count($this->template_args['users']) == 1) {
				redirect("eighth/vcp_schedule/view/uid/{$this->template_args['users'][0]->uid}");
			}
			usort($this->template_args['users'], array("User", 'name_cmp'));
			$this->title = "Search Students";
		}
		else if($op == 'view') {
			if(!isset($args['start_date'])) {
				$args['start_date'] = NULL;
			}
			$this->template_args['start_date'] = ($args['start_date'] ? strtotime($args['start_date']) : time());
			$this->template_args['user'] = new User($args['uid']);
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($args['uid'], $args['start_date']));
			$this->template_args['absences'] = EighthSchedule::get_absences($args['uid']);
			$this->template_args['absence_count'] = count($this->template_args['absences']);
			$this->template = 'eighth_vcp_schedule_view.tpl';
			$this->title = 'View Schedule';
		}
		else if($op == 'format') {
			if(!isset($args['start_date'])) {
				$args['start_date'] = NULL;
			}
			$this->setup_format_selection("vcp_schedule", "Student Schedule", array("uid" => $args['uid']) + ($args['start_date'] ? array("start_date" => $args['start_date']) : array()), TRUE);
		}
		else if($op == "print") {
			EighthPrint::print_student_schedule($args['uid'], $args['start_date'], $args['format']);
		}
		else if($op == "choose") {
			$this->template_args['activities'] = EighthActivity::get_all_activities($args['bid']);
			$this->template_args['bid'] = $args['bid'];
			$this->template_args['uid'] = $args['uid'];
			$this->template = "eighth_vcp_schedule_choose.tpl";
			$this->title = "Choose an Activity";
		}
		else if($op == "change") {
			if ($args['bid'] && $args['aid']) {
				$activity = new EighthActivity($args['aid'], $args['bid']);
				$ret = $activity->add_member($args['uid'], false);
				$status = "";
				if($ret & EighthActivity::CANCELLED) {
					$status .= "[Cancelled]";
				}
				if($ret & EighthActivity::PERMISSIONS) {
					$status .= "[Insufficient permissions]";
				}
				if($ret & EighthActivity::CAPACITY) {
					$status .= "[Over capacity]";
				}
				if($ret & EighthActivity::STICKY) {
					$status .= "[Can't switch out]";
				}
				if($ret & EighthActivity::ONEADAY) {
					$status .= "[Only one a day]";
				}
				if($ret & EighthActivity::PRESIGN) {
					$status .= "[Pre-sign activity]";
				}
				if(!$ret) {
					$status .= "[Successful]";
				}
				else if($ret >= 64 || $ret < 0) {
					$status .= "[Unsuccessful]";
				}
				$this->template = "eighth_vcp_schedule_change.tpl";
				$this->template_args['status'] = $status;
				//redirect("eighth/vcp_schedule/view/uid/{$args['uid']}");
			}
		}
		else if($op == "force_change") {
			if ($args['bid'] && $args['aid']) {
				$activity = new EighthActivity($args['aid'], $args['bid']);
				$activity->add_member($args['uid'], true);
				redirect("eighth/vcp_schedule/view/uid/{$args['uid']}");
			}
		}
		else if($op == "roster") {
			$this->template_args['activity'] = new EighthActivity($args['aid'], $args['bid']);
			$this->template_args['num_members'] = count($this->template_args['activity']->members);
			$this->template = "eighth_vcp_schedule_roster.tpl";
			$this->title = "Activity Roster";
		}
		else if($op == "absences") {
			$absences = EighthActivity::id_to_Activity(EighthSchedule::get_absences($args['uid']));
			$this->template_args['absences'] = $absences;
			$this->template_args['uid'] = $args['uid'];
			$this->template_args['admin'] = TRUE;
			$this->template = "eighth_vcp_schedule_absences.tpl";
		}
		else if($op == "remove_absence") {
			EighthSchedule::remove_absentee($args['bid'], $args['uid']);
			redirect("eighth");
		}
	}

	public function view($op, $args) {
		if($op == "") {
		}
		else if($op == "comments") {
			/* Editing comments code */
			$this->template = "eighth_edit_comments.tpl";
			$user = new User($args['uid']);
			$this->template_args['user'] = $user;
			$this->title = "Edit Comments";
		}
		else if($op == "student") {
			/* Editing student code */
			$this->template = "eighth_edit_student.tpl";
			$user = new User($args['uid']);
			$this->template_args['user'] = $user;
			$this->title = "Edit Student Data";
		}
	}
	public function edit($op, $args) {
		if($op == "") {
		}
		else if($op == "comments") {
			/* Editing comments code */
			$user = new User($args['uid']);
			$user->comments = $args['comments'];
			redirect("eighth");
		}
		else if($op == "student") {
			/* Editing student code */
			$user = new User($args['uid']);
		}
	}
}

?>
