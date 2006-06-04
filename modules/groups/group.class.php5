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
	* The encapsulated Group backend object to use for Group database info.
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

	public function __get($var) {
		return $this->wrap->__get($var);
	}

	public function __construct($group) {
		$this->wrap = new GroupSQL($group);
	}
	
	/**
	* Gets all of the members in this group.
	*
	* @return Array An array of UIDs, one for each member in this group.
	*/
	public function get_members() {
		return $this->wrap->get_members();
	}
	
	/**
	* Gets all groups at once, or all groups with a certain prefix.
	*
	* @param string $module If passed, all returned group names will start with this string. If not passed, just return all groups in the system.
	* @return Array An array of {@link Group} objects for all of the groups requested.
	*/
	public static function get_all_groups($module = NULL) {
		return GroupSQL::get_all_groups($module);
	}

	/**
	* Gets the names of all groups in the database.
	*
	* @return Result A {@link Result} object with all group names.
	*/
	public static function get_all_group_names() {
		return GroupSQL::get_all_group_names();
	}

	/**
	* Adds a user to this group.
	*
	* @param mixed $user Either the {@link User} object or the UID of the user you want to add to this group.
	*/
	public function add_user($user) {
		return $this->wrap->add_user($user);
	}

	/**
	* Forces a user addition to this group, ignoring permissions.  Use with care!
	*
	* @param mixed $user Either the {@link User} object or the UID of the user you want to add to this group.
	*/
	public function add_user_force($user) {
		return $this->wrap->add_user_force($user);
	}

	/**
	* Removes a certain user from this group.
	* @param User $user The user to remove from this group.
	*/
	public function remove_user(User $user) {
		return $this->wrap->remove_user($user);
	}

	/**
	* Removes all users from this group.
	*/
	public function remove_all_members() {
		return $this->wrap->remove_all_members();
	}

	/**
	* Grants a permission to a certain user in this group.
	*
	* @param User $user The user to grant the permission to.
	* @param string $perm The permission to grant.
	*/
	public function grant_permission(User $user, $perm) {
		return $this->wrap->grant_permission($user, $perm);
	}
	
	/**
	* Revokes a permission from a user for this group.
	*
	* @param User $user The user to revoke the permission from.
	* @param string $perm The permission to revoke.
	*/
	public function revoke_permission(User $user, $perm) {
		return $this->wrap->revoke_permission($user, $perm);
	}

	/**
	* Get all permissions for a certain user in this group.
	*
	* @param User $user Which user to list permissions for.
	* @return Result A {@link Result} object containing all of the permissions for this group for the specified user.
	*/
	public function get_permissions(User $user) {
		return $this->wrap->get_permissions($user);
	}

	/**
	* Determines whether the specified user has a certain permission in this group.
	*
	* @param User $user The user for which to check the permission.
	* @param string $perm Which permission to check to see if the user has.
	* @return bool TRUE if $user has permission $perm in this group, FALSE otherwise.
	*/
	public function has_permission(User $user, $perm) {
		return $this->wrap->has_permission($user, $perm);
	}

	/**
	* Determines if a user is a member of this group.
	*
	* If this is an admin_* group, this method also checks to see if the user is a member of the admin_all group, and returns TRUE if they are. Otherwise, just checks if this group is listed as one that the user is a member of.
	*
	* @param User $user The user for which to check membership. If not specified or NULL, defaults to the currently logged-in user.
	* @return bool TRUE if the user is considered a member of this group, FALSE otherwise.
	*/
	public function has_member($user=NULL) {
		return $this->wrap->has_member($user);
	}

	/**
	* Change the name of this group in the database.
	*
	* @param string $name The new name of the group.
	*/
	public function set_group_name($name) {
		return $this->wrap->set_group_name($name);
	}

	/**
	* Gets the groups of which a user is a member.
	*
	* @param User $user The {@link User} for which to fetch the groups.
	* @return array The Groups in which the user has membership.
	*/
	public static function get_user_groups(User $user, $include_special = TRUE, $perms = NULL) {
		return GroupSQL::get_user_groups($user,$include_special, $perms);
	}

	/**
	* Gets all groups for which a user is an admin.
	*
	* @param User $user The user who must be considered an admin in the groups that are returned.
	* @return Array An array of {@link Group} objects that the user is an administrator in.
	*/
	public static function get_admin_groups(User $user) {
		return GroupSQL::get_admin_groups($user);
	}
	
	/**
	* Deletes this group from the database, including all membership information associated with this group.
	*/
	public function delete_group() {
		return $this->wrap->delete_group();
	}

	/**
	* Creates a new group.
	*
	* @param string $name The name for the new group.
	*/
	public static function add_group($name,$description="No description available",$gid=NULL) {
		return GroupSQL::add_group($name,$description,$gid);
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
			case 'all':
					  return -999;
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
			case '-999':
					  return 'all';
		}
		return FALSE;
	}

	public static function get_special_groups($user = NULL) {
		if (!$user) {
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
			   ),
				new Group(array(
					'gid'=>-999,
					'name'=>'all',
					'description'=>'All users')
						  )
		 );
		}
		else {
				  $user = new User($user);
				  if ($user->objectClass == 'tjhsstStudent') {
				  		$grade = $user->grade;
				  } else {
						// Make gid be -8, which is grade_staff
						$grade = 8;
				  }
				  return array ( new Group(array (
							 'gid' => -1*$grade,
							 'name' => 'grade_'.$grade,
							 'description' => 'Grade '.$grade)),
				new Group(array(
					'gid'=>-999,
					'name'=>'all',
					'description'=>'All users')
						  )
							 );
		}
	}

	/**
	* Determines if a user is an administrator for this group.
	*
	* To be considered an admin for a group, a user must either be a member with the administrator privilege, or must be part of the admin_all group.
	*
	* @param User $user Which user to check admin-ness of.
	* @return bool TRUE if the user is an administrator of the group, FALSE otherwise.
	*/
	public function is_admin(User $user) {
		return $this->wrap->is_admin($user);
	}

	/**
	* Generate a bunch of groups at once.
	*
	* @param Array $gids An array of Group IDs to generate groups for.
	* @return Array An array of {@link Group} objects
	*/
	public static function generate($gids) {
		return GroupSQL::generate($gids);
	}
}
?>
