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
			return array("Eighth Period Office Online: Home", "Eighth");
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
				return "Eighth Period Office Online: " . ucwords(strtr($method, "_", " "));
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
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($I2_USER->uid, $date, 2));
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
		$this->template_args['filed'] = $field;
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
	private function setup_activity_selection($add = FALSE, $blockid = NULL, $restricted = FALSE, $field = "aid", $title = "Select an activity:", $autoredirect = TRUE) {
		$activities = EighthActivity::get_all_activities($blockid, $restricted);
		$this->template = "eighth_activity_selection.tpl";
		$this->template_args += array("activities" => $activities, "add" => $add);
		$this->template_args['title'] = $title;
		$this->template_args['filed'] = $field;
		$this->template_args['autoredirect'] = $autoredirect;
	}

	/**
	* Sets up for displaying the group selection screen.
	*
	* @access private
	* @param bool $add Whether to include the add field or not.
	* @param string $title The title for the group list.
	*/
	private function setup_group_selection($add = FALSE, $title = "Select a group:") {
		$groups = EighthGroup::get_all_groups();
		$this->template = "eighth_group_selection.tpl";
		$this->template_args += array("groups" => $groups, "add" => $add);
		$this->template_args['title'] = $title;
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
		}
		else if($op == "group") {
			$this->setup_group_selection();
			$this->template_args['op'] = "commit/bid/{$args['bid']}/aid/{$args['aid']}";
		}
		else if($op == "commit") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$group = new EighthGroup($args['gid']);
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
			EighthGroup::add_group($args['name']);
		}
		else if($op == "modify") {
			$group = new EighthGroup($args['gid']);
			$group->name = $args['name'];
			redirect("eighth/amr_group/view/gid/{$args['gid']}");
		}
		else if($op == "remove") {
			EighthGroup::remove_group($args['gid']);
			redirect("eighth");
		}
		else if($op == "view") {
			$group = new EighthGroup($args['gid']);
			$members = User::id_to_user($group->members);
			usort($members, array($this, 'name_cmp'));
			$this->template = "eighth_amr_group.tpl";
			$this->template_args['members'] = $members;
			$this->template_args['gid'] = $args['gid'];
		}
		else if($op == "add_member") {
			$group = new EighthGroup($args['gid']);
			$group->add_member($args['uid']);
			redirect("eighth/amr_group/view/gid/{$args['gid']}");
		}
		else if($op == "remove_member") {
			$group = new EighthGroup($args['gid']);
			$group->remove_member($args['uid']);
			redirect("eighth/amr_group/view/gid/{$args['gid']}");
		}
		else if($op == "remove_all") {
			$group = new EighthGroup($args['gid']);
			$group->remove_all();
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
		else if($op == "add_group") {
			$activity = new EighthActivity($args['aid']);
			$group = new EighthGroup($args['gid']);
			$activity->add_restricted_members($group->members);
		}
		else if($op == "add_member") {
			$activity = new EighthActivity($args['aid']);
			$activity->add_restricted_member($args['uid']);
		}
		else if($op == "remove_member") {
			$activity = new EighthActivity($args['aid']);
			$activity->remove_restricted_member($args['uid']);
		}
		else if($op == "remove_all") {
			$activity = new EighthActivity($args['aid']);
			$activity->remove_restricted_all();
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
		}
		else if($op == "activity_from") {
			$this->setup_activity_selection(FALSE, $args['bid_from'], FALSE, "aid_from", "From this activity:");
			$this->template_args['op'] = "activity_to/bid/{$args['bid']}";
		}
		else if($op == "block_to") {
			$this->setup_block_selection(FALSE, "bid_to");
			$this->template_args['op'] = "activity_to/bid_from/{$args['bid_from']}/aid_from/{$args['aid_from']}";
		}
		else if($op == "activity_to") {
			$this->setup_activity_selection(FALSE, $args['bid_to'], FALSE, "aid_to", "To this activity:");
			$this->template_args['op'] = "confirm/bid_from/{$args['bid_from']}/aid_from/{$args['aid_from']}/bid_to/{$args['bid_to']}";
		}
		else if($op == "confirm") {
			if($args['aid_from'] == $args['aid_to']) {
				redirect("eighth/people_switch/activity_to/bid_from/{$args['bid_from']}/aid_from/{$args['aid_from']}/bid_to/{$args['bid_to']}");
			}
			$this->template = "eighth_people_switch.tpl";
			$this->template_args['activity_from'] = new EighthActivity($args['aid_from'], $args['bid_from']);
			$this->template_args['activity_to'] = new EighthActivity($args['aid_to'], $args['bid_to']);
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
		}
		else if($op == "add") {
			$aid = EighthActivity::add_activity($args['name']);
			redirect("eighth/amr_activity/view/aid/{$aid}");
		}
		else if($op == "modify") {
			$activity = new EighthActivity($args['aid']);
			$activity->name = $args['name'];
			$activity->sponsors = $args['sponsors'];
			//$activity->rooms = $args['rooms'];
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
		}
		else if($op == "add") {
			EighthRoom::add_room($args['name'], $args['capacity']);
		}
		else if($op == "modify") {
			$room = new EighthRoom($args['rid']);
			$room->name = $args['name'];
			$room->capacity = $args['capacity'];
			redirect("eighth/amr_room/view/rid/{$args['rid']}");
		}
		else if($op == "remove") {
			EighthRoom::remove_room($args['rid']);
			redirect("eighth");
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
		}
		else if($op == "add") {
			$sid = EighthSponsor::add_sponsor($args['fname'], $args['lname']);
			redirect("eighth/amr_sponsor/view/sid/{$sid}");
		}
		else if($op == "modify") {
			$sponsor = new EighthSponsor($args['sid']);
			$sponsor->fname = $args['fname'];
			$sponsor->lname = $args['lname'];
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
			$this->template_args['activities'] = EighthBlock::get_activity_schedule($args['aid']);
			$this->template_args['aid'] = $args['aid'];
		}
		else if($op == "modify") {
			foreach($args['modify'] as $bid) {
				if($args['sponsor_list'][$bid] == array("") && $args['room_list'][$bid] == array("")) {
					echo "Unscheduling";
					EighthSchedule::unschedule_activity($bid, $args['aid']);
				}
				else {
					EighthSchedule::schedule_activity($bid, $args['aid'], $args['sponsor_list'][$bid], $args['room_list'][$bid]);
				}
			}
			redirect("eighth");
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
		}
		else if($op == "view") {
			$this->template = "eighth_vp_room_view.tpl";
			$this->template_args['block'] = new EighthBlock($args['bid']);
			$this->template_args['utilizations'] = EighthRoom::get_utilization($args['bid'], $args['include'], !empty($args['overbooked']));
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
			$this->template_args['activities'] = $sponsor->schedule;
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
		}
		else if($op == "update") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$members = $activity->get_members();
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
		}
		else if($op == "mark_absent") {
			EighthSchedule::add_absentee($args['bid'], $args['uid']);
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
			$this->template = "eighth_vp_delinquent.tpl";
		}
		else if($op == "query") {
			// TODO: Query the delinquents
			$this->template = "eighth_vp_delinquent.tpl";
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
			$this->setup_block_selection();
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
		if($op == "") {
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
				$this->template_args['users'] = User::id_to_user(flatten($I2_SQL->query("SELECT uid FROM user WHERE uid LIKE %d", $args['uid'])->fetch_all_arrays(MYSQL_NUM)));
			}
			else {
				$this->template_args['users'] = User::search_info("{$args['fname']} {$args['lname']}");
				usort($this->template_args['users'], array("User", 'name_cmp'));
			}
		}
		else if($op == "view") {
			if(!isset($args['start_date'])) {
				$args['start_date'] = NULL;
			}
			$this->template_args['start_date'] = ($args['start_date'] ? strtotime($args['start_date']) : time());
			$this->template_args['user'] = new User($args['uid']);
			$this->template_args['activities'] = EighthActivity::id_to_activity(EighthSchedule::get_activities($args['uid'], $args['start_date']));
			$this->template = "eighth_vcp_schedule_view.tpl";
		}
		else if($op == "choose") {
			$this->template_args['activities'] = EighthActivity::get_all_activities($args['bid']);
			$this->template = "eighth_vcp_schedule_choose.tpl";
		}
		else if($op == "change") {
			$activity = new EighthActivity($args['aid'], $args['bid']);
			$activity->add_member($args['uid']);
		}
	}
}

?>
