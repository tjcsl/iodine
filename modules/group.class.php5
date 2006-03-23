<?php
/**
* Just contains the definition for the class {@link Group}.
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
class Group {

	/**
	* Commonly accessed administrative groups.
	*/
	private static $admin_groups = NULL;
	private static $admin_all = NULL;
	private static $admin_eighth = NULL;
	private static $admin_ldap = NULL;
	private static $admin_mysql = NULL;

	/**
	* An encapsulated GroupSQL or GroupLDAP object
	*/
	private $wrap;
	
	public static function admin_all() {
		if(self::$admin_all === NULL) {
			self::$admin_all = new Group('admin_all');
		}
		return self::$admin_all;
	}
	
	public static function admin_groups() {
		if(self::$admin_groups === NULL) {
			self::$admin_groups = new Group('admin_groups');
		}
		return self::$admin_groups;
	}

	public static function admin_eighth() {
		if(self::$admin_eighth === NULL) {
			self::$admin_eighth = new Group('admin_eighth');
		}
		return self::$admin_eighth;
	}

	public static function admin_ldap() {
		if (self::$admin_ldap === NULL) {
			self::$admin_ldap = new Group('admin_ldap');
		}
		return self::$admin_ldap;
	}

	public static function admin_mysql() {
		if (self::$admin_mysql === NULL) {
			self::$admin_mysql = new Group('admin_mysql');
		}
		return self::$admin_mysql;
	}

	private $mygid;
	private $myname;

	public function __get($var) {
		return $this->wrap->__get($var);
	}

	public function __construct($group) {
		$this->wrap = new GroupSQL($group);
	}

	public function get_members() {
		return $this->wrap->get_members();
	}
	
	/**
	* Gets all groups.
	*
	* @return Array An containing all of the Group objects.
	*/
	public static function get_all_groups($module = NULL) {
		return GroupSQL::get_all_groups($module);
	}

	/**
	* Gets the name of every group
	*
	* @return Result A Result containing each group's name.
	*/
	public static function get_all_group_names() {
		return GroupSQL::get_all_group_names();
	}

	public function add_user($user) {
		return $this->wrap->add_user($user);
	}

	public function remove_user(User $user) {
		return $this->wrap->remove_user($user);
	}

	public function remove_all_members() {
		return $this->wrap->remove_all_members();
	}

	public function grant_permission(User $user, $perm) {
		return $this->wrap->grant_permission($user, $perm);
	}
	
	public function revoke_permission(User $user, $perm) {
		return $this->wrap->revoke_permission($user, $perm);
	}

	public function get_permissions(User $user) {
		return $this->wrap->get_permissions($user);
	}

	public function has_permission(User $user, $perm) {
		return $this->wrap->has_permission($user, $perm);
	}

	/**
	* Determine whether a user is a member of this group.
	*
	* Returns whether or not $user is a member of the group. If $user is ommitted, or NULL, the currently logged-in user is checked.
	*
	* @param User $user The user to check, or $I2_USER if unspecified.
	* @return bool TRUE if the user is a member of the group, FALSE otherwise.
	*/
	public function has_member($user=NULL) {
		return $this->wrap->has_member($user);
	}

	public function set_group_name($name) {
		return $this->wrap->set_group_name($name);
	}

	/**
	* Gets the groups of which a user is a member.
	*
	*
	* @param User $user The {@link User} for which to fetch the groups.
	* @return array The Groups in which the user has membership.
	*/
	public static function get_user_groups(User $user, $include_special = TRUE, $perms = NULL) {
		return GroupSQL::get_user_groups($user,$include_special, $perms);
	}

	/**
	* Gets the admin groups of which a user is a member.
	*
	*
	* @param int $uid The userID of which to fetch the groups.
	* @return array The group IDs of admin groups for the given user.
	*/
	public static function get_admin_groups(User $user) {
		return GroupSQL::get_admin_groups($user);
	}
	/**
	* Deletes this group.
	*/
	public function delete_group() {
		return $this->wrap->delete_group();
	}

	/**
	* Adds a new group with the passed name.
	*
	* @param string $name The name of the new group
	*/
	public static function add_group($name) {
		return $this->wrap->add_group($name);
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
		return FALSE;
	}

	public static function get_special_groups() {
		return array(
			new Group(array(
				'gid'=>-9,
				'name'=>'grade_9',
				'description'=>'Freshmen')
			),
			new Group(array(
				'gid'=>-10,
				'name'=>'grade_10',
				'description'=>'Sophomores')
			),
			new Group(array(
				'gid'=>-11,
				'name'=>'grade_11',
				'description'=>'Juniors')
			),
			new Group(array(
				'gid'=>-12,
				'name'=>'grade_12',
				'description'=>'Seniors')
			),
			new Group(array(
				'gid'=>-8,
				'name'=>'grade_staff',
				'description'=>'Staff')
			)
		);
	}

	public function is_admin(User $user) {
		return $this->wrap->is_admin($user);
	}

	public static function generate($gids) {
		return GroupSQL::generate($gids);
	}
}
?>
