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
class Groups implements Module {

	/**
	* Template for the specified action
	*/
	private $template = 'group_home.tpl';

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();

	/**
	* Required by the {@link Module} interface.
	*/
	public function init_pane() {
		global $I2_ARGS, $I2_USER;

		$args = array();
		if(count($I2_ARGS) <= 1) {
			$this->template = 'groups_home.tpl';
			$this->template_args['groups'] = Group::get_static_groups($I2_USER);
			if (Group::admin_all()->has_member($I2_USER)) {
				$this->template_args['admin'] = 1;
			}
			return array('Groups: Home', 'Groups');
		}
		else {
			$method = $I2_ARGS[1];
			if(method_exists($this, $method)) {
				return $this->$method();
			}
			$this->template = 'groups_error.tpl';
			$this->template_args = array('method' => $method, 'args' => $I2_ARGS);
		}
		return array('Error', 'Error');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	public function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	public function init_box() {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	public function display_box($display) {
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	public function get_name() {
		return 'Groups';
	}

	/**
	* Grants a new permission to a user in a certain group.
	*
	* Uses parameters in $I2_ARGS:
	* <ul><li>$I2_ARGS[2]: UID of user</li>
	* <li>$I2_ARGS[3]: GID of group</li></ul>
	*/
	public function grant() {
		global $I2_USER, $I2_ARGS;
		$grp = new Group($I2_ARGS[3]);

		if (!$grp->is_admin($I2_USER)) {
			$this->template = 'groups_error.tpl';
			return 'Permission denied';
		}

		if(isset($_REQUEST['groups_grant_permission'])) {
			$grp->grant_permission(new User($I2_ARGS[2]), $_REQUEST['groups_grant_permission']);
			redirect('groups/pane/'.$grp->gid);
		}
		else {
			$this->template_args['subject'] = new User($I2_ARGS[2]);
			$this->template_args['group'] = new Group($I2_ARGS[3]);
			$this->template_args['perms'] = Group::list_permissions();
		}
		$this->template = 'grant_perm.tpl';
		return 'Groups: Grant Permissions';
	}

	public function grantgroup() {
		global $I2_USER, $I2_ARGS;
		$grp = new Group($I2_ARGS[3]);

		if (!$grp->is_admin($I2_USER)) {
			$this->template = 'groups_error.tpl';
			return 'Permission denied';
		}

		if(isset($_REQUEST['groups_grant_permission'])) {
			$grp->grant_permission(new Group($I2_ARGS[2]), $_REQUEST['groups_grant_permission']);
			redirect('groups/pane/'.$grp->gid);
		}
		else {
			$this->template_args['subject'] = new Group($I2_ARGS[2]);
			$this->template_args['group'] = new Group($I2_ARGS[3]);
			$this->template_args['perms'] = Group::list_permissions();
			$this->template_args['group_perm'] = TRUE;
		}
		$this->template = 'grant_perm.tpl';
		return 'Groups: Grant Permissions';
	}

	/**
	* Revokes a permission from a user in a certain group.
	*
	* Uses parameters in $I2_ARGS:
	* <ul><li>$I2_ARGS[2]: UID of user</li>
	* <li>$I2_ARGS[3]: GID of group</li>
	* <li>$I2_ARGS[4]: Permission to revoke</li></ul>
	*/	
	public function revoke() {
		global $I2_USER, $I2_ARGS;
		$grp = new Group($I2_ARGS[3]);

		if (!$grp->is_admin($I2_USER)) {
			$this->template = 'groups_error.tpl';
			return 'Permission denied';
		}

		$grp->revoke_permission(new User($I2_ARGS[2]), $I2_ARGS[4]);

		redirect('groups/pane/'.$grp->gid);
	}

	/**
	* Revokes a permission from a group in a certain group.
	*
	* Uses parameters in $I2_ARGS:
	* <ul><li>$I2_ARGS[2]: GID of group to remove permission from</li>
	* <li>$I2_ARGS[3]: GID of group</li>
	* <li>$I2_ARGS[4]: Permission to revoke</li></ul>
	*/	
	public function revokegroup() {
		global $I2_USER, $I2_ARGS;
		$grp = new Group($I2_ARGS[3]);

		if (!$grp->is_admin($I2_USER)) {
			$this->template = 'groups_error.tpl';
			return 'Permission denied';
		}

		$grp->revoke_permission(new Group($I2_ARGS[2]), $I2_ARGS[4]);

		redirect('groups/pane/'.$grp->gid);
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
	public function pane() {
		global $I2_ARGS, $I2_USER, $I2_SQL;
		$group = new Group($I2_ARGS[2]);
		$this->template_args['group'] = $group->name;
		$this->template_args['gid'] = $group->gid;
		
		// Only admins can alter permissions
		$can_set_perms = $group->is_admin($I2_USER);
		$can_add = $group->has_permission($I2_USER, Group::PERM_ADD);

		$action = FALSE;
		if( isset($_REQUEST['group_form'])) {
			if(isset($_REQUEST['uid'])) {
				$user = new User($_REQUEST['uid']);
				if(!$user) {
					$this->template = 'groups_error.tpl';
					return 'Invalid user specified';
				}
			}
			if(isset($_REQUEST['gid'])) {
				$req_group = new Group($_REQUEST['gid']);
				if(!$req_group) {
					$this->template = 'groups_error.tpl';
					return 'Invalid group specified';
				}
			}
			$action = $_REQUEST['group_form'];
		}

		$this->template_args['can_set_perms'] = $this->template_args['can_add'] = $this->template_args['can_remove'] = FALSE;

		if($group->is_admin($I2_USER)) {
			$this->template_args['can_set_perms'] = TRUE;
			$this->template_args['perms'] = Group::list_permissions();

			switch($action) {
				case 'grant':
					$group->grant_permission($user, $_REQUEST['permission']);
					break;
				case 'grantgroup':
					$group->grant_permission($req_group, $_REQUEST['permission']);
					break;
			}
		}

		if($group->has_permission($I2_USER, Group::PERM_ADD)) {
			$this->template_args['can_add'] = TRUE;

			if($action == 'add') {
				$group->add_user($user);
			}
		}

		if($group->has_permission($I2_USER, Group::PERM_REMOVE)) {
			$this->template_args['can_remove'] = TRUE;
			
			if($action == 'remove') {
				$group->remove_user($user);
			}
		}

		$this->template_args['members'] = self::get_memberinfo($group);
		$this->template_args['perm_users'] = self::get_perm_users($group);
		$this->template_args['perm_groups'] = self::get_perm_groups($group);
		$this->template = 'groups_group.tpl';
		return 'Groups: ' .  $group->name;
	}

	/**
	 * Private helper function
	 */
	private static function get_memberinfo(Group $group) {
		$group_members = array();
		
		$users = $group->members_obj_sorted;
		foreach($users as $person_user) {
			$person_array = array();

			$person_array['name'] = $person_user->name;
			$person_array['uid'] = $person_user->uid;

			if ($group->is_admin($person_user)) {
				$person_array['admin'] = 'Admin';
			}

			$group_members[] = $person_array;
		}

		return $group_members;
	}

	/**
	* Helper function to get info about users that have permissions in this group.
	*/
	private static function get_perm_users(Group $group) {
		$users = $group->users_with_perm();

		if(count($users) < 1) {
			return array();
		}

		$ret = array();
		foreach($users as $usr) {
			$row['name'] = $usr->name;
			$row['uid'] = $usr->uid;
			$row['perms'] = $group->get_permissions($usr);
			foreach($row['perms'] as $i=>$perm) {
				$row['perms'][$i] = Group::list_permissions($perm);
			}
			$ret[] = $row;
		}

		return $ret;
	}

	/**
	* Helper function to get info about groups that have permissions in this group.
	*/
	private static function get_perm_groups(Group $group) {
		$groups = $group->groups_with_perm();

		if(count($groups) < 1) {
			return array();
		}

		$ret = array();
		foreach($groups as $grp) {
			$row['name'] = $grp->name;
			$row['gid'] = $grp->gid;
			$row['perms'] = $group->get_permissions($grp);
			foreach($row['perms'] as $i=>$perm) {
				$row['perms'][$i] = Group::list_permissions($perm);
			}
			$ret[] = $row;
		}

		return $ret;
	}
	
	/**
	* The master admin interface
	*/
	function admin() {
		global $I2_USER;
		if(Group::admin_all()->has_member($I2_USER)) {
			if(isset($_REQUEST['group_admin_form'])) {
				if($_REQUEST['group_admin_form'] == 'add') {
					Group::add_group($_REQUEST['name']);
				}
				if($_REQUEST['group_admin_form'] == 'remove') {
					$group = new Group($_REQUEST['name']);
					$group->delete_group();
				}
			}
			$this->template_args['groups'] = Group::get_all_groups();
			$this->template = 'groups_admin.tpl';
		}
		else {
			$this->template = 'groups_error.tpl';
		}
		return 'Groups: Admin';
	}
}
?>
