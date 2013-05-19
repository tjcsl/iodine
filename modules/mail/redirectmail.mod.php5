<?php
/**
* Just contains the definition for the {@link RedirectMail} {@link Module}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage mail
* @filesource
*/

/**
* A module to redirect to a webmail interface for reading/sending mail
* @package modules
* @subpackage mail
*/
class RedirectMail extends Module {
	
	private $tpl_args = [];
	private $tpl = NULL;
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'redirectmail';
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

		$url = i2config_get('redirectmailurl', 'https://webmail.tjhsst.edu', 'mail');

		header("Location: " . $url);

		return 'redirectmail';
	}
}
?>
