<?php
/**
* Just contains the definition for the module {@link LDAPInterface}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Admin
* @filesource
*/

/**
* A module to run direct ldap queries (for admins/devs only).
* Wholeheartedly ripped off from the MySQL interface.
* 
* @package modules
* @subpackage Admin
*/
class LDAPInterface implements Module {

	private $query_data = FALSE;
	private $query = FALSE;
	private $dn = FALSE;
	private $searchtype = 'search';
	private $attrs = FALSE;

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Better message.
	*/
	function init_cli() {
		return "ldapinterface";
	}

	/**
	* Make it look like it works, but it doesn't.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return "<div>Access Denied</div>";
	}

	/**
	* Unused; we don't display a box (yet)
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_box($disp) {
		return FALSE;
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		$header_data = NULL;
		$disp->disp('ldapinterface_pane.tpl', 
			array( 
				'query_data' => $this->query_data, 
				'query' => addslashes($this->query), 
				'last_dn' => addslashes($this->dn),
				'searchtype' => $this->searchtype,
				'last_attrs' => $this->attrs
			));
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'ldapinterface';
	}

	/**
	* Unused; we don't display a box
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
	*/
	function init_box() {
		return FALSE;
	}

	/**
	* Performs all initialization necessary for this module to be
	* displayed as the main page.
	*
	* @returns mixed Either a string, which will be the title for both the
	*                main pane and for part of the page title, or an array
	*                of two strings: the first is part of the page title,
	*                and the second is the title of the content pane. To
	*                specify no titles, return an empty array. To specify
	*                that this module has no main content pane (and will
	*                show an error if someone tries to access it as such),
	*                return FALSE.
	* @abstract
	*/
	function init_pane() {
		global $I2_LDAP, $I2_ARGS, $I2_USER;

		// Only available to people with LDAP admin privs
		// TODO: It may be permissible to allow non-admins with user bind
		if (! $I2_USER->is_ldap_admin()) {
			return FALSE;
		}
		
		if( isset($_POST['ldapinterface_submit']) && $_POST['ldapinterface_submit']) {
			if (isSet($_POST['ldapinterface_query'])) {
				$this->query = $_POST['ldapinterface_query'];
			} elseif (!$this->query) {
				$this->query = 'objectClass=*';
			}
			
			if (isSet($_POST['ldapinterface_dn'])) {
				$this->dn = $_POST['ldapinterface_dn'];
			} else {
				$this->dn = i2config_get('base_dn','dc=tjhsst,dc=edu','ldap');
			}
			
			if (isSet($_POST['ldap_searchtype'])) {
				if ($_POST['ldap_searchtype'] == 'list') {
					$this->searchtype = 'list';
				} else if ($_POST['ldap_searchtype'] == 'search') {
					$this->searchtype = 'search';
				} else if ($_POST['ldap_searchtype'] == 'read') {
					$this->searchtype = 'read';
				} else if ($_POST['ldap_searchtype'] == 'delete') {
					$this->searchtype = 'delete';
				} else if ($_POST['ldap_searchtype'] == 'delete_recursive') {
					$this->searchtype = 'delete_recursive';
				}
			}

			$myattrs = array('*');

			if (isSet($_POST['ldapinterface_attrs']) && $_POST['ldapinterface_attrs'] != '') {
				$this->attrs = $_POST['ldapinterface_attrs'];
				$myattrs = explode(',',$this->attrs);
			} else {
				$this->attrs = FALSE;
			}
			
			$ldap = $I2_LDAP;
			try {
				$res = NULL;
				if ($this->searchtype == 'search') {
					$res = $ldap->search($this->dn,$this->query,$myattrs);
					$this->query_data = $res->fetch_all_arrays(Result::ASSOC);
				} else if ($this->searchtype == 'list') {	
					$res = $ldap->search_one($this->dn,$this->query,$myattrs);
					$this->query_data = $res->fetch_all_arrays(Result::ASSOC);
				} else if ($this->searchtype == 'read') {
					$res = $ldap->search_base($this->dn);
					$this->query_data = $res->fetch_all_arrays(Result::ASSOC);
				} else if ($this->searchtype == 'delete') {
					if (isSet($this->query)) {
						$res = $ldap->search($this->dn,$this->query,array('dn'));
						while ($dn = $res->fetch_single_value()) {
							$res = $ldap->delete($dn);
							$this->query_data = $res;
						}
					} else {
						$res = $ldap->delete($dn);
						$this->query_data = $res;
					}
				} else if ($this->searchtype == 'delete_recursive') {
					$res = $ldap->delete_recursive($this->dn);
					$this->query_data = $res;
				}
				
				d("LDAP $this->searchtype Results:",7);
				d_r($this->query_data,7);
			} catch (I2Exception $e) {
				$this->query_data = 'LDAP error: '.$e->get_message();
			}
		}
		return 'LDAP Admin Interface';
	}
	
}
?>
