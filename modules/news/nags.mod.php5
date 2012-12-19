<?php
/**
* Just contains the definition for the {@link Module} {@link Nags}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage News
* @filesource
*/

/**
* The {@link Module} that displays {@link Nag}s.
* @package modules
* @subpackage News
*/
class Nags implements Module {

	private $template = 'pane.tpl';
	private $template_args = array();

	public function get_name() {
		return 'nags';
	}

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
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	public function init_box() {
	}

	public function display_box($display) {
		$display->disp('box.tpl');
	}

	public function init_pane() {
		global $I2_USER,$I2_SQL;
		$this->template_args['nags'] = Nag::get_user_nags();		
		return 'Nags';
	}

	public function display_pane($display) {
		$display->disp($this->template,$this->template_args);
	}

	/**
	* A hook invoked by Display before anything else to permit the nags module to ambush them.
	*
	* @return mixed FALSE if the user was not 'hooked' - display should continue if so.
	*/
	public static function login_hook() {
		global $I2_USER, $I2_SQL, $I2_ARGS;
		//return FALSE;
		//return TRUE;
		$res = $I2_SQL->query('SELECT nid FROM nag_group_map WHERE active=1 AND critical=1');
		$loc = implode('/',$I2_ARGS);
		$visible = FALSE;
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$nag = new Nag($row['nid']);
			if ($nag->is_visible($I2_USER)) {
				if ($nag->allows_visit($loc)) {
					// Visible and allowed - return
					return TRUE;	
				}
				$visible = TRUE;
			}
		}
		if ($visible) {
			// A nag is visible but the site the user wants is not allowed
			redirect('nags');
		}
		return FALSE;
	}


}
?>
