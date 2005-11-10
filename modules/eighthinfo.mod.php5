<?php
/**
* Just contains the definition for the {@link Eighthinfo} module.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Eighthinfo
* @filesource
*/

/**
* The page for people (presumably freshmen...?) to learn about eighth period
* @package modules
* @subpackage Eighthinfo
*/
class Eighthinfo implements Module {

	private $template;
	
	/**
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_box($display) {
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_pane($display) {
		$display->disp($this->template);
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	function get_name() {
		return 'Eighthinfo';
	}

	function is_intrabox() {
		return false;
	}

	/**
	* Performs all initialization necessary for this module to be 
	* displayed in an ibox.
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
	* @abstract
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
		global $I2_ARGS;

		if( count($I2_ARGS) > 1) {
			$method = $I2_ARGS[1];
			if(method_exists($this, $method)) {
				$pagename = $this->$method();
				return 'Eighth Period Information: '.$pagename;
			}
		}

		// catch anything that didn't return above
		$this->template = "eighthinfo.tpl";
		return 'Eighth Period Information';
	}

	function eighthwhat() {
		$this->template = "eighthwhat.tpl";
		return "What is eighth period?";
	}

	function eighthsignup() {
		$this->template = "eighthsignup.tpl";
		return "How to sign up for eighth period";
	}
	
	function eighthclear() {
		$this->template = "eighthclear.tpl";
		return "How to clear an eighth period absence";
	}

	function eighthstart() {
		$this->template = "eighthstart.tpl";
		return "How to start a club";
	}

	function eighthmakeup() {
		$this->template = "eighthmakeup.tpl";
		return "How to use your eighth period record for make-up credit";
	}
	
	function eighthactive() {
		$this->template = "eighthactive.tpl";
		return "Active clubs";
	}
		
	function eighthinactive() {
		$this->template = "eighthinactive.tpl";
		return "Inactive clubs";
	}
}
?>

