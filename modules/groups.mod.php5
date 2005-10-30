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
			$result = $I2_SQL->query('SELECT is_admin FROM group_user_map WHERE uid=%d AND gid=%d', $I2_USER->uid, $gid)->fetch_array(RESULT_NUM);
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
		//FIXME: this isn't abstracted enough...
		$result = $I2_SQL->query('SELECT fname,lname,admin_all,admin_news FROM user INNER JOIN group_user_map USING (uid) INNER JOIN groups USING (gid) WHERE groups.name=%s', $gname);
		while($member = $result->fetch_array(RESULT_ASSOC)) {
			$name = $member['fname']." ".$member['lname'];
			$person_array = array("name" => $name);
			if($member['admin_all'] == 1) {
				$person_array['admin'] = "Admin";
			}
			else if($member['admin_news'] == 1) {
				$person_array['admin'] = "May post news";
			}
			$group_members[] = $person_array;
		}
		return $group_members;
	}

	public static function get_group_members($gid) {
		global $I2_SQL;
		return $I2_SQL->query("SELECT uid FROM group_user_map WHERE gid=%d",$gid);
	}
	
	public static function get_group_members_by_id($gname) {
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
		return $I2_SQL->query('SELECT gid FROM groups WHERE name=%s',$gname)->fetch_single_value();
	}
	
	public static function add_user_to_group($uid,$gid) {
		global $I2_SQL;
		return $I2_SQL->query('INSERT INTO group_user_map (gid,uid) VALUES(%d,%d)',$gid,$groupname);
	}

	public static function add_user_to_group_by_name($uid,$gname) {
		return self::add_user_to_group($uid,self::get_group_id($gname));
	}

	public static function remove_user_from_group($uid,$gid) {
		global $I2_SQL;
		return $I2_SQL->query('DELETE FROM group_user_map WHERE uid=%d AND gid=%d',$uid,$gid);
	}

	public static function remove_user_from_group_by_name($uid,$gname) {
		return self::remove_user_from_group($uid,self::get_group_id($gname));
	}

	public static function remove_all_from_group($gid) {
		global $I2_SQL;
		return $I2_SQL->query("DELETE FROM group_user_map");
	}

	public static function remove_all_from_group_by_name($gname) {
		return self::remove_all_from_group(self::get_group_id($gname));
	}

	public static function is_group_member($uid,$groupname) {
		$groups = self::get_user_groups($uid);
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
	*/
	public static function get_user_groups($uid) {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d',$uid);
		//TODO: consider this
		return $res->fetch_all_arrays(RESULT_NUM);
	}

	/**
	* Gets the names of groups a user belongs to.
	*
	* @param int $uid The UID for which to fetch groups.
	* @return array An array of the names of groups for the passed user.
	* @todo Consider using a JOIN statement instead of a loop.
	*/
	public static function get_user_group_names($uid) {
		$res = self::get_user_groups($uid);
		$ret = array();
		foreach ($res as $gid) {
			$ret[] = self::get_group_name($gid[0]);
		}	
		return $ret;
	}

	/**
	* @todo Write this; it's not just dropping a group but also cleaning up.
	*/
	public static function delete_group($gid) {
	}

}

?>
