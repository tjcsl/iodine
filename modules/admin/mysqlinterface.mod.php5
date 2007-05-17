<?php
/**
* Just contains the definition for the module {@link MySQLInterface}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Admin
* @filesource
*/

/**
* A module to run direct mysql queries (for admins/devs only).
* 
* @package modules
* @subpackage Admin
*/
class MySQLInterface implements Module {
	private $query_data = FALSE;
	private $query = FALSE;

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
		if(!is_string($this->query_data)) {
			if($this->query_data != NULL) {
				$header_data = array();
				foreach($this->query_data as $dat) {
					foreach($dat as $key=>$unused) {
						if(!is_int($key))
							$header_data[] = $key;
					}
					break;
				}
				$this->query_data->rewind();
			}
		}
		$disp->disp('mysqlinterface_pane.tpl', array( 'query_data' => $this->query_data, 'header_data' => $header_data, 'query' => $this->query));
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'mysqlinterface';
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
		global $I2_SQL;

		// Only available to people in the 'admin_mysql' group
		$mysql_group = new Group('admin_mysql');
		if(!$mysql_group->has_member()) {
			return FALSE;
		}
		
		if( isset($_POST['mysqlinterface_submit']) && $_POST['mysqlinterface_submit'] && $_POST['mysqlinterface_query']) {
			$this->query = $_POST['mysqlinterface_query'];
			
			try {
				$this->query_data = $I2_SQL->query($this->query);
			} catch (I2Exception $e) {
				if (strstr($e->get_message(),'MySQL error') == 0)
					$this->query_data = $e->get_message();
				else
					$this->query_data = 'MySQL error: '.$e->get_message();
			}
		}
		return 'MySQL Admin Interface';
	}
}
?>
