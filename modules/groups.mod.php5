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
	* Commonly accessed administrative groups.
	*/
	private static $admin_groups = NULL;
	private static $admin_all = NULL;

	/**
	* Commonly accesed group memberships
	*/
	private static $is_admin_all = NULL;
	private static $is_admin_groups = NULL;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER;

		// Ensure that admin groups are initialized
		if( self::$admin_groups === NULL || self::$admin_all === NULL) {
			self::$admin_groups = new Group('admin_groups');
			self::$admin_all = new Group('admin_all');

			self::$is_admin_groups = self::$admin_groups->has_member($I2_USER);
			self::$is_admin_all = self::$admin_all->has_member($I2_USER);
		}
		
		$args = array();
		if(count($I2_ARGS) <= 1) {
			$this->template = 'groups_home.tpl';
			$this->template_args['groups'] = Group::get_user_groups($I2_USER,FALSE);
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
		return 'Groups';
	}

	public function grant() {
		$this->template = 'permissions.tpl';
		return 'Grant Permissions';
	}

	public function revoke() {
		$this->template = 'permissions.tpl';
		return 'Revoke Permissions';
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
		$this->template_args['gid'] = $group->gid;
		
		$is_single_admin = $group->is_admin($I2_USER);
		$is_higher_admin = (self::$is_admin_all || (self::$is_admin_groups && substr($group->name,0,6) != 'admin_'));

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
					if(is_numeric($_REQUEST['uid'])) {
						$user = new User($_REQUEST['uid']);
					}
					else {
						$user = User::get_by_uname($_REQUEST['uid']);
					}
					if(!$user) {
						throw new I2Exception('Invalid user specified');
					}
					switch($_REQUEST['group_form']) {
						case 'add':	$group->add_user($user);
								break;
						case 'remove':	$group->remove_user($user);
								break;
					}
				}
			}
			$this->template_args['members'] = self::get_memberinfo($group);
			$this->template = 'groups_group.tpl';
		}
		else {
			$this->template = 'groups_error.tpl';
		}
		return array('Groups: Group', 'Groups');
	}
	/**
	* The master admin interface
	*/
	function admin() {
		global $I2_USER;
		if(self::$is_admin_groups) {
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
	}

	/**
	* Private helper function
	*/
	private static function get_memberinfo(Group $group) {
		$group_members = array();

		$uids = $group->get_members();
		foreach($uids as $uid) {
			$person_array = array();

			$person_user = new User($uid);
			$person_array['name'] = $person_user->name;
			$person_array['uid'] = $person_user->uid;
			$person_array['perms'] = $group->get_permissions($person_user);
			$person_array['has_perms'] = $person_array['perms']->num_rows() > 0;

			if ($group->is_admin($person_user)) {
				$person_array['admin'] = 'Admin';
			}

			$group_members[] = $person_array;
		}

		return $group_members;
	}
}
?>
