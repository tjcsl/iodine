<?php
/**
* Just contains the definition for the interface {@link Module}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Module
* @filesource
*/

/**
* The API for all Intranet2 modules to extend.
* @package core
* @subpackage Module
*/
class Prom implements Module {

		  private $template = 'prom_pane.tpl';
		  private $template_args = array();

	/**
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_box($disp) {
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_pane($disp) {
		$disp->disp($this->template,$this->template_args);
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	function get_name() {
		return 'Prom Registration';
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
		global $I2_USER,$I2_SQL;
		if (!$I2_USER->grade == 12) {
			throw new I2Exception('Only seniors may register for the SENIOR prom!');
		}
		if (isSet($_POST['firstpdteacher'])) {
			if ($_POST['attending'] == 1) {
                		$going = 1;
        		} else {
               			$going = 0;
        		}
			$I2_SQL->query('REPLACE INTO prom SET uid=%d,dateschool=%s,attending=%d,teacher=%s,room=%s,datename=%s',$I2_USER->uid,$_POST['dateschool'],$going,$_POST['firstpdteacher'],$_POST['firstpdroom'],$_POST['datename']);
			redirect();
		}
		return 'Prom Registration';
	}

}
?>
