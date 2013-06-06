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
class Nags extends Module {

	private $template = 'pane.tpl';
	private $template_args = [];

	function get_name() {
		return 'nags';
	}
	
	function init_box() {
		return 'nags';
	}

	function display_box($disp) {
		$disp->disp('box.tpl');
	}

	function init_pane() {
		$this->template_args['nags'] = Nag::get_user_nags();		
		return 'Nags';
	}

	function display_pane($disp) {
		$disp->disp($this->template,$this->template_args);
	}

	/**
	* A hook invoked by Display before anything else to permit the nags module to ambush them.
	*
	* @return mixed FALSE if the user was not 'hooked' - display should continue if so.
	*/
	public static function login_hook() {
		global $I2_USER, $I2_SQL, $I2_ARGS, $I2_CACHE;
		$res = unserialize($I2_CACHE->read(get_class(),'nag_group_map'));
		if($res === FALSE) {
			$res = $I2_SQL->query('SELECT nid FROM nag_group_map WHERE active=1 AND critical=1')->fetch_all_arrays(Result::ASSOC);;
			$I2_CACHE->store(get_class(),'nag_group_map',serialize($res));
		}
		$loc = implode('/',$I2_ARGS);
		$visible = FALSE;
		foreach ($res as $row) {
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
			//redirect('nags');
		}
		return FALSE;
	}


}
?>
