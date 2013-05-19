<?php
/**
* Just contains the definition for the module {@link CodeInterface}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Admin
* @filesource
*/

/**
* A module to run raw php code (for admins/devs only, to test functionality 
* before creating interfaces)
* 
* @package modules
* @subpackage Admin
*/
class CodeInterface extends Module {
	private $return;
	private $code;

	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		$disp->disp('codeinterface_pane.tpl', array('code' => $this->code, 'return' => $this->return));
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'codeinterface';
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
		global $I2_SQL, $I2_USER;

		// Only available to people in the 'admin_all' group
		if (! $I2_USER->is_group_member('admin_all')) {
			return FALSE;
		}
		
		if ( isset($_POST['codeinterface_submit']) && $_POST['codeinterface_submit'] && $_POST['codeinterface_code']) {
			d('got code');
			$this->code = stripslashes($_POST['codeinterface_code']);
			try {
				$this->return = print_r(eval($this->code), 1);
			} catch (Exception $e) {
				$this->return .= "\nError: " . $e->get_message();
			}
		}
		return 'Code Testing Interface';
	}
	
}
?>
