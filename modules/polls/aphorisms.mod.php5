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
class Aphorisms implements Module {

		  private $aphorism;
		  private $updated = FALSE;

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
			  $disp->smarty_assign('aphorism',$this->aphorism);
			  $disp->smarty_assign('updated',$this->updated);
			  $disp->disp('aphorisms_pane.tpl');
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	function get_name() {
			  return 'Aphorisms';
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
		if ($I2_USER->grade != 12) {
				  return FALSE;
		}
		$uidnumber = $I2_USER->uid;
		if (isSet($_REQUEST['posting'])) {
			$I2_SQL->query('REPLACE INTO aphorisms SET uid=%d,college=%s,collegeplans=%s,nationalmeritsemifinalist=%d,nationalmeritfinalist=%d,
					  nationalachievement=%d,hispanicachievement=%d,honor1=%s,honor2=%s,honor3=%s,aphorism=%s',$uidnumber,
					  $_REQUEST['college'],$_REQUEST['collegeplans'],isSet($_REQUEST['nationalmeritsemifinalist'])?1:0,
					  isSet($_REQUEST['nationalmeritfinalist'])?1:0,isSet($_REQUEST['nationalachievement'])?1:0,isSet($_REQUEST['hispanicachievement'])?1:0,
					  $_REQUEST['honor1'],$_REQUEST['honor2'],$_REQUEST['honor3'],$_REQUEST['aphorism']
			);
			$this->updated = TRUE;
		}
		$this->aphorism = $I2_SQL->query('SELECT * FROM aphorisms WHERE uid=%d',$uidnumber)->fetch_array(Result::ASSOC);
		return 'Aphorisms';
	}
}
?>
