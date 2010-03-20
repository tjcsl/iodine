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
	* The permission that grants all permissions to a user in a group.
	*/
	const PERM_ADMIN = 'GROUP_ADMIN';

	/**
	* The permission required to add users to a group.
	*/
	const PERM_ADD = 'GROUP_ADD';

	/**
	* The permission required to remove users from a group.
	*/
	const PERM_REMOVE = 'GROUP_REMOVE';

	/**
	* The permission required for a user to be able to join a group on their own.
	*/
	const PERM_JOIN = 'GROUP_JOIN';

	/**
	* The permission prefix for permissions granting "prefix" admin status. See {@link prefix_admin}.
	*/
	const PERM_ADMIN_PREFIX = 'ADMIN_';

	/**
	* The delimeter character that separates a group into a prefix and the rest of the name. See {@link prefix_admin}.
	*/
	const PREFIX_DELIMETER = '_';

	/**
	* Commonly accessed administrative groups.
	*/
	private static $all = NULL;
	private static $admin_all = NULL;
	private static $admin_eighth = NULL;
	private static $admin_mysql = NULL;

	/**
	* The encapsulated Group backend object to use for Group database info.
	*/
	private $wrap;
	
	public static function all() {
		if(self::$all === NULL) {
			self::$all = new Group('all');
		}
		return self::$all;
	}
	
	public static function admin_all() {
		if(self::$admin_all === NULL) {
			self::$admin_all = new Group('admin_all');
		}
		return self::$admin_all;
	}
	
	public static function admin_eighth() {
		if(self::$admin_eighth === NULL) {
			self::$admin_eighth = new Group('admin_eighth');
		}
		return self::$admin_eighth;
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
	public function get_static_members() {
		return $this->wrap->get_static_members();
	}

	/**
	* Gets all the dynamic members in this group. PHP rules not applicable!
	*
	* @return Array An array of UIDs.
	*/
	public function get_dynamic_members() {
		return $this->wrap->get_dynamic_members();
	}

	/**
	* Gets ALL of the members of this group, for real this time.
	*
	* @return Array An array of UIDs
	*/
	public function get_all_members() {
		return array_unique(array_merge($this->get_static_members, $this->get_dynamic_members));
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
	* BUG: This function does not actually accept UID's.
	* @param User $user Either the {@link User} object or the UID of the user you want to add to this group.
	*/
	public function add_user(User $user) {
		return $this->wrap->add_user($user);
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
		$this->remove_static_members();
		$this->remove_dynamic_rules();
	}

	/**
	 * Removes all static members from this group
	 */
	public function remove_static_members() {
		return $this->wrap->remove_static_members();
	}

	/**
	* Removes dynamic rules for this group.
	*/
	public function remove_dynamic_rules() {
		return $this->wrap->remove_dynamic_rules();
	}

	/**
	* Grants a permission to a certain user in this group.
	*
	* @param mixed $subject The user or group to grant the permission to.
	* @param string $perm The permission to grant.
	*/
	public function grant_permission($subject, Permission $perm) {
		return $this->wrap->grant_permission($subject, $perm);
	}
	
	/**
	* Revokes a permission from a user for this group.
	*
	* @param mixed $subject The user or group to revoke the permission from.
	* @param string $perm The permission to revoke.
	*/
	public function revoke_permission($subject, $perm) {
		return $this->wrap->revoke_permission($subject, $perm);
	}

	/**
	* Get all permissions for a certain user in this group.
	*
	* @param mixed $subject Which user or group to list permissions for.
	* @return Array An array containing all of the permissions for this group for the specified user or group.
	*/
	public function get_permissions($subject) {
		return $this->wrap->get_permissions($subject);
	}

	/**
	* Determines whether the specified user has a certain permission in this group.
	*
	* @param mixed $subject The user or group for which to check the permission.
	* @param string $perm Which permission to check to see if the user has.
	* @return bool TRUE if $subject has permission $perm in this group, FALSE otherwise.
	*/
	public function has_permission($subject, $perm) {
		if(is_string($perm)) {
			if($perm=="join") {
				$perm=Permission::getPermission('GROUP_JOIN');
			}
		}
		return $this->wrap->has_permission($subject, $perm);
	}

	/**
	* Lists all available permissions in the system, or lists a specific permission.
	*
	* @param int $pid The PID to get information about, if specified.
	* @return mixed A {@link Result} object with the PID, name, and description of all permissions if $pid is not passed. If $pid is passed, then returns an array of information about that specific permission.
	*/
	public static function list_permissions($pid = NULL) {
		return GroupSQL::list_permissions($pid);
	}

	/**
	* Lists all groups that have permissions in this group.
	*
	* If $pid is specified, returns all groups that have that permission in this group. If not specified, returns all groups that have any permissions in this group.
	* @param int $pid What permission the group must have in this group to be included in the returned list.
	* @return array An array of {@link Group}s, as outlined above.
	*/
	public function groups_with_perm($pid = NULL) {
		return $this->wrap->groups_with_perm($pid);
	}

	/**
	* Lists all users that have permissions in this group.
	*
	* If $pid is specified, returns all users that have that permission in this group. If not specified, returns all users that have any permissions in this group.
	* @param int $pid What permission the user must have in this group to be included in the returned list.
	* @return array An array of {@link User}s, as outlined above.
	*/
	public function users_with_perm($pid = NULL) {
		return $this->wrap->users_with_perm($pid);
	}

	/**
	* Returns a list of all dynamic membership rules for this group.
	*
	* @return array An array describing each dynamic membership rule.
	*/
	public function list_dynamic_rules() {
		return $this->wrap->list_dynamic_rules();
	}

	/**
	* Delete a group's dynamic rules.
	*/
	public function delete_dynamic_rules() {
		return $this->wrap->delete_dynamic_rules();
	}

	/**
	* Add a dynamic rule to a group.
	*/
	public function add_dynamic_rule($type,$query) {
		return $this->wrap->add_dynamic_rule($type,$query);
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
	public static function get_static_groups(User $user, $perms = NULL) {
		return GroupSQL::get_static_groups($user,$perms);
	}

	/**
	* Gets the dynamic groups of which a user is a member.
	*
	* @param User $user The {@link User} for which to fetch the groups.
	* @return array The Groups in which the user has membership.
	*/
	public static function get_dynamic_groups(User $user, $perms = NULL) {
		return GroupSQL::get_dynamic_groups($user,$perms);
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
	* Gets all groups in which a user is a member.
	*
	* This includes both static and dynamic groups.
	*
	* @param User $user The user.
	* @return Array An array of {@link Group} objects that the user is a member of.
	*/
	public static function get_user_groups(User $user, $perms = NULL) {
		d('begin static');
		$static = Group::get_static_groups($user, $perms);
		d('end static; begin dynamic');
		$dynamic = Group::get_dynamic_groups($user, $perms);
		d('end dynamic');
		$concat = array_merge($static, $dynamic);
		usort($concat, array('Group', 'name_cmp'));
		return $concat;
	}

	/**
	* Gets all groups in which a user has a specific permission.
	*
	* @param User $user The user.
	* @param Permission $perm The permission for which to check.
	* @return Array An array of {@link Group} objects that the user has the specified permission in.
	*/
	public static function get_userperm_groups(User $user, Permission $perm) {
		return GroupSQL::get_userperm_groups($user, $perm);
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
	public static function add_group($name,$description='No description available',$gid=NULL) {
		$name = str_replace("&","&amp;",$name); // this is supposed to fix an error where the & character is used and breaks validation.
		return GroupSQL::add_group($name,$description,$gid);
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
		if($this->has_permission($user, Permission::getPermission(Group::PERM_ADMIN))) {
			return TRUE;
		}

		// admin_all members get admin access to all groups
		if(Group::admin_all()->has_member($user)) {
			return TRUE;
		}

		// prefix admins get admin access to groups with their prefix
		if(self::prefix($this->name)
			&& Permission::perm_exists(self::PERM_ADMIN_PREFIX . self::prefix($this->name))
			&& Group::all()->has_permission($user, Permission::getPermission(self::PERM_ADMIN_PREFIX . self::prefix($this->name)))) {
			return TRUE;
		}

		// They're not an admin of this group nor are they a member of admin_all
		return FALSE;
	}

	/**
	* Generates several groups at once.
	*
	* @param mixed $gids Either an array of GIDs or a single GID
	* @return array An array of {@link Group} objects.
	*/
	public static function generate($gids) {
		if(is_array($gids)) {
			$ret = array();
			foreach($gids as $gid) {
				$ret[] = new Group($gid);
			}
		} else {
			return new Group($gids);
		}
		return $ret;
	}

	/**
	 * Gets all prefixes for which a user is a "prefix" admin.
	 *
	 * @param User $user The user.
	 */
	public static function user_admin_prefixes(User $user) {
		return GroupSQL::user_admin_prefixes($user);
	}

	/**
	* Determines whether or not a user is a "prefix" admin.
	*
	* A prefix admin is a user who is an administrator for all groups that
	* have the same prefix (i.e. the eighth period office has admin rights
	* over all groups beginning with eighth_. This checks to see if the
	* user has the permission Group::PERM_ADMIN_PREFIX appended by the
	* prefix of the group name passed. They must have the permission in the
	* group 'all'.
	*
	* @param string $name The name of the group.
	* @param User $user Whom we are checking whether or not they are a prefix admin.
	* @return bool TRUE if they are a "prefix" admin, FALSE otherwise.
	*/
	public static function prefix_admin($name, $user) {
		// Group has no prefix, thus has no prefix admins
		if(Group::prefix($name) === FALSE) {
			return FALSE;
		}

		if(Group::all()->has_permission($user, Permission::getPermission(Group::PERM_ADMIN_PREFIX . Group::prefix($name)))) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Determines the prefix for a group name.
	*
	* @param string $name The name of the group.
	* @return string The prefix of the group name. Returns FALSE on error.
	*/
	public static function prefix($name) {
		if(strpos($name, Group::PREFIX_DELIMETER) === FALSE) {
			return FALSE;
		}
		return substr($name, 0, strpos($name, Group::PREFIX_DELIMETER));
	}

	/**
	 * Determines whether the current user can create a group with a given name.
	 *
	 * @param string $groupname The name of the group; defaults to NULL, representing any group.
	 * @return bool TRUE if the user is allowed to create the group, FALSE otherwise.
	 */
	public static function user_can_create($groupname = NULL) {
		global $I2_AUTH, $I2_USER;

		return $I2_AUTH->get_auth_method() == 'master'
			|| self::admin_all()->has_member($I2_USER)
			|| self::prefix_admin($groupname, $I2_USER);
	}

	/**
	 * Sort groups by name.
	 * Use this for sorting group arrays that haven't been or can't be easily sorted by the database backend.
	 *
	 * @param object $group1 The first group.
	 * @param object $group2 The second group.
	 * @return int Depending on order, less than 0, 0, or greater than 0.
	 */
	public static function name_cmp($group1, $group2) {
		return strcasecmp($group1->name, $group2->name);
	}
}
?>
