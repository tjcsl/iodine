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
* The module that runs groups
* @package modules
* @subpackage Groups
* @todo We should probably pass around User objects instead of uids, in many cases.
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
			$this->template_args['groups'] = self::get_user_group_names($I2_USER->uid,FALSE);
			d("grps admin:");
			d($I2_USER->is_group_member('admin_groups'));
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

	function is_intrabox() {
		return false;
	}

	/**
	* View a group
	*
	* Group admins may add or remove members and news posting privileges. However, they can
	* only add admin privileges. (This is so that one admin can't take over the group by removing
	* all the other admins.) Only members of admin_groups may remove adminship, along with having
	* all the other group admin abilities.
	*
	* This is slightly changed for "admin_" groups. Admin_groups members are no longer allowed
	* to even view the group: admin_all takes the place of admin_groups in having admin abilities.
	*/
	function group() {
		global $I2_ARGS, $I2_USER, $I2_SQL;

		$group = $I2_ARGS[2];
		$this->template_args['group'] = $group;
		$gid = self::get_group_id($group);
		$is_single_admin = self::is_group_admin($I2_USER->uid, $gid);
		$is_groups_admin = $I2_USER->is_group_member("admin_groups");
		$is_master_admin = $I2_USER->is_group_member("admin_all");
		$is_higher_admin = ($is_master_admin || ($is_groups_admin && substr($group,0,6) != 'admin_'));

		if($is_higher_admin || $I2_USER->is_group_member($group)) {
			// user is group member, groups admin if a normal group, or master admin if admin group

			if($is_single_admin || $is_higher_admin) {
				// user is single-group admin or groups admin (or master admin)

				// differentiate between single group admin and admin_groups/admin_all
				if($is_higher_admin) {
					$this->template_args['admin'] = "master";
				}
				else {
					$this->template_args['admin'] = "all";
				}

				if( isset($_REQUEST['group_form']) ) {
					if($_REQUEST['group_form'] == "add") {
						$new_member_uid = $_REQUEST['uid'];
						self::add_user_to_group($new_member_id, $gid);
					}
					if($_REQUEST['group_form'] == "remove") {
						$id_to_remove = $_REQUEST['uid'];
						self::remove_user_from_group($id_to_remove, $gid);
					}
					if($_REQUEST['group_form'] == "make_admin") {
						$new_admin_id = $_REQUEST['uid'];
						self::bestow_admin_privileges($new_admin_id, $gid);
					}
					if($_REQUEST['group_form'] == "remove_admin" && $is_groups_admin){
						//to remove admin, must be more than single group admin
						$id_to_remove = $_REQUEST['uid'];
						self::deprive_of_admin_privileges($id_to_remove, $gid);
					}
				}
			}
			$this->template_args['members'] = self::get_memberinfo_helper($group);
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
			while($group = $result->fetch_array(RESULT_NUM)) {
				array_push($this->template_args['groups'], $group[0]);
			}
			$this->template = "groups_admin.tpl";
		}
		else {
			$this->template = "groups_error.tpl";
		}
	}
	/**
	* Private helper function
	*/
	private static function get_memberinfo_helper($gname) {
		global $I2_SQL;
		$group_members = array();

		$uids = self::get_group_members_by_name($gname);
		foreach($uids as $uid) {
			$person_array = array();

			d($uid);
			$person_user = new User($uid);
			$person_array['name'] = $person_user->name;

			if (self::is_group_admin_by_name($uid, $gname)) {
				$person_array['admin'] = "Admin";
			}

			$group_members[] = $person_array;
		}

		return $group_members;
	}

	public static function get_group_members($gid) {
		global $I2_SQL;
		return flatten($I2_SQL->query("SELECT uid FROM group_user_map WHERE gid=%d",$gid)->fetch_all_arrays(RESULT_NUM));
	}
	
	public static function get_group_members_by_name($gname) {
		return self::get_group_members(self::get_group_id($gname));
	}

	/**
	* Gets all groups.
	*
	* @return mixed A Result containing gids of all groups.
	*/
	public static function get_all_groups() {
		global $I2_SQL;
		return $I2_SQL->query("SELECT gid FROM groups");
	}

	/**
	* Gets the name of every group
	*
	* @return mixed A Result containing each group's name.
	*/
	public static function get_all_groups_names() {
		global $I2_SQL;
		return $I2_SQL->query("SELECT name FROM groups");
	
	}
	
	/**
	* Get the name of a group.
	*
	* Returns a group's name.  This function will throw an error if the passed groupid is invalid.
	*
	* @param int $gid The ID of a group.
	* @return string The group's name. 
	*/
	public static function get_group_name($gid) {

		if (!is_numeric($gid)) {
			throw new i2exception("Non-numerical groupid `$gid' passed to get_group_name!");
		}
		global $I2_SQL;

		return $I2_SQL->query('SELECT name FROM groups WHERE gid=%d',$gid)->fetch_single_value();
	}

	/**
	* Gets a group's ID by name.
	*
	* Performs a lookup of a group's ID with the given name.
	*
	* @param string $gname The name of the group to look up.
	*/
	public static function get_group_id($gname) {
		global $I2_SQL;

		if(self::is_special_group($gname)) {
			return self::get_special_group($gname);
		}
		
		return $I2_SQL->query('SELECT gid FROM groups WHERE name=%s',$gname)->fetch_single_value();
	}
	
	public static function add_user_to_group($uid,$gid) {
		global $I2_SQL;

		if(self::is_special_group($gid)) {
			throw I2Exception("Attempted to add user $uid to invalid group $gid");
		}
		
		return $I2_SQL->query('INSERT INTO group_user_map (gid,uid) VALUES(%d,%d)',$gid,$groupname);
	}

	public static function add_user_to_group_by_name($uid,$gname) {
		return self::add_user_to_group($uid,self::get_group_id($gname));
	}

	public static function remove_user_from_group($uid,$gid) {
		global $I2_SQL;

		if(self::is_special_group($gid)) {
			throw I2Exception("Attempted to add user $uid to invalid group $gid");
		}
		
		return $I2_SQL->query('DELETE FROM group_user_map WHERE uid=%d AND gid=%d',$uid,$gid);
	}

	public static function remove_user_from_group_by_name($uid,$gname) {
		return self::remove_user_from_group($uid,self::get_group_id($gname));
	}

	public static function remove_all_from_group($gid) {
		global $I2_SQL;

		if(self::is_special_group($gid)) {
			throw I2Exception("Attempted to add user $uid to invalid group $gid");
		}

		return $I2_SQL->query("DELETE FROM group_user_map");
	}

	public static function remove_all_from_group_by_name($gname) {
		return self::remove_all_from_group(self::get_group_id($gname));
	}

	public static function bestow_admin_privileges($uid, $gid) {
		global $I2_SQL;
		return $I2_SQL->query('UPDATE group_user_map SET is_admin=1 WHERE uid=%d AND gid=%d', $uid, $gid);
	}
	
	public static function bestow_admin_privileges_by_name($uid, $gname) {
		return self::bestow_admin_privileges($uid, self::get_group_id($gname));
	}

	public static function deprive_of_admin_privileges($uid, $gid) {
		global $I2_SQL;
		return $I2_SQL->query('UPDATE group_user_map SET is_admin=0 WHERE uid=%d AND gid=%d', $uid, $gid);
	}

	public static function deprive_of_admin_privileges_by_name($uid, $gname) {
		return self::deprive_of_admin_privileges($uid, self::get_group_id($gname));
	}

	public static function is_group_member_by_name($uid,$groupname) {
		$groups = self::get_user_group_names($uid);
		if ($groups != NULL && in_array($groupname,$groups,FALSE)) {
			return TRUE;
		}	
		/*
		** If the user is an admin_all, they're also admin_anything
		*/
		if (substr($groupname,6) == 'admin_'  && in_array($groups,'admin_all')) {
			return TRUE;
		}
		return FALSE;
	}

	public static function is_group_admin($uid, $gid) {
		global $I2_SQL;
		if ($I2_SQL->query('SELECT is_admin FROM group_user_map WHERE uid=%d AND gid=%d', $uid, $gid)->fetch_single_value()) {
			return TRUE;
		}
		return FALSE;
	}

	public static function is_group_admin_by_name($uid, $gname) {
		return self::is_group_admin($uid, self::get_group_id($gname));
	}

	public static function set_group_name($gid,$name) {
		global $I2_SQL;
		return $I2_SQL->query("UPDATE groups SET $name=%s WHERE $gid=%d",$name,$gid);
	}

	/**
	* Gets the groups of which a user is a member.
	*
	*
	* @param int $uid The userID of which to fetch the groups.
	* @return array The group IDs of groups for the given user.
	* @todo Make this return the grade_whatever group and have admin_all return all admin groups?
	* @todo Return group names instead of ids?
	*/
	public static function get_user_group_ids($uid) {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d',$uid);
		$ids = flatten($res->fetch_all_arrays(RESULT_NUM));
		$user = new User($uid);
		if($user->grade) {
			$ids[] = self::get_special_group("grade_{$user->grade}");
		}
		return $ids;
	}

	/**
	* Gets the names of groups a user belongs to.
	*
	* @param int $uid The UID for which to fetch groups.
	* @param bool $include_special Whether or not to include special groups in the result.
	* @return array An array of the names of groups for the passed user.
	* @todo Consider using a JOIN statement instead of a loop.
	*/
	public static function get_user_group_names($uid, $include_special = TRUE) {
		$res = self::get_user_group_ids($uid);
		$ret = array();
		foreach ($res as $gid) {
			$ret[] = self::get_group_name($gid);
		}	
		if($include_special) {
			$user = new User($uid);
			if($user->grade)
				$ret[] = 'grade_'.$user->grade;
		}
		return $ret;
	}

	/**
	* @todo Write this; it's not just dropping a group but also cleaning up.
	*/
	public static function delete_group($gid) {
	}

	/**
	* Gets special group information.
	*
	* Given a special group name, returns the negative GID. Given a negative
	* GID, returns the group name.
	*
	* @param $group mixed Either a string, the group name, or an int, the
	*		GID.
	* @return mixed Either a string, the group name, or an int, the GID.
	*/
	protected static function get_special_group($group) {
		switch($group) {
			case -9:
			case '-9':
				return 'grade_9';
			case -10:
			case '-10':
				return 'grade_10';
			case -11:
			case '-11':
				return 'grade_11';
			case -12:
			case '-12':
				return 'grade_12';
			case 'staff':
				return 'grade_staff';
			case 'grade_9':
				return -9;
			case 'grade_10':
				return -10;
			case 'grade_11':
				return -11;
			case 'grade_12':
				return -12;
			case 'grade_staff':
				return -8;
			case -8:
			case '-8':
				return 'grade_staff';
		}
		throw I2Exception('Invalid special group '.$group.' passed to get_special_group');
	}

	/**
	* Determines whether a group is a special group or not.
	*
	* If the group is a special group, returns TRUE, otherwise FALSE.
	*
	* @param $group mixed A group name or GID.
	* @return bool TRUE if the group is a special group, FALSE otherwise.
	*/
	protected static function is_special_group($group) {
		try {
			self::get_special_group($group);
			return TRUE;
		}
		catch( I2Exception $e ) {
			return FALSE;
		}
	}
}

?>
