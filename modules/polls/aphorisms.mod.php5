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

		  private $aphorism = '';
		  private $updated = FALSE;
		  private $template = 'aphorisms_pane.tpl';
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
			  $disp->smarty_assign('aphorism',$this->aphorism);
			  $disp->smarty_assign('updated',$this->updated);
			  $disp->disp($this->template,$this->template_args);
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
		global $I2_USER,$I2_SQL,$I2_ARGS;
		$uidnumber = FALSE;
		$admin = $I2_USER->is_group_member('aphorisms');
		$this->template_args['username'] = $I2_USER->name;
		$this->template_args['admin_aphorisms'] = $admin;
		if (isSet($I2_ARGS[1])) {
			if ($I2_ARGS[1] == 'choose') {
				$this->template = 'choose.tpl';
				$this->template_args['search_destination'] = 'aphorisms/searched/';
				return 'Find a Student';
			} else if ($I2_ARGS[1] == 'searched') {
				$this->template = 'choose.tpl';
				$this->template_args['results_destination'] = 'aphorisms/';
				$this->template_args['return_destination'] = 'aphorisms/choose/';
				$this->template_args['info'] = Search::get_results();
				return 'Search Results';
			} else if($I2_ARGS[1] == 'data') {
				$this->template = 'data.tpl';
				$this->template_args['data'] = $I2_SQL->query('SELECT * FROM aphorisms ORDER BY uid')->fetch_array(Result::ASSOC);
			}
			$uidnumber = $I2_ARGS[1];
			$user = new User($uidnumber);
			$this->template_args['username'] = $user->name;
		}
		if (!$uidnumber) {
			$uidnumber = $I2_USER->uid;
		}				  
		if ($uidnumber != $I2_USER->uid && !$admin) {
				  throw new I2Exception('You are not authorized to edit this student\'s aphorisms.');
		}
		if ($I2_USER->grade != 12) {
				  throw new I2Exception('User is not a senior!');
		}
		if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'edit') {
		}
		if (isSet($_REQUEST['posting'])) {
		   if (strlen(ereg_replace("\n| |\t|\r\n","",$_REQUEST['aphorism'])) >= 205) {
				throw new I2Exception('Your aphorism may not be longer than 200 characters, excluding spaces!');
			}
		/*	$I2_SQL->query('REPLACE INTO aphorisms SET uid=%d,college=%s,nationalmeritsemifinalist=%d,nationalmeritfinalist=%d,
					  nationalachievement=%d,hispanicachievement=%d,honor1=%s,honor2=%s,honor3=%s,aphorism=%s',$uidnumber,
					  $_REQUEST['college'],isSet($_REQUEST['nationalmeritsemifinalist'])?1:0,
					  isSet($_REQUEST['nationalmeritfinalist'])?1:0,isSet($_REQUEST['nationalachievement'])?1:0,isSet($_REQUEST['hispanicachievement'])?1:0,
					  $_REQUEST['honor1'],$_REQUEST['honor2'],$_REQUEST['honor3'],$_REQUEST['aphorism']
			);*/
			$I2_SQL->query('REPLACE INTO aphorisms SET uid=%d,college=%s,honor1=%s,honor2=%s,honor3=%s,aphorism=%s',$uidnumber,
					  $_REQUEST['college'],$_REQUEST['honor1'],$_REQUEST['honor2'],$_REQUEST['honor3'],$_REQUEST['aphorism']
			);
			$this->updated = TRUE;
		}
		$this->aphorism = $I2_SQL->query('SELECT * FROM aphorisms WHERE uid=%d',$uidnumber)->fetch_array(Result::ASSOC);
		return 'Aphorisms';
	}
}
?>
