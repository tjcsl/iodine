<?php
/**
* Just contains the definition for the {@link Module} Info.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Info
* @filesource
*/

/**
* A Module to display static information (such as help), so that the information does not require an entire class devoted to it.
* @package modules
* @subpackage Info
*/
class Info extends Module {
	private $info_tpl;
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		global $I2_ARGS;
		
		try {
			$disp->disp($this->info_tpl);
		}
		catch( I2Exception $e ) {
			$disp->disp('error.tpl');
		}
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'info';
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
	*/
	function init_pane() {
		global $I2_ARGS,$I2_USER;
		
		if(isset($I2_ARGS[1])) {
			$arr = array_slice($I2_ARGS, 1);
			$mainhelppage = false;
		} else {
			// They might have hit 'help' when on the main page, when there are no arguments, so load the help for their startpage
			$arr = array($I2_USER->startpage);
			$mainhelppage = true;
		}
		
		$last_try = FALSE;
		while(TRUE) {
			$this->info_tpl = strtolower(implode('/',$arr)) . '/index.tpl';
			if(Display::is_template('info/'.$this->info_tpl)) {
				break;
			}

			$this->info_tpl = strtolower(implode('/',$arr)) . '.tpl';
			if(Display::is_template('info/'.$this->info_tpl)) {
				break;
			}
			if(array_pop($arr) === NULL) {
				$this->info_tpl = 'error.tpl';
				break;
			}
		}
			
		if(isset($I2_ARGS[1])) {
			return 'Iodine Info: '.ucfirst($I2_ARGS[1]);
		}else if($mainhelppage) {
			return;
		}
		return 'Error';
	}
}
?>
