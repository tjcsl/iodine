<?php
/**
* Just contains the definition for the class {@link Groups}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Group
* @filesource
*/

/**
* The module that runs groups
* @package modules
* @subpackage Group
*/
class Group implements Module {

	/**
	* Template for the specified action
	*/
	private $template = 'group_home.tpl';

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

	/**
	* Commonly accessed administrative groups.
	*/
	private static $admin_groups = NULL;
	private static $admin_all = NULL;

	private $mygid;

	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'gid':
				return $this->mygid;
			case 'name':
				return $I2_SQL->query('SELECT name FROM groups WHERE gid=%d',$this->mygid)->fetch_single_value();
			case 'special':
				return ($this->mygid < 0);
		}
	}

	public function __construct($group=NULL) {
		// Ensure that admin groups are initialized
//		if( (self::$admin_groups === NULL || self::$admin_all === NULL) && $group != 'admin_groups' && $group != 'admin_all') { // these last two clauses are important; without them a infinitely-recursive constructor loop occurs, and apache segfaults
//			self::$admin_groups = new Group('admin_groups');
//			self::$admin_all = new Group('admin_all');
//		}

		if($group != NULL) {
			$this->mygid = self::get_group_id($group);
		}
	}

	private static function get_group_id($group) {
		global $I2_SQL;

		if(is_numeric($group)) {
			if( $I2_SQL->query('SELECT gid FROM groups WHERE gid=%d', $group)->num_rows() > 0 ) {
				return $group;
			}
			else {
				throw new I2Exception("Nonexistent group id $group given to the Group module");
			}
		}
		else {
			$gid = $I2_SQL->query('SELECT gid FROM groups WHERE name=%s',$group)->fetch_single_value();
			if($gid) {
				return $gid;
			}
			else {
				try {
					return self::get_special_group($group);
				} catch (I2Exception $e) {
					throw new I2Exception("Nonexistent group $group given to the Group module");
				}
			}
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER;

		// Ensure that admin groups are initialized
		if( self::$admin_groups === NULL || self::$admin_all === NULL) {
			self::$admin_groups = new Group('admin_groups');
			self::$admin_all = new Group('admin_all');
		}
		
		$args = array();
		if(count($I2_ARGS) <= 1) {
			$this->template = 'groups_home.tpl';
			$this->template_args['groups'] = self::get_user_groups($I2_USER,FALSE);
			d(self::$admin_groups->has_member($I2_USER));
			if (self::$admin_groups->has_member($I2_USER)) {
				$this->template_args['admin'] = 1;
			}
			return array('Groups: Home', 'Groups');
		}
		else {
			$method = $I2_ARGS[1];
			if(method_exists($this, $method)) {
				$this->$method();
				$this->template_args['method'] = $method;
				return 'Groups: ' . ucwords(strtr($method, '_', ' '));
			}
			else {
				$this->template = 'groups_error.tpl';
				$this->template_args = array('method' => $method, 'args' => $I2_ARGS);
			}
		}
		return array('Error', 'Error');
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
		return 'Eighth';
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
	function pane() {
		global $I2_ARGS, $I2_USER, $I2_SQL;

		$group = new Group($I2_ARGS[2]);
		$this->template_args['group'] = $group->name;
		
		$is_single_admin = $group->is_admin($I2_USER);
		$is_groups_admin = $I2_USER->is_group_member('admin_groups');
		$is_master_admin = $I2_USER->is_group_member('admin_all');
		$is_higher_admin = ($is_master_admin || ($is_groups_admin && substr($group->name,0,6) != 'admin_'));

		if($is_higher_admin || $group->has_member($I2_USER)) {
			// user is group member, groups admin if a normal group, or master admin if admin group

			if($is_single_admin || $is_higher_admin) {
				// user is single-group admin or groups admin (or master admin)

				// differentiate between single group admin and admin_groups/admin_all
				if($is_higher_admin) {
					$this->template_args['admin'] = 'master';
				}
				else {
					$this->template_args['admin'] = 'all';
				}

				if( isset($_REQUEST['group_form']) ) {
					if($_REQUEST['group_form'] == 'add') {
						$new_member = new User($_REQUEST['uid']);
						$group->add_user($new_member);
					}
					if($_REQUEST['group_form'] == 'remove') {
						$user_to_remove = new User($_REQUEST['uid']);
						$group->remove_user($user_to_remove);
					}
					if($_REQUEST['group_form'] == 'make_admin') {
						$new_admin = new User($_REQUEST['uid']);
						$group->grant_admin($new_admin);
					}
					if($_REQUEST['group_form'] == 'remove_admin' && $is_groups_admin){
						//to remove admin, must be more than single group admin
						$admin_to_remove = new User($_REQUEST['uid']);
						$group->revoke_admin($admin_to_remove);
					}
				}
			}
			$this->template_args['members'] = $group->get_memberinfo();
			$this->template = 'groups_group.tpl';
		}
		else {
			d('not a member');
			$this->template = 'groups_error.tpl';
		}
		return array('Groups: Group', 'Groups');
	}
	/**
	* The master admin interface
	*/
	function admin() {
		global $I2_SQL, $I2_USER;
		if(self::$admin_groups->has_member($I2_USER)) {
			if(isset($_REQUEST['group_admin_form'])) {
				if($_REQUEST['group_admin_form'] == 'add') {
					$I2_SQL->query('INSERT INTO groups SET name=%s', $_REQUEST['name']);
				}
				if($_REQUEST['group_admin_form'] == 'remove') {
					$I2_SQL->query('DELETE FROM groups WHERE name=%s', $_REQUEST['name']);
				}
			}
			$this->template_args['groups'] = array();
			$result = $I2_SQL->query('SELECT name FROM groups ORDER BY gid');
			foreach($result as $group) {
				array_push($this->template_args['groups'], $group[0]);
			}
			$this->template = 'groups_admin.tpl';
		}
		else {
			$this->template = 'groups_error.tpl';
		}
	}
	/**
	* Private helper function
	*/
	private function get_memberinfo() {
		global $I2_SQL;
		$group_members = array();

		$uids = $this->get_members();
		foreach($uids as $uid) {
			$person_array = array();

			$person_user = new User($uid);
			$person_array['name'] = $person_user->name;

			if ($this->is_admin($person_user)) {
				$person_array['admin'] = 'Admin';
			}

			$group_members[] = $person_array;
		}

		return $group_members;
	}

	public function get_members() {
		global $I2_SQL;

		return flatten($I2_SQL->query('SELECT uid FROM group_user_map WHERE gid=%d',$this->mygid)->fetch_all_arrays(RESULT_NUM));
	}
	
	/**
	* Gets all groups.
	*
	* @return Result A Result containing gids of all groups.
	*/
	public static function get_all_groups() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT gid FROM groups');
	}

	/**
	* Gets the name of every group
	*
	* @return Result A Result containing each group's name.
	*/
	public static function get_all_group_names() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT name FROM groups');
	
	}

	public function add_user(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to add user {$user->uid} to invalid group {$this->mygid}");
		}
		return $I2_SQL->query('INSERT INTO group_user_map (gid,uid) VALUES(%d,%d)',$this->mygid,$user->uid);
	}

	public function remove_user(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to remove user {$user->uid} from invalid group {$this->mygid}");
		}
		
		return $I2_SQL->query('DELETE FROM group_user_map WHERE uid=%d AND gid=%d',$user->uid,$this->mygid);
	}

	public function remove_all_members() {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to remove all users from invalid group {$this->mygid}");
		}

		return $I2_SQL->query('DELETE FROM group_user_map WHERE gid=%d', $this->mygid);
	}

	public function grant_admin(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to grant admin privileges to user {$user->uid} for invalid group {$this->mygid}");
		}
		
		return $I2_SQL->query('UPDATE group_user_map SET is_admin=1 WHERE uid=%d AND gid=%d', $user->uid, $this->mygid);
	}
	
	public function revoke_admin(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to revoke admin privileges from user {$user->uid} for invalid group {$this->mygid}");
		}

		return $I2_SQL->query('UPDATE group_user_map SET is_admin=0 WHERE uid=%d AND gid=%d', $uid, $gid);
	}

	public function has_member(User $user) {
		global $I2_SQL;

		// If the user is in admin_all, they're also admin_anything
		if (substr($this->name,6) == 'admin_'  && $this->name != 'admin_all' && self::$admin_all->has_member($user)) {
			return TRUE;
		}

		// Check for 'special' groups
		if( $this->special ) {
			$specs = self::get_special_groups($user);
			return in_array($this->mygid, $specs);
		}
		
		// Standard DB check
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d AND gid=%d', $user->uid, $this->mygid);
		if( $res->num_rows() > 0) {
			return TRUE;
		}

		return FALSE;
	}

	public function is_group_admin($user) {
		global $I2_SQL;

		if( $this->special ) {
			throw I2Exception("is_group_admin() called on invalid group {$this->mygid} for user {$user->uid}");
		}
		
		if ($I2_SQL->query('SELECT is_admin FROM group_user_map WHERE uid=%d AND gid=%d', $user->uid, $group)->fetch_single_value()) {
			return TRUE;
		}
		return FALSE;
	}

	public function set_group_name($name) {
		global $I2_SQL;
		return $I2_SQL->query('UPDATE groups SET name=%s WHERE gid=%d',$name,$this->mygid);
	}

	/**
	* Gets the groups of which a user is a member.
	*
	*
	* @param int $uid The userID of which to fetch the groups.
	* @return array The group IDs of groups for the given user.
	*/
	public static function get_user_groups(User $user, $include_special = TRUE) {
		global $I2_SQL;
		$ret = array();
		
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d',$user->uid);
		$groups = flatten($res->fetch_all_arrays(RESULT_NUM));
		
		foreach($groups as $gid) {
			$ret[] = new Group($gid);
		}
		if($include_special && $user->grade) {
			$ret[] = new Group("grade_{$user->grade}");
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
	private static function get_special_group($group) {
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
		throw new I2Exception('Invalid special group '.$group.' passed to get_special_group');
	}
}

?>
