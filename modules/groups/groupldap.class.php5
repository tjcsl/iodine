<?php
/**
* Just contains the definition for the class {@link GroupLDAP}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Group
* @filesource
*/

/**
* The module runs groups using LDAP
* @package modules
* @subpackage Group
*/
class GroupLDAP {

	private $mygid;
	private $myname;
	
	/**
	* groupname to GID mapping
	*/
	private static $gid_map;

	public function __get($var) {
		switch($var) {
			case 'gid':
				return $this->mygid;
			case 'name':
				return $this->myname;
			case 'special':
				return ($this->mygid < 0);
			case 'members':
				return $this->get_members();
			case 'members_obj':
				return User::id_to_user($this->get_members());
		}
	}

	public function __construct($group) {
	}

	public function get_members() {
	}
	
	public static function get_all_groups($module = NULL) {
	}

	public static function get_all_group_names() {
	}

	public function add_user($user) {
	}

	public function remove_user(User $user) {
	}

	public function remove_all_members() {
	}

	public function grant_admin(User $user) {
	}
	
	public function revoke_admin(User $user) {
	}

	public function has_member($user=NULL) {
	}

	public function set_group_name($name) {
	}

	public static function get_user_groups(User $user, $include_special = TRUE) {
	}

	public static function get_admin_groups(User $user) {
	}
	
	public function delete_group() {
	}

	public static function add_group($name) {
	}

	public function is_admin(User $user) {
	}

}
?>
