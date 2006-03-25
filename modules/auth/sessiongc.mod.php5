<?php
/**
* Just contains the definition for the {@link Module} SessionGC.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Auth
* @filesource
*/

/**
* A module to clean up expired sessions.
* @package core
* @subpackage Auth
*/
class SessionGC implements Module {
	const SESS_DIR = '/var/lib/php5/';

	private $loggedout = '';

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
		if($this->loggedout === FALSE) {
			$disp->disp('error.tpl');
			return;
		}
		if($this->loggedout) {
			$this->loggedout = substr($this->loggedout, 0, -2);
		}
		$disp->disp('success.tpl', array('loggedout' => $this->loggedout));
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'SessionGC';
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
	*/
	function init_pane() {
		clearstatcache();
		if($dh = opendir(self::SESS_DIR)) {
			while(FALSE !== ($file = readdir($dh))) {
				if(is_file($file) && is_readable($file)) {
					$sess = self::unserializesession(file_get_contents(self::SESS_DIR.$file));
					if($sess && $sess['i2_login_time'] > time()+i2config_get('timeout',500,'login')) {
						if(isset($sess['logout_funcs'])) {
							foreach($sess['logout_funcs'] as $callback) {
								if(is_callable($callback[0])) {
									call_user_func_array($callback[0], $callback[1]);
								}
							}
						}
						unlink($file);
						$this->loggedout .= "$file, ";
					}
				}
			}
		}
		else {
			$this->loggedout = FALSE;
			return 'Error';
		}
		return 'Success';
	}

	private static function unserializesession($data) {
		$vars=preg_split(
			'/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/',
			$data,-1,PREG_SPLIT_NO_EMPTY |
			PREG_SPLIT_DELIM_CAPTURE
		);
		for($i=0; $vars[$i]; $i++) {
			$result[$vars[$i++]]=unserialize($vars[$i]);
		}
		return $result;
	}
}
?>
