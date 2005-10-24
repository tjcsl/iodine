<?php
/**
* Just contains the definition for the class {@link Groups}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Groups
* @filesource
*/

/**
* The module that keeps the eighth block office happy.
* @package modules
* @subpackage Groups
*/
class Groups implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template = "groups_home.tpl";

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER;
		$args = array();
		if(count($I2_ARGS) <= 1) {
			$this->template = "groups_home.tpl";
			$this->template_args['groups'] = $I2_USER->get_groups();
			if ($I2_USER->is_group_member("admin_groups")) {
				$this->template_args['admin'] = 1;
			}
			return array("Groups: Home", "Groups");
		}
		else {
			$method = $I2_ARGS[1];
			if(method_exists($this, $method)) {
				$this->$method();
				$this->template_args['method'] = $method;
				return "Groups: " . ucwords(strtr($method, "_", " "));
			}
			else {
				$this->template = "groups_error.tpl";
				$this->template_args = array("method" => $method, "args" => $I2_ARGS);
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
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Eighth";
	}

	/**
	* View a group
	*/
	function group() {
		global $I2_ARGS, $I2_USER, $I2_SQL;

		$group = $I2_ARGS[2];
		$this->template_args['group'] = $group;
		$gid = user::get_group_id($group);
		$is_groups_admin = $I2_USER->is_group_member("admin_groups");
		if($I2_USER->is_group_member($group) || $is_groups_admin) {
			$result = $I2_SQL->query('SELECT is_admin FROM group_user_map WHERE uid=%d AND gid=%d', $I2_USER->uid, $gid)->fetch_array(MYSQL_NUM);
			if($result[0] == 1 || $is_groups_admin) {
				$this->template_args['admin'] = "all";
				if( isset($_REQUEST['group_form']) ) {
					if($_REQUEST['group_form'] == "add") {
						$new_member_uid = $_REQUEST['uid'];
						$new_member = new User($new_member_uid);
						$new_member->add_group($group);
					}
					if($_REQUEST['group_form'] == "remove") {
						$id_to_remove = $_REQUEST['uid'];
						$I2_SQL->query('DELETE FROM group_user_map WHERE uid=%d AND gid=%d', $id_to_remove, $gid);
					}
					if($_REQUEST['group_form'] == "make_admin") {
						$new_admin_id = $_REQUEST['uid'];
						$I2_SQL->query('UPDATE group_user_map SET is_admin=1 WHERE uid=%d AND gid=%d', $new_admin_id, $gid);
					}
					if($_REQUEST['group_form'] == "remove_admin" && $is_groups_admin){
						$id_to_remove = $_REQUEST['uid'];
						$I2_SQL->query('UPDATE group_user_map SET is_admin=0 WHERE uid=%d AND gid=%d', $id_to_remove, $gid);
					}
					if($_REQUEST['group_form'] == "make_poster") {
						$new_poster_id = $_REQUEST['uid'];
						$I2_SQL->query('UPDATE group_user_map SET can_post=1 WHERE uid=%d AND gid=%d', $new_poster_id, $gid);
					}
					if($_REQUEST['group_form'] == "remove_poster") {
						$id_to_remove = $_REQUEST['uid'];
						$I2_SQL->query('UPDATE group_user_map SET can_post=0 WHERE uid=%d AND gid=%d', $id_to_remove, $gid);
					}
				}
			}
			if($is_groups_admin) {
				$this->template_args['admin'] = "master";
			}
			$this->template_args['members'] = self::get_group_members($group);
			$this->template = "groups_group.tpl";
		}
		else {
			d("not a member");
			$this->template = "groups_error.tpl";
		}
		return array("Groups: Group", "Groups");
	}
	/**
	* The master admin interface
	*/
	function admin() {
		global $I2_SQL, $I2_USER;
		if($I2_USER->is_group_member("admin_groups")) {
			if(isset($_REQUEST['group_admin_form'])) {
				if($_REQUEST['group_admin_form'] == "add") {
					$I2_SQL->query('INSERT INTO groups SET name=%s', $_REQUEST['name']);
				}
				if($_REQUEST['group_admin_form'] == "remove") {
					$I2_SQL->query('DELETE FROM groups WHERE name=%s', $_REQUEST['name']);
				}
			}
			$this->template_args['groups'] = array();
			$result = $I2_SQL->query('SELECT name FROM groups ORDER BY gid');
			while($group = $result->fetch_array(MYSQL_NUM)) {
				array_push($this->template_args['groups'], $group[0]);
			}
			$this->template = "groups_admin.tpl";
		}
		else {
			$this->template = "groups_error.tpl";
		}
	}
	/**
	* Get a list of group members
	*/
	function get_group_members($group) {
		global $I2_SQL;
		$group_members = array();
		$result = $I2_SQL->query('SELECT fname, lname, is_admin, can_post FROM user INNER JOIN group_user_map USING (uid) INNER JOIN groups USING (gid) WHERE groups.name=%s', $group);
		while($member = $result->fetch_array(MYSQL_ASSOC)) {
			$name = $member['fname']." ".$member['lname'];
			$person_array = array("name" => $name);
			if($member['is_admin'] == 1) {
				$person_array['admin'] = "Admin";
			}
			else if($member['can_post'] == 1) {
				$person_array['admin'] = "May post news";
			}
			$group_members[] = $person_array;
		}
		return $group_members;
	}
}

?>
