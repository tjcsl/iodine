<?php
/**
* Just contains the definition for the class {@link Help}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2006 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Module
* @filesource
*/

/**
* The module to provide help for all modules.
* @package core
* @subpackage Module
*/
class Help implements Module {

	/**
	* We don't use an intrabox for help.
	*/
	function display_box($disp) {
	}
	
	/**
	* Main display pane, displays help info.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		global $I2_ARGS;
		if(!isset($I2_ARGS[1])) {
			$disp->disp('help.tpl');
			return;
		}

		$mod = new $I2_ARGS[1]();
		if(method_exists($mod, 'display_help')) {
			$mod->display_help(new Display($I2_ARGS[1]), array_slice($I2_ARGS, 2));
			return;
		}
		$disp->disp('nohelp.tpl');
	}
	
	/**
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'Help';
	}

	/**
	* Not used, we don't display a box.
	*/
	function init_box() {
		return FALSE;
	}

	/**
	*/
	function init_pane() {
		global $I2_ARGS;

		if(isset($I2_ARGS[1])) {
			return 'Iodine Help: '.$I2_ARGS[1];
		}
		else {
			return 'Iodine Help';
		}
	}
}
?>
