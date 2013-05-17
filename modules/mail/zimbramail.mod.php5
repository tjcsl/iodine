<?php
/**
* Just contains the definition for the {@link Zimbramail} {@link Module}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage mail
* @filesource
*/

/**
* A module to redirect to a Zimbra webmail interface for reading/sending mail
* @package modules
* @subpackage mail
*/
class Zimbramail implements Module {
	
	private $tpl_args = [];
	private $tpl = NULL;

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
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	/**
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_box($disp) {
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		$disp->disp($this->tpl, $this->tpl_args);
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'zimbramail';
	}

	/**
	* Performs all initialization necessary for this module to be 
	* displayed in an ibox.
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
		global $I2_USER, $I2_AUTH, $I2_ARGS;

		$base_url = i2config_get('url_prefix', 'https://webmail.tjhsst.edu', 'mail');

		if(isset($I2_ARGS[1])) {
			if($I2_ARGS[1] == 'redirect') {

				Display::stop_display();

				$account = $I2_USER->get_tjmail();
				$by = 'name';
				list($frac, $secs) = explode(' ', microtime());
				$timestamp = $secs . ((int) ($frac * 1000));
				$expires = 0;
				$hmac_data = "$account|$by|$expires|$timestamp";
				$hmac_key = i2config_get('hmac_key', NULL, 'mail');
				$preauth = hash_hmac('sha1', $hmac_data, $hmac_key);
		
				header("Location: $base_url/service/preauth?account=$account&by=$by&timestamp=$timestamp&expires=$expires&preauth=$preauth");
				exit();
			}
			
			// invalid URL given, just return our equivalent of a 404
			return FALSE;
		}
		
		$this->tpl_args['mail_url'] = "$base_url/";
		$this->tpl = 'iframe.tpl';

		return 'Zimbramail';

	}
}
?>
